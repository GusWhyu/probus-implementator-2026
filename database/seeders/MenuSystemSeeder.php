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
                'name' => 'Sales',
                'description' => 'Modul transaksi penjualan, kasir POS, reservasi meja, dan pendapatan.',
                'order' => 1,
            ],
            [
                'name' => 'Production',
                'description' => 'Modul pengolahan dapur, resep, kitchen display system (KDS), dan produksi makanan/minuman.',
                'order' => 2,
            ],
            [
                'name' => 'Cost Control',
                'description' => 'Modul pengendalian biaya, food & beverage cost, dan laporan analisa selisih.',
                'order' => 3,
            ],
            [
                'name' => 'Purchasing / AP',
                'description' => 'Modul pengadaan barang, purchase request (PR), purchase order (PO), receiving, dan hutang usaha.',
                'order' => 4,
            ],
            [
                'name' => 'Accounts Receivable / AR',
                'description' => 'Modul piutang usaha, city ledger, penagihan invoice klien, dan pelunasan.',
                'order' => 5,
            ],
            [
                'name' => 'Accounting System',
                'description' => 'Modul sistem akuntansi, jurnal umum, buku besar (GL), neraca, dan laporan keuangan.',
                'order' => 6,
            ],
            [
                'name' => 'Tools',
                'description' => 'Modul utilitas sistem, konfigurasi hardware & printer, cash drawer, dan diagnostik perangkat.',
                'order' => 7,
            ],
            [
                'name' => 'Calendar',
                'description' => 'Modul kalender kerja, reservasi event, jadwal operasional, dan agenda sistem.',
                'order' => 8,
            ],
            [
                'name' => 'Calculator',
                'description' => 'Modul kalkulator sistem, simulasi perhitungan harga, diskon, dan konversi kurs.',
                'order' => 9,
            ],
            [
                'name' => 'Reconnecting',
                'description' => 'Modul pemulihan koneksi offline/online, sinkronisasi data cloud, dan pemulihan jaringan.',
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
