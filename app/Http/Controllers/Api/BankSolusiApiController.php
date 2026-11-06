<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\ManualBook;
use App\Models\Kategori;
use App\Models\MenuSystem;

class BankSolusiApiController extends Controller
{
    /**
     * Endpoint Publik untuk Golang Portal: Daftar Artikel Solusi Publik
     * GET /api/bank-solusi
     */
    public function index(Request $request)
    {
        $query = Article::where('visibility', 'publik')
            ->where('status', 'published')
            ->with(['user', 'menuSystems.kategori'])
            ->orderBy('created_at', 'desc');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter sistem (kategori)
        if ($request->filled('system_id')) {
            $sysId = $request->system_id;
            $query->whereHas('menuSystems', function ($q) use ($sysId) {
                $q->where('kategori_id', $sysId);
            });
        }

        // Filter modul
        if ($request->filled('module_id')) {
            $modId = $request->module_id;
            $query->whereHas('menuSystems', function ($q) use ($modId) {
                $q->where('menu_systems.id', $modId);
            });
        }

        $perPage = $request->input('per_page', 15);
        $articles = $query->paginate($perPage);

        $formatted = $articles->getCollection()->map(function ($art) {
            $ms = $art->menuSystems->first();
            return [
                'id' => $art->id,
                'title' => $art->title,
                'slug' => Str::slug($art->title),
                'summary' => $art->summary,
                'content' => $art->content,
                'system' => ($ms && $ms->kategori) ? [
                    'id' => $ms->kategori->id,
                    'name' => $ms->kategori->name,
                ] : null,
                'module' => $ms ? [
                    'id' => $ms->id,
                    'name' => $ms->name,
                ] : null,
                'author' => $art->user ? $art->user->name : 'Probus Implementator',
                'created_at' => $art->created_at ? $art->created_at->toIso8601String() : null,
                'updated_at' => $art->updated_at ? $art->updated_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar artikel solusi publik berhasil diambil.',
            'data' => $formatted,
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Endpoint Publik: Detail Artikel Solusi Publik
     * GET /api/bank-solusi/{id}
     */
    public function show($id)
    {
        $art = Article::where('visibility', 'publik')
            ->where('status', 'published')
            ->with(['user', 'menuSystems.kategori'])
            ->find($id);

        if (!$art) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel solusi tidak ditemukan atau berstatus internal.',
            ], 404);
        }

        $ms = $art->menuSystems->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $art->id,
                'title' => $art->title,
                'slug' => Str::slug($art->title),
                'summary' => $art->summary,
                'content' => $art->content,
                'system' => ($ms && $ms->kategori) ? [
                    'id' => $ms->kategori->id,
                    'name' => $ms->kategori->name,
                ] : null,
                'module' => $ms ? [
                    'id' => $ms->id,
                    'name' => $ms->name,
                ] : null,
                'author' => $art->user ? $art->user->name : 'Probus Implementator',
                'created_at' => $art->created_at ? $art->created_at->toIso8601String() : null,
                'updated_at' => $art->updated_at ? $art->updated_at->toIso8601String() : null,
            ],
        ]);
    }

    /**
     * Endpoint Publik: Daftar Kategori & Modul Sistem Probus
     * GET /api/bank-solusi-systems
     */
    public function systems()
    {
        $categories = Kategori::whereHas('menuSystems')
            ->with(['menuSystems' => function ($q) {
                $q->orderBy('order');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Endpoint Publik: Daftar Manual Book Publik
     * GET /api/manual-books
     */
    public function manualBooks(Request $request)
    {
        $query = ManualBook::where('visibility', 'publik')
            ->where('status', 'published')
            ->with(['user', 'menuSystems.kategori'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('module_id')) {
            $modId = $request->module_id;
            $query->whereHas('menuSystems', function ($q) use ($modId) {
                $q->where('menu_systems.id', $modId);
            });
        }

        $manuals = $query->get()->map(function ($mb) {
            $ms = $mb->menuSystems->first();
            return [
                'id' => $mb->id,
                'title' => $mb->title,
                'summary' => $mb->summary,
                'content' => $mb->content,
                'system' => ($ms && $ms->kategori) ? [
                    'id' => $ms->kategori->id,
                    'name' => $ms->kategori->name,
                ] : null,
                'module' => $ms ? [
                    'id' => $ms->id,
                    'name' => $ms->name,
                ] : null,
                'created_at' => $mb->created_at ? $mb->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $manuals,
        ]);
    }
}
