<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\MenuSystem;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Administrator',
                'email' => 'admin@probus.com',
                'password' => bcrypt('password'),
            ]);
        }

        $tools = MenuSystem::where('name', 'Tools')->first();
        $hwId = $tools ? $tools->id : null;

        $sales = MenuSystem::where('name', 'Sales')->first();
        $otId = $sales ? $sales->id : $hwId;

        $articles = [
            [
                'title' => 'Cara Menangani Printer Kasir Thermal yang Tidak Mencetak Struk',
                'visibility' => 'publik',
                'status' => 'published',
                'summary' => 'Langkah pengecekan lampu indikator LED, arah gulungan kertas thermal, power cycle 10 detik, dan uji cetak dari layar Order Taker.',
                'menu_system_id' => $hwId,
                'content' => '
<p>Ikuti langkah pengecekan berikut secara berurutan untuk mengatasi printer kasir atau dapur yang mendadak tidak mencetak struk.</p>

<h2>1. Periksa Fisik Printer &amp; Lampu Indikator (LED)</h2>
<p>Langkah awal adalah memeriksa bagian fisik luar printer thermal Anda di meja kasir. Perhatikan tuas penutup kertas dan status lampu indikator pada panel depan printer:</p>

<table class="table-penjelasan" style="width: 100%; border-collapse: collapse; margin: 15px 0;">
    <thead>
        <tr style="background-color: #f1f5f9; text-align: left;">
            <th style="padding: 10px; border: 1px solid #cbd5e1; width: 40px; text-align: center;">No</th>
            <th style="padding: 10px; border: 1px solid #cbd5e1; width: 180px;">Bagian / Indikator</th>
            <th style="padding: 10px; border: 1px solid #cbd5e1;">Deskripsi &amp; Solusi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: center;">1</td>
            <td style="padding: 10px; border: 1px solid #cbd5e1; font-weight: bold;">Cover Kertas Thermal</td>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">Tekan penutup kertas hingga berbunyi klik di kedua sisi agar sensor printer siap mencetak.</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: center;">2</td>
            <td style="padding: 10px; border: 1px solid #cbd5e1; font-weight: bold; color: #dc2626;">Lampu ERROR (Merah)</td>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">Bila berkedip merah, penutup kertas belum rapat atau kertas macet di pemotong (cutter).</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: center;">3</td>
            <td style="padding: 10px; border: 1px solid #cbd5e1; font-weight: bold; color: #d97706;">Lampu PAPER (Kuning)</td>
            <td style="padding: 10px; border: 1px solid #cbd5e1;">Bila menyala kuning, gulungan kertas thermal habis atau posisi pemasangannya terbalik.</td>
        </tr>
    </tbody>
</table>

<h2>2. Pastikan Posisi Kertas &amp; Restart Printer (Power Cycle)</h2>
<ul>
    <li>Pastikan gulungan kertas thermal tidak terbalik (goreskan kuku pada permukaan kertas; sisi yang meninggalkan goresan hitam harus menghadap pemanas).</li>
    <li>Matikan saklar power printer (OFF), tunggu 10 detik untuk mengosongkan antrean memori (spooler), lalu nyalakan kembali (ON).</li>
</ul>

<h2>3. Lakukan Uji Cetak dari Layar Order Taker</h2>
<p>Setelah lampu indikator printer menyala normal, buka menu <strong>Pengaturan Printer</strong> pada aplikasi Order Taker lalu klik tombol <strong>Uji Cetak Struk Sample</strong>.</p>
',
            ],
            [
                'title' => 'Mengatur IP Address Statis Printer Kitchen (LAN Port 9100)',
                'visibility' => 'internal',
                'status' => 'published',
                'summary' => 'Panduan mengecek IP printer lewat self-test print dan menyamakan alamat IP pada menu pengaturan printer di aplikasi kasir.',
                'menu_system_id' => $hwId,
                'content' => '
<p>Panduan teknis khusus implementator untuk konfigurasi IP Address statis printer dapur yang terhubung melalui kabel LAN (Ethernet):</p>
<h2>Langkah Konfigurasi:</h2>
<ol>
    <li>Nyalakan printer sembari menahan tombol FEED untuk mencetak struk diagnostik konfigurasi jaringan.</li>
    <li>Catat IP Address default printer (biasanya 192.168.1.200 atau 192.168.192.168).</li>
    <li>Hubungkan kabel LAN printer langsung ke laptop teknisi dan ubah segmen IP Ethernet laptop menjadi satu subnet.</li>
    <li>Akses Web Config printer via browser untuk menetapkan IP statis permanen (misal: 192.168.1.180, Port 9100).</li>
    <li>Buka Back Office / POS Setting, daftarkan printer dapur dengan IP baru tersebut dan lakukan uji ping.</li>
</ol>
',
            ],
            [
                'title' => 'Solusi Laci Uang (Cash Drawer) Tidak Terbuka Otomatis Saat Bayar',
                'visibility' => 'publik',
                'status' => 'published',
                'summary' => 'Cara memeriksa konektor RJ-11 di belakang printer kasir dan mengaktifkan opsi Open Drawer on Payment pada pengaturan POS.',
                'menu_system_id' => $hwId,
                'content' => '
<p>Jika laci uang tidak mau melompat terbuka saat menekan tombol selesaikan bayar tunai:</p>
<ul>
    <li>Periksa kabel konektor RJ-11 laci kasir tertancap kuat ke port DK (Drawer Kick) di belakang printer thermal kasir.</li>
    <li>Pastikan anak kunci laci berada pada posisi vertikal (mode otomatis terbuka elektrik), bukan posisi terkunci manual horizontal.</li>
    <li>Di aplikasi PRESTO POS, buka menu Pengaturan Kasir > Perangkat Keras > Centang "Buka Laci Otomatis Saat Selesai Bayar Tunai".</li>
</ul>
',
            ],
            [
                'title' => 'Mengatasi Kertas Struk Macet (Paper Jam) pada Pemotong Otomatis',
                'visibility' => 'publik',
                'status' => 'published',
                'summary' => 'Prosedur aman membebaskan pisau auto-cutter yang mengunci penutup printer tanpa merusak komponen mekanis kepala cetak.',
                'menu_system_id' => $hwId,
                'content' => '
<p>Prosedur aman saat pisau cutter macet:</p>
<ol>
    <li>JANGAN memaksa menarik tuas penutup printer saat pisau cutter sedang macet di posisi mengunci.</li>
    <li>Matikan saklar power printer kasir terlebih dahulu demi keamanan.</li>
    <li>Buka penutup kecil di bagian depan printer (penutup manual gear putar cutter).</li>
    <li>Putar roda gerigi (gear) secara perlahan sampai bilah pisau cutter mundur kembali ke posisi awal (home position).</li>
    <li>Tekan tuas penutup, buka bodi printer, rapikan sisa kertas yang terjepit, lalu tutup rapat kembali.</li>
</ol>
',
            ],
            [
                'title' => 'Mengalihkan Cetakan Order Dapur Sementara Saat Printer Utama Rusak',
                'visibility' => 'internal',
                'status' => 'published',
                'summary' => 'Langkah mengaktifkan fitur Redirect Printer di Order Taker agar pesanan dapur tetap tercetak di printer bar atau kasir utama.',
                'menu_system_id' => $otId,
                'content' => '
<p>Fitur cadangan darurat (failover) printer dapur:</p>
<ol>
    <li>Buka aplikasi kasir utama PRESTO POS dengan akun Supervisor / Manager on Duty.</li>
    <li>Pilih menu Pengaturan Jaringan > Pengalihan Printer (Printer Redirection).</li>
    <li>Pada target "Printer Dapur 1 (Kitchen)", ubah pengalihan sementara ke "Printer Bar" atau "Printer Kasir".</li>
    <li>Pilih opsi cetak tambahan "Tandai Header: ORDER KHUSUS DAPUR (PENGALIHAN)".</li>
</ol>
',
            ],
            [
                'title' => 'Cara Menghubungkan Printer Thermal Bluetooth ke Tablet Kasir Android',
                'visibility' => 'publik',
                'status' => 'published',
                'summary' => 'Panduan pairing perangkat Bluetooth, pemilihan ukuran kertas 58mm atau 80mm, dan kalibrasi batas margin cetakan struk.',
                'menu_system_id' => $hwId,
                'content' => '
<p>Panduan pairing Bluetooth tablet kasir:</p>
<ol>
    <li>Aktifkan Bluetooth pada tablet kasir Android dan nyalakan printer Bluetooth.</li>
    <li>Masuk ke Pengaturan Bluetooth tablet, cari perangkat baru bernama "RPP02N" atau "POS-58/80".</li>
    <li>Masukkan PIN pairing default (0000 atau 1234) hingga status "Tersambung / Paired".</li>
    <li>Buka PRESTO POS > Pengaturan Printer > Tipe Koneksi pilih Bluetooth.</li>
</ol>
',
            ],
        ];

        foreach ($articles as $data) {
            $menuSystemId = $data['menu_system_id'];
            unset($data['menu_system_id']);

            $article = Article::firstOrCreate(
                ['title' => $data['title']],
                array_merge($data, ['user_id' => $user->id])
            );

            if ($menuSystemId && !$article->menuSystems()->where('menu_systems.id', $menuSystemId)->exists()) {
                $article->menuSystems()->attach($menuSystemId);
            }
        }
    }
}
