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
        Schema::create('menu_has_manual_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_system_id')->constrained('menu_systems')->onDelete('cascade');
            $table->foreignId('manual_book_id')->constrained('manual_books')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_has_manual_books');
    }
};

