<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Membersihkan modul-modul legacy/duplikat kosong yang tersisa sebelum standarisasi 10 modul,
     * sehingga setiap sistem hanya memiliki 10 modul standar (01 s/d 10) tanpa bentrok nomor.
     *
     * @return void
     */
    public function up(): void
    {
        $standardOrder = [
            1 => 'Setup Master',
            2 => 'Transaksi',
            3 => 'Outlet Activity',
            4 => 'Cost Control',
            5 => 'Purcashing AP',
            6 => 'AR',
            7 => 'General Cashier',
            8 => 'Accounting System',
            9 => 'Report',
            10 => 'Tools',
        ];

        $standardNames = array_values($standardOrder);

        // 1. Hapus modul non-standar yang kosong (tidak memiliki manual book dan tidak memiliki artikel)
        $legacyModules = DB::table('menu_systems')
            ->whereNotIn('name', $standardNames)
            ->get();

        foreach ($legacyModules as $mod) {
            $hasBooks = DB::table('menu_has_manual_books')->where('menu_system_id', $mod->id)->exists();
            $hasArticles = DB::table('menu_has_articles')->where('menu_system_id', $mod->id)->exists();

            if (!$hasBooks && !$hasArticles) {
                DB::table('menu_systems')->where('id', $mod->id)->delete();
            }
        }

        // 2. Pastikan nomor order 1 s/d 10 konsisten dan rapi untuk setiap kategori/sistem
        foreach ($standardOrder as $order => $name) {
            DB::table('menu_systems')
                ->where('name', $name)
                ->update(['order' => $order]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Data pembersihan modul duplikat kosong tidak perlu di-restore
    }
};
