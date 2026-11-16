<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('menu_has_manual_books', 'order')) {
            Schema::table('menu_has_manual_books', function (Blueprint $table) {
                $table->integer('order')->default(1)->after('manual_book_id');
            });
        }

        // 1. Copy initial order from manual_books to menu_has_manual_books
        DB::statement("
            UPDATE menu_has_manual_books
            JOIN manual_books ON manual_books.id = menu_has_manual_books.manual_book_id
            SET menu_has_manual_books.`order` = COALESCE(manual_books.`order`, 1)
        ");

        // 2. Set specific correct module orders for existing linked data

        // MenuSystem 1 (PRESTO - 01. Setup Master)
        $setupMasterOrders = [
            12 => 1, // 1. Restauran Sales Login
            13 => 2, // 2. List Of Sales Transaction
            14 => 3, // 3. Reservation Form
            15 => 4, // 4. Open Shift
            16 => 5, // 05. Menu Maintenance
            7  => 6, // 01. Sales (ditautkan)
            17 => 7, // 1. Master Menu (ditautkan)
        ];
        foreach ($setupMasterOrders as $mbId => $ord) {
            DB::table('menu_has_manual_books')
                ->where('menu_system_id', 1)
                ->where('manual_book_id', $mbId)
                ->update(['order' => $ord]);
        }

        // MenuSystem 35 (PRESTO - 02. Transaksi)
        $transaksiOrders = [
            7 => 1, // 01. Sales
            1 => 2, // 02. Order Taker & Transaksi Meja
            3 => 3, // 03. Pembayaran Multi-Metode & Split Bill
        ];
        foreach ($transaksiOrders as $mbId => $ord) {
            DB::table('menu_has_manual_books')
                ->where('menu_system_id', 35)
                ->where('manual_book_id', $mbId)
                ->update(['order' => $ord]);
        }

        // MenuSystem 6 (PHIS - 01. Setup Master)
        $phisSetupMasterOrders = [
            17 => 1, // 1. Master Menu
            18 => 2, // 2. Master Customer
            7  => 3, // 01. Sales (ditautkan)
        ];
        foreach ($phisSetupMasterOrders as $mbId => $ord) {
            DB::table('menu_has_manual_books')
                ->where('menu_system_id', 6)
                ->where('manual_book_id', $mbId)
                ->update(['order' => $ord]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('menu_has_manual_books', 'order')) {
            Schema::table('menu_has_manual_books', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
    }
};
