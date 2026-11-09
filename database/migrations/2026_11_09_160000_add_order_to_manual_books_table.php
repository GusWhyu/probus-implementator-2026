<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToManualBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('manual_books', 'order')) {
            Schema::table('manual_books', function (Blueprint $table) {
                $table->integer('order')->default(1)->after('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('manual_books', 'order')) {
            Schema::table('manual_books', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
    }
}

