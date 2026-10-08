<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePerformanceReviewsTableToMatchSheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->decimal('module_score', 8, 2)->after('total_score')->default(0);
            $table->decimal('performance_score', 8, 2)->after('module_score')->default(0);
            $table->decimal('review_score', 8, 2)->after('performance_score')->default(0);
            
            $table->enum('status', ['DRAFT', 'FINAL'])->after('review_score')->default('DRAFT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('module_score');
            $table->dropColumn('performance_score');
            $table->dropColumn('review_score');
        });

        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->enum('status', ['DRAFT', 'PUBLISHED'])->after('total_score')->default('DRAFT');
        });
    }
}
