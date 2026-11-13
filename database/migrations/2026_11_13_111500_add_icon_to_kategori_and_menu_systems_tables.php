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
        // 1. Tambah kolom icon pada tabel kategori
        if (Schema::hasTable('kategori') && !Schema::hasColumn('kategori', 'icon')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->string('icon', 50)->nullable()->after('name');
            });
        }

        // 2. Tambah kolom icon pada tabel menu_systems
        if (Schema::hasTable('menu_systems') && !Schema::hasColumn('menu_systems', 'icon')) {
            Schema::table('menu_systems', function (Blueprint $table) {
                $table->string('icon', 50)->nullable()->after('name');
            });
        }

        // 3. Seed icon bawaan untuk kategori sistem yang sudah ada
        $categoryIconMap = [
            'PHIS'         => 'building-2',
            'PRESTO'       => 'monitor-check',
            'PPOS'         => 'printer',
            'SELF ORDER'   => 'smartphone',
            'PR-PO ONLINE' => 'package',
            'ORDER TAKER'  => 'utensils',
            'VRESTROPICAL' => 'sparkles',
            'PPE'          => 'zap',
            'BARONG POS'   => 'laptop',
        ];

        foreach ($categoryIconMap as $catName => $icon) {
            DB::table('kategori')
                ->where('name', $catName)
                ->whereNull('icon')
                ->update(['icon' => $icon]);
        }

        // Set fallback icon untuk kategori lain yang belum terisi
        DB::table('kategori')
            ->whereNull('icon')
            ->update(['icon' => 'monitor-check']);

        // 4. Seed icon bawaan untuk menu_systems berdasarkan nama atau urutan modul
        $menuSystems = DB::table('menu_systems')->get();
        foreach ($menuSystems as $menu) {
            $icon = 'book-open';
            $name = strtolower($menu->name);

            if (str_contains($name, 'setup') || str_contains($name, 'master') || $menu->order == 1) {
                $icon = 'settings';
            } elseif (str_contains($name, 'transaksi') || str_contains($name, 'order taker') || str_contains($name, 'production') || str_contains($name, 'sales') || $menu->order == 2) {
                $icon = 'utensils';
            } elseif (str_contains($name, 'outlet') || str_contains($name, 'activity') || $menu->order == 3) {
                $icon = 'store';
            } elseif (str_contains($name, 'cost') || str_contains($name, 'control') || $menu->order == 4) {
                $icon = 'pie-chart';
            } elseif (str_contains($name, 'purchasing') || str_contains($name, 'purcashing') || str_contains($name, 'ap') || $menu->order == 5) {
                $icon = 'shopping-bag';
            } elseif (str_contains($name, 'ar') || str_contains($name, 'receivable') || $menu->order == 6) {
                $icon = 'receipt';
            } elseif (str_contains($name, 'general cashier') || str_contains($name, 'cashier') || str_contains($name, 'kas') || $menu->order == 7) {
                $icon = 'wallet';
            } elseif (str_contains($name, 'accounting') || str_contains($name, 'akuntansi') || $menu->order == 8) {
                $icon = 'bar-chart-3';
            } elseif (str_contains($name, 'report') || str_contains($name, 'laporan') || $menu->order == 9) {
                $icon = 'trending-up';
            } elseif (str_contains($name, 'tools') || str_contains($name, 'alat') || $menu->order == 10) {
                $icon = 'sliders';
            } elseif (str_contains($name, 'kitchen') || str_contains($name, 'kds')) {
                $icon = 'monitor';
            } elseif (str_contains($name, 'calendar') || str_contains($name, 'agenda')) {
                $icon = 'calendar';
            } elseif (str_contains($name, 'calculator')) {
                $icon = 'calculator';
            } elseif (str_contains($name, 'reconnecting') || str_contains($name, 'network') || str_contains($name, 'jaringan')) {
                $icon = 'network';
            }

            DB::table('menu_systems')
                ->where('id', $menu->id)
                ->whereNull('icon')
                ->update(['icon' => $icon]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('kategori') && Schema::hasColumn('kategori', 'icon')) {
            Schema::table('kategori', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }

        if (Schema::hasTable('menu_systems') && Schema::hasColumn('menu_systems', 'icon')) {
            Schema::table('menu_systems', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }
    }
};
