<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Kategori;
use App\Models\MenuSystem;
use App\Models\ManualBook;
use App\Helpers\DocxParser;

class ManualBookController extends Controller
{
    /**
     * Halaman Ringkasan & Katalog Manual Book Sistem (Summary View)
     */
    public function index(Request $request)
    {
        $data = $this->getManualBookData();

        $totalSystems = count($data['systems']);
        $totalModules = 0;
        $totalChapters = 0;
        $documentedModules = 0;
        $publishedChapters = 0;

        foreach ($data['modulesBySystem'] as $modList) {
            foreach ($modList as $m) {
                $totalModules++;
                $totalChapters += $m['chapterCount'];
                if ($m['chapterCount'] > 0) {
                    $documentedModules++;
                }
                $publishedChapters += ($m['publishedCount'] ?? 0);
            }
        }

        return view('manual_book.index', array_merge($data, [
            'totalSystems' => $totalSystems,
            'totalModules' => $totalModules,
            'totalChapters' => $totalChapters,
            'documentedModules' => $documentedModules,
            'publishedChapters' => $publishedChapters,
        ]));
    }

    /**
     * Halaman Editor & Manajemen CRUD Bab Manual Book
     */
    public function manage(Request $request)
    {
        $data = $this->getManualBookData();
        return view('manualbook', $data);
    }

