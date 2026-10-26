<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('tipe_penanganan');
        });

        // Backfill existing tickets with DATE(created_at)
        DB::statement('UPDATE tickets SET start_date = DATE(created_at) WHERE start_date IS NULL');
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
};
