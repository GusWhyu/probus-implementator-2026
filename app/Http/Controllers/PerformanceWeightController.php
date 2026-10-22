<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceWeight;

class PerformanceWeightController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorize('aspv');
            return $next($request);
        });
    }

    /**
     * Menampilkan daftar master data bobot penilaian kinerja.
     */
    public function index()
    {
        $weights = PerformanceWeight::orderBy('urutan', 'asc')->get();

        // Pastikan 3 komponen default ada jika tabel kosong
        if ($weights->isEmpty()) {
            PerformanceWeight::getWeightsMap(); // Pemicu inisialisasi default
            $weights = PerformanceWeight::orderBy('urutan', 'asc')->get();
        }

        $totalBobot = $weights->sum('bobot');

        return view('settings.bobot_penilaian', compact('weights', 'totalBobot'));
    }

    /**
     * Memperbarui bobot seluruh komponen penilaian (Batch Update).
     */
    public function update(Request $request)
    {
        $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|min:0|max:100',
        ], [
            'weights.*.required' => 'Nilai bobot wajib diisi.',
            'weights.*.numeric' => 'Nilai bobot harus berupa angka.',
            'weights.*.min' => 'Nilai bobot minimal 0%.',
            'weights.*.max' => 'Nilai bobot maksimal 100%.',
        ]);

        $submittedWeights = $request->input('weights', []);
        $total = 0;
        foreach ($submittedWeights as $val) {
            $total += (float)$val;
        }

        // Validasi: Total seluruh bobot harus tepat 100%
        if (abs($total - 100.0) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Total akumulasi bobot harus tepat 100%! Saat ini total bobot yang Anda masukkan adalah {$total}%. Silakan sesuaikan kembali nilai persentasenya.");
        }

        // Update masing-masing komponen
        foreach ($submittedWeights as $key => $val) {
            PerformanceWeight::where('key', $key)->update([
                'bobot' => round((float)$val, 2),
            ]);
        }

        return redirect()->route('bobot-penilaian.index')
            ->with('success', 'Master data bobot penilaian berhasil diperbarui! Perhitungan pada formulir penilaian dan rapor kinerja staff akan otomatis menggunakan komposisi bobot baru ini.');
    }

    /**
     * Mengembalikan komposisi bobot ke standar default (30% Tiket, 40% Soft Skill, 30% Kepuasan Klien).
     */
    public function resetDefault()
    {
        $defaults = [
            'ticket' => 30.00,
            'soft_skill' => 40.00,
            'client_review' => 30.00,
        ];

        foreach ($defaults as $key => $val) {
            PerformanceWeight::where('key', $key)->update(['bobot' => $val]);
        }

        return redirect()->route('bobot-penilaian.index')
            ->with('success', 'Komposisi bobot berhasil dikembalikan ke standar default (30% Tiket, 40% Soft Skill, 30% Kepuasan Klien).');
    }
}