    /**
     * Helper untuk mengambil dan memformat data sistem, modul, dan bab panduan
     */
    private function getManualBookData(): array
    {
        // 1. Ambil kategori beserta modul utama (parent_id is null) dan sub-menu bersarangnya
        $categories = Kategori::with(['menuSystems' => function ($q) {
            $q->whereNull('parent_id')
              ->orderBy('order')
              ->orderBy('id')
              ->with([
                  'manualBooks' => function ($mb) {
                      $mb->orderBy('order')->orderBy('id');
                  },
                  'subMenus' => function ($sm) {
                      $sm->orderBy('order')->orderBy('id')->with(['manualBooks' => function ($mb) {
                          $mb->orderBy('order')->orderBy('id');
                      }]);
                  }
              ]);
        }])->get();

        $systems = [];
        $modulesBySystem = [];

        foreach ($categories as $cat) {
            $modList = [];

            foreach ($cat->menuSystems as $ms) {
                // Bab langsung dari modul utama
                $chapters = [];
                foreach ($ms->manualBooks as $idx => $book) {
                    $chapters[] = $this->formatChapterItem($book, $idx);
                }

                $cleanName = trim(preg_replace('/^\d+[\.\s\-]+/', '', $ms->name));
                $cleanName = $cleanName ?: $ms->name;

                // Format daftar sub-menu jika ada
                $subMenusList = [];
                foreach ($ms->subMenus as $sIdx => $sub) {
                    $subChapters = [];
                    foreach ($sub->manualBooks as $sbIdx => $sBook) {
                        $subChapters[] = $this->formatChapterItem($sBook, $sbIdx);
                    }

                    $subTotalChapters = count($subChapters);
                    $subPublishedCount = count(array_filter($subChapters, function ($c) {
                        return $c['status'] === 'published';
                    }));

                    $subCleanName = trim(preg_replace('/^\d+[\.\s\-]+/', '', $sub->name));
                    $subCleanName = $subCleanName ?: $sub->name;
                    $subOrderPrefix = $sub->order ? $sub->order . '. ' : (($sIdx + 1) . '. ');
                    $subTitle = $subOrderPrefix . $subCleanName;

                    $subStatusLabel = 'Belum Ada Bab';
                    $subStatusClass = 'text-slate-400 dark:text-slate-500';
                    if ($subTotalChapters > 0) {
                        if ($subPublishedCount === $subTotalChapters) {
                            $subStatusLabel = $subTotalChapters . ' Bab • Terisi Lengkap ✓';
                            $subStatusClass = 'text-emerald-600 dark:text-emerald-400';
                        } else {
                            $subStatusLabel = $subTotalChapters . ' Bab (' . $subPublishedCount . ' Terbit)';
                            $subStatusClass = 'text-blue-600 dark:text-blue-400';
                        }
                    }

                    $subMenusList[] = [
                        'id' => (string) $sub->id,
                        'parent_id' => (string) $ms->id,
                        'menu_system_id' => $sub->id,
                        'system_id' => (string) $cat->id,
                        'system_name' => $cat->name,
                        'title' => $subTitle,
                        'raw_name' => $subCleanName,
                        'order' => $sub->order,
                        'description' => $sub->description ?: '',
                        'statusLabel' => $subStatusLabel,
                        'statusClass' => $subStatusClass,
                        'chapterCount' => $subTotalChapters,
                        'publishedCount' => $subPublishedCount,
                        'breadcrumb' => strtoupper($cat->name) . ' • ' . strtoupper($cleanName) . ' • ' . strtoupper($subCleanName) . ' • BUKU PANDUAN PENGGUNAAN',
                        'chapters' => $subChapters,
                    ];
                }

                // Kalkulasi total bab (modul utama + semua sub menu)
                $ownChapterCount = count($chapters);
                $subChapterCount = array_sum(array_column($subMenusList, 'chapterCount'));
                $totalChapters = $ownChapterCount + $subChapterCount;

                $ownPublished = count(array_filter($chapters, function ($c) {
                    return $c['status'] === 'published';
                }));
                $subPublished = array_sum(array_column($subMenusList, 'publishedCount'));
                $publishedCount = $ownPublished + $subPublished;

                $hasSubMenus = count($subMenusList) > 0;

                $statusLabel = 'Belum Ada Bab';
                $statusClass = 'text-slate-400 dark:text-slate-500';
                if ($hasSubMenus) {
                    $statusLabel = count($subMenusList) . ' Sub Menu • ' . ($totalChapters > 0 ? $totalChapters . ' Bab' : 'Belum Ada Bab');
                    $statusClass = 'text-indigo-600 dark:text-indigo-400';
                } elseif ($totalChapters > 0) {
                    if ($publishedCount === $totalChapters) {
                        $statusLabel = $totalChapters . ' Bab • Terisi Lengkap ✓';
                        $statusClass = 'text-emerald-600 dark:text-emerald-400';
                    } else {
                        $statusLabel = $totalChapters . ' Bab (' . $publishedCount . ' Terbit)';
                        $statusClass = 'text-blue-600 dark:text-blue-400';
                    }
                }

                $orderPrefix = $ms->order ? str_pad($ms->order, 2, '0', STR_PAD_LEFT) . '. ' : '';
                $defaultTitle = $orderPrefix ? $orderPrefix . $cleanName : $cleanName;

                $modList[] = [
                    'id' => (string) $ms->id,
                    'parent_id' => null,
                    'menu_system_id' => $ms->id,
                    'system_id' => (string) $cat->id,
                    'system_name' => $cat->name,
                    'title' => $defaultTitle,
                    'raw_name' => $cleanName,
                    'order' => $ms->order,
                    'description' => $ms->description ?: '',
                    'has_sub_menus' => $hasSubMenus,
                    'sub_menus' => $subMenusList,
                    'statusLabel' => $statusLabel,
                    'statusClass' => $statusClass,
                    'ownChapterCount' => $ownChapterCount,
                    'chapterCount' => $totalChapters,
                    'publishedCount' => $publishedCount,
                    'breadcrumb' => strtoupper($cat->name) . ' • ' . strtoupper($cleanName) . ' • BUKU PANDUAN PENGGUNAAN',
                    'chapters' => $chapters,
                ];
            }

            $systems[] = [
                'id' => (string) $cat->id,
                'name' => $cat->name,
                'moduleCount' => count($modList),
            ];

            $modulesBySystem[(string) $cat->id] = $modList;
        }

        return [
            'systems' => $systems,
            'modulesBySystem' => $modulesBySystem,
            'categories' => $categories,
        ];
    }

    /**
     * Helper untuk memformat item bab buku manual
     */
    private function formatChapterItem($book, $idx): array
    {
        $orderNum = $book->order ? (int) $book->order : ($idx + 1);
        $hasContent = !empty(trim(strip_tags($book->content, '<img>')));
        $isPublished = $book->status === 'published';

        $statusLabel = 'Belum Diisi • Draft';
        $statusClass = 'text-amber-600 dark:text-amber-400';
        if ($hasContent) {
            if ($isPublished) {
                $statusLabel = 'Panduan Terisi ✓';
                $statusClass = 'text-emerald-600 dark:text-emerald-400';
            } else {
                $statusLabel = 'Sedang Diedit ✎';
                $statusClass = 'text-blue-600 dark:text-blue-400';
            }
        }

        return [
            'id' => (string) $book->id,
            'order' => $orderNum,
            'title' => $book->title,
            'description' => $book->description ?: '',
            'content' => $book->content ?: '',
            'status' => $book->status ?: 'draft',
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'updated_at' => $book->updated_at ? $book->updated_at->format('d M Y H:i') : '-',
        ];
    }

