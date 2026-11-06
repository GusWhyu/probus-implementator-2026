<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManualBook;
use App\Models\MenuSystem;
use App\Models\User;

class ManualBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $modules = [
            'Order Taker & Kasir' => [
                'title' => '02. Order Taker & Transaksi Meja',
                'description' => 'Panduan langkah demi langkah bagi kasir & pramusaji untuk membuka meja tamu, menginput pesanan menu beserta catatan dapur (notes), hingga memindahkan atau menggabungkan meja aktif.',
                'status' => 'published',
                'content' => '
<div class="space-y-4">
    <h3>1. Membuka Meja Baru (Open Table)</h3>
    <p>Pilih area denah restoran (misal: <em>Indoor AC</em>, <em>Outdoor Garden</em>, atau <em>Bar Area</em>). Klik pada nomor meja yang berwarna <strong>HIJAU (Kosong)</strong>. Masukkan perkiraan jumlah tamu (Pax) pada pop-up yang muncul, lalu tekan tombol <strong>Buka Meja</strong>.</p>

    <h3>2. Input Pesanan Menu &amp; Catatan Khusus Dapur (Notes)</h3>
    <p>Cari item menu berdasarkan kategori atau ketik di kolom pencarian. Klik item untuk menambahkan kuantitas pesanan. Untuk menambahkan catatan masak (contoh: <em>Less spicy</em>, <em>No onion</em>, <em>Allergy alert</em>), klik tombol pensil di sebelah nama item.</p>

    <h3>3. Keterangan Status Meja &amp; Panduan Pindah Meja (Move Table)</h3>
    <p>Berikut adalah acuan status warna meja pada denah restoran dan tata cara pindah meja jika tamu berpindah tempat duduk:</p>

    <table style="width: 100%; border-collapse: collapse; margin: 15px 0; border: 1px solid #cbd5e1;">
        <thead>
            <tr style="background-color: #f1f5f9; font-weight: bold; text-align: left;">
                <th style="padding: 8px 12px; border: 1px solid #cbd5e1; width: 40px; text-align: center;">No</th>
                <th style="padding: 8px 12px; border: 1px solid #cbd5e1; width: 180px;">Status / Aksi</th>
                <th style="padding: 8px 12px; border: 1px solid #cbd5e1;">Keterangan Petunjuk Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align: center;">1</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: 600; color: #16a34a;">Meja Kosong (Hijau)</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1;">Meja belum terisi tamu, siap dibuka untuk transaksi tamu baru.</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align: center;">2</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: 600; color: #dc2626;">Meja Terisi (Merah)</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1;">Meja aktif sedang makan dan pesanan telah terkirim ke dapur.</td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align: center;">3</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: 600; color: #2563eb;">Pindah Meja (Move Table)</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1;">Buka meja aktif, klik Pindah / Gabung Meja di sudut kanan atas, lalu pilih nomor meja tujuan.</td>
            </tr>
        </tbody>
    </table>
</div>
',
            ],
            'Hardware & Printer' => [
                'title' => '06. Pengaturan Hardware & Printer',
                'description' => 'Panduan konfigurasi printer thermal bluetooth/LAN untuk kasir dan kitchen printer, serta cash drawer trigger.',
                'status' => 'published',
                'content' => '
<div class="space-y-4">
    <h3>1. Konfigurasi Printer Thermal LAN (Port 9100)</h3>
    <p>Pastikan printer dapur dan router outlet berada pada satu segmen IP (misal: 192.168.1.xxx). Buka menu Pengaturan POS > Hardware > Tambah Printer.</p>

    <h3>2. Pengujian Cetak Struk &amp; Auto-Cutter</h3>
    <p>Lakukan uji cetak untuk memastikan pemotong struk bekerja otomatis dan laci kasir (cash drawer) melompat terbuka.</p>
</div>
',
            ],
            'QRIS & Pembayaran EDC' => [
                'title' => '03. Pembayaran Multi-Metode & Split Bill',
                'description' => 'Panduan pembayaran multi-metode (Cash, EDC, QRIS, Voucher), cetak bill sementara, serta fitur split bill per item / per nominal.',
                'status' => 'published',
                'content' => '
<div class="space-y-4">
    <h3>1. Penyelesaian Pembayaran Tunai &amp; Kembalian</h3>
    <p>Pilih metode bayar Tunai, masukkan jumlah uang yang diserahkan pelanggan. Sistem akan menampilkan nominal kembalian secara otomatis.</p>

    <h3>2. Split Bill Berdasarkan Item</h3>
    <p>Klik tombol Split Bill, pilih menu yang ingin dibayarkan terpisah, lalu cetak bill masing-masing tamu.</p>
</div>
',
            ],
        ];

        foreach ($modules as $menuName => $bookData) {
            $menuSystem = MenuSystem::where('name', $menuName)->first();
            if ($menuSystem) {
                $manualBook = ManualBook::firstOrCreate(
                    ['title' => $bookData['title']],
                    [
                        'user_id' => $userId,
                        'description' => $bookData['description'],
                        'content' => $bookData['content'],
                        'status' => $bookData['status'],
                    ]
                );

                if (!$menuSystem->manualBooks()->where('manual_books.id', $manualBook->id)->exists()) {
                    $menuSystem->manualBooks()->attach($manualBook->id);
                }
            }
        }
    }
}
