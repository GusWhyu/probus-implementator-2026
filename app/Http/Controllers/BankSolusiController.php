<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\Kategori;
use App\Models\MenuSystem;
use App\Models\MediaUpload;

class BankSolusiController extends Controller
{
    /**
     * Menampilkan katalog Bank Solusi & Panduan Sistem
     */
    public function index(Request $request)
    {
        // 1. Ambil Kategori yang memiliki Menu Systems
        $categories = Kategori::whereHas('menuSystems')
            ->with(['menuSystems' => function ($q) {
                $q->orderBy('order')->withCount('articles');
            }])
            ->get();

        // Siapkan struktur sistem untuk filter tab
        $systems = [];
        $modulesBySystem = [];
        $totalAllArticles = 0;

        foreach ($categories as $cat) {
            $catArticleCount = 0;
            $modList = [];

            foreach ($cat->menuSystems as $ms) {
                $count = $ms->articles_count;
                $catArticleCount += $count;
                $modList[] = [
                    'id' => (string) $ms->id,
                    'name' => $ms->name,
                    'count' => $count,
                    'description' => $ms->description,
                ];
            }

            $totalAllArticles += $catArticleCount;

            // Tambahkan opsi "Semua Modul" di awal daftar modul sistem
            array_unshift($modList, [
                'id' => 'all-modules',
                'name' => 'Semua Modul',
                'count' => $catArticleCount,
                'description' => 'Seluruh modul dalam ' . $cat->name,
            ]);

            $systems[] = [
                'id' => (string) $cat->id,
                'name' => $cat->name,
                'description' => 'Modul dan solusi untuk ' . $cat->name,
                'count' => $catArticleCount,
            ];

            $modulesBySystem[(string) $cat->id] = $modList;
        }

        // Tambahkan sistem "Semua Sistem" (All)
        array_unshift($systems, [
            'id' => 'all',
            'name' => 'Semua Sistem',
            'description' => 'Seluruh panduan dari semua sistem Probus',
            'count' => $totalAllArticles,
        ]);

        // Daftar modul untuk "all"
        $allModulesFlat = [];
        foreach ($modulesBySystem as $mList) {
            foreach ($mList as $m) {
                if ($m['id'] !== 'all-modules' && !in_array($m['name'], array_column($allModulesFlat, 'name'))) {
                    $allModulesFlat[] = $m;
                }
            }
        }
        array_unshift($allModulesFlat, [
            'id' => 'all-modules',
            'name' => 'Semua Modul',
            'count' => $totalAllArticles,
            'description' => 'Seluruh modul dari semua sistem',
        ]);
        $modulesBySystem['all'] = $allModulesFlat;

        // 2. Query data artikel dari database
        $articleQuery = Article::with(['user', 'menuSystems.kategori'])
            ->orderBy('created_at', 'desc');

        // Filter status: tampilkan yang published secara default
        if ($request->filled('status') && in_array($request->status, ['draft', 'published', 'all'])) {
            if ($request->status !== 'all') {
                $articleQuery->where('status', $request->status);
            }
        } else {
            $articleQuery->where('status', 'published');
        }

        // Filter visibilitas jika ada
        if ($request->filled('visibility') && in_array($request->visibility, ['publik', 'internal'])) {
            $articleQuery->where('visibility', $request->visibility);
        }

        // Filter pencarian teks
        if ($request->filled('search')) {
            $search = $request->search;
            $articleQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan modul atau kategori
        if ($request->filled('module_id') && $request->module_id !== 'all' && $request->module_id !== 'all-modules') {
            $moduleId = $request->module_id;
            $articleQuery->whereHas('menuSystems', function ($q) use ($moduleId) {
                $q->where('menu_systems.id', $moduleId);
            });
        } elseif ($request->filled('system_id') && $request->system_id !== 'all') {
            $sysId = $request->system_id;
            $articleQuery->whereHas('menuSystems', function ($q) use ($sysId) {
                $q->where('kategori_id', $sysId);
            });
        }

        $rawArticles = $articleQuery->get();

        // 3. Format artikel agar sesuai dengan tampilan kartu blade
        $solutions = [];
        foreach ($rawArticles as $art) {
            $firstMs = $art->menuSystems->first();
            $systemName = ($firstMs && $firstMs->kategori) ? $firstMs->kategori->name : 'Sistem Probus';
            $systemId = ($firstMs && $firstMs->kategori) ? (string) $firstMs->kategori->id : 'all';
            $moduleName = $firstMs ? $firstMs->name : 'Umum';
            $moduleId = $firstMs ? (string) $firstMs->id : 'all-modules';

            // Hitung estimasi waktu baca (rata-rata 150 kata/menit)
            $wordCount = str_word_count(strip_tags($art->content));
            $readTime = max(1, (int) round($wordCount / 150)) . ' min baca';

            $solutions[] = [
                'id' => $art->id,
                'system_id' => $systemId,
                'system_name' => $systemName,
                'module_id' => $moduleId,
                'module_name' => $moduleName,
                'audience' => $art->visibility === 'publik' ? 'klien' : 'internal',
                'audience_label' => $art->visibility === 'publik' ? 'BLOG KLIEN' : 'INTERNAL',
                'title' => $art->title,
                'excerpt' => $art->summary ? $art->summary : Str::limit(strip_tags($art->content), 120),
                'content' => $art->content,
                'author' => $art->user ? $art->user->name : 'Implementator',
                'date' => $art->created_at ? $art->created_at->format('d M Y') : date('d M Y'),
                'read_time' => $readTime,
                'status' => $art->status,
            ];
        }

        return view('bank_solusi.index', [
            'systems' => $systems,
            'modules' => $modulesBySystem,
            'solutions' => $solutions,
            'categories' => $categories,
        ]);
    }

    /**
     * Menampilkan formulir buat / edit artikel solusi
     */
    public function create(Request $request)
    {
        $categories = Kategori::whereHas('menuSystems')
            ->with(['menuSystems' => function ($q) {
                $q->orderBy('order');
            }])
            ->get();

        return view('bank_solusi.create', compact('categories'));
    }

    /**
     * Menyimpan artikel solusi baru ke tabel articles & menu_has_articles
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'visibility' => 'required|in:publik,internal',
            'status' => 'required|in:draft,published',
            'menu_system_id' => 'required|exists:menu_systems,id',
            'content' => 'required|string',
            'summary' => 'nullable|string',
        ]);

        $summary = $request->summary;
        if (empty($summary)) {
            $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags($request->content)));
            $summary = Str::limit($cleanText, 140);
        }

        $userId = Auth::id() ? Auth::id() : 1;

        $article = Article::create([
            'user_id' => $userId,
            'title' => $request->title,
            'visibility' => $request->visibility,
            'status' => $request->status,
            'summary' => $summary,
            'content' => $request->content,
        ]);

        // Hubungkan ke tabel pivot menu_has_articles
        $article->menuSystems()->sync([$request->menu_system_id]);

        $message = $request->status === 'published' 
            ? 'Artikel Solusi berhasil diterbitkan!' 
            : 'Draft Artikel Solusi berhasil disimpan!';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'article' => $article,
                'redirect_url' => route('bank-solusi.index'),
            ]);
        }

        return redirect()->route('bank-solusi.index')->with('success', $message);
    }

    /**
     * Menampilkan detail artikel solusi
     */
    public function show(Request $request, $id)
    {
        $article = Article::with(['user', 'menuSystems.kategori'])->findOrFail($id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $article,
            ]);
        }

