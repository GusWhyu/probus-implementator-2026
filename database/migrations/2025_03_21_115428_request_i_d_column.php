<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RequestIDColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('updatesystem', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('request')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('updatesystem', function (Blueprint $table) {
            //
        });
    }
}
