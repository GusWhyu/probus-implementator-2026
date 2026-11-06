<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kategori;
use App\Models\MenuSystem;
use App\Models\ManualBook;

class ManualBookController extends Controller
{
    /**
     * Tampilan UI Manual Book & Tata Cara Penggunaan Sistem
     */
    public function index(Request $request)
    {
        // 1. Ambil kategori yang memiliki menuSystems (atau semua kategori)
        $categories = Kategori::with(['menuSystems' => function ($q) {
            $q->orderBy('order')->with(['manualBooks' => function ($mb) {
                $mb->latest();
            }]);
        }])->get();

        $systems = [];
        $modulesBySystem = [];

        foreach ($categories as $cat) {
            $modList = [];

            foreach ($cat->menuSystems as $ms) {
                $latestBook = $ms->manualBooks->first();
                $hasContent = $latestBook && !empty(trim($latestBook->content));
                $isDraft = $latestBook && $latestBook->status === 'draft';

                $statusLabel = 'Belum Diisi • Draft';
                $statusClass = 'text-amber-600 dark:text-amber-400';

                if ($hasContent) {
                    if ($isDraft) {
                        $statusLabel = 'Sedang Diedit ✎';
                        $statusClass = 'text-blue-600 dark:text-blue-400';
                    } else {
                        $statusLabel = 'Panduan Terisi ✓';
                        $statusClass = 'text-emerald-600 dark:text-emerald-400';
                    }
                }

                $orderPrefix = $ms->order ? str_pad($ms->order, 2, '0', STR_PAD_LEFT) . '. ' : '';
                $defaultTitle = $latestBook ? $latestBook->title : ($orderPrefix ? $orderPrefix . $ms->name : $ms->name);

                $modList[] = [
                    'id' => (string) $ms->id,
                    'menu_system_id' => $ms->id,
                    'title' => $defaultTitle,
                    'raw_name' => $ms->name,
                    'statusLabel' => $statusLabel,
                    'statusClass' => $statusClass,
                    'breadcrumb' => strtoupper($cat->name) . ' • ' . strtoupper($ms->name) . ' • PENGISIAN TATA CARA PENGGUNAAN',
                    'description' => $latestBook ? ($latestBook->description ? $latestBook->description : $ms->description) : ($ms->description ? $ms->description : ''),
                    'content' => $latestBook ? $latestBook->content : '',
                    'manual_book_id' => $latestBook ? $latestBook->id : null,
                    'status' => $latestBook ? $latestBook->status : 'draft',
                ];
            }

            $systems[] = [
                'id' => (string) $cat->id,
                'name' => $cat->name,
                'moduleCount' => count($modList),
            ];

            $modulesBySystem[(string) $cat->id] = $modList;
        }

        return view('manualbook', [
            'systems' => $systems,
            'modulesBySystem' => $modulesBySystem,
            'categories' => $categories,
        ]);
    }

    /**
     * Menyimpan atau memperbarui panduan tata cara modul (Manual Book)
     */
    public function saveModule(Request $request)
    {
        $request->validate([
            'menu_system_id' => 'required|exists:menu_systems,id',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $menuSystem = MenuSystem::findOrFail($request->menu_system_id);
        $userId = Auth::id() ? Auth::id() : 1;

        // Cari apakah modul ini sudah memiliki manual book
        $manualBook = $menuSystem->manualBooks()->first();

        if ($manualBook) {
            $manualBook->update([
                'user_id' => $userId,
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content,
                'status' => $request->status,
            ]);
        } else {
            $manualBook = ManualBook::create([
                'user_id' => $userId,
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content,
                'status' => $request->status,
            ]);

            $menuSystem->manualBooks()->attach($manualBook->id);
        }

        // Perbarui juga deskripsi di modul jika diisi
        if ($request->filled('description')) {
            $menuSystem->update(['description' => $request->description]);
        }

        $message = $request->status === 'published'
            ? 'Tata cara modul "' . $manualBook->title . '" berhasil disimpan dan dipublikasikan ke Manual Book!'
            : 'Draft panduan modul "' . $manualBook->title . '" berhasil disimpan!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'manual_book' => $manualBook,
            'statusLabel' => $request->status === 'published' ? 'Panduan Terisi ✓' : 'Sedang Diedit ✎',
            'statusClass' => $request->status === 'published' ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400',
        ]);
    }

