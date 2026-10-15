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
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('takeover_duration_minutes')->nullable()->default(60)->after('pending_advisor_at');
            $table->timestamp('pending_advisor_expires_at')->nullable()->after('takeover_duration_minutes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['takeover_duration_minutes', 'pending_advisor_expires_at']);
        });
    }
};