        $firstMs = $article->menuSystems->first();
        $system = ($firstMs && $firstMs->kategori) ? $firstMs->kategori : null;
        $module = $firstMs ?: null;

        // Hitung estimasi waktu baca (rata-rata 150 kata/menit)
        $wordCount = str_word_count(strip_tags($article->content));
        $readTime = max(1, (int) round($wordCount / 150)) . ' min baca';

        // Artikel terkait dalam modul yang sama
        $relatedArticles = collect();
        if ($module) {
            $relatedArticles = Article::where('id', '!=', $article->id)
                ->where('status', 'published')
                ->whereHas('menuSystems', function ($q) use ($module) {
                    $q->where('menu_systems.id', $module->id);
                })
                ->take(4)
                ->get();
        }

        return view('bank_solusi.show', compact('article', 'system', 'module', 'readTime', 'relatedArticles'));
    }

    /**
     * Menampilkan formulir edit artikel solusi
     */
    public function edit($id)
    {
        $article = Article::with(['menuSystems.kategori'])->findOrFail($id);
        
        $categories = Kategori::whereHas('menuSystems')
            ->with(['menuSystems' => function ($q) {
                $q->orderBy('order');
            }])
            ->get();

        $currentModule = $article->menuSystems->first();
        $currentCategoryId = $currentModule && $currentModule->kategori_id 
            ? $currentModule->kategori_id 
            : ($categories->first()->id ?? null);
        $currentModuleId = $currentModule ? $currentModule->id : null;

        return view('bank_solusi.edit', compact('article', 'categories', 'currentCategoryId', 'currentModuleId'));
    }

    /**
     * Memperbarui artikel solusi di database
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:100',
            'visibility' => 'required|in:publik,internal',
            'status' => 'required|in:draft,published',
            'menu_system_id' => 'required|exists:menu_systems,id',
            'content' => 'required|string',
            'summary' => 'nullable|string',
        ]);

        $summary = $request->summary;
        if (empty($summary)) {
            $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags($request->content)));
            $summary = Str::limit($cleanText, 140);
        }

        $article->update([
            'title' => $request->title,
            'visibility' => $request->visibility,
            'status' => $request->status,
            'summary' => $summary,
            'content' => $request->content,
        ]);

        // Perbarui relasi pivot menu_has_articles
        $article->menuSystems()->sync([$request->menu_system_id]);

        $message = $request->status === 'published' 
            ? 'Artikel Solusi berhasil diperbarui dan diterbitkan!' 
            : 'Perubahan Draft Artikel Solusi berhasil disimpan!';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'article' => $article,
                'redirect_url' => route('bank-solusi.show', $article->id),
            ]);
        }

        return redirect()->route('bank-solusi.show', $article->id)->with('success', $message);
    }

    /**
     * Toggle cepat jangkauan visibilitas artikel (publik <-> internal)
     */
    public function toggleVisibility(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $newVisibility = $article->visibility === 'publik' ? 'internal' : 'publik';
        $article->update(['visibility' => $newVisibility]);

        $label = $newVisibility === 'publik' ? 'BLOG KLIEN' : 'INTERNAL';
        $msg = $newVisibility === 'publik'
            ? 'Artikel sekarang berstatus Publik (Blog Solusi Klien & Internal).'
            : 'Artikel sekarang berstatus Khusus Internal (Implementator).';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'visibility' => $newVisibility,
                'label' => $label,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Menghapus artikel solusi dari database
     */
    public function destroy(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $title = $article->title;

        // Lepaskan relasi pivot sebelum delete
        $article->menuSystems()->detach();
        $article->delete();

        $message = 'Artikel solusi "' . $title . '" berhasil dihapus!';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('bank-solusi.index'),
            ]);
        }

        return redirect()->route('bank-solusi.index')->with('success', $message);
    }

    /**
     * Handler upload gambar / screenshot untuk TinyMCE Editor
     * Menyimpan file ke public/uploads/editor/ dan mencatatnya ke tabel media_uploads
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'upload' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
        ]);

        $file = $request->file('image');
        if (!$file) {
            $file = $request->file('file');
        }
        if (!$file) {
            $file = $request->file('upload');
        }

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file gambar yang diunggah.',
            ], 422);
        }

        $ext = $file->getClientOriginalExtension();
        if (!$ext) {
            $ext = 'png';
        }

        $fileName = 'editor_' . time() . '_' . Str::random(8) . '.' . $ext;
        
        // 1. Simpan ke public/uploads/editor
        $targetDir = public_path('uploads/editor');
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $file->move($targetDir, $fileName);

        // 2. Salin juga ke storage/app/public/uploads/editor bila ada
        $storageDir = storage_path('app/public/uploads/editor');
        if (file_exists(storage_path('app/public'))) {
            if (!file_exists($storageDir)) {
                @mkdir($storageDir, 0777, true);
            }
            @copy($targetDir . DIRECTORY_SEPARATOR . $fileName, $storageDir . DIRECTORY_SEPARATOR . $fileName);
        }

        $url = asset('uploads/editor/' . $fileName);
        $userId = Auth::id() ? Auth::id() : 1;

        // 3. Simpan riwayat upload ke tabel media_uploads
        $mediaUpload = MediaUpload::create([
            'user_id' => $userId,
            'file_name' => $fileName,
            'file_path' => 'uploads/editor/' . $fileName,
            'original_name' => $file->getClientOriginalName(),
        ]);

        // Return format yang didukung oleh TinyMCE dan custom script
        return response()->json([
            'success' => true,
            'location' => $url,
            'url' => $url,
            'media_id' => $mediaUpload->id,
            'file_name' => $fileName,
        ]);
    }
}
