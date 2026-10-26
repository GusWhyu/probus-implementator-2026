<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\PerformanceReview;

class PeriodService
{
    /**
     * Parse input periode menjadi rentang tanggal dan metadata lengkap.
     * Mendukung:
     * - Bulanan: '2026-10' (1m)
     * - Triwulan / Kuartal: '2026-Q4' atau '2026-T4' (3m)
     * - Semester: '2026-S2' (6m)
     */
    public static function parse($period = null, $month = null, $year = null, $periodType = null): array
    {
        $now = Carbon::now();
        $currentYear = (int)$now->year;
        $currentMonth = (int)$now->month;

        $period = trim((string)$period);
        $periodType = trim((string)$periodType);

        // Jika ada period_type tetapi period tidak diisi / tidak cocok dengan typenya
        if ($periodType && empty($period)) {
            if ($periodType === '3m') {
                $q = (int)ceil($currentMonth / 3);
                $period = "{$currentYear}-Q{$q}";
            } elseif ($periodType === '6m') {
                $s = $currentMonth > 6 ? 2 : 1;
                $period = "{$currentYear}-S{$s}";
            } else {
                $period = sprintf('%04d-%02d', $currentYear, $currentMonth);
            }
        }

        // Jika period masih kosong, cek input month & year
        if (empty($period)) {
            $m = (int)($month ?: $currentMonth);
            $y = (int)($year ?: $currentYear);
            $period = sprintf('%04d-%02d', $y, $m);
        }

        // 1. Deteksi Kuartal / Triwulan (3 Bulan): e.g. "2026-Q4", "2026-T4"
        if (preg_match('/^(\d{4})-[QT](\d)$/i', $period, $matches)) {
            $y = (int)$matches[1];
            $q = (int)$matches[2];
            $q = max(1, min(4, $q));

            $startMonth = (($q - 1) * 3) + 1;
            $endMonth = $startMonth + 2;

            $startDate = Carbon::createFromDate($y, $startMonth, 1)->startOfDay();
            $endDate = Carbon::createFromDate($y, $endMonth, 1)->endOfMonth()->endOfDay();

            $monthNames = [
                1 => 'Jan - Mar',
                2 => 'Apr - Jun',
                3 => 'Jul - Sep',
                4 => 'Okt - Des',
            ];

            $periodKey = "{$y}-Q{$q}";
            $periodType = '3m';
            $label = "{$monthNames[$q]} {$y}";
            $shortLabel = "{$monthNames[$q]} {$y}";
            $cutoffLabel = $endDate->locale('id')->translatedFormat('d F Y');
            $representativeMonth = $startMonth;

            $isCurrent = ($now->between($startDate, $endDate));

            return [
                'period_key' => $periodKey,
                'period_type' => $periodType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'label' => $label . ($isCurrent ? ' (Berjalan)' : ''),
                'raw_label' => $label,
                'short_label' => $shortLabel,
                'cutoff_label' => $cutoffLabel,
                'month' => $representativeMonth,
                'year' => $y,
                'quarter' => $q,
                'semester' => null,
                'periode_date' => $startDate->format('Y-m-d'),
                'periode_end' => $endDate->format('Y-m-d'),
                'is_current' => $isCurrent,
            ];
        }

        // 2. Deteksi Semester (6 Bulan): e.g. "2026-S2", "2026-S1"
        if (preg_match('/^(\d{4})-S(\d)$/i', $period, $matches)) {
            $y = (int)$matches[1];
            $s = (int)$matches[2];
            $s = max(1, min(2, $s));

            if ($s === 1) {
                $startMonth = 1;
                $endMonth = 6;
                $rangeName = 'Jan - Jun';
            } else {
                $startMonth = 7;
                $endMonth = 12;
                $rangeName = 'Jul - Des';
            }

            $startDate = Carbon::createFromDate($y, $startMonth, 1)->startOfDay();
            $endDate = Carbon::createFromDate($y, $endMonth, 1)->endOfMonth()->endOfDay();

            $periodKey = "{$y}-S{$s}";
            $periodType = '6m';
            $label = "{$rangeName} {$y}";
            $shortLabel = "{$rangeName} {$y}";
            $cutoffLabel = $endDate->locale('id')->translatedFormat('d F Y');
            $representativeMonth = $startMonth;

            $isCurrent = ($now->between($startDate, $endDate));

            return [
                'period_key' => $periodKey,
                'period_type' => $periodType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'label' => $label . ($isCurrent ? ' (Berjalan)' : ''),
                'raw_label' => $label,
                'short_label' => $shortLabel,
                'cutoff_label' => $cutoffLabel,
                'month' => $representativeMonth,
                'year' => $y,
                'quarter' => null,
                'semester' => $s,
                'periode_date' => $startDate->format('Y-m-d'),
                'periode_end' => $endDate->format('Y-m-d'),
                'is_current' => $isCurrent,
            ];
        }

        // 3. Deteksi Bulanan (1 Bulan): e.g. "2026-10"
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $period, $matches)) {
            $y = (int)$matches[1];
            $m = (int)$matches[2];
        } else {
            $y = (int)($year ?: $currentYear);
            $m = (int)($month ?: $currentMonth);
        }

        $m = max(1, min(12, $m));
        $startDate = Carbon::createFromDate($y, $m, 1)->startOfDay();
        $endDate = Carbon::createFromDate($y, $m, 1)->endOfMonth()->endOfDay();

        $periodKey = sprintf('%04d-%02d', $y, $m);
        $periodType = '1m';
        $label = $startDate->locale('id')->translatedFormat('F Y');
        $shortLabel = $startDate->locale('id')->translatedFormat('M Y');
        $cutoffLabel = $endDate->locale('id')->translatedFormat('d F Y');

        $isCurrent = ($now->year === $y && $now->month === $m);

        return [
            'period_key' => $periodKey,
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'label' => $label . ($isCurrent ? ' (Berjalan)' : ''),
            'raw_label' => $label,
            'short_label' => $shortLabel,
            'cutoff_label' => $cutoffLabel,
            'month' => $m,
            'year' => $y,
            'quarter' => (int)ceil($m / 3),
            'semester' => $m > 6 ? 2 : 1,
            'periode_date' => $startDate->format('Y-m-d'),
            'periode_end' => $endDate->format('Y-m-d'),
            'is_current' => $isCurrent,
        ];
    }

    /**
     * Daftar opsi periode terorganisir untuk dropdown di Laporan, Rapor, Penilaian.
     */
    public static function getOptions(): array
    {
        $now = Carbon::now();
        $currentYear = (int)$now->year;
        $currentMonth = (int)$now->month;
        $currentQuarter = (int)ceil($currentMonth / 3);
        $currentSemester = $currentMonth > 6 ? 2 : 1;

        // 1. Bulanan (1 Bulan) - 12 Bulan Terakhir
        $monthlyOptions = [];
        for ($i = 0; $i < 12; $i++) {
            $d = $now->copy()->subMonths($i);
            $val = $d->format('Y-m');
            $isCur = ($i === 0);
            $rawLabel = $d->locale('id')->translatedFormat('F Y');
            $monthlyOptions[] = [
                'value' => $val,
                'label' => $rawLabel . ($isCur ? ' (Berjalan)' : ''),
                'raw_label' => $rawLabel,
                'type' => '1m',
                'is_current' => $isCur,
            ];
        }

        // 2. 3 Bulan (Okt - Des, dsb) - 8 Periode Terakhir
        $quarterlyOptions = [];
        $monthNames = [
            1 => 'Jan - Mar',
            2 => 'Apr - Jun',
            3 => 'Jul - Sep',
            4 => 'Okt - Des',
        ];

        // Generate mulai dari periode berjalan mundur 8 periode
        $qYear = $currentYear;
        $qQuarter = $currentQuarter;
        for ($k = 0; $k < 8; $k++) {
            $val = "{$qYear}-Q{$qQuarter}";
            $isCur = ($k === 0);
            $rawLabel = "{$monthNames[$qQuarter]} {$qYear}";
            $quarterlyOptions[] = [
                'value' => $val,
                'label' => $rawLabel . ($isCur ? ' (Berjalan)' : ''),
                'raw_label' => $rawLabel,
                'type' => '3m',
                'is_current' => $isCur,
            ];

            $qQuarter--;
            if ($qQuarter < 1) {
                $qQuarter = 4;
                $qYear--;
            }
        }

        // 3. 6 Bulan (Jan - Jun, Jul - Des) - 6 Periode Terakhir
        $semesterOptions = [];
        $sYear = $currentYear;
        $sSem = $currentSemester;
        for ($s = 0; $s < 6; $s++) {
            $val = "{$sYear}-S{$sSem}";
            $isCur = ($s === 0);
            $rangeText = $sSem === 1 ? 'Jan - Jun' : 'Jul - Des';
            $rawLabel = "{$rangeText} {$sYear}";
            $semesterOptions[] = [
                'value' => $val,
                'label' => $rawLabel . ($isCur ? ' (Berjalan)' : ''),
                'raw_label' => $rawLabel,
                'type' => '6m',
                'is_current' => $isCur,
            ];

            $sSem--;
            if ($sSem < 1) {
                $sSem = 2;
                $sYear--;
            }
        }

        return [
            'monthly' => $monthlyOptions,
            'quarterly' => $quarterlyOptions,
            'semester' => $semesterOptions,
            'all' => array_merge($monthlyOptions, $quarterlyOptions, $semesterOptions),
        ];
    }

    /**
     * Membangun riwayat tren 4-periode untuk chart rapor.
     */
    public static function buildChartHistory($userId, array $periodInfo, int $take = 4): array
    {
        $type = $periodInfo['period_type'];
        $chartHistory = [];

        if ($type === '3m') {
            $monthNames = [
                1 => 'Jan - Mar',
                2 => 'Apr - Jun',
                3 => 'Jul - Sep',
                4 => 'Okt - Des',
            ];

            // Mundur 4 periode 3-bulan
            $startYear = $periodInfo['year'];
            $startQ = $periodInfo['quarter'] ?: 4;

            $steps = [];
            for ($k = $take - 1; $k >= 0; $k--) {
                $curQ = $startQ - $k;
                $curY = $startYear;
                while ($curQ < 1) {
                    $curQ += 4;
                    $curY -= 1;
                }
                $steps[] = [
                    'key' => "{$curY}-Q{$curQ}",
                    'label' => "{$monthNames[$curQ]} {$curY}",
                    'is_current' => ($k === 0),
                    'year' => $curY,
                    'quarter' => $curQ
                ];
            }

            foreach ($steps as $st) {
                $revItem = PerformanceReview::with('details.category')
                    ->where('user_id', $userId)
                    ->where('period_key', $st['key'])
                    ->first();

                $chartHistory[] = self::formatHistoryItem($st['label'], $st['is_current'], $revItem);
            }
        } elseif ($type === '6m') {
            // Mundur 4 periode 6-bulan
            $startYear = $periodInfo['year'];
            $startS = $periodInfo['semester'] ?: 2;

            $steps = [];
            for ($k = $take - 1; $k >= 0; $k--) {
                $curS = $startS - $k;
                $curY = $startYear;
                while ($curS < 1) {
                    $curS += 2;
                    $curY -= 1;
                }
                $rangeText = $curS === 1 ? 'Jan - Jun' : 'Jul - Des';
                $steps[] = [
                    'key' => "{$curY}-S{$curS}",
                    'label' => "{$rangeText} {$curY}",
                    'is_current' => ($k === 0),
                    'year' => $curY,
                    'semester' => $curS
                ];
            }

            foreach ($steps as $st) {
                $revItem = PerformanceReview::with('details.category')
                    ->where('user_id', $userId)
                    ->where('period_key', $st['key'])
                    ->first();

                $chartHistory[] = self::formatHistoryItem($st['label'], $st['is_current'], $revItem);
            }
        } else {
            // Bulanan (1m) - Mundur 4 bulan
            $year = $periodInfo['year'];
            $month = $periodInfo['month'];

            for ($k = $take - 1; $k >= 0; $k--) {
                $refDate = Carbon::createFromDate($year, $month, 1)->subMonths($k);
                $key = $refDate->format('Y-m');

                $revItem = PerformanceReview::with('details.category')
                    ->where('user_id', $userId)
                    ->where(function($q) use ($key, $refDate) {
                        $q->where('period_key', $key)
                          ->orWhere(function($sub) use ($refDate) {
                              $sub->whereMonth('periode', $refDate->month)
                                  ->whereYear('periode', $refDate->year)
                                  ->where(function($sub2) {
                                      $sub2->where('period_type', '1m')->orWhereNull('period_type');
                                  });
                          });
                    })
                    ->first();

                $chartHistory[] = self::formatHistoryItem(
                    $refDate->locale('id')->translatedFormat('M Y'),
                    ($k === 0),
                    $revItem
                );
            }
        }

        return $chartHistory;
    }

    private static function formatHistoryItem(string $label, bool $isCurrent, $revItem): array
    {
        $ticketSc = 0;
        $qualSc = 0;
        $survSc = 0;
        $totSc = 0;

        if ($revItem) {
            $ticketSc = (float)($revItem->module_score ?: (optional($revItem->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket"))->score ?: 0));
            $qualSc = (float)($revItem->performance_score ?: (optional($revItem->details->firstWhere("category.name", "Kualitas Solusi"))->score ?: 0));
            $survSc = (float)($revItem->review_score ?: (optional($revItem->details->firstWhere("category.name", "Kepuasan Klien (Survey)"))->score ?: 0));
            $totSc = (float)($revItem->total_score ?: 0);
        }

        return [
            'label' => $label,
            'is_current' => $isCurrent,
            'review' => $revItem,
            'ticket_score' => $ticketSc,
            'quality_score' => $qualSc,
            'survey_score' => $survSc,
            'total_score' => $totSc,
        ];
    }
}
