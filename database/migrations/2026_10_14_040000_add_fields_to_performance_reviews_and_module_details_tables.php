<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Update performance_review_module_details: tambahkan kolom rata_rata_waktu
        Schema::table('performance_review_module_details', function (Blueprint $table) {
            if (!Schema::hasColumn('performance_review_module_details', 'rata_rata_waktu')) {
                $table->string('rata_rata_waktu')->nullable()->after('total_ticket');
            }
        });

        // 2. Update performance_reviews: tambahkan fields total_ticket, rata_rata_waktu, total_ticket_yang_di_advice
        Schema::table('performance_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('performance_reviews', 'total_ticket')) {
                $table->integer('total_ticket')->default(0)->after('review_score');
            }
            if (!Schema::hasColumn('performance_reviews', 'rata_rata_waktu')) {
                $table->string('rata_rata_waktu')->nullable()->after('total_ticket');
            }
            if (!Schema::hasColumn('performance_reviews', 'total_ticket_yang_di_advice')) {
                $table->integer('total_ticket_yang_di_advice')->default(0)->after('rata_rata_waktu');
            }
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
            if (Schema::hasColumn('performance_review_module_details', 'rata_rata_waktu')) {
                $table->dropColumn('rata_rata_waktu');
            }
        });

        Schema::table('performance_reviews', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('performance_reviews', 'total_ticket')) {
                $columnsToDrop[] = 'total_ticket';
            }
            if (Schema::hasColumn('performance_reviews', 'rata_rata_waktu')) {
                $columnsToDrop[] = 'rata_rata_waktu';
            }
            if (Schema::hasColumn('performance_reviews', 'total_ticket_yang_di_advice')) {
                $columnsToDrop[] = 'total_ticket_yang_di_advice';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
