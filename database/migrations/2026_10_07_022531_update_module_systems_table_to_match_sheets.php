<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateModuleSystemsTableToMatchSheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('module_systems', function (Blueprint $table) {
            $table->dropColumn('description');
            
            $table->integer('bobot')->after('name')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_systems', function (Blueprint $table) {
            $table->dropColumn('bobot');
            
            $table->text('description')->nullable()->after('name');
        });
    }
}
