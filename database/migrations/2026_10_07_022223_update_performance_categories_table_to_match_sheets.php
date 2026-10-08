<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePerformanceCategoriesTableToMatchSheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('performance_categories', function (Blueprint $table) {
            $table->dropColumn('weight');
            $table->dropColumn('description');
            
            $table->decimal('bobot', 5, 2)->after('name')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_categories', function (Blueprint $table) {
            $table->dropColumn('bobot');
            
            $table->decimal('weight', 5, 2)->comment('Weight percentage, e.g. 30.00 for 30%')->after('name')->default(0);
            $table->text('description')->nullable()->after('weight');
        });
    }
}
