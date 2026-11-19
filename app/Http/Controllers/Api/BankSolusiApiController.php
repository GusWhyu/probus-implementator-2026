<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\ManualBook;
use App\Models\Kategori;
use App\Models\MenuSystem;
use App\Models\WhatsAppSupport;

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
                'summary' => $art->summary ? trim(preg_replace('/[\s\x{00A0}\x{200B}]+/u', ' ', html_entity_decode($art->summary, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) : Str::limit(trim(preg_replace('/[\s\x{00A0}\x{200B}]+/u', ' ', html_entity_decode(strip_tags($art->content), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), 140),
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

        $latestLog = $art->logs()->with('user')->whereIn('action', ['UPDATED', 'STATUS_CHANGED', 'VISIBILITY_CHANGED'])->first();
        $editorName = ($latestLog && $latestLog->user) ? $latestLog->user->name : ($art->user ? $art->user->name : 'Superadministrator');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $art->id,
                'title' => $art->title,
                'slug' => Str::slug($art->title),
                'summary' => $art->summary ? trim(preg_replace('/[\s\x{00A0}\x{200B}]+/u', ' ', html_entity_decode($art->summary, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) : Str::limit(trim(preg_replace('/[\s\x{00A0}\x{200B}]+/u', ' ', html_entity_decode(strip_tags($art->content), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), 140),
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
                'created_by' => $art->user ? $art->user->name : 'Superadministrator',
                'created_at' => $art->created_at ? $art->created_at->toIso8601String() : null,
                'created_at_formatted' => $art->created_at ? $art->created_at->format('d M Y, H:i') : '-',
                'updated_by' => $editorName,
                'updated_at' => $art->updated_at ? $art->updated_at->toIso8601String() : null,
                'updated_at_formatted' => $art->updated_at ? $art->updated_at->format('d M Y, H:i') : '-',
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

    /**
     * Endpoint Publik: Daftar Kontak WhatsApp Support Aktif
     * GET /api/whatsapp-supports
     */
    public function whatsappSupports()
    {
        $contacts = WhatsAppSupport::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'badge' => $c->badge,
                    'description' => $c->description,
                    'phone_number' => $c->phone_number,
                    'clean_phone_number' => $c->clean_phone_number,
                    'wa_url' => $c->wa_url,
                    'icon' => $c->icon,
                    'badge_color' => $c->badge_color,
                    'order' => $c->order,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Daftar kontak WhatsApp support berhasil diambil.',
            'total' => $contacts->count(),
            'data' => $contacts,
        ]);
    }
}
