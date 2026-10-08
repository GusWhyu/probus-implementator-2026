<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTicketSurveysTableToMatchSheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_surveys', function (Blueprint $table) {
            $table->dropColumn(['submitted_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_surveys', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
}
