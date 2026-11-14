<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan composite unique constraint pada (kategori_id, order) dan (kategori_id, name)
     * untuk mencegah duplikasi nomor urut maupun nama modul pada sistem yang sama.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('menu_systems', function (Blueprint $table) {
            $table->unique(['kategori_id', 'order'], 'menu_systems_kategori_order_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('menu_systems', function (Blueprint $table) {
            $table->dropUnique('menu_systems_kategori_order_unique');
        });
    }
};
