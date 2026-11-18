<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            if (!Schema::hasColumn('kategori', 'badge')) {
                $table->string('badge', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('kategori', 'description')) {
                $table->text('description')->nullable()->after('badge');
            }
            if (!Schema::hasColumn('kategori', 'icon_mode')) {
                $table->string('icon_mode', 20)->default('preset')->after('description');
            }
            if (!Schema::hasColumn('kategori', 'icon')) {
                $table->string('icon', 255)->nullable()->after('icon_mode');
            }
        });

        // Seed data awal dari file system_cards.json jika ada
        $cardsPath = public_path('uploads/system_cards.json');
        $cards = [];
        if (file_exists($cardsPath)) {
            $json = @file_get_contents($cardsPath);
            $cards = json_decode($json, true) ?: [];
        }

        $defaultMeta = [
            '1' => ['badge' => 'Hotel & Resort', 'desc' => 'Property Management System komprehensif untuk front office, reservasi kamar, dan housekeeping.', 'icon_mode' => 'upload', 'icon' => '/uploads/icons/sys_icon_1794972612_M1oFW91K.png'],
            '2' => ['badge' => 'Restoran & POS', 'desc' => 'Sistem point-of-sale restoran terpadu mulai dari order taker, kitchen display, hingga kasir & split bill.', 'icon_mode' => 'preset', 'icon' => 'laptop'],
            '3' => ['badge' => 'Retail & Kasir', 'desc' => 'Sistem kasir cepat dan terintegrasi untuk outlet FnB, stand makanan, dan penjualan langsung.', 'icon_mode' => 'preset', 'icon' => 'printer'],
            '4' => ['badge' => 'Self Service', 'desc' => 'Sistem pemesanan mandiri oleh tamu melalui scan QR code meja tanpa antre di kasir.', 'icon_mode' => 'preset', 'icon' => 'smartphone'],
            '5' => ['badge' => 'Logistik & Gudang', 'desc' => 'Manajemen persediaan barang gudang, kartu stok, purchase request, dan penerimaan barang.', 'icon_mode' => 'preset', 'icon' => 'package'],
            '6' => ['badge' => 'Waiter App', 'desc' => 'Aplikasi mobile waiter untuk input pesanan langsung dari meja tamu terkirim instan ke dapur.', 'icon_mode' => 'preset', 'icon' => 'utensils'],
            '7' => ['badge' => 'Club & Lounge', 'desc' => 'Solusi kasir dan manajemen resto khusus konsep tropical bar, beach club, dan open-air dining.', 'icon_mode' => 'preset', 'icon' => 'sparkles'],
            '8' => ['badge' => 'Facility & Asset', 'desc' => 'Platform manajemen pemeliharaan fasilitas (maintenance), penugasan staf, dan kontrol aset.', 'icon_mode' => 'preset', 'icon' => 'zap'],
            '9' => ['badge' => 'Cloud POS', 'desc' => 'Sistem point of sales generasi baru dengan arsitektur modular dan sinkronisasi multi-outlet.', 'icon_mode' => 'preset', 'icon' => 'laptop'],
            '12' => ['badge' => 'Channel Manager', 'desc' => 'Sistem integrasi channel manager dan distribusi ketersediaan kamar ke berbagai OTA global.', 'icon_mode' => 'preset', 'icon' => 'globe'],
        ];

        $categories = DB::table('kategori')->get();
        foreach ($categories as $cat) {
            $card = $cards[(string) $cat->id] ?? $cards[$cat->name] ?? $defaultMeta[(string) $cat->id] ?? $defaultMeta[$cat->name] ?? null;
            if ($card) {
                DB::table('kategori')->where('id', $cat->id)->update([
                    'badge' => $card['badge'] ?? null,
                    'description' => $card['desc'] ?? null,
                    'icon_mode' => $card['icon_mode'] ?? 'preset',
                    'icon' => $card['icon'] ?? null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('kategori', 'icon')) {
                $columnsToDrop[] = 'icon';
            }
            if (Schema::hasColumn('kategori', 'icon_mode')) {
                $columnsToDrop[] = 'icon_mode';
            }
            if (Schema::hasColumn('kategori', 'description')) {
                $columnsToDrop[] = 'description';
            }
            if (Schema::hasColumn('kategori', 'badge')) {
                $columnsToDrop[] = 'badge';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
