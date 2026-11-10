<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\MenuSystem;
use App\Models\ManualBook;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sesuai arahan PM: Sub Menu dan Bab adalah hal yang sama.
     * Semua bab panduan dihubungkan langsung ke modul induknya (Menu Utama),
     * dan struktur modul kembali dibuat bersih tanpa nesting bertingkat.
     */
    public function up()
    {
        // 1. Pindahkan Bab Panduan dari child sub-menus ke Modul Transaksi (id: 35)
        $transaksi = MenuSystem::find(35);
        if ($transaksi) {
            // Bab Order Taker & Kasir (id: 1)
            $chap1 = ManualBook::find(1);
            if ($chap1) {
                $chap1->update(['order' => 2, 'title' => '02. Order Taker & Transaksi Meja']);
                DB::table('menu_has_manual_books')->updateOrInsert(
                    ['manual_book_id' => 1, 'menu_system_id' => 35],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }

            // Bab QRIS & EDC (id: 3)
            $chap3 = ManualBook::find(3);
            if ($chap3) {
                $chap3->update(['order' => 3, 'title' => '03. Pembayaran Multi-Metode & Split Bill']);
                DB::table('menu_has_manual_books')->updateOrInsert(
                    ['manual_book_id' => 3, 'menu_system_id' => 35],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }

            // Bab Sales (id: 7)
            $chap7 = ManualBook::find(7);
            if ($chap7) {
                $chap7->update(['order' => 1, 'title' => '01. Sales']);
                DB::table('menu_has_manual_books')->updateOrInsert(
                    ['manual_book_id' => 7, 'menu_system_id' => 35],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // 2. Pindahkan Bab Menu Maintenance (id: 16) ke Modul Hardware & Printer (id: 1)
        $hardware = MenuSystem::find(1);
        if ($hardware) {
            $chap16 = ManualBook::find(16);
            if ($chap16) {
                $chap16->update(['order' => 5, 'title' => '05. Menu Maintenance']);
                DB::table('menu_has_manual_books')->updateOrInsert(
                    ['manual_book_id' => 16, 'menu_system_id' => 1],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // 3. Pindahkan relasi artikel di Bank Solusi dari id 2 ke id 35
        DB::table('menu_has_articles')->where('menu_system_id', 2)->update(['menu_system_id' => 35]);

        // 4. Bersihkan relasi lama di menu_has_manual_books untuk menu_systems 2, 3, 106, 107
        DB::table('menu_has_manual_books')->whereIn('menu_system_id', [2, 3, 106, 107])->delete();

        // 5. Hapus dummy sub-menus 106 dan 107, serta id 2 dan 3 yang sudah digabung ke Transaksi
        MenuSystem::whereIn('id', [106, 107, 2, 3])->delete();

        // 6. Reset parent_id seluruh modul yang tersisa menjadi null
        MenuSystem::whereNotNull('parent_id')->update(['parent_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // No-op rollback
    }
};
