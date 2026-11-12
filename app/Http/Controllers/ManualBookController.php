<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Kategori;
use App\Models\MenuSystem;
use App\Models\ManualBook;
use App\Models\ArticleAndManualBooksLog;
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
        // Ambil kategori beserta modul-modulnya dan bab panduannya
        $categories = Kategori::with(['menuSystems' => function ($q) {
            $q->orderBy('order')
              ->orderBy('id')
              ->with(['manualBooks' => function ($mb) {
                  $mb->orderBy('order')
                     ->orderBy('id')
                     ->with(['user', 'editor', 'logs.user']);
              }]);
        }])->get();

        $systems = [];
        $modulesBySystem = [];

        foreach ($categories as $cat) {
            $modList = [];

            foreach ($cat->menuSystems as $ms) {
                // Bab / Sub Menu langsung di bawah modul
                $chapters = [];
                foreach ($ms->manualBooks as $idx => $book) {
                    $chapters[] = $this->formatChapterItem($book, $idx);
                }

                $cleanName = trim(preg_replace('/^\d+[\.\s\-]+/', '', $ms->name));
                $cleanName = $cleanName ?: $ms->name;

                $totalChapters = count($chapters);
                $publishedCount = count(array_filter($chapters, function ($c) {
                    return $c['status'] === 'published';
                }));

                $statusLabel = 'Belum Ada Sub Menu';
                $statusClass = 'text-slate-400 dark:text-slate-500';
                if ($totalChapters > 0) {
                    if ($publishedCount === $totalChapters) {
                        $statusLabel = $totalChapters . ' Sub Menu • Terisi Lengkap ✓';
                        $statusClass = 'text-emerald-600 dark:text-emerald-400';
                    } else {
                        $statusLabel = $totalChapters . ' Sub Menu (' . $publishedCount . ' Terbit)';
                        $statusClass = 'text-blue-600 dark:text-blue-400';
                    }
                }

                $orderPrefix = $ms->order ? str_pad($ms->order, 2, '0', STR_PAD_LEFT) . '. ' : '';
                $defaultTitle = $orderPrefix ? $orderPrefix . $cleanName : $cleanName;

                $modList[] = [
                    'id' => (string) $ms->id,
                    'menu_system_id' => $ms->id,
                    'system_id' => (string) $cat->id,
                    'system_name' => $cat->name,
                    'title' => $defaultTitle,
                    'raw_name' => $cleanName,
                    'order' => $ms->order,
                    'description' => $ms->description ?: '',
                    'statusLabel' => $statusLabel,
                    'statusClass' => $statusClass,
                    'subMenuCount' => $totalChapters,
                    'chapterCount' => $totalChapters,
                    'publishedCount' => $publishedCount,
                    'breadcrumb' => strtoupper($cat->name) . ' • ' . strtoupper($cleanName) . ' • BUKU PANDUAN PENGGUNAAN',
                    'sub_menus' => $chapters,
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

        $normalizedContent = $book->content ?: '';
        if ($normalizedContent) {
            $normalizedContent = preg_replace('/src=(["\'])uploads\/editor\//i', 'src=$1/uploads/editor/', $normalizedContent);
        }

        $creatorName = $book->user ? $book->user->name : 'Superadministrator';
        $editorName = $book->editor ? $book->editor->name : $creatorName;

        // Ambil riwayat log aktivitas jika tersedia
        $logs = [];
        if ($book->relationLoaded('logs') && $book->logs) {
            foreach ($book->logs as $log) {
                $logs[] = [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'user_name' => $log->user ? $log->user->name : 'Sistem',
                    'created_at' => $log->created_at ? $log->created_at->format('d M Y, H:i') : '-',
                    'created_at_relative' => $log->created_at ? $log->created_at->diffForHumans() : '-',
                ];
            }
        }

        return [
            'id' => (string) $book->id,
            'order' => $orderNum,
            'title' => $book->title,
            'description' => $book->description ?: '',
            'content' => $normalizedContent,
            'status' => $book->status ?: 'draft',
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'created_by_name' => $creatorName,
            'created_at' => $book->created_at ? $book->created_at->format('d M Y H:i') : '-',
            'created_at_formatted' => $book->created_at ? $book->created_at->format('d M Y, H:i') : '-',
            'created_at_relative' => $book->created_at ? $book->created_at->diffForHumans() : '-',
            'updated_by_name' => $editorName,
            'updated_at' => $book->updated_at ? $book->updated_at->format('d M Y H:i') : '-',
            'updated_at_formatted' => $book->updated_at ? $book->updated_at->format('d M Y, H:i') : '-',
            'updated_at_relative' => $book->updated_at ? $book->updated_at->diffForHumans() : '-',
            'logs' => $logs,
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
        $actorName = Auth::user()->name ?? 'Superadministrator';

        $targetChapterId = $request->sub_menu_id ?: $request->chapter_id;
        $manualBook = null;
        if (!empty($targetChapterId) && is_numeric($targetChapterId)) {
            $manualBook = ManualBook::whereHas('menuSystems', function ($q) use ($menuSystem) {
                $q->where('menu_systems.id', $menuSystem->id);
            })->find($targetChapterId);
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

        $content = $request->content;
        if ($content) {
            $content = preg_replace('/src=(["\'])uploads\/editor\//i', 'src=$1/uploads/editor/', $content);
        }

        if ($manualBook) {
            $oldStatus = $manualBook->status;

            // Catat pengedit ke updated_by tanpa menimpa pembuat awal (user_id)
            $manualBook->update([
                'updated_by' => $userId,
                'title' => trim($request->title),
                'order' => $order,
                'description' => $request->description,
                'content' => $content,
                'status' => $request->status,
            ]);

            // Catat log jika status berubah
            if ($oldStatus !== $request->status) {
                $statusBefore = $oldStatus === 'published' ? 'Terbit' : 'Draft';
                $statusAfter = $request->status === 'published' ? 'Terbit' : 'Draft';
                ArticleAndManualBooksLog::record(
                    $manualBook,
                    'STATUS_CHANGED',
                    "Status sub menu diubah dari {$statusBefore} menjadi {$statusAfter} oleh {$actorName}.",
                    $userId
                );
            }

            // Catat log update perubahan materi
            ArticleAndManualBooksLog::record(
                $manualBook,
                'UPDATED',
                "Perubahan isi materi panduan dan informasi sub menu disimpan oleh {$actorName}.",
                $userId
            );

        } else {
            // Pembuatan sub menu baru
            $manualBook = ManualBook::create([
                'user_id' => $userId,
                'updated_by' => $userId,
                'title' => trim($request->title),
                'order' => $order,
                'description' => $request->description,
                'content' => $content,
                'status' => $request->status,
            ]);
            $menuSystem->manualBooks()->attach($manualBook->id);

            $statusText = $request->status === 'published' ? 'Terbit' : 'Draft';
            ArticleAndManualBooksLog::record(
                $manualBook,
                'CREATED',
                "Sub menu panduan \"{$manualBook->title}\" berhasil dibuat ({$statusText}) oleh {$actorName}.",
                $userId
            );
        }

        // Muat ulang relasi user, editor, logs untuk respon JSON
        $manualBook->load(['user', 'editor', 'logs.user']);
        $chapterData = $this->formatChapterItem($manualBook, $order - 1);

        $message = $request->status === 'published'
            ? 'Sub Menu "' . $manualBook->title . '" berhasil disimpan dan dipublikasikan!'
            : 'Draft Sub Menu "' . $manualBook->title . '" berhasil disimpan!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'sub_menu' => $chapterData,
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
            'message' => 'Sub Menu "' . $title . '" berhasil dihapus!',
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
            $html = $parsed['html'];
            if ($html) {
                $html = preg_replace('/src=(["\'])uploads\/editor\//i', 'src=$1/uploads/editor/', $html);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'File Word "' . $rawName . '" berhasil dibaca!',
                'title' => $title,
                'order' => $order,
                'content' => $html,
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
        $cleanName = trim(preg_replace('/^\d+[\.\s\-]+/', '', $menuSystem->name));
        $cleanName = $cleanName ?: $menuSystem->name;

        $orderPrefix = str_pad($order, 2, '0', STR_PAD_LEFT) . '. ';
        $fullTitle = $orderPrefix . $cleanName;
        $breadcrumb = strtoupper($kategori ? $kategori->name : 'SISTEM') . ' • ' . strtoupper($cleanName) . ' • BUKU PANDUAN PENGGUNAAN';

        return response()->json([
            'success' => true,
            'message' => 'Modul baru "' . $menuSystem->name . '" berhasil ditambahkan!',
            'module' => [
                'id' => (string) $menuSystem->id,
                'menu_system_id' => $menuSystem->id,
                'system_id' => (string) $menuSystem->kategori_id,
                'system_name' => $kategori ? $kategori->name : '',
                'title' => $fullTitle,
                'raw_name' => $cleanName,
                'order' => $order,
                'statusLabel' => 'Belum Ada Sub Menu',
                'statusClass' => 'text-slate-400 dark:text-slate-500',
                'subMenuCount' => 0,
                'chapterCount' => 0,
                'publishedCount' => 0,
                'breadcrumb' => $breadcrumb,
                'description' => 'Panduan langkah demi langkah untuk ' . $menuSystem->name,
                'sub_menus' => [],
                'chapters' => [],
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

    /**
     * Memperbarui urutan sub menu panduan secara batch
     */
    public function reorderSubMenus(Request $request)
    {
        $request->validate([
            'menu_system_id' => 'required|exists:menu_systems,id',
            'orders' => 'required|array',
        ]);

        $menuSystem = MenuSystem::findOrFail($request->menu_system_id);

        \DB::transaction(function () use ($request) {
            foreach ($request->orders as $index => $item) {
                if (is_array($item)) {
                    $id = $item['id'] ?? null;
                    $order = isset($item['order']) ? (int) $item['order'] : ($index + 1);
                } else {
                    $id = $item;
                    $order = $index + 1;
                }

                if ($id) {
                    ManualBook::where('id', $id)->update(['order' => $order]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Urutan sub menu panduan berhasil diperbarui!',
        ]);
    }
}
