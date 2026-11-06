<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankSolusiController extends Controller
{
    /**
     * Menampilkan halaman Bank Solusi & Panduan Sistem
     */
    public function index(Request $request)
    {
        // Daftar Sistem utama sesuai tampilan referensi
        $systems = [
            [
                'id' => 'presto-resto-pos',
                'name' => 'PRESTO Resto POS',
                'description' => 'Aplikasi Point of Sale untuk Restoran & F&B',
            ],
            [
                'id' => 'probus-hotel-pms',
                'name' => 'Probus Hotel PMS',
                'description' => 'Sistem Property Management untuk Perhotelan',
            ],
            [
                'id' => 'ppe-2-management',
                'name' => 'PPE 2.0 Management',
                'description' => 'Platform Pengelolaan Probus Enterprise',
            ],
            [
                'id' => 'back-office-akuntansi',
                'name' => 'Back Office & Akuntansi',
                'description' => 'Modul Keuangan, GL, Inventory & Laporan',
            ],
            [
                'id' => 'all',
                'name' => 'Semua Sistem',
                'description' => 'Seluruh panduan dari semua sistem Probus',
            ],
        ];

        // Daftar Modul berdasarkan sistem
        $modules = [
            'presto-resto-pos' => [
                ['id' => 'hardware-printer', 'name' => 'Hardware & Printer', 'count' => 6],
                ['id' => 'order-taker-kasir', 'name' => 'Order Taker & Kasir', 'count' => 7],
                ['id' => 'qris-edc', 'name' => 'QRIS & Pembayaran EDC', 'count' => 5],
                ['id' => 'kds', 'name' => 'Kitchen Display / KDS', 'count' => 4],
                ['id' => 'tutup-shift-laporan', 'name' => 'Tutup Shift & Laporan', 'count' => 2],
                ['id' => 'all-modules', 'name' => 'Semua Modul', 'count' => 24],
            ],
            'probus-hotel-pms' => [
                ['id' => 'front-office', 'name' => 'Front Office & Reservasi', 'count' => 5],
                ['id' => 'housekeeping', 'name' => 'Housekeeping & Room Status', 'count' => 3],
                ['id' => 'night-audit', 'name' => 'Night Audit & EOD', 'count' => 4],
                ['id' => 'all-modules', 'name' => 'Semua Modul', 'count' => 12],
            ],
            'ppe-2-management' => [
                ['id' => 'user-access', 'name' => 'Manajemen Hak Akses', 'count' => 3],
                ['id' => 'multi-property', 'name' => 'Sinkronisasi Multi-Outlet', 'count' => 3],
                ['id' => 'all-modules', 'name' => 'Semua Modul', 'count' => 6],
            ],
            'back-office-akuntansi' => [
                ['id' => 'inventory-cost', 'name' => 'Inventory & Food Cost', 'count' => 4],
                ['id' => 'jurnal-gl', 'name' => 'Jurnal & Buku Besar', 'count' => 3],
                ['id' => 'all-modules', 'name' => 'Semua Modul', 'count' => 7],
            ],
            'all' => [
                ['id' => 'hardware-printer', 'name' => 'Hardware & Printer', 'count' => 6],
                ['id' => 'order-taker-kasir', 'name' => 'Order Taker & Kasir', 'count' => 7],
                ['id' => 'qris-edc', 'name' => 'QRIS & Pembayaran EDC', 'count' => 5],
                ['id' => 'front-office', 'name' => 'Front Office & Reservasi', 'count' => 5],
                ['id' => 'all-modules', 'name' => 'Semua Modul', 'count' => 49],
            ],
        ];

        // Daftar solusi panduan (6 kartu pertama persis 100% sama dengan gambar referensi)
        $solutions = [
            [
                'id' => 1,
                'system_id' => 'presto-resto-pos',
                'system_name' => 'PRESTO Resto POS',
                'module_id' => 'hardware-printer',
                'module_name' => 'Hardware & Printer',
                'audience' => 'klien', // Blog Klien
                'audience_label' => 'BLOG KLIEN',
                'title' => 'Cara Menangani Printer Kasir Thermal yang Tidak Mencetak Struk',
                'excerpt' => 'Langkah pengecekan lampu indikator LED, arah gulungan kertas thermal, power cycle 10 detik, dan uji cetak dari layar Order Taker.',
                'author' => 'Rafi (Admin System)',
                'date' => '04 Nov 2026',
                'read_time' => '3 min baca',
                'steps' => [
                    'Pastikan lampu indikator POWER berwarna hijau stabil dan lampu ERROR/PAPER tidak menyala merah atau berkedip.',
                    'Buka penutup printer, pastikan kertas thermal terpasang searah putaran jarum jam (sisi luar mengarah ke pemotong).',
                    'Lakukan Power Cycle: Matikan saklar power printer, cabut kabel adaptor selama 10 detik, lalu tancapkan dan nyalakan kembali.',
                    'Tekan dan tahan tombol FEED saat printer dinyalakan untuk melakukan Self-Test Print mandiri.',
                    'Buka aplikasi Order Taker, masuk ke menu Pengaturan > Test Print Struk untuk memastikan komunikasi data berjalan lancar.',
                ],
                'notes' => 'Jika lampu ERROR berkedip merah cepat, kemungkinan kepala pemanas (thermal head) mengalami overheat atau terhalang debu struk.',
            ],
            [
                'id' => 2,
                'system_id' => 'presto-resto-pos',
                'system_name' => 'PRESTO Resto POS',
                'module_id' => 'hardware-printer',
                'module_name' => 'Hardware & Printer',
                'audience' => 'internal', // Internal Tim
                'audience_label' => 'INTERNAL',
                'title' => 'Mengatur IP Address Statis Printer Kitchen (LAN Port 9100)',
                'excerpt' => 'Panduan mengecek IP printer lewat self-test print dan menyamakan alamat IP pada menu pengaturan printer di aplikasi kasir.',
                'author' => 'Tim Support Probus',
                'date' => '02 Nov 2026',
                'read_time' => '4 min baca',
                'steps' => [
                    'Nyalakan printer sembari menahan tombol FEED untuk mencetak struk diagnostik konfigurasi jaringan.',
                    'Catat IP Address default printer (biasanya 192.168.1.200 atau 192.168.192.168).',
                    'Hubungkan kabel LAN printer langsung ke laptop teknisi dan ubah segmen IP Ethernet laptop menjadi satu subnet.',
                    'Akses Web Config printer via browser atau tool POS Utility untuk menetapkan IP statis permanen (misal: 192.168.1.180, Port 9100).',
                    'Buka Back Office / POS Setting, daftarkan printer dapur dengan IP baru tersebut dan lakukan uji ping.',
                ],
                'notes' => 'Pastikan router outlet telah mereservasi IP printer pada daftar DHCP Binding agar tidak tertabrak perangkat tamu.',
            ],
            [
                'id' => 3,
                'system_id' => 'presto-resto-pos',
                'system_name' => 'PRESTO Resto POS',
                'module_id' => 'hardware-printer',
                'module_name' => 'Hardware & Printer',
                'audience' => 'klien', // Blog Klien
                'audience_label' => 'BLOG KLIEN',
                'title' => 'Solusi Laci Uang (Cash Drawer) Tidak Terbuka Otomatis Saat Bayar',
                'excerpt' => 'Cara memeriksa konektor RJ-11 di belakang printer kasir dan mengaktifkan opsi Open Drawer on Payment pada pengaturan POS.',
                'author' => 'Gus Wahyu (Implementator)',
                'date' => '01 Nov 2026',
                'read_time' => '2 min baca',
                'steps' => [
                    'Periksa kabel konektor RJ-11 laci kasir (kabel mirip telepon) tertancap kuat ke port DK (Drawer Kick) di belakang printer thermal kasir.',
                    'Pastikan anak kunci laci berada pada posisi vertikal (mode otomatis terbuka elektrik), bukan posisi terkunci manual horizontal.',
                    'Di aplikasi PRESTO POS, buka menu Pengaturan Kasir > Perangkat Keras > Centang "Buka Laci Otomatis Saat Selesai Bayar Tunai".',
                    'Coba selesaikan transaksi tunai dummy untuk memastikan solenoid laci menerima pulsa pembuka dari printer.',
                ],
                'notes' => 'Jika terdengar bunyi "klik" solenoid namun laci tidak terdorong keluar, bersihkan rel laci dari debu atau koin yang terselip.',
            ],
            [
                'id' => 4,
                'system_id' => 'presto-resto-pos',
                'system_name' => 'PRESTO Resto POS',
                'module_id' => 'hardware-printer',
                'module_name' => 'Hardware & Printer',
                'audience' => 'klien', // Blog Klien
                'audience_label' => 'BLOG KLIEN',
                'title' => 'Mengatasi Kertas Struk Macet (Paper Jam) pada Pemotong Otomatis',
                'excerpt' => 'Prosedur aman membebaskan pisau auto-cutter yang mengunci penutup printer tanpa merusak komponen mekanis kepala cetak.',
                'author' => 'Rafi (Admin System)',
                'date' => '28 Okt 2026',
                'read_time' => '3 min baca',
                'steps' => [
                    'JANGAN memaksa menarik tuas penutup printer saat pisau cutter sedang macet di posisi mengunci.',
                    'Matikan saklar power printer kasir terlebih dahulu demi keamanan.',
                    'Buka penutup kecil di bagian depan printer (penutup manual gear putar cutter).',
                    'Putar roda gerigi (gear) secara perlahan sampai bilah pisau cutter mundur kembali ke posisi awal (home position).',
                    'Tekan tuas penutup, buka bodi printer, rapikan sisa kertas yang terjepit, lalu tutup rapat kembali.',
                ],
                'notes' => 'Gunakan selalu kertas thermal berdiameter standar agar roll tidak menekan tutup printer terlalu kencang.',
            ],
            [
                'id' => 5,
                'system_id' => 'presto-resto-pos',
                'system_name' => 'PRESTO Resto POS',
                'module_id' => 'hardware-printer',
                'module_name' => 'Hardware & Printer',
                'audience' => 'internal', // Internal Tim
                'audience_label' => 'INTERNAL',
                'title' => 'Mengalihkan Cetakan Order Dapur Sementara Saat Printer Utama Rusak',
                'excerpt' => 'Langkah mengaktifkan fitur Redirect Printer di Order Taker agar pesanan dapur tetap tercetak di printer bar atau kasir utama.',
                'author' => 'Sathya Pramerta (SPV)',
                'date' => '25 Okt 2026',
                'read_time' => '3 min baca',
                'steps' => [
                    'Buka aplikasi kasir utama PRESTO POS dengan akun Supervisor / Manager on Duty.',
                    'Pilih menu Pengaturan Jaringan > Pengalihan Printer (Printer Redirection).',
                    'Pada target "Printer Dapur 1 (Kitchen)", ubah pengalihan sementara ke "Printer Bar" atau "Printer Kasir".',
                    'Pilih opsi cetak tambahan "Tandai Header: ORDER KHUSUS DAPUR (PENGALIHAN)" agar staf bar dapat segera menyerahkan tiket ke juru masak.',
                    'Simpan pengaturan dan lakukan simulasi pemesanan makanan dari meja tamu.',
                ],
                'notes' => 'Segera hubungi tim implementator untuk penggantian unit backup atau pengecekan adaptor printer yang bermasalah.',
            ],
            [
                'id' => 6,
                'system_id' => 'presto-resto-pos',
                'system_name' => 'PRESTO Resto POS',
                'module_id' => 'hardware-printer',
                'module_name' => 'Hardware & Printer',
                'audience' => 'klien', // Blog Klien
                'audience_label' => 'BLOG KLIEN',
                'title' => 'Cara Menghubungkan Printer Thermal Bluetooth ke Tablet Kasir Android',
                'excerpt' => 'Panduan pairing perangkat Bluetooth, pemilihan ukuran kertas 58mm atau 80mm, dan kalibrasi batas margin cetakan struk.',
                'author' => 'Tim Support Probus',
                'date' => '20 Okt 2026',
                'read_time' => '3 min baca',
                'steps' => [
                    'Aktifkan Bluetooth pada tablet kasir Android dan pastikan printer thermal Bluetooth dalam kondisi menyala (lampu biru berkedip).',
                    'Masuk ke Pengaturan Bluetooth tablet, cari perangkat baru bernama "RPP02N", "MTP-II", atau "POS-58/80".',
                    'Masukkan PIN pairing default (biasanya 0000 atau 1234) hingga status perangkat "Tersambung / Paired".',
                    'Buka aplikasi PRESTO POS Android > menu Pengaturan Printer > Tipe Koneksi pilih Bluetooth > Pilih nama printer yang telah di-pair.',
                    'Tentukan lebar kertas (58mm atau 80mm) kemudian tekan tombol "Uji Cetak Bluetooth".',
                ],
                'notes' => 'Jika koneksi sering terputus, pastikan jarak tablet dengan printer tidak melebihi 5 meter dan tidak terhalang sekat logam/dinding tebal.',
            ],
        ];

        return view('bank_solusi.index', compact('systems', 'modules', 'solutions'));
    }

    /**
     * Menampilkan formulir buat / edit artikel solusi
     */
    public function create(Request $request)
    {
        return view('bank_solusi.create');
    }
}

