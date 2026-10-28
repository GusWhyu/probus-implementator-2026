<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\PerformanceReview;
use App\Models\PerformanceCategory;

class PeriodService
{
    /**
     * Parse input periode menjadi struktur data periode 1 bulan:
     * - e.g. 2026-10 (Oktober 2026)
     */
    public static function parse($period = null, $month = null, $year = null, $periodType = null): array
    {
        $now = Carbon::now();
        $currentYear = (int)$now->year;
        $currentMonth = (int)$now->month;

        $periodStr = trim((string)$period);

        // 1. Deteksi Bulanan "YYYY-MM": e.g. "2026-10", "2026-10_3m", dll
        if (preg_match('/^(\d{4})-(\d{1,2})/', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $m = max(1, min(12, (int)$matches[2]));
            return self::buildMonthlyPeriod($y, $m);
        }

        // 2. Deteksi Legacy Quarter (Q1-Q4 / T1-T4): e.g. "2026-Q4", "2026-Q1"
        if (preg_match('/^(\d{4})[-_]?[QqTt](\d)$/i', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $q = max(1, min(4, (int)$matches[2]));
            $startM = (($q - 1) * 3) + 1;
            return self::buildMonthlyPeriod($y, $startM);
        }

        // 3. Deteksi Legacy Semester (S1-S2): e.g. "2026-S2", "2026-S1"
        if (preg_match('/^(\d{4})[-_]?[Ss](\d)$/i', $periodStr, $matches)) {
            $y = (int)$matches[1];
            $s = max(1, min(2, (int)$matches[2]));
            $startM = ($s === 1) ? 1 : 7;
            return self::buildMonthlyPeriod($y, $startM);
        }

        // 4. Fallback jika $period kosong: gunakan $year dan $month
        $y = (!empty($year) && is_numeric($year)) ? (int)$year : $currentYear;
        $m = (!empty($month) && is_numeric($month)) ? max(1, min(12, (int)$month)) : $currentMonth;

        return self::buildMonthlyPeriod($y, $m);
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
     * Daftar opsi periode bulanan untuk dropdown di Laporan, Rapor, dan Penilaian:
     * - 12 bulan terakhir (mulai bulan berjalan hingga 11 bulan ke belakang)
     */
    public static function getOptions(): array
    {
        $now = Carbon::now();

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

        return [
            'monthly' => $monthlyOptions,
            'quarterly' => [],
            'semester' => [],
            'all' => $monthlyOptions,
        ];
    }

    /**
     * Membangun riwayat tren 4-periode untuk chart rapor (mundur 4 bulan).
     */
    public static function buildChartHistory($userId, array $periodInfo, int $take = 4): array
    {
        $chartHistory = [];
        $year = $periodInfo['year'];
        $month = $periodInfo['month'];

        // Mundur 4 bulan secara konsisten
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