    /**
     * Menyimpan atau memperbarui bab / dokumen panduan tata cara modul
     */
    public function saveModule(Request $request)
    {
        $request->validate([
            'menu_system_id' => 'required|exists:menu_systems,id',
            'chapter_id' => 'nullable',
            'order' => 'nullable|integer',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $menuSystem = MenuSystem::findOrFail($request->menu_system_id);
        $userId = Auth::id() ? Auth::id() : 1;

        $manualBook = null;
        if ($request->filled('chapter_id') && is_numeric($request->chapter_id)) {
            $manualBook = ManualBook::whereHas('menuSystems', function ($q) use ($menuSystem) {
                $q->where('menu_systems.id', $menuSystem->id);
            })->find($request->chapter_id);
        }

        $order = $request->order ? (int) $request->order : null;
        if (!$order) {
            if ($manualBook && $manualBook->order) {
                $order = (int) $manualBook->order;
            } else {
                $maxOrder = $menuSystem->manualBooks()->max('order');
                $order = $maxOrder ? $maxOrder + 1 : 1;
            }
        }

        $saveData = [
            'user_id' => $userId,
            'title' => trim($request->title),
            'order' => $order,
            'description' => $request->description,
            'content' => $request->content,
            'status' => $request->status,
        ];

        if ($manualBook) {
            $manualBook->update($saveData);
        } else {
            $manualBook = ManualBook::create($saveData);
            $menuSystem->manualBooks()->attach($manualBook->id);
        }

        $hasContent = !empty(trim(strip_tags($manualBook->content, '<img>')));
        $statusLabel = $request->status === 'published' ? 'Panduan Terisi ✓' : 'Sedang Diedit ✎';
        $statusClass = $request->status === 'published' ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400';

        $chapterData = [
            'id' => (string) $manualBook->id,
            'order' => (int) $manualBook->order,
            'title' => $manualBook->title,
            'description' => $manualBook->description ? $manualBook->description : '',
            'content' => $manualBook->content ? $manualBook->content : '',
            'status' => $manualBook->status,
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'updated_at' => $manualBook->updated_at ? $manualBook->updated_at->format('d M Y H:i') : '-',
        ];

        $message = $request->status === 'published'
            ? 'Bab panduan "' . $manualBook->title . '" berhasil disimpan dan dipublikasikan!'
            : 'Draft bab panduan "' . $manualBook->title . '" berhasil disimpan!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'chapter' => $chapterData,
            'menu_system_id' => (string) $menuSystem->id,
        ]);
    }

