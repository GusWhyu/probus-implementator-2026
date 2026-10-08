<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTicketsTableToMatchSheets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['assignee_id']);
            $table->dropColumn('assignee_id');
            
            $table->unsignedBigInteger('advisor_id')->nullable()->after('user_id');
            $table->foreign('advisor_id')->references('id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('system')->nullable()->after('module_system_id');
            $table->foreign('system')->references('id')->on('kategori')->nullOnDelete();

            $table->string('link_id')->nullable()->unique()->after('updated_at');
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
            $table->dropColumn('link_id');

            $table->dropForeign(['system']);
            $table->dropColumn('system');

            $table->dropForeign(['advisor_id']);
            $table->dropColumn('advisor_id');
            
            $table->unsignedBigInteger('assignee_id')->nullable()->after('user_id');
            $table->foreign('assignee_id')->references('id')->on('users')->nullOnDelete();
        });
    }
}
