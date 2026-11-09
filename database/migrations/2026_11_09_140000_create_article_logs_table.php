<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // CREATED, UPDATED, VISIBILITY_CHANGED, STATUS_CHANGED
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed riwayat awal untuk artikel-artikel yang sudah ada di database
        if (Schema::hasTable('articles')) {
            $existingArticles = DB::table('articles')->get();
            foreach ($existingArticles as $art) {
                $user = DB::table('users')->where('id', $art->user_id)->first();
                $userName = $user ? $user->name : 'Superadministrator';
                $visLabel = $art->visibility === 'publik' ? 'BLOG KLIEN' : 'INTERNAL';

                // Catat log pembuatan awal artikel
                DB::table('article_logs')->insert([
                    'article_id' => $art->id,
                    'user_id' => $art->user_id,
                    'action' => 'CREATED',
                    'description' => "Artikel #{$art->id} berhasil dibuat dengan visibilitas {$visLabel} (" . ucfirst($art->status) . ") oleh {$userName}.",
                    'created_at' => $art->created_at ?: now(),
                    'updated_at' => $art->created_at ?: now(),
                ]);

                // Jika artikel pernah diperbarui setelah tanggal pembuatan
                if ($art->updated_at && $art->updated_at > $art->created_at) {
                    DB::table('article_logs')->insert([
                        'article_id' => $art->id,
                        'user_id' => $art->user_id,
                        'action' => 'UPDATED',
                        'description' => "Artikel diperbarui dan disimpan oleh {$userName}.",
                        'created_at' => $art->updated_at,
                        'updated_at' => $art->updated_at,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_logs');
    }
};