    /**
     * Menghapus bab / dokumen panduan spesifik
     */
    public function deleteChapter($id)
    {
        $manualBook = ManualBook::findOrFail($id);
        $title = $manualBook->title;

        // Lepas relasi pivot
        $manualBook->menuSystems()->detach();
        $manualBook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bab panduan "' . $title . '" berhasil dihapus!',
            'deleted_id' => (string) $id,
        ]);
    }

    /**
     * Import file Microsoft Word (.docx) secara otomatis ke format web TinyMCE
     */
    public function importWord(Request $request)
    {
        $request->validate([
            'menu_system_id' => 'required|exists:menu_systems,id',
            'word_file' => 'required|file|max:25600', // maksimal 25 MB
        ]);

        $file = $request->file('word_file');
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext !== 'docx') {
            return response()->json([
                'success' => false,
                'message' => 'Format file harus berupa Microsoft Word Document (.docx)!',
            ], 422);
        }

        $rawName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Bersihkan nama & deteksi nomor urutan jika ada (misal: "12.Balance Sheet" -> order: 12, title: "12. Balance Sheet")
        $order = 1;
        $title = $rawName;
        if (preg_match('/^(\d+)[\.\s_-]*(.*)$/', $rawName, $m)) {
            $order = (int) $m[1];
            $cleanTitle = trim($m[2]);
            $title = $order . '. ' . ($cleanTitle ? $cleanTitle : $rawName);
        }

        if (mb_strlen($title) > 100) {
            $title = mb_substr($title, 0, 100);
        }

        try {
            $parsed = DocxParser::parse($file->getPathname());
            
            return response()->json([
                'success' => true,
                'message' => 'File Word "' . $rawName . '" berhasil dibaca!',
                'title' => $title,
                'order' => $order,
                'content' => $parsed['html'],
                'image_count' => $parsed['image_count'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca isi file Word: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menghapus modul (MenuSystem) beserta relasinya
     */
    public function deleteModule($id)
    {
        $menuSystem = MenuSystem::with('subMenus')->findOrFail($id);
        $name = $menuSystem->name;
        $kategoriId = $menuSystem->kategori_id;
        $parentId = $menuSystem->parent_id;

        // Jika modul utama memiliki sub-menus, lepas juga relasi sub-menus
        foreach ($menuSystem->subMenus as $sub) {
            $sub->manualBooks()->detach();
            $sub->articles()->detach();
            $sub->delete();
        }

        // Lepas relasi pivot manual books & articles
        $menuSystem->manualBooks()->detach();
        $menuSystem->articles()->detach();
        $menuSystem->delete();

        return response()->json([
            'success' => true,
            'message' => ($parentId ? 'Sub Menu "' : 'Modul "') . $name . '" berhasil dihapus!',
            'deleted_id' => (string) $id,
            'parent_id' => $parentId ? (string) $parentId : null,
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
     * Menambahkan modul baru atau sub menu (MenuSystem Level 2/3)
     */
    public function addModule(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'parent_id' => 'nullable|exists:menu_systems,id',
            'name' => 'required|string|max:100',
            'order' => 'nullable|integer',
        ]);

        $parentId = $request->parent_id ?: null;
        $order = $request->order ? (int) $request->order : 0;

        if ($order === 0) {
            if ($parentId) {
                $maxOrder = MenuSystem::where('parent_id', $parentId)->max('order');
            } else {
                $maxOrder = MenuSystem::where('kategori_id', $request->kategori_id)
                    ->whereNull('parent_id')
                    ->max('order');
            }
            $order = $maxOrder ? $maxOrder + 1 : 1;
        }

        $menuSystem = MenuSystem::create([
            'kategori_id' => $request->kategori_id,
            'parent_id' => $parentId,
            'name' => trim($request->name),
            'order' => $order,
        ]);

        $kategori = $menuSystem->kategori;
        $cleanName = trim(preg_replace('/^\d+[\.\s\-]+/', '', $menuSystem->name));
        $cleanName = $cleanName ?: $menuSystem->name;

        if ($parentId) {
            $parent = MenuSystem::find($parentId);
            $parentCleanName = $parent ? trim(preg_replace('/^\d+[\.\s\-]+/', '', $parent->name)) : '';
            $subOrderPrefix = $order ? $order . '. ' : '';
            $fullTitle = $subOrderPrefix . $cleanName;
            $breadcrumb = strtoupper($kategori ? $kategori->name : 'SISTEM') . ' • ' . strtoupper($parentCleanName) . ' • ' . strtoupper($cleanName) . ' • BUKU PANDUAN PENGGUNAAN';
        } else {
            $orderPrefix = str_pad($order, 2, '0', STR_PAD_LEFT) . '. ';
            $fullTitle = $orderPrefix . $cleanName;
            $breadcrumb = strtoupper($kategori ? $kategori->name : 'SISTEM') . ' • MODUL ' . str_pad($order, 2, '0', STR_PAD_LEFT) . ' • PENGISIAN TATA CARA PENGGUNAAN';
        }

        return response()->json([
            'success' => true,
            'message' => ($parentId ? 'Sub Menu baru "' : 'Modul baru "') . $menuSystem->name . '" berhasil ditambahkan!',
            'module' => [
                'id' => (string) $menuSystem->id,
                'parent_id' => $parentId ? (string) $parentId : null,
                'menu_system_id' => $menuSystem->id,
                'system_id' => (string) $menuSystem->kategori_id,
                'system_name' => $kategori ? $kategori->name : '',
                'title' => $fullTitle,
                'raw_name' => $cleanName,
                'order' => $order,
                'statusLabel' => 'Belum Ada Bab',
                'statusClass' => 'text-slate-400 dark:text-slate-500',
                'chapterCount' => 0,
                'publishedCount' => 0,
                'breadcrumb' => $breadcrumb,
                'description' => 'Panduan langkah demi langkah untuk ' . $menuSystem->name,
                'chapters' => [],
                'has_sub_menus' => false,
                'sub_menus' => [],
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
