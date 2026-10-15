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
        // 1. Perbesar kapasitas kolom score di performance_review_module_details agar tidak overflow (misal bobot 80 x 20 = 1600)
        DB::statement("ALTER TABLE `performance_review_module_details` MODIFY `score` DECIMAL(10,2) NOT NULL DEFAULT '0.00'");

        // 2. Tambahkan kolom pendukung di performance_reviews untuk transparansi formula Σ(bobot x qty)
        Schema::table('performance_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('performance_reviews', 'raw_ticket_score')) {
                $table->decimal('raw_ticket_score', 10, 2)->default(0)->after('module_score');
            }
            if (!Schema::hasColumn('performance_reviews', 'max_ticket_score')) {
                $table->decimal('max_ticket_score', 10, 2)->default(0)->after('raw_ticket_score');
            }
            if (!Schema::hasColumn('performance_reviews', 'team_portion')) {
                $table->decimal('team_portion', 5, 2)->default(0)->after('max_ticket_score');
            }
        });

        // 3. Beri bobot awal yang realistis pada master module_systems jika saat ini masih 0
        $weights = [
            'FO' => 70,
            'POS' => 70,
            'STORE' => 75,
            'Cost Control' => 85,
            'Accounting' => 90,
            'Ezzy' => 70,
            'PR/PO Online' => 80,
            'OT' => 60,
        ];

        foreach ($weights as $name => $bobot) {
            DB::table('module_systems')
                ->where('name', $name)
                ->where('bobot', 0)
                ->update(['bobot' => $bobot]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $dropCols = [];
            if (Schema::hasColumn('performance_reviews', 'raw_ticket_score')) {
                $dropCols[] = 'raw_ticket_score';
            }
            if (Schema::hasColumn('performance_reviews', 'max_ticket_score')) {
                $dropCols[] = 'max_ticket_score';
            }
            if (Schema::hasColumn('performance_reviews', 'team_portion')) {
                $dropCols[] = 'team_portion';
            }
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });

        DB::statement("ALTER TABLE `performance_review_module_details` MODIFY `score` DECIMAL(5,2) NOT NULL DEFAULT '0.00'");
    }
};
