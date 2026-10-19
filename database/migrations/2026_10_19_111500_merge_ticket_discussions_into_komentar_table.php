<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MergeTicketDiscussionsIntoKomentarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tambahkan kolom ticket_id pada tabel komentar
        Schema::table('komentar', function (Blueprint $table) {
            if (!Schema::hasColumn('komentar', 'ticket_id')) {
                $table->unsignedBigInteger('ticket_id')->nullable()->after('request_id');
                $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            }
        });

        // 2. Migrasikan data yang sudah ada dari ticket_discussions ke komentar (jika tabel ada)
        if (Schema::hasTable('ticket_discussions')) {
            $discussions = DB::table('ticket_discussions')->get();
            foreach ($discussions as $disc) {
                DB::table('komentar')->insert([
                    'ticket_id' => $disc->ticket_id,
                    'request_id' => null,
                    'user_id' => $disc->user_id,
                    'body' => $disc->message,
                    'created_at' => $disc->created_at,
                    'updated_at' => $disc->updated_at,
                ]);
            }

            // 3. Hapus tabel ticket_discussions sesuai permintaan user
            Schema::dropIfExists('ticket_discussions');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate ticket_discussions
        if (!Schema::hasTable('ticket_discussions')) {
            Schema::create('ticket_discussions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ticket_id');
                $table->unsignedBigInteger('user_id');
                $table->text('message');
                $table->timestamps();

                $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            // Restore ticket comments back to ticket_discussions
            $ticketComments = DB::table('komentar')->whereNotNull('ticket_id')->get();
            foreach ($ticketComments as $tc) {
                DB::table('ticket_discussions')->insert([
                    'ticket_id' => $tc->ticket_id,
                    'user_id' => $tc->user_id,
                    'message' => $tc->body,
                    'created_at' => $tc->created_at,
                    'updated_at' => $tc->updated_at,
                ]);
            }
        }

        Schema::table('komentar', function (Blueprint $table) {
            if (Schema::hasColumn('komentar', 'ticket_id')) {
                $table->dropForeign(['ticket_id']);
                $table->dropColumn('ticket_id');
            }
        });
    }
}
