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
        $modulesByCategory = [
            'PRESTO' => [
                ['name' => 'Hardware & Printer', 'description' => 'Panduan printer struk thermal, printer dapur, cash drawer, dan barcode scanner', 'order' => 1],
                ['name' => 'Order Taker & Kasir', 'description' => 'Tata cara input order, hold bill, diskon, split bill, dan cetak bill kasir', 'order' => 2],
                ['name' => 'QRIS & Pembayaran EDC', 'description' => 'Konfigurasi QRIS statis/dinamis, EDC BCA/Mandiri, dan e-wallet', 'order' => 3],
                ['name' => 'Kitchen Display / KDS', 'description' => 'Pengoperasian display pesanan dapur dan status kesiapan makanan', 'order' => 4],
                ['name' => 'Tutup Shift & Laporan', 'description' => 'Prosedur rekonsiliasi kas kasir, serah terima shift, dan cetak summary', 'order' => 5],
            ],
            'PHIS' => [
                ['name' => 'Front Office & Reservasi', 'description' => 'Check-in, check-out, reservasi tamu, dan guest folio', 'order' => 1],
                ['name' => 'Housekeeping & Room Status', 'description' => 'Status kamar (Clean/Dirty/OOO), update status melalui aplikasi HK', 'order' => 2],
                ['name' => 'Night Audit & EOD', 'description' => 'Prosedur tutup hari, posting room charge otomatis, dan laporan audit', 'order' => 3],
                ['name' => 'Point of Sale Resto / Bar', 'description' => 'Posting tagihan restoran langsung ke kamar tamu (Room Charge)', 'order' => 4],
            ],
            'PPE' => [
                ['name' => 'Manajemen Hak Akses', 'description' => 'Pengaturan role, permission user, dan batasan wewenang modul', 'order' => 1],
                ['name' => 'Sinkronisasi Multi-Outlet', 'description' => 'Sinkronisasi data master menu, harga, dan inventory antar cabang', 'order' => 2],
                ['name' => 'Integrasi Channel Manager', 'description' => 'Konektivitas OTA (Booking.com, Agoda, Tiket.com) secara otomatis', 'order' => 3],
            ],
            'PPOS' => [
                ['name' => 'Kasir POS', 'description' => 'Transaksi penjualan harian, void, dan refund', 'order' => 1],
                ['name' => 'Pengaturan Menu & Harga', 'description' => 'Kelola master item, harga jual, dan paket menu promo', 'order' => 2],
                ['name' => 'Laporan Penjualan', 'description' => 'Analisa omzet per kasir, per kategori produk, dan per shift', 'order' => 3],
            ],
            'ORDER TAKER' => [
                ['name' => 'Floor Map & Denah Meja', 'description' => 'Pemilihan area dan nomor meja saat pelanggan memesan', 'order' => 1],
                ['name' => 'Split Bill & Gabung Meja', 'description' => 'Pembagian pesanan atau penggabungan beberapa meja jadi satu transaksi', 'order' => 2],
            ],
            'SELF ORDER' => [
                ['name' => 'QR Menu Pelanggan', 'description' => 'Scan QR di meja untuk membuka e-menu mandiri oleh tamu', 'order' => 1],
                ['name' => 'Payment Gateway Self Order', 'description' => 'Pembayaran otomatis langsung saat pemesanan melalui QRIS / e-wallet', 'order' => 2],
            ],
            'PR-PO ONLINE' => [
                ['name' => 'Purchase Request (PR)', 'description' => 'Pengajuan permintaan pembelian bahan baku oleh departemen outlet', 'order' => 1],
                ['name' => 'Purchase Order (PO) & Approval', 'description' => 'Persetujuan pesanan pembelian ke supplier oleh Purchasing & GM', 'order' => 2],
                ['name' => 'Receiving & Goods Receipt', 'description' => 'Penerimaan barang datang dan pencocokan dengan invoice PO', 'order' => 3],
            ],
        ];

        foreach ($modulesByCategory as $categoryName => $modules) {
            $kategori = Kategori::where('name', 'LIKE', '%' . $categoryName . '%')->first();
            if ($kategori) {
                foreach ($modules as $mod) {
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
}

