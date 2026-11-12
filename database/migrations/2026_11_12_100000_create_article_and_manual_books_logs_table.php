<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel terpusat article_and_manual_books_logs
        if (!Schema::hasTable('article_and_manual_books_logs')) {
            Schema::create('article_and_manual_books_logs', function (Blueprint $table) {
                $table->id();
                $table->string('loggable_type');
                $table->unsignedBigInteger('loggable_id');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 50);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['loggable_type', 'loggable_id'], 'amb_logs_loggable_idx');
                $table->index('user_id', 'amb_logs_user_id_idx');
                $table->index('action', 'amb_logs_action_idx');
                $table->index('created_at', 'amb_logs_created_at_idx');
            });
        }

        // 2. Migrasikan seluruh data riwayat dari tabel article_logs
        if (Schema::hasTable('article_logs')) {
            $articleLogs = DB::table('article_logs')->get();
            $now = now();
            foreach ($articleLogs as $log) {
                DB::table('article_and_manual_books_logs')->insert([
                    'loggable_type' => 'App\Models\Article',
                    'loggable_id' => $log->article_id,
                    'user_id' => $log->user_id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'created_at' => $log->created_at ?? $now,
                    'updated_at' => $log->updated_at ?? $now,
                ]);
            }
        }

        // 3. Migrasikan seluruh data riwayat dari tabel manual_book_logs
        if (Schema::hasTable('manual_book_logs')) {
            $manualBookLogs = DB::table('manual_book_logs')->get();
            $now = now();
            foreach ($manualBookLogs as $log) {
                DB::table('article_and_manual_books_logs')->insert([
                    'loggable_type' => 'App\Models\ManualBook',
                    'loggable_id' => $log->manual_book_id,
                    'user_id' => $log->user_id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'created_at' => $log->created_at ?? $now,
                    'updated_at' => $log->updated_at ?? $now,
                ]);
            }
        }

        // 4. Hapus tabel lama yang sudah digabung
        Schema::dropIfExists('article_logs');
        Schema::dropIfExists('manual_book_logs');
        Schema::dropIfExists('activity_logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Kembalikan tabel article_logs
        if (!Schema::hasTable('article_logs')) {
            Schema::create('article_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('action', 50);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['article_id', 'created_at']);
            });

            if (Schema::hasTable('article_and_manual_books_logs')) {
                $logs = DB::table('article_and_manual_books_logs')->where('loggable_type', 'App\Models\Article')->get();
                foreach ($logs as $log) {
                    DB::table('article_logs')->insert([
                        'article_id' => $log->loggable_id,
                        'user_id' => $log->user_id ?: 1,
                        'action' => $log->action,
                        'description' => $log->description,
                        'created_at' => $log->created_at,
                        'updated_at' => $log->updated_at,
                    ]);
                }
            }
        }

        // 2. Kembalikan tabel manual_book_logs
        if (!Schema::hasTable('manual_book_logs')) {
            Schema::create('manual_book_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('manual_book_id')->constrained('manual_books')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('action', 50);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['manual_book_id', 'created_at']);
            });

            if (Schema::hasTable('article_and_manual_books_logs')) {
                $logs = DB::table('article_and_manual_books_logs')->where('loggable_type', 'App\Models\ManualBook')->get();
                foreach ($logs as $log) {
                    DB::table('manual_book_logs')->insert([
                        'manual_book_id' => $log->loggable_id,
                        'user_id' => $log->user_id ?: 1,
                        'action' => $log->action,
                        'description' => $log->description,
                        'created_at' => $log->created_at,
                        'updated_at' => $log->updated_at,
                    ]);
                }
            }
        }

        // 3. Hapus tabel article_and_manual_books_logs
        Schema::dropIfExists('article_and_manual_books_logs');
    }
};
