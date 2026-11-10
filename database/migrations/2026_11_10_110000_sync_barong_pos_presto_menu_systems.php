<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MenuSystem;
use App\Models\Kategori;
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
        $targetModules = [
            1 => [
                'name' => 'Setup Master',
                'desc' => 'Modul setup master data, parameter sistem, konfigurasi outlet, dan pengaturan master.',
            ],
            2 => [
                'name' => 'Transaksi',
                'desc' => 'Modul transaksi penjualan operasional kasir POS, order taker, meja, dan split bill.',
            ],
            3 => [
                'name' => 'Outlet Activity',
                'desc' => 'Modul aktivitas dan operasional outlet, shift kerja kasir, dan pantauan kegiatan harian.',
            ],
            4 => [
                'name' => 'Cost Control',
                'desc' => 'Modul pengendalian biaya, resep, food & beverage cost, dan laporan analisa selisih.',
            ],
            5 => [
                'name' => 'Purcashing AP',
                'desc' => 'Modul pengadaan barang, purchasing / AP, purchase request (PR), purchase order (PO), receiving, dan hutang usaha.',
            ],
            6 => [
                'name' => 'AR',
                'desc' => 'Modul piutang usaha (Accounts Receivable / AR), penagihan invoice klien, city ledger, dan rekonsiliasi.',
            ],
            7 => [
                'name' => 'General Cashier',
                'desc' => 'Modul kasir umum, pencatatan penerimaan & pengeluaran kas, serta rekonsiliasi kas harian.',
            ],
            8 => [
                'name' => 'Accounting System',
                'desc' => 'Modul sistem akuntansi, jurnal umum, buku besar (GL), neraca, dan laporan keuangan.',
            ],
            9 => [
                'name' => 'Report',
                'desc' => 'Modul pusat laporan operasional, laporan penjualan harian/bulanan, analitik, dan statistik performa.',
            ],
            10 => [
                'name' => 'Tools',
                'desc' => 'Modul utilitas sistem, konfigurasi hardware & printer, cash drawer, dan diagnostik perangkat.',
            ],
        ];

        // 1. UPDATE BARONG POS
        $barongPos = Kategori::where('name', 'LIKE', '%BARONG POS%')->first();
        if ($barongPos) {
            $existingBarong = MenuSystem::where('kategori_id', $barongPos->id)->orderBy('order')->get();
            foreach ($targetModules as $order => $data) {
                $existing = $existingBarong->where('order', $order)->first();
                if (!$existing && isset($existingBarong[$order - 1])) {
                    $existing = $existingBarong[$order - 1];
                }

                if ($existing) {
                    $existing->update([
                        'name' => $data['name'],
                        'description' => $data['desc'],
                        'order' => $order,
                    ]);
                } else {
                    MenuSystem::create([
                        'kategori_id' => $barongPos->id,
                        'name' => $data['name'],
                        'description' => $data['desc'],
                        'order' => $order,
                    ]);
                }
            }
        }

        // 2. UPDATE PRESTO
        $presto = Kategori::where('name', 'PRESTO')->first();
        if ($presto) {
            $toolsModule = MenuSystem::where('kategori_id', $presto->id)->where('name', 'Tools')->first();
            if ($toolsModule) {
                $toolsModule->update([
                    'order' => 10,
                    'description' => $targetModules[10]['desc'],
                ]);
            }

            $transaksiModule = MenuSystem::where('kategori_id', $presto->id)->where('id', 35)->first();
            if ($transaksiModule) {
                $transaksiModule->update([
                    'name' => 'Transaksi',
                    'order' => 2,
                    'description' => $targetModules[2]['desc'],
                ]);

                // Link relevant manual books & articles to Transaksi
                DB::table('menu_has_manual_books')->where('menu_system_id', 34)->update(['menu_system_id' => 35]);
                DB::table('menu_has_articles')->where('menu_system_id', 34)->update(['menu_system_id' => 35]);
            }

            $setupMaster = MenuSystem::where('kategori_id', $presto->id)->where('id', 34)->first();
            if ($setupMaster) {
                $setupMaster->update([
                    'name' => 'Setup Master',
                    'order' => 1,
                    'description' => $targetModules[1]['desc'],
                ]);
            }

            $moduleMapping = [
                3 => ['current_id' => 36, 'target' => 3],
                4 => ['current_id' => 37, 'target' => 4],
                5 => ['current_id' => 38, 'target' => 5],
                6 => ['current_id' => 39, 'target' => 6],
                7 => ['current_id' => 41, 'target' => 7],
                8 => ['current_id' => 42, 'target' => 8],
                9 => ['current_id' => 43, 'target' => 9],
            ];

            foreach ($moduleMapping as $m) {
                $mod = MenuSystem::where('kategori_id', $presto->id)->where('id', $m['current_id'])->first();
                $targetOrder = $m['target'];
                $targetData = $targetModules[$targetOrder];
                if ($mod) {
                    $mod->update([
                        'name' => $targetData['name'],
                        'order' => $targetOrder,
                        'description' => $targetData['desc'],
                    ]);
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
        // No-op or revert to old default names if desired
    }
};
