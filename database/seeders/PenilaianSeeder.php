<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penilaian;
use Carbon\Carbon;

class PenilaianSeeder extends Seeder
{
    public function run()
    {
        $users = [3, 4, 6, 9]; // Specific CS IDs
        $reviewer_id = 1; // Administrator

        // Generate exactly the data for the mockup (Sept 2024 - Feb 2025)
        $periods = [
            ['month' => 9, 'year' => 2024, 't' => 72, 's' => 83, 'k' => 78, 'akhir' => 77.8],
            ['month' => 10, 'year' => 2024, 't' => 74, 's' => 85, 'k' => 80, 'akhir' => 79.8],
            ['month' => 11, 'year' => 2024, 't' => 76, 's' => 86, 'k' => 82, 'akhir' => 81.5],
            ['month' => 12, 'year' => 2024, 't' => 78, 's' => 88, 'k' => 84, 'akhir' => 83.5],
            ['month' => 1, 'year' => 2025, 't' => 80, 's' => 89, 'k' => 85, 'akhir' => 84.8],
            ['month' => 2, 'year' => 2025, 't' => 82, 's' => 91, 'k' => 87, 'akhir' => 86.7],
        ];

        foreach ($users as $user_id) {
            foreach ($periods as $p) {
                // Add slight variations for other users so they aren't all identical
                $variance = ($user_id == 3) ? 0 : rand(-5, 5); 
                
                $n_t = min(100, max(0, $p['t'] + $variance));
                $n_s = min(100, max(0, $p['s'] + $variance));
                $n_k = min(100, max(0, $p['k'] + $variance));
                $n_akhir = ($n_t * 0.3) + ($n_s * 0.4) + ($n_k * 0.3);

                Penilaian::create([
                    'user_id' => $user_id,
                    'reviewer_id' => $reviewer_id,
                    'periode' => Carbon::createFromDate($p['year'], $p['month'], 1)->format('Y-m-d'),
                    'produktivitas' => 4,
                    'kualitas_solving' => 4,
                    'kecepatan_respon' => 5,
                    'sikap_cs' => 4,
                    'kepuasan_pelanggan' => 4,
                    'nilai_tiket' => $n_t,
                    'kualitas_cs' => $n_s,
                    'kepuasan_klien' => $n_k,
                    'nilai_akhir' => $n_akhir,
                    'catatan' => "Menunjukkan peningkatan produktivitas dan konsistensi.\nKualitas penyelesaian masalah sudah baik, namun perlu ditingkatkan pada:\n- Dokumentasi solusi\n- Komunikasi dan follow-up",
                    'saran' => "Tingkatkan kualitas dokumentasi solusi.\nPerkuat komunikasi dan follow-up ke klien."
                ]);
            }
        }
    }
}
