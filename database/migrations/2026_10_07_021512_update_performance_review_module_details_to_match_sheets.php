<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePerformanceReviewModuleDetailsToMatchSheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('performance_review_module_details', function (Blueprint $table) {
            $table->dropForeign(['module_system_id']);
            $table->dropColumn('module_system_id');
            $table->dropColumn('ticket_count');
            $table->dropColumn('module_score');
            
            $table->foreignId('module_id')->after('id')->constrained('performance_categories')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->after('module_id')->default(0);
            $table->integer('bobot')->after('score')->default(0);
            $table->integer('total_ticket')->after('bobot')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_review_module_details', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
            $table->dropColumn('score');
            $table->dropColumn('bobot');
            $table->dropColumn('total_ticket');

            $table->foreignId('module_system_id')->after('performance_review_id')->constrained('module_systems')->cascadeOnDelete();
            $table->integer('ticket_count')->default(0)->after('module_system_id');
            $table->decimal('module_score', 8, 2)->default(0)->after('ticket_count');
        });
    }
}
