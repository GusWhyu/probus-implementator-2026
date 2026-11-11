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
        // 1. Tambah kolom updated_by ke manual_books jika belum ada
        if (Schema::hasTable('manual_books') && !Schema::hasColumn('manual_books', 'updated_by')) {
            Schema::table('manual_books', function (Blueprint $table) {
                $table->foreignId('updated_by')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            });
        }

        // 2. Buat tabel manual_book_logs untuk mencatat audit trail aktivitas
        if (!Schema::hasTable('manual_book_logs')) {
            Schema::create('manual_book_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('manual_book_id')->constrained('manual_books')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('action', 50); // CREATED, UPDATED, STATUS_CHANGED, REORDERED
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Seed riwayat awal untuk data sub menu yang sudah ada
        if (Schema::hasTable('manual_books')) {
            $existingBooks = DB::table('manual_books')->get();
            foreach ($existingBooks as $book) {
                // Set default updated_by ke user_id pembuat jika masih null
                DB::table('manual_books')->where('id', $book->id)->update([
                    'updated_by' => $book->user_id,
                ]);

                $creator = DB::table('users')->where('id', $book->user_id)->first();
                $creatorName = $creator ? $creator->name : 'Superadministrator';
                $statusLabel = $book->status === 'published' ? 'Terbit' : 'Draft';

                // Catat log pembuatan awal
                DB::table('manual_book_logs')->insert([
                    'manual_book_id' => $book->id,
                    'user_id' => $book->user_id,
                    'action' => 'CREATED',
                    'description' => "Sub menu panduan \"{$book->title}\" dibuat ({$statusLabel}) oleh {$creatorName}.",
                    'created_at' => $book->created_at ?: now(),
                    'updated_at' => $book->created_at ?: now(),
                ]);

                // Jika pernah diperbarui setelah tanggal pembuatan
                if ($book->updated_at && $book->updated_at > $book->created_at) {
                    DB::table('manual_book_logs')->insert([
                        'manual_book_id' => $book->id,
                        'user_id' => $book->user_id,
                        'action' => 'UPDATED',
                        'description' => "Isi panduan sub menu \"{$book->title}\" diperbarui oleh {$creatorName}.",
                        'created_at' => $book->updated_at,
                        'updated_at' => $book->updated_at,
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
        Schema::dropIfExists('manual_book_logs');

        if (Schema::hasTable('manual_books') && Schema::hasColumn('manual_books', 'updated_by')) {
            Schema::table('manual_books', function (Blueprint $table) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            });
        }
    }
};
