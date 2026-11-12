<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use App\Models\MenuSystem;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Standarisasi 10 Modul Sistem Utama untuk SEMUA kategori / sistem:
     * 1. Setup Master
     * 2. Transaksi
     * 3. Outlet Activity
     * 4. Cost Control
     * 5. Purcashing AP
     * 6. AR
     * 7. General Cashier
     * 8. Accounting System
     * 9. Report
     * 10. Tools
     *
     * @return void
     */
    public function up()
    {
        $targetModules = [
            1 => [
                'name' => 'Setup Master',
                'description' => 'Modul setup master data, parameter sistem, konfigurasi outlet, dan pengaturan master.',
            ],
            2 => [
                'name' => 'Transaksi',
                'description' => 'Modul transaksi penjualan operasional kasir POS, order taker, meja, dan split bill.',
            ],
            3 => [
                'name' => 'Outlet Activity',
                'description' => 'Modul aktivitas dan operasional outlet, shift kerja kasir, dan pantauan kegiatan harian.',
            ],
            4 => [
                'name' => 'Cost Control',
                'description' => 'Modul pengendalian biaya, resep, food & beverage cost, dan laporan analisa selisih.',
            ],
            5 => [
                'name' => 'Purcashing AP',
                'description' => 'Modul pengadaan barang, purchasing / AP, purchase request (PR), purchase order (PO), receiving, dan hutang usaha.',
            ],
            6 => [
                'name' => 'AR',
                'description' => 'Modul piutang usaha (Accounts Receivable / AR), penagihan invoice klien, city ledger, dan rekonsiliasi.',
            ],
            7 => [
                'name' => 'General Cashier',
                'description' => 'Modul kasir umum, pencatatan penerimaan & pengeluaran kas, serta rekonsiliasi kas harian.',
            ],
            8 => [
                'name' => 'Accounting System',
                'description' => 'Modul sistem akuntansi, jurnal umum, buku besar (GL), neraca, dan laporan keuangan.',
            ],
            9 => [
                'name' => 'Report',
                'description' => 'Modul pusat laporan operasional, laporan penjualan harian/bulanan, analitik, dan statistik performa.',
            ],
            10 => [
                'name' => 'Tools',
                'description' => 'Modul utilitas sistem, konfigurasi hardware & printer, cash drawer, dan diagnostik perangkat.',
            ],
        ];

        // 1. PPOS (kategori_id = 3): Reassign manual books ke modul Transaksi jika masih di modul lama
        $pposMod44 = DB::table('menu_systems')->where('id', 44)->where('kategori_id', 3)->first();
        $pposMod45 = DB::table('menu_systems')->where('id', 45)->where('kategori_id', 3)->first();
        if ($pposMod44 && $pposMod45) {
            $hasMB44 = DB::table('menu_has_manual_books')->where('menu_system_id', 44)->exists();
            if ($hasMB44) {
                DB::table('menu_has_manual_books')->where('menu_system_id', 44)->update(['menu_system_id' => 45]);
            }
        }

        // 2. Loop semua kategori dan selaraskan 10 modul standar
        $allCategories = DB::table('kategori')->get();

        foreach ($allCategories as $cat) {
            for ($order = 1; $order <= 10; $order++) {
                $data = $targetModules[$order];

                // Cek apakah modul dengan nama yang sama sudah ada
                $existingByName = DB::table('menu_systems')
                    ->where('kategori_id', $cat->id)
                    ->whereNull('parent_id')
                    ->where('name', $data['name'])
                    ->first();

                if ($existingByName) {
                    DB::table('menu_systems')->where('id', $existingByName->id)->update([
                        'order' => $order,
                        'description' => $data['description'],
                        'updated_at' => now(),
                    ]);
                } else {
                    // Cek apakah ada modul dengan order ini
                    $existingByOrder = DB::table('menu_systems')
                        ->where('kategori_id', $cat->id)
                        ->whereNull('parent_id')
                        ->where('order', $order)
                        ->first();

                    if ($existingByOrder) {
                        DB::table('menu_systems')->where('id', $existingByOrder->id)->update([
                            'name' => $data['name'],
                            'description' => $data['description'],
                            'order' => $order,
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Tambah modul baru jika belum ada
                        DB::table('menu_systems')->insert([
                            'kategori_id' => $cat->id,
                            'parent_id' => null,
                            'name' => $data['name'],
                            'description' => $data['description'],
                            'order' => $order,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Hapus modul sisa dengan order > 10 yang tidak terpakai
            $extras = DB::table('menu_systems')
                ->where('kategori_id', $cat->id)
                ->whereNull('parent_id')
                ->where('order', '>', 10)
                ->get();

            foreach ($extras as $extra) {
                $hasMB = DB::table('menu_has_manual_books')->where('menu_system_id', $extra->id)->exists();
                $hasArt = DB::table('menu_has_articles')->where('menu_system_id', $extra->id)->exists();
                if (!$hasMB && !$hasArt) {
                    DB::table('menu_systems')->where('id', $extra->id)->delete();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Standarisasi modul tidak perlu di-rollback karena mempertahankan data relasi yang ada
    }
};
