<?php

namespace App\Http\Controllers;

use App\Models\MenuSystem;
use App\Models\Kategori;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MenuSystemController extends Controller
{
    /**
     * Tampilan Master Menu Modul Sistem
     */
    public function index(Request $request)
    {
        $title = 'Hapus Modul Sistem!';
        $text = 'Apakah Anda yakin ingin menghapus modul sistem ini?';
        confirmDelete($title, $text);

        $categories = Kategori::withCount('menuSystems')->orderBy('name', 'asc')->get();

        $query = MenuSystem::with(['kategori'])
            ->withCount(['articles', 'manualBooks']);

        // Filter berdasarkan sistem (Kategori)
        $activeCategoryId = null;
        if ($request->filled('kategori_id') && $request->kategori_id !== 'all') {
            $activeCategoryId = (int) $request->kategori_id;
            $query->where('kategori_id', $activeCategoryId);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function ($kq) use ($search) {
                      $kq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan sistem lalu urutan modul (order)
        $modules = $query->join('kategori', 'menu_systems.kategori_id', '=', 'kategori.id')
            ->select('menu_systems.*')
            ->orderBy('kategori.name', 'asc')
            ->orderBy('menu_systems.order', 'asc')
            ->get();

        // Statistik ringkas
        $totalModules = MenuSystem::count();
        $totalCategories = $categories->count();
        $totalConnected = MenuSystem::has('articles')->orHas('manualBooks')->count();
        $systemModuleCounts = $categories->pluck('menu_systems_count', 'id')->toArray();

        return view('menu_system.index', compact(
            'modules',
            'categories',
            'activeCategoryId',
            'totalModules',
            'totalCategories',
            'totalConnected',
            'systemModuleCounts'
        ));
    }

    /**
     * Form Tambah Modul Sistem Baru
     */
    public function create(Request $request)
    {
        $categories = Kategori::orderBy('name', 'asc')->get();
        $selectedKategoriId = $request->input('kategori_id', $categories->first() ? $categories->first()->id : null);

        // Hitung estimasi order berikutnya untuk kategori yang dipilih
        $nextOrder = 1;
        if ($selectedKategoriId) {
            $maxOrder = MenuSystem::where('kategori_id', $selectedKategoriId)->max('order');
            $nextOrder = $maxOrder ? $maxOrder + 1 : 1;
        }

        $standardSuggestions = [
            ['name' => 'Sales', 'desc' => 'Modul transaksi penjualan, kasir POS, reservasi meja, dan pendapatan.'],
            ['name' => 'Production', 'desc' => 'Modul pengolahan dapur, resep, kitchen display system (KDS), dan produksi makanan/minuman.'],
            ['name' => 'Cost Control', 'desc' => 'Modul pengendalian biaya, food & beverage cost, dan laporan analisa selisih.'],
            ['name' => 'Purchasing / AP', 'desc' => 'Modul pengadaan barang, purchase request (PR), purchase order (PO), receiving, dan hutang usaha.'],
            ['name' => 'Accounts Receivable / AR', 'desc' => 'Modul piutang usaha, city ledger, penagihan invoice klien, dan pelunasan.'],
            ['name' => 'Accounting System', 'desc' => 'Modul sistem akuntansi, jurnal umum, buku besar (GL), neraca, dan laporan keuangan.'],
            ['name' => 'Tools', 'desc' => 'Modul utilitas sistem, konfigurasi hardware & printer, cash drawer, dan diagnostik perangkat.'],
            ['name' => 'Calendar', 'desc' => 'Modul kalender kerja, reservasi event, jadwal operasional, dan agenda sistem.'],
            ['name' => 'Calculator', 'desc' => 'Modul kalkulator sistem, simulasi perhitungan harga, diskon, dan konversi kurs.'],
            ['name' => 'Reconnecting', 'desc' => 'Modul pemulihan koneksi offline/online, sinkronisasi data cloud, dan pemulihan jaringan.'],
        ];

        return view('menu_system.create', compact('categories', 'selectedKategoriId', 'nextOrder', 'standardSuggestions'));
    }

    /**
     * Simpan Modul Sistem Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'name' => 'required|string|max:100',
            'order' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ], [
            'kategori_id.required' => 'Sistem / Kategori wajib dipilih.',
            'kategori_id.exists' => 'Sistem / Kategori tidak valid.',
            'name.required' => 'Nama modul wajib diisi.',
            'name.max' => 'Nama modul maksimal 100 karakter.',
            'order.required' => 'Urutan modul wajib diisi.',
            'order.integer' => 'Urutan modul harus berupa angka bulat.',
        ]);

        MenuSystem::create([
            'kategori_id' => $validated['kategori_id'],
            'name' => trim($validated['name']),
            'order' => (int) $validated['order'],
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ]);

        Alert::success('Berhasil!', 'Modul sistem "' . $validated['name'] . '" berhasil ditambahkan.');

        return redirect()->route('menu-system.index', ['kategori_id' => $validated['kategori_id']]);
    }

    /**
     * Form Edit Modul Sistem
     */
    public function edit($id)
    {
        $module = MenuSystem::with('kategori')->findOrFail($id);
        $categories = Kategori::orderBy('name', 'asc')->get();

        return view('menu_system.edit', compact('module', 'categories'));
    }

    /**
     * Update Modul Sistem
     */
    public function update(Request $request, $id)
    {
        $module = MenuSystem::findOrFail($id);

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'name' => 'required|string|max:100',
            'order' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ], [
            'kategori_id.required' => 'Sistem / Kategori wajib dipilih.',
            'kategori_id.exists' => 'Sistem / Kategori tidak valid.',
            'name.required' => 'Nama modul wajib diisi.',
            'name.max' => 'Nama modul maksimal 100 karakter.',
            'order.required' => 'Urutan modul wajib diisi.',
            'order.integer' => 'Urutan modul harus berupa angka bulat.',
        ]);

        $module->update([
            'kategori_id' => $validated['kategori_id'],
            'name' => trim($validated['name']),
            'order' => (int) $validated['order'],
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ]);

        Alert::success('Berhasil!', 'Data modul sistem berhasil diperbarui.');

        return redirect()->route('menu-system.index', ['kategori_id' => $validated['kategori_id']]);
    }

    /**
     * Hapus Modul Sistem
     */
    public function destroy($id)
    {
        $module = MenuSystem::findOrFail($id);
        $name = $module->name;
        $kategoriId = $module->kategori_id;

        // Lepaskan relasi pivot jika ada artikel / manual book agar tidak error constraint
        $module->articles()->detach();
        $module->manualBooks()->detach();
        $module->delete();

        Alert::success('Berhasil!', 'Modul sistem "' . $name . '" berhasil dihapus.');

        return redirect()->route('menu-system.index', ['kategori_id' => $kategoriId]);
    }

    /**
     * Auto-Generate 10 Modul Standar untuk Kategori tertentu
     */
    public function autoSeedCategory($kategoriId)
    {
        $kategori = Kategori::findOrFail($kategoriId);

        $standardModules = [
            ['name' => 'Setup Master', 'desc' => 'Modul setup master data, parameter sistem, konfigurasi outlet, dan pengaturan master.', 'order' => 1],
            ['name' => 'Transaksi', 'desc' => 'Modul transaksi penjualan operasional kasir POS, order taker, meja, dan split bill.', 'order' => 2],
            ['name' => 'Outlet Activity', 'desc' => 'Modul aktivitas dan operasional outlet, shift kerja kasir, dan pantauan kegiatan harian.', 'order' => 3],
            ['name' => 'Cost Control', 'desc' => 'Modul pengendalian biaya, resep, food & beverage cost, dan laporan analisa selisih.', 'order' => 4],
            ['name' => 'Purcashing AP', 'desc' => 'Modul pengadaan barang, purchasing / AP, purchase request (PR), purchase order (PO), receiving, dan hutang usaha.', 'order' => 5],
            ['name' => 'AR', 'desc' => 'Modul piutang usaha (Accounts Receivable / AR), penagihan invoice klien, city ledger, dan rekonsiliasi.', 'order' => 6],
            ['name' => 'General Cashier', 'desc' => 'Modul kasir umum, pencatatan penerimaan & pengeluaran kas, serta rekonsiliasi kas harian.', 'order' => 7],
            ['name' => 'Accounting System', 'desc' => 'Modul sistem akuntansi, jurnal umum, buku besar (GL), neraca, dan laporan keuangan.', 'order' => 8],
            ['name' => 'Report', 'desc' => 'Modul pusat laporan operasional, laporan penjualan harian/bulanan, analitik, dan statistik performa.', 'order' => 9],
            ['name' => 'Tools', 'desc' => 'Modul utilitas sistem, konfigurasi hardware & printer, cash drawer, dan diagnostik perangkat.', 'order' => 10],
        ];

        foreach ($standardModules as $m) {
            $existing = MenuSystem::where('kategori_id', $kategori->id)
                ->whereNull('parent_id')
                ->where(function ($q) use ($m) {
                    $q->where('name', $m['name'])
                      ->orWhere('order', $m['order']);
                })->first();

            if ($existing) {
                $existing->update([
                    'name' => $m['name'],
                    'description' => $m['desc'],
                    'order' => $m['order'],
                ]);
            } else {
                MenuSystem::create([
                    'kategori_id' => $kategori->id,
                    'parent_id' => null,
                    'name' => $m['name'],
                    'description' => $m['desc'],
                    'order' => $m['order'],
                ]);
            }
        }

        Alert::success('Berhasil!', '10 Modul standar berhasil dibuat untuk sistem "' . $kategori->name . '".');

        return redirect()->route('menu-system.index', ['kategori_id' => $kategori->id]);
    }
}
