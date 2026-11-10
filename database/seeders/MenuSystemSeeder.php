<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\MenuSystem;

class MenuSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $targetModules = [
            [
                'name' => 'Setup Master',
                'description' => 'Modul setup master data, parameter sistem, konfigurasi outlet, dan pengaturan master.',
                'order' => 1,
            ],
            [
                'name' => 'Transaksi',
                'description' => 'Modul transaksi penjualan operasional kasir POS, order taker, meja, dan split bill.',
                'order' => 2,
            ],
            [
                'name' => 'Outlet Activity',
                'description' => 'Modul aktivitas dan operasional outlet, shift kerja kasir, dan pantauan kegiatan harian.',
                'order' => 3,
            ],
            [
                'name' => 'Cost Control',
                'description' => 'Modul pengendalian biaya, resep, food & beverage cost, dan laporan analisa selisih.',
                'order' => 4,
            ],
            [
                'name' => 'Purcashing AP',
                'description' => 'Modul pengadaan barang, purchasing / AP, purchase request (PR), purchase order (PO), receiving, dan hutang usaha.',
                'order' => 5,
            ],
            [
                'name' => 'AR',
                'description' => 'Modul piutang usaha (Accounts Receivable / AR), penagihan invoice klien, city ledger, dan rekonsiliasi.',
                'order' => 6,
            ],
            [
                'name' => 'General Cashier',
                'description' => 'Modul kasir umum, pencatatan penerimaan & pengeluaran kas, serta rekonsiliasi kas harian.',
                'order' => 7,
            ],
            [
                'name' => 'Accounting System',
                'description' => 'Modul sistem akuntansi, jurnal umum, buku besar (GL), neraca, dan laporan keuangan.',
                'order' => 8,
            ],
            [
                'name' => 'Report',
                'description' => 'Modul pusat laporan operasional, laporan penjualan harian/bulanan, analitik, dan statistik performa.',
                'order' => 9,
            ],
            [
                'name' => 'Tools',
                'description' => 'Modul utilitas sistem, konfigurasi hardware & printer, cash drawer, dan diagnostik perangkat.',
                'order' => 10,
            ],
        ];

        $allCategories = Kategori::all();

        foreach ($allCategories as $kategori) {
            foreach ($targetModules as $mod) {
                MenuSystem::firstOrCreate(
                    [
                        'kategori_id' => $kategori->id,
                        'name' => $mod['name'],
                    ],
                    [
                        'description' => $mod['description'],
                        'order' => $mod['order'],
                    ]
                );
            }
        }
    }
}
