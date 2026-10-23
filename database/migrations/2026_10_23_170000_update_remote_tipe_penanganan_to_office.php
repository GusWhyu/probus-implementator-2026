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
        // Update all tickets with tipe_penanganan 'Remote' to 'Office'
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'tipe_penanganan')) {
            DB::table('tickets')
                ->whereRaw('LOWER(tipe_penanganan) = ?', ['remote'])
                ->update(['tipe_penanganan' => 'Office']);
        }

        // Update all request records with tipe_penanganan 'Remote' to 'Office' if any
        if (Schema::hasTable('request') && Schema::hasColumn('request', 'tipe_penanganan')) {
            DB::table('request')
                ->whereRaw('LOWER(tipe_penanganan) = ?', ['remote'])
                ->update(['tipe_penanganan' => 'Office']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No reverse operation needed as Remote is removed
    }
};