    /**
     * Menghapus modul (MenuSystem) beserta relasinya
     */
    public function deleteModule($id)
    {
        $menuSystem = MenuSystem::findOrFail($id);
        $name = $menuSystem->name;
        $kategoriId = $menuSystem->kategori_id;

        // Lepas relasi pivot manual books & articles
        $menuSystem->manualBooks()->detach();
        $menuSystem->articles()->detach();
        $menuSystem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Modul "' . $name . '" berhasil dihapus!',
            'deleted_id' => (string) $id,
            'kategori_id' => (string) $kategoriId,
        ]);
    }

    /**
     * Menghapus sistem (Kategori)
     */
    public function deleteSystem($id)
    {
        $kategori = Kategori::with('menuSystems')->findOrFail($id);

        if ($kategori->menuSystems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sistem "' . $kategori->name . '" masih memiliki ' . $kategori->menuSystems()->count() . ' modul. Hapus atau kosongkan modulnya terlebih dahulu.',
            ], 422);
        }

        $name = $kategori->name;
        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sistem "' . $name . '" berhasil dihapus!',
            'deleted_id' => (string) $id,
        ]);
    }

    /**
     * Menambahkan sistem baru (Kategori Level 1)
     */
    public function addSystem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:kategori,name',
        ]);

        $kategori = Kategori::create([
            'name' => trim($request->name),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sistem baru "' . $kategori->name . '" berhasil ditambahkan!',
            'system' => [
                'id' => (string) $kategori->id,
                'name' => $kategori->name,
                'moduleCount' => 0,
            ],
        ]);
    }

    /**
     * Menambahkan modul baru (MenuSystem Level 2)
     */
    public function addModule(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'name' => 'required|string|max:100',
            'order' => 'nullable|integer',
        ]);

        $order = $request->order ? (int) $request->order : 0;
        if ($order === 0) {
            $maxOrder = MenuSystem::where('kategori_id', $request->kategori_id)->max('order');
            $order = $maxOrder ? $maxOrder + 1 : 1;
        }

        $menuSystem = MenuSystem::create([
            'kategori_id' => $request->kategori_id,
            'name' => trim($request->name),
            'order' => $order,
        ]);

        $kategori = $menuSystem->kategori;
        $orderPrefix = str_pad($order, 2, '0', STR_PAD_LEFT) . '. ';
        $fullTitle = $orderPrefix . $menuSystem->name;

        return response()->json([
            'success' => true,
            'message' => 'Modul baru "' . $menuSystem->name . '" berhasil ditambahkan!',
            'module' => [
                'id' => (string) $menuSystem->id,
                'menu_system_id' => $menuSystem->id,
                'title' => $fullTitle,
                'raw_name' => $menuSystem->name,
                'statusLabel' => 'Belum Diisi • Draft',
                'statusClass' => 'text-amber-600 dark:text-amber-400',
                'breadcrumb' => strtoupper($kategori ? $kategori->name : 'SISTEM') . ' • MODUL ' . str_pad($order, 2, '0', STR_PAD_LEFT) . ' • PENGISIAN TATA CARA PENGGUNAAN',
                'description' => 'Panduan langkah demi langkah untuk modul ' . $menuSystem->name,
                'content' => '',
                'manual_book_id' => null,
                'status' => 'draft',
            ],
        ]);
    }

    /**
     * Mengambil data spesifik modul beserta isi manual book
     */
    public function getModuleData($id)
    {
        $menuSystem = MenuSystem::with(['kategori', 'manualBooks' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        $book = $menuSystem->manualBooks->first();

        return response()->json([
            'success' => true,
            'data' => [
                'menu_system' => $menuSystem,
                'manual_book' => $book,
            ],
        ]);
    }
}
