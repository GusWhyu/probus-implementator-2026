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
        Schema::table('performance_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('performance_reviews', 'period_key')) {
                $table->string('period_key', 30)->nullable()->index()->after('periode');
            }
            if (!Schema::hasColumn('performance_reviews', 'period_type')) {
                $table->string('period_type', 10)->default('1m')->index()->after('period_key');
            }
            if (!Schema::hasColumn('performance_reviews', 'periode_end')) {
                $table->date('periode_end')->nullable()->after('period_type');
            }
        });

        // Backfill existing reviews to 1m period
        DB::statement("
            UPDATE performance_reviews 
            SET period_key = DATE_FORMAT(periode, '%Y-%m'),
                period_type = '1m',
                periode_end = LAST_DAY(periode)
            WHERE period_key IS NULL AND periode IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('performance_reviews', 'periode_end')) {
                $table->dropColumn('periode_end');
            }
            if (Schema::hasColumn('performance_reviews', 'period_type')) {
                $table->dropColumn('period_type');
            }
            if (Schema::hasColumn('performance_reviews', 'period_key')) {
                $table->dropColumn('period_key');
            }
        });
    }
};
