<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\ManualBook;
use App\Models\Kategori;
use App\Models\MenuSystem;
use Carbon\Carbon;

class KontenSayaController extends Controller
{
    /**
     * Menampilkan halaman Konten & Publikasi Saya (khusus per-user yang login)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        // 1. Ambil seluruh artikel Bank Solusi yang dibuat oleh user ini
        $articles = Article::with(['menuSystems.kategori'])
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

        // 2. Ambil seluruh bab Manual Book yang dibuat oleh user ini
        $manualBooks = ManualBook::with(['menuSystems.kategori'])
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();

        // 3. Normalisasi data konten gabungan (Unified Content List)
        $contentList = [];

        foreach ($articles as $art) {
            $firstMenu = $art->menuSystems->first();
            $systemName = ($firstMenu && $firstMenu->kategori) ? $firstMenu->kategori->name : 'Sistem Probus';
            $systemId = ($firstMenu && $firstMenu->kategori) ? (string) $firstMenu->kategori->id : 'general';
            $moduleName = $firstMenu ? $firstMenu->name : 'Umum';
            $moduleId = $firstMenu ? (string) $firstMenu->id : '';

            // Hitung estimasi langkah atau topik ringkas
            $stepsCount = preg_match_all('/<(h[2-4]|li)[\s>]/i', $art->content);
            $subtitle = $art->summary ? Str::limit(strip_tags($art->summary), 95) : null;
            if (!$subtitle) {
                $subtitle = 'Kategori: ' . $systemName . ($stepsCount > 0 ? " • {$stepsCount} langkah instruksi" : ' • Solusi Pemecahan Masalah');
            }

            $contentList[] = [
                'id' => (string) $art->id,
                'content_type' => 'solusi', // 'solusi' | 'manual_book'
                'type_label' => 'Solusi',
                'title' => $art->title,
                'subtitle' => $subtitle,
                'system_id' => $systemId,
                'system_name' => $systemName,
                'module_id' => $moduleId,
                'module_name' => $moduleName,
                'visibility' => strtolower($art->visibility ?: 'publik'), // 'publik' | 'internal'
                'status' => strtolower($art->status ?: 'draft'), // 'published' | 'draft'
                'status_label' => ($art->status === 'published') ? 'Terbit' : 'Draft',
                'raw_content' => $art->content,
                'updated_at' => $art->updated_at ? $art->updated_at->timestamp : 0,
                'updated_at_formatted' => $this->formatHumanDate($art->updated_at),
                'edit_url' => route('bank-solusi.edit', $art->id),
                'view_url' => route('bank-solusi.show', $art->id),
                'delete_url' => route('bank-solusi.destroy', $art->id),
            ];
        }

        foreach ($manualBooks as $mb) {
            $firstMenu = $mb->menuSystems->first();
            $systemName = ($firstMenu && $firstMenu->kategori) ? $firstMenu->kategori->name : 'Probus System';
            $systemId = ($firstMenu && $firstMenu->kategori) ? (string) $firstMenu->kategori->id : 'general';
            $moduleName = $firstMenu ? $firstMenu->name : 'Modul';
            $moduleId = $firstMenu ? (string) $firstMenu->id : '';

            $cleanTitle = trim(preg_replace('/^\d+[\.\s\-]+/', '', $mb->title));
            $cleanTitle = $cleanTitle ?: $mb->title;
            $orderNum = $mb->order ?: 1;

            $subtitle = $mb->description ? Str::limit(strip_tags($mb->description), 95) : null;
            if (!$subtitle) {
                $subtitle = "Dokumentasi SOP Operasional • Bab #{$orderNum}";
            }

            $contentList[] = [
                'id' => (string) $mb->id,
                'content_type' => 'manual_book',
                'type_label' => 'Manual Book',
                'title' => $mb->title,
                'subtitle' => $subtitle,
                'system_id' => $systemId,
                'system_name' => $systemName,
                'module_id' => $moduleId,
                'module_name' => $moduleName,
                'visibility' => 'publik', // Manual book tersinkron ke portal publik
                'status' => strtolower($mb->status ?: 'draft'),
                'status_label' => ($mb->status === 'published') ? 'Terbit' : 'Draft',
                'raw_content' => $mb->content,
                'updated_at' => $mb->updated_at ? $mb->updated_at->timestamp : 0,
                'updated_at_formatted' => $this->formatHumanDate($mb->updated_at),
                'edit_url' => route('manualbook.manage', [
                    'system' => $systemId,
                    'module' => $moduleId,
                    'chapter' => $mb->id,
                ]),
                'view_url' => route('manualbook', ['system' => $systemId, 'search' => $cleanTitle]),
                'delete_url' => route('manualbook.chapter.delete', $mb->id),
            ];
        }

        // Urutkan default: terbaru
        usort($contentList, function ($a, $b) {
            return $b['updated_at'] <=> $a['updated_at'];
        });

        // 4. Hitung Statistik Ringkas untuk 3 Kartu Atas
        $totalContent = count($contentList);
        $totalPublished = count(array_filter($contentList, fn($c) => $c['status'] === 'published'));
        $totalDraft = count(array_filter($contentList, fn($c) => $c['status'] === 'draft'));

        $articleCount = $articles->count();
        $articlePublished = $articles->where('status', 'published')->count();
        $articleDraft = $articles->where('status', 'draft')->count();
        $articlePublic = $articles->where('visibility', 'publik')->count();
        $articleInternal = $articles->where('visibility', 'internal')->count();

        $manualBookCount = $manualBooks->count();
        $manualBookPublished = $manualBooks->where('status', 'published')->count();
        $manualBookDraft = $manualBooks->where('status', 'draft')->count();

        // 5. Daftar Sistem untuk filter dropdown
        $systems = Kategori::orderBy('name', 'asc')->get(['id', 'name'])->map(function ($k) {
            return [
                'id' => (string) $k->id,
                'name' => $k->name,
            ];
        })->toArray();

        return view('konten_saya.index', compact(
            'contentList',
            'totalContent',
            'totalPublished',
            'totalDraft',
            'articleCount',
            'articlePublished',
            'articleDraft',
            'articlePublic',
            'articleInternal',
            'manualBookCount',
            'manualBookPublished',
            'manualBookDraft',
            'systems'
        ));
    }

    /**
     * Format tanggal ramah pengguna (Hari ini, Kemarin, atau format d M Y)
     */
    private function formatHumanDate($date): string
    {
        if (!$date) return '-';

        $c = Carbon::parse($date);
        if ($c->isToday()) {
            return 'Hari Ini, ' . $c->format('H:i');
        }
        if ($c->isYesterday()) {
            return 'Kemarin, ' . $c->format('H:i');
        }
        return $c->format('d M Y');
    }
}
