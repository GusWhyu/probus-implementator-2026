<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class KategoriController extends Controller
{
    /**
     * Preset ikon populer untuk System Card
     */
    private function getPopularPresets()
    {
        return [
            'building-2' => '🏨 building-2 — Hotel, Villa & Resort (PHIS)',
            'monitor-check' => '🖥️ monitor-check — Restoran, POS & Kasir (PRESTO)',
            'printer' => '🖨️ printer — Retail & Kasir (PPOS)',
            'smartphone' => '📱 smartphone — Self Order & Waiter App',
            'package' => '📦 package — Logistik, Purchasing & Gudang (PR-PO)',
            'utensils-crossed' => '🍽️ utensils-crossed — FnB, Dapur & Resto (Order Taker)',
            'sparkles' => '✨ sparkles — Club, Lounge & Entertainment (Vrestropical)',
            'zap' => '⚡ zap — Maintenance, Aset & Fasilitas (PPE)',
            'laptop' => '💻 laptop — Cloud POS, Web Portal & Integrasi (Barong POS)',
            'globe' => '🌐 globe — Channel Manager, Booking Engine & OTA Online',
            'credit-card' => '💳 credit-card — Pembayaran, Kasir & Finansial',
            'calendar' => '📅 calendar — Reservasi & Manajemen Jadwal',
            'users' => '👥 users — Member, CRM & Data Tamu',
            'shield' => '🛡️ shield — Keamanan, Audit & Kontrol Akses',
            'settings' => '⚙️ settings — Konfigurasi Sistem & Admin Center',
        ];
    }

    /**
     * Membaca metadata kartu sistem dari file JSON bersama
     */
    private function getSystemCards()
    {
        $path = public_path('uploads/system_cards.json');
        if (file_exists($path)) {
            $json = @file_get_contents($path);
            $data = json_decode($json, true);
            if (is_array($data)) {
                return $data;
            }
        }

        // Fallback default metadata untuk kategori bawaan
        return [
            '1' => ['name' => 'PHIS', 'badge' => 'Hotel & Resort', 'desc' => 'Property Management System komprehensif untuk front office, reservasi kamar, dan housekeeping.', 'icon_mode' => 'preset', 'icon' => 'building-2'],
            '2' => ['name' => 'PRESTO', 'badge' => 'Restoran & POS', 'desc' => 'Sistem point-of-sale restoran terpadu mulai dari order taker, kitchen display, hingga kasir & split bill.', 'icon_mode' => 'preset', 'icon' => 'monitor-check'],
            '3' => ['name' => 'PPOS', 'badge' => 'Retail & Kasir', 'desc' => 'Sistem kasir cepat dan terintegrasi untuk outlet FnB, stand makanan, dan penjualan langsung.', 'icon_mode' => 'preset', 'icon' => 'printer'],
            '4' => ['name' => 'SELF ORDER', 'badge' => 'Self Service', 'desc' => 'Sistem pemesanan mandiri oleh tamu melalui scan QR code meja tanpa antre di kasir.', 'icon_mode' => 'preset', 'icon' => 'smartphone'],
            '5' => ['name' => 'PR-PO ONLINE', 'badge' => 'Logistik & Gudang', 'desc' => 'Manajemen persediaan barang gudang, kartu stok, purchase request, dan penerimaan barang.', 'icon_mode' => 'preset', 'icon' => 'package'],
            '6' => ['name' => 'ORDER TAKER', 'badge' => 'Waiter App', 'desc' => 'Aplikasi mobile waiter untuk input pesanan langsung dari meja tamu terkirim instan ke dapur.', 'icon_mode' => 'preset', 'icon' => 'utensils-crossed'],
            '7' => ['name' => 'VRESTROPICAL', 'badge' => 'Club & Lounge', 'desc' => 'Solusi kasir dan manajemen resto khusus konsep tropical bar, beach club, dan open-air dining.', 'icon_mode' => 'preset', 'icon' => 'sparkles'],
            '8' => ['name' => 'PPE', 'badge' => 'Facility & Asset', 'desc' => 'Platform manajemen pemeliharaan fasilitas (maintenance), penugasan staf, dan kontrol aset.', 'icon_mode' => 'preset', 'icon' => 'zap'],
            '9' => ['name' => 'BARONG POS', 'badge' => 'Cloud POS', 'desc' => 'Sistem point of sales generasi baru dengan arsitektur modular dan sinkronisasi multi-outlet.', 'icon_mode' => 'preset', 'icon' => 'laptop'],
        ];
    }

    /**
     * Menyimpan metadata kartu sistem ke JSON bersama & menyinkronkannya
     */
    private function saveSystemCards(array $cards)
    {
        $json = json_encode($cards, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // 1. Simpan di project implementator
        $targetDir = public_path('uploads');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }
        @file_put_contents($targetDir . '/system_cards.json', $json);

        // 2. Sinkronkan ke Help-Center-Probus jika path tersedia
        $hcUploads = base_path('../Help-Center-Probus/public/uploads');
        if (file_exists($hcUploads)) {
            @file_put_contents($hcUploads . '/system_cards.json', $json);
        }

        $hcFrontend = base_path('../Help-Center-Probus/frontend/public');
        if (file_exists($hcFrontend)) {
            @file_put_contents($hcFrontend . '/system_cards.json', $json);
        }
    }

    /**
     * Resolusi ikon sistem dari input form (preset, custom text, atau upload file gambar)
     */
    private function resolveIconFromRequest(Request $request, $existingIcon = null)
    {
        $iconMode = $request->input('icon_mode', 'preset');

        // 1. File baru diupload
        if ($request->hasFile('icon_file')) {
            $file = $request->file('icon_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileName = 'sys_icon_' . time() . '_' . Str::random(8) . '.' . $ext;
            
            $dest = public_path('uploads/icons');
            if (!file_exists($dest)) {
                @mkdir($dest, 0777, true);
            }
            $file->move($dest, $fileName);

            $iconPath = '/uploads/icons/' . $fileName;

            // Mirror file ke Help-Center-Probus jika ada
            $hcIcons = base_path('../Help-Center-Probus/public/uploads/icons');
            if (file_exists($hcIcons)) {
                @copy($dest . '/' . $fileName, $hcIcons . '/' . $fileName);
            }

            return $iconPath;
        }

        // 2. Mode upload dengan file yang sudah ada
        if ($iconMode === 'upload') {
            if ($request->boolean('remove_icon_file')) {
                return null;
            }
            if ($request->filled('existing_icon_file')) {
                return trim($request->input('existing_icon_file'));
            }
            return !empty($existingIcon) && (str_starts_with($existingIcon, '/uploads/') || preg_match('/\.(png|svg|jpg|jpeg|webp)$/i', $existingIcon))
                ? $existingIcon
                : null;
        }

        // 3. Mode Custom text Lucide
        if ($iconMode === 'custom') {
            $custom = trim($request->input('icon_custom', ''));
            return !empty($custom) ? strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $custom)) : null;
        }

        // 4. Mode Preset dropdown
        if ($iconMode === 'preset') {
            $preset = trim($request->input('icon', ''));
            return !empty($preset) ? $preset : null;
        }

        return null;
    }

    public function index()
    {
        $this->authorize('aspv');
        $title = 'Delete Category!';
        $text = 'Are you sure you want to delete this data?';
        confirmDelete($title, $text);

        $kategori = Kategori::all();
        $systemCards = $this->getSystemCards();

        return view('tablekategori.kategori', compact('kategori', 'systemCards'));
    }

    public function createkategori()
    {
        $this->authorize('aspv');
        $popularPresets = $this->getPopularPresets();
        return view('tablekategori.createkategori', compact('popularPresets'));
    }

    public function editkategori($id)
    {
        $this->authorize('aspv');
        $data = Kategori::findOrFail($id);

        $systemCards = $this->getSystemCards();
        $card = $systemCards[$id] ?? $systemCards[$data->name] ?? [];

        $popularPresets = $this->getPopularPresets();

        $selectedBadge = $card['badge'] ?? '';
        $selectedDesc = $card['desc'] ?? '';
        $selectedIcon = $card['icon'] ?? '';
        $iconMode = $card['icon_mode'] ?? 'preset';
        $iconCustom = '';
        $iconFile = '';
        $isImageIcon = false;

        if (!empty($selectedIcon)) {
            if (str_starts_with($selectedIcon, '/uploads/') || preg_match('/\.(png|svg|jpg|jpeg|webp)$/i', $selectedIcon)) {
                $iconMode = 'upload';
                $iconFile = $selectedIcon;
                $isImageIcon = true;
            } elseif (array_key_exists(strtolower($selectedIcon), $popularPresets)) {
                $iconMode = 'preset';
            } else {
                $iconMode = 'custom';
                $iconCustom = $selectedIcon;
            }
        }

        return view('tablekategori.editkategori', compact(
            'data',
            'popularPresets',
            'selectedBadge',
            'selectedDesc',
            'selectedIcon',
            'iconMode',
            'iconCustom',
            'iconFile',
            'isImageIcon'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('aspv');

        $request->validate([
            'name' => 'required|max:255|unique:kategori,name',
            'badge' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'icon_mode' => 'nullable|string|in:preset,custom,upload',
            'icon' => 'nullable|string|max:50',
            'icon_custom' => 'nullable|string|max:50',
            'icon_file' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:1024',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah terdaftar.',
            'icon_file.mimes' => 'Format file logo harus berupa PNG, JPG, JPEG, SVG, atau WebP.',
            'icon_file.max' => 'Ukuran file icon maksimal 1 MB (1024 KB).',
        ]);

        // 1. Simpan kategori murni ke database (Ticketing & Request aman 100%)
        $kategori = new Kategori;
        $kategori->name = trim($request->input('name'));
        $kategori->save();

        // 2. Simpan konfigurasi tampilan kartu sistem ke system_cards.json
        $icon = $this->resolveIconFromRequest($request);
        $cards = $this->getSystemCards();
        $cards[(string) $kategori->id] = [
            'name' => $kategori->name,
            'badge' => trim($request->input('badge', 'Sistem Probus')),
            'desc' => trim($request->input('description', '')),
            'icon_mode' => $request->input('icon_mode', 'preset'),
            'icon' => $icon,
        ];
        $this->saveSystemCards($cards);

        Alert::success('Berhasil!', 'Kategori baru berhasil ditambahkan.');
        return redirect('/kategori');
    }

    public function updatekategori(Request $request, $id)
    {
        $this->authorize('aspv');
        $data = Kategori::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255|unique:kategori,name,' . $id,
            'badge' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'icon_mode' => 'nullable|string|in:preset,custom,upload',
            'icon' => 'nullable|string|max:50',
            'icon_custom' => 'nullable|string|max:50',
            'icon_file' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:1024',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori ini sudah terdaftar.',
            'icon_file.mimes' => 'Format file logo harus berupa PNG, JPG, JPEG, SVG, atau WebP.',
            'icon_file.max' => 'Ukuran file icon maksimal 1 MB (1024 KB).',
        ]);

        $cards = $this->getSystemCards();
        $existingCard = $cards[(string) $id] ?? $cards[$data->name] ?? [];
        $existingIcon = $existingCard['icon'] ?? null;

        // 1. Perbarui nama kategori murni ke database
        $data->update([
            'name' => trim($request->input('name'))
        ]);

        // 2. Perbarui konfigurasi kartu sistem
        $icon = $this->resolveIconFromRequest($request, $existingIcon);
        $cards[(string) $id] = [
            'name' => $data->name,
            'badge' => trim($request->input('badge', 'Sistem Probus')),
            'desc' => trim($request->input('description', '')),
            'icon_mode' => $request->input('icon_mode', 'preset'),
            'icon' => $icon,
        ];
        $this->saveSystemCards($cards);

        Alert::success('Berhasil!', 'Data kategori dan kartu sistem berhasil diperbarui.');
        return redirect('/kategori');
    }

    public function delete($id)
    {
        $this->authorize('aspv');
        $request = \App\Models\Request::where('kategori_id', $id)->get();
        $updatesystem = \App\Models\UpdateSystem::where('kategori_id', $id)->get();
        $menuSystems = \App\Models\MenuSystem::where('kategori_id', $id)->get();

        if ($request->count() > 0 || $updatesystem->count() > 0 || $menuSystems->count() > 0) {
            Alert::error('Gagal Menghapus Data!', 'Kategori ini masih digunakan oleh request, update sistem, atau menu modul.');
            return redirect()->back();
        }

        $data = Kategori::findOrFail($id);
        $name = $data->name;
        $data->delete();

        // Hapus metadata kartu dari JSON
        $cards = $this->getSystemCards();
        unset($cards[(string) $id]);
        unset($cards[$name]);
        $this->saveSystemCards($cards);

        Alert::success('Berhasil!', 'Kategori berhasil dihapus.');
        return redirect('/kategori');
    }
}
