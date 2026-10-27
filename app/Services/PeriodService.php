<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\PerformanceReview;
use App\Models\PerformanceCategory;

class PeriodService
{
    /**
     * Parse input periode menjadi struktur data periode:
     * - interval 1m: 1 bulan (e.g. 2026-10)
     * - interval 3m: 3 bulan rolling per bulan (e.g. 2026-11_3m, 2026-10_3m, atau legacy 2026-Q4)
     * - interval 6m: 6 bulan rolling per bulan (e.g. 2026-11_6m, 2026-10_6m, atau legacy 2026-S2)
     */
    public static function parse($period = null, $month = null, $year = null, $periodType = null): array
    {
        $now = Carbon::now();
        $currentYear = (int)$now->year;
        $currentMonth = (int)$now->month;

        $periodStr = trim((string)$period);
        $periodType = $periodType ? strtolower(trim((string)$periodType)) : null;

        // 1. Deteksi Format 3 Bulan Rolling: e.g. "2026-11_3m", "2026-10-3m", "2026-10_3M"
        if (preg_match('/^(\d{4})-(\d{1,2})[-_]3m$/i', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $startM = max(1, min(12, (int)$matches[2]));
            return self::buildRollingPeriod($y, $startM, 3, '3m');
        }

        // 2. Deteksi Format 6 Bulan Rolling: e.g. "2026-11_6m", "2026-10-6m"
        if (preg_match('/^(\d{4})-(\d{1,2})[-_]6m$/i', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $startM = max(1, min(12, (int)$matches[2]));
            return self::buildRollingPeriod($y, $startM, 6, '6m');
        }

        // 3. Deteksi Legacy Quarter (Q1-Q4 / T1-T4): e.g. "2026-Q4", "2026-Q1"
        if (preg_match('/^(\d{4})[-_]?[QqTt](\d)$/i', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $q = max(1, min(4, (int)$matches[2]));
            $startM = (($q - 1) * 3) + 1;
            return self::buildRollingPeriod($y, $startM, 3, '3m', $q, null);
        }

        // 4. Deteksi Legacy Semester (S1-S2): e.g. "2026-S2", "2026-S1"
        if (preg_match('/^(\d{4})[-_]?[Ss](\d)$/i', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $s = max(1, min(2, (int)$matches[2]));
            $startM = ($s === 1) ? 1 : 7;
            return self::buildRollingPeriod($y, $startM, 6, '6m', null, $s);
        }

        // 5. Deteksi Bulanan "YYYY-MM": e.g. "2026-10"
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $m = max(1, min(12, (int)$matches[2]));

            if ($periodType === '3m') {
                return self::buildRollingPeriod($y, $m, 3, '3m');
            } elseif ($periodType === '6m') {
                return self::buildRollingPeriod($y, $m, 6, '6m');
            }

            return self::buildMonthlyPeriod($y, $m);
        }

        // 6. Fallback jika $period kosong: gunakan $year dan $month
        $y = (!empty($year) && is_numeric($year)) ? (int)$year : $currentYear;
        $m = (!empty($month) && is_numeric($month)) ? max(1, min(12, (int)$month)) : $currentMonth;

        if ($periodType === '3m') {
            return self::buildRollingPeriod($y, $m, 3, '3m');
        } elseif ($periodType === '6m') {
            return self::buildRollingPeriod($y, $m, 6, '6m');
        }

        return self::buildMonthlyPeriod($y, $m);
    }

    /**
     * Helper membuat payload periode rolling multi-bulan (3m atau 6m)
     */
    private static function buildRollingPeriod(int $startYear, int $startMonth, int $lengthMonths, string $periodType, ?int $legacyQ = null, ?int $legacyS = null): array
    {
        $now = Carbon::now();
        $startDate = Carbon::createFromDate($startYear, $startMonth, 1)->startOfDay();
        $endDate = $startDate->copy()->addMonths($lengthMonths - 1)->endOfMonth()->endOfDay();

        $periodKey = sprintf('%04d-%02d_%s', $startYear, $startMonth, $periodType);
        $matchKeys = [$periodKey];

        // Format label rentang bulan yang rapi & elegan
        $startName = $startDate->locale('id')->translatedFormat('F');
        $endName = $endDate->locale('id')->translatedFormat('F');
        $startShort = $startDate->locale('id')->translatedFormat('M');
        $endShort = $endDate->locale('id')->translatedFormat('M');

        if ($startDate->year === $endDate->year) {
            $label = "{$startName} - {$endName} {$endDate->year}";
            $shortLabel = "{$startShort} - {$endShort} {$endDate->year}";
        } else {
            $label = "{$startName} {$startDate->year} - {$endName} {$endDate->year}";
            $shortLabel = "{$startShort} {$startDate->year} - {$endShort} {$endDate->year}";
        }

        $cutoffLabel = $endDate->locale('id')->translatedFormat('d F Y');

        // Aliasing untuk backward compatibility dengan database yang menyimpan YYYY-Qx atau YYYY-Sx
        $calculatedQ = $legacyQ;
        $calculatedS = $legacyS;

        if ($lengthMonths === 3) {
            if ($startMonth === 1) { $calculatedQ = 1; $matchKeys[] = "{$startYear}-Q1"; $matchKeys[] = "{$startYear}-T1"; }
            elseif ($startMonth === 4) { $calculatedQ = 2; $matchKeys[] = "{$startYear}-Q2"; $matchKeys[] = "{$startYear}-T2"; }
            elseif ($startMonth === 7) { $calculatedQ = 3; $matchKeys[] = "{$startYear}-Q3"; $matchKeys[] = "{$startYear}-T3"; }
            elseif ($startMonth === 10) { $calculatedQ = 4; $matchKeys[] = "{$startYear}-Q4"; $matchKeys[] = "{$startYear}-T4"; }
        } elseif ($lengthMonths === 6) {
            if ($startMonth === 1) { $calculatedS = 1; $matchKeys[] = "{$startYear}-S1"; }
            elseif ($startMonth === 7) { $calculatedS = 2; $matchKeys[] = "{$startYear}-S2"; }
        }

        // Cek apakah bulan saat ini berada dalam rentang periode ini
        $isCurrent = ($now->betweenIncluded($startDate, $endDate) || ($now->year === $startYear && $now->month === $startMonth));

        return [
            'period_key' => $periodKey,
            'match_keys' => array_unique($matchKeys),
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'label' => $label . ($isCurrent ? ' (Berjalan)' : ''),
            'raw_label' => $label,
            'short_label' => $shortLabel,
            'cutoff_label' => $cutoffLabel,
            'month' => $startMonth,
            'year' => $startYear,
            'quarter' => $calculatedQ,
            'semester' => $calculatedS,
            'periode_date' => $startDate->format('Y-m-d'),
            'periode_end' => $endDate->format('Y-m-d'),
            'is_current' => $isCurrent,
        ];
    }

    /**
     * Helper membuat payload periode 1 bulan
     */
    private static function buildMonthlyPeriod(int $year, int $month): array
    {
        $now = Carbon::now();
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

        $periodKey = sprintf('%04d-%02d', $year, $month);
        $label = $startDate->locale('id')->translatedFormat('F Y');
        $shortLabel = $startDate->locale('id')->translatedFormat('M Y');
        $cutoffLabel = $endDate->locale('id')->translatedFormat('d F Y');

        $isCurrent = ($now->year === $year && $now->month === $month);

        return [
            'period_key' => $periodKey,
            'match_keys' => [$periodKey],
            'period_type' => '1m',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'label' => $label . ($isCurrent ? ' (Berjalan)' : ''),
            'raw_label' => $label,
            'short_label' => $shortLabel,
            'cutoff_label' => $cutoffLabel,
            'month' => $month,
            'year' => $year,
            'quarter' => (int)ceil($month / 3),
            'semester' => $month > 6 ? 2 : 1,
            'periode_date' => $startDate->format('Y-m-d'),
            'periode_end' => $endDate->format('Y-m-d'),
            'is_current' => $isCurrent,
        ];
    }

    /**
     * Daftar opsi periode untuk dropdown di Laporan, Rapor, dan Penilaian:
     * - monthly: 13 bulan terakhir (mulai bulan depan hingga 12 bulan lalu)
     * - quarterly: pilihan rentang 3 bulan per bulan (13 opsi)
     * - semester: pilihan rentang 6 bulan per bulan (13 opsi)
     */
    public static function getOptions(): array
    {
        $now = Carbon::now();

        // 1. Bulanan (1 Bulan: Mulai bulan saat ini hingga 11 bulan ke belakang, tidak ada bulan depan)
        $monthlyOptions = [];
        for ($i = 0; $i <= 11; $i++) {
            $d = $now->copy()->startOfMonth()->subMonths($i);
            $val = $d->format('Y-m');
            $isCur = ($d->year === $now->year && $d->month === $now->month);
            $rawLabel = $d->locale('id')->translatedFormat('F Y');
            $monthlyOptions[] = [
                'value' => $val,
                'match_keys' => [$val],
                'label' => $rawLabel . ($isCur ? ' (Berjalan)' : ''),
                'raw_label' => $rawLabel,
                'type' => '1m',
                'is_current' => $isCur,
            ];
        }

        // 2. 3 Bulan (Rolling 3 Bulan mulai periode berjalan ke belakang)
        $quarterlyOptions = [];
        for ($i = 0; $i <= 11; $i++) {
            $start = $now->copy()->startOfMonth()->subMonths($i);
            $parsed = self::buildRollingPeriod($start->year, $start->month, 3, '3m');
            $isCur = ($start->year === $now->year && $start->month === $now->month);
            $quarterlyOptions[] = [
                'value' => $parsed['period_key'],
                'match_keys' => $parsed['match_keys'],
                'label' => $parsed['raw_label'] . ($isCur ? ' (Berjalan)' : ''),
                'raw_label' => $parsed['raw_label'],
                'type' => '3m',
                'is_current' => $isCur,
            ];
        }

        // 3. 6 Bulan (Rolling 6 Bulan mulai periode berjalan ke belakang)
        $semesterOptions = [];
        for ($i = 0; $i <= 11; $i++) {
            $start = $now->copy()->startOfMonth()->subMonths($i);
            $parsed = self::buildRollingPeriod($start->year, $start->month, 6, '6m');
            $isCur = ($start->year === $now->year && $start->month === $now->month);
            $semesterOptions[] = [
                'value' => $parsed['period_key'],
                'match_keys' => $parsed['match_keys'],
                'label' => $parsed['raw_label'] . ($isCur ? ' (Berjalan)' : ''),
                'raw_label' => $parsed['raw_label'],
                'type' => '6m',
                'is_current' => $isCur,
            ];
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
        $type = $periodInfo['period_type'] ?? '1m';
        $chartHistory = [];
        $startDate = $periodInfo['start_date'];

        if ($type === '3m') {
            // Mundur 4 periode per interval 3 bulan
            for ($k = $take - 1; $k >= 0; $k--) {
                $refDate = $startDate->copy()->subMonths($k * 3);
                $stepInfo = self::parse($refDate->format('Y-m') . '_3m');

                $revItem = PerformanceReview::with('details.category')
                    ->where('user_id', $userId)
                    ->where(function($q) use ($stepInfo) {
                        $q->whereIn('period_key', $stepInfo['match_keys']);
                    })
                    ->first();

                $chartHistory[] = self::formatHistoryItem(
                    $stepInfo['short_label'],
                    ($k === 0),
                    $revItem
                );
            }
        } elseif ($type === '6m') {
            // Mundur 4 periode per interval 6 bulan
            for ($k = $take - 1; $k >= 0; $k--) {
                $refDate = $startDate->copy()->subMonths($k * 6);
                $stepInfo = self::parse($refDate->format('Y-m') . '_6m');

                $revItem = PerformanceReview::with('details.category')
                    ->where('user_id', $userId)
                    ->where(function($q) use ($stepInfo) {
                        $q->whereIn('period_key', $stepInfo['match_keys']);
                    })
                    ->first();

                $chartHistory[] = self::formatHistoryItem(
                    $stepInfo['short_label'],
                    ($k === 0),
                    $revItem
                );
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
