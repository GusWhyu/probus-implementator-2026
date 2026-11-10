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
     * Tampilan UI Manual Book & Tata Cara Penggunaan Sistem (Struktur Opsi A: Sistem -> Modul -> Daftar Bab)
     */
    public function index(Request $request)
    {
        // 1. Ambil kategori yang memiliki menuSystems beserta semua bab manualBooks
        $categories = Kategori::with(['menuSystems' => function ($q) {
            $q->orderBy('order')->with(['manualBooks' => function ($mb) {
                $mb->orderBy('order')->orderBy('id');
            }]);
        }])->get();

        $systems = [];
        $modulesBySystem = [];

        foreach ($categories as $cat) {
            $modList = [];

            foreach ($cat->menuSystems as $ms) {
                $chapters = [];
                foreach ($ms->manualBooks as $idx => $book) {
                    $orderNum = $book->order ? (int) $book->order : ($idx + 1);
                    $hasPdf = !empty($book->pdf_path);
                    $hasContent = !empty(trim(strip_tags($book->content, '<img>'))) || $hasPdf;
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

                    $chapters[] = [
                        'id' => (string) $book->id,
                        'order' => $orderNum,
                        'title' => $book->title,
                        'description' => $book->description ? $book->description : '',
                        'content' => $book->content ? $book->content : '',
                        'status' => $book->status ? $book->status : 'draft',
                        'pdf_path' => $book->pdf_path,
                        'pdf_url' => $book->pdf_url,
                        'pdf_name' => $book->pdf_name ? $book->pdf_name : '',
                        'pdf_size' => $book->pdf_size ? (int) $book->pdf_size : null,
                        'pdf_size_formatted' => $book->pdf_size_formatted ? $book->pdf_size_formatted : '',
                        'statusLabel' => $statusLabel,
                        'statusClass' => $statusClass,
                        'updated_at' => $book->updated_at ? $book->updated_at->format('d M Y H:i') : '-',
                    ];
                }

                $totalChapters = count($chapters);
                $publishedCount = 0;
                foreach ($chapters as $c) {
                    if ($c['status'] === 'published') {
                        $publishedCount++;
                    }
                }

                $statusLabel = 'Belum Ada Bab';
                $statusClass = 'text-slate-400 dark:text-slate-500';
                if ($totalChapters > 0) {
                    if ($publishedCount === $totalChapters) {
                        $statusLabel = $totalChapters . ' Bab • Terisi Lengkap ✓';
                        $statusClass = 'text-emerald-600 dark:text-emerald-400';
                    } else {
                        $statusLabel = $totalChapters . ' Bab (' . $publishedCount . ' Terbit)';
                        $statusClass = 'text-blue-600 dark:text-blue-400';
                    }
                }

                $orderPrefix = $ms->order ? str_pad($ms->order, 2, '0', STR_PAD_LEFT) . '. ' : '';
                $defaultTitle = $orderPrefix ? $orderPrefix . $ms->name : $ms->name;

                $modList[] = [
                    'id' => (string) $ms->id,
                    'menu_system_id' => $ms->id,
                    'title' => $defaultTitle,
                    'raw_name' => $ms->name,
                    'order' => $ms->order,
                    'description' => $ms->description ? $ms->description : '',
                    'statusLabel' => $statusLabel,
                    'statusClass' => $statusClass,
                    'chapterCount' => $totalChapters,
                    'breadcrumb' => strtoupper($cat->name) . ' • ' . strtoupper($ms->name) . ' • BUKU PANDUAN PENGGUNAAN',
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

        return view('manualbook', [
            'systems' => $systems,
            'modulesBySystem' => $modulesBySystem,
            'categories' => $categories,
        ]);
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
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'pdf_path' => 'nullable|string',
            'pdf_name' => 'nullable|string',
            'pdf_size' => 'nullable|numeric',
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

        $pdfPath = $request->pdf_path ? $request->pdf_path : null;
        $pdfName = $request->pdf_name ? $request->pdf_name : null;
        $pdfSize = $request->pdf_size ? (int) $request->pdf_size : null;

        $saveData = [
            'user_id' => $userId,
            'title' => trim($request->title),
            'order' => $order,
            'description' => $request->description,
            'content' => $request->content !== null ? $request->content : '',
            'status' => $request->status,
            'pdf_path' => $pdfPath,
            'pdf_name' => $pdfName,
            'pdf_size' => $pdfSize,
        ];

        if ($manualBook) {
            // Jika PDF lama diganti atau dihapus, bersihkan berkas lama dari disk
            if (!empty($manualBook->pdf_path) && $manualBook->pdf_path !== $pdfPath) {
                $oldPdf = public_path($manualBook->pdf_path);
                if (file_exists($oldPdf)) {
                    @unlink($oldPdf);
                }
            }
            $manualBook->update($saveData);
        } else {
            $manualBook = ManualBook::create($saveData);
            $menuSystem->manualBooks()->attach($manualBook->id);
        }

        $hasContent = !empty(trim(strip_tags($manualBook->content, '<img>'))) || !empty($manualBook->pdf_path);
        $statusLabel = $request->status === 'published' ? 'Panduan Terisi ✓' : 'Sedang Diedit ✎';
        $statusClass = $request->status === 'published' ? 'text-emerald-600 dark:text-emerald-400' : 'text-blue-600 dark:text-blue-400';

        $chapterData = [
            'id' => (string) $manualBook->id,
            'order' => (int) $manualBook->order,
            'title' => $manualBook->title,
            'description' => $manualBook->description ? $manualBook->description : '',
            'content' => $manualBook->content ? $manualBook->content : '',
            'status' => $manualBook->status,
            'pdf_path' => $manualBook->pdf_path,
            'pdf_url' => $manualBook->pdf_url,
            'pdf_name' => $manualBook->pdf_name ? $manualBook->pdf_name : '',
            'pdf_size' => $manualBook->pdf_size ? (int) $manualBook->pdf_size : null,
            'pdf_size_formatted' => $manualBook->pdf_size_formatted ? $manualBook->pdf_size_formatted : '',
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

        // Jika memiliki berkas PDF fisik, hapus dari penyimpanan server
        if (!empty($manualBook->pdf_path)) {
            $pdfFullPath = public_path($manualBook->pdf_path);
            if (file_exists($pdfFullPath)) {
                @unlink($pdfFullPath);
            }
        }

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
     * Unggah berkas dokumen PDF untuk bab panduan manual book
     */
    public function uploadPdf(Request $request)
    {
        $request->validate([
            'menu_system_id' => 'required|exists:menu_systems,id',
            'pdf_file' => 'required|file|mimes:pdf|max:51200', // maksimal 50 MB
        ]);

        $file = $request->file('pdf_file');
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $rawName = pathinfo($originalName, PATHINFO_FILENAME);

        // Deteksi urutan dan judul dari nama file (misal: "1. Panduan POS.pdf" -> order: 1, title: "1. Panduan POS")
        $order = null;
        $title = $rawName;
        if (preg_match('/^(\d+)[\.\s_-]*(.*)$/', $rawName, $m)) {
            $order = (int) $m[1];
            $cleanTitle = trim($m[2]);
            $title = $order . '. ' . ($cleanTitle ? $cleanTitle : $rawName);
        }

        if (mb_strlen($title) > 100) {
            $title = mb_substr($title, 0, 100);
        }

        // Simpan file ke public/uploads/pdf/manual_books/
        $targetDir = public_path('uploads/pdf/manual_books');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $safeName = 'manual_' . time() . '_' . substr(md5(microtime() . $originalName), 0, 8) . '.pdf';
        $file->move($targetDir, $safeName);

        $relativePath = 'uploads/pdf/manual_books/' . $safeName;
        $url = asset($relativePath);

        // Format ukuran file (KB / MB)
        $formattedSize = $fileSize >= 1048576 
            ? number_format($fileSize / 1048576, 2) . ' MB'
            : number_format($fileSize / 1024, 1) . ' KB';

        return response()->json([
            'success' => true,
            'message' => 'Berkas PDF "' . $originalName . '" berhasil diunggah!',
            'pdf_path' => $relativePath,
            'pdf_url' => $url,
            'pdf_name' => $originalName,
            'pdf_size' => $fileSize,
            'pdf_size_formatted' => $formattedSize,
            'detected_title' => $title,
            'detected_order' => $order,
        ]);
    }

    /**
     * Hapus berkas PDF dari bab tertentu
     */
    public function removePdf($id)
    {
        $manualBook = ManualBook::findOrFail($id);

        if (!empty($manualBook->pdf_path)) {
            $pdfFullPath = public_path($manualBook->pdf_path);
            if (file_exists($pdfFullPath)) {
                @unlink($pdfFullPath);
            }
        }

        $manualBook->update([
            'pdf_path' => null,
            'pdf_name' => null,
            'pdf_size' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berkas PDF pada bab ini berhasil dihapus!',
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
