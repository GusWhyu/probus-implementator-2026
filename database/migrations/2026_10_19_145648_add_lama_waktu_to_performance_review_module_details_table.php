<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLamaWaktuToPerformanceReviewModuleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('performance_review_module_details', function (Blueprint $table) {
            if (!Schema::hasColumn('performance_review_module_details', 'lama_waktu')) {
                $table->string('lama_waktu')->nullable()->after('total_ticket');
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
            if (Schema::hasColumn('performance_review_module_details', 'lama_waktu')) {
                $table->dropColumn('lama_waktu');
            }
        });
    }
}
