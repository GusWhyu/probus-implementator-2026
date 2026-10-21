<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDescriptionAndDeskripsiToLongtext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `tickets` MODIFY `description` LONGTEXT NULL");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `request` MODIFY `deskripsi` LONGTEXT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `tickets` MODIFY `description` TEXT NULL");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `request` MODIFY `deskripsi` TEXT NULL");
    }
}
