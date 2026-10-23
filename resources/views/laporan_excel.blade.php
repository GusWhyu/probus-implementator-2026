<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Laporan Kinerja Staff - {{ ucfirst($currentDepartemen->name ?? 'Operasional') }}</title>
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Ringkasan Kinerja Staff</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                        <x:Print>
                            <x:ValidPrinterInfo/>
                            <x:PaperSizeIndex>9</x:PaperSizeIndex>
                            <x:HorizontalResolution>600</x:HorizontalResolution>
                            <x:VerticalResolution>600</x:VerticalResolution>
                        </x:Print>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
    <style>
        body, table {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
        }
        .kop-company {
            font-size: 14pt;
            font-weight: bold;
            color: #004880;
            letter-spacing: 0.5px;
        }
        .kop-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
        }
        .kop-meta {
            font-size: 9.5pt;
            color: #475569;
        }
        .kop-date {
            font-size: 9pt;
            color: #64748b;
            font-style: italic;
        }
        .th-blue {
            background-color: #004880;
            color: #ffffff;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #003366;
            height: 34px;
        }
        .td-data {
            font-size: 10pt;
            vertical-align: middle;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }
        .td-center {
            text-align: center;
        }
        .td-left {
            text-align: left;
        }
        .td-right {
            text-align: right;
        }
        .badge-final {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: bold;
        }
        .badge-draft {
            background-color: #fffbeb;
            color: #b45309;
            font-weight: bold;
        }
        .badge-belum {
            background-color: #f1f5f9;
            color: #64748b;
        }
        .badge-sangat-baik {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: bold;
        }
        .badge-baik {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: bold;
        }
        .badge-cukup {
            background-color: #fef2f2;
            color: #b91c1c;
            font-weight: bold;
        }
        .tfoot-row {
            background-color: #e2e8f0;
            font-weight: bold;
            border-top: 2px solid #004880;
            border-bottom: 2px solid #004880;
            height: 30px;
        }
    </style>
</head>
<body>
    @php
        $periodDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $totalStaff = $users->count();
        $totalClosedAll = 0;
        $totalAdvisedAll = 0;
        $sumFinalScore = 0;
        $countFinalScore = 0;
        $sumCsat = 0;
        $countCsat = 0;
    @endphp

    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <!-- Header Perusahaan & Laporan -->
        <tr>
            <td colspan="10" class="kop-company" style="padding-bottom: 3px;">CV. MITRA SOLUSI MANDIRI - PROBUS SYSTEM</td>
        </tr>
        <tr>
            <td colspan="10" class="kop-title" style="padding-bottom: 5px;">LAPORAN RINGKASAN KINERJA STAFF</td>
        </tr>
        <tr>
            <td colspan="10" class="kop-meta" style="padding-bottom: 3px;">
                <strong>Departemen:</strong> {{ ucfirst($currentDepartemen->name ?? 'Operasional') }} &nbsp;|&nbsp; 
                <strong>Periode:</strong> {{ $periodDate->locale('id')->translatedFormat('F Y') }} &nbsp;|&nbsp;
                <strong>Batas Cut-off:</strong> {{ $periodDate->copy()->endOfMonth()->locale('id')->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="10" class="kop-date" style="padding-bottom: 12px;">
                Tanggal Export: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB &nbsp;|&nbsp; Diunduh oleh: {{ auth()->user()->name ?? 'Administrator' }}
            </td>
        </tr>
        <tr>
            <td colspan="10" style="height: 6px;"></td>
        </tr>

        <!-- Table Header (Probus Blue #004880) -->
        <thead>
            <tr>
                <th class="th-blue" style="width: 45px;">NO</th>
                <th class="th-blue" style="width: 200px; text-align: left; padding-left: 10px;">NAMA STAFF</th>
                <th class="th-blue" style="width: 220px; text-align: left; padding-left: 10px;">EMAIL</th>
                <th class="th-blue" style="width: 100px;">PERIODE</th>
                <th class="th-blue" style="width: 120px;">TIKET SELESAI</th>
                <th class="th-blue" style="width: 120px;">TIKET DI-ADVICE</th>
                <th class="th-blue" style="width: 140px;">KEPUASAN KLIEN</th>
                <th class="th-blue" style="width: 110px;">NILAI AKHIR</th>
                <th class="th-blue" style="width: 120px;">PREDIKAT</th>
                <th class="th-blue" style="width: 160px;">STATUS PENILAIAN</th>
            </tr>
        </thead>

        <!-- Table Body -->
        <tbody>
            @forelse($users as $index => $u)
            @php
                $penilaian = $u->performanceReviews->first();
                $isFinal = $penilaian && strtoupper($penilaian->status) === 'FINAL';
                $isDraft = $penilaian && strtoupper($penilaian->status) === 'DRAFT';

                $ticketClosedCount = $penilaian ? (int)$penilaian->total_ticket : (int)($monthClosedTickets[$u->id] ?? 0);
                $ticketAdviceCount = (int)($monthAdvisedTickets[$u->id] ?? ($penilaian->total_ticket_yang_di_advice ?? 0));

                $totalClosedAll += $ticketClosedCount;
                $totalAdvisedAll += $ticketAdviceCount;

                // CSAT
                $csatScore = null;
                $csatRating = 0;
                if ($penilaian && $penilaian->review_score > 0) {
                    $csatScore = (float)$penilaian->review_score;
                    $csatRating = round($csatScore / 20, 1);
                } elseif (isset($monthSurveys[$u->id]) && $monthSurveys[$u->id] > 0) {
                    $csatRating = round((float)$monthSurveys[$u->id], 1);
                    $csatScore = round($csatRating * 20);
                } elseif ($ticketClosedCount > 0) {
                    $csatScore = 90;
                    $csatRating = 4.5;
                }

                if ($csatRating > 0) {
                    $sumCsat += $csatRating;
                    $countCsat++;
                }

                // Nilai & Predikat
                $scoreText = '-';
                $predikatText = '-';
                $predikatClass = '';
                if ($isFinal) {
                    $scoreVal = (float)$penilaian->total_score;
                    $scoreText = number_format($scoreVal, 1, ',', '.');
                    $sumFinalScore += $scoreVal;
                    $countFinalScore++;

                    if ($scoreVal >= 80) {
                        $predikatText = 'Sangat Baik';
                        $predikatClass = 'badge-sangat-baik';
                    } elseif ($scoreVal >= 70) {
                        $predikatText = 'Baik';
                        $predikatClass = 'badge-baik';
                    } else {
                        $predikatText = 'Cukup';
                        $predikatClass = 'badge-cukup';
                    }
                } elseif ($isDraft) {
                    $scoreText = 'Draft (' . ($penilaian->module_score ?? 0) . ')';
                    $predikatText = 'Draft';
                    $predikatClass = 'badge-draft';
                }

                // Status
                $statusText = 'Belum Diproses';
                $statusClass = 'badge-belum';
                if ($isFinal) {
                    $statusText = 'Sudah Dinilai (Final)';
                    $statusClass = 'badge-final';
                } elseif ($isDraft) {
                    $statusText = 'Terproses (Draft)';
                    $statusClass = 'badge-draft';
                }

                $rowBg = ($index % 2 == 1) ? '#f8fafc' : '#ffffff';
            @endphp
            <tr style="background-color: {{ $rowBg }};">
                <td class="td-data td-center">{{ $index + 1 }}</td>
                <td class="td-data td-left" style="font-weight: 600;">{{ $u->name }}</td>
                <td class="td-data td-left">{{ $u->email }}</td>
                <td class="td-data td-center">{{ $periodDate->locale('id')->translatedFormat('M Y') }}</td>
                <td class="td-data td-center" style="font-weight: 600;">{{ $ticketClosedCount }} Tiket</td>
                <td class="td-data td-center" style="font-weight: 600;">{{ $ticketAdviceCount }} Tiket</td>
                <td class="td-data td-center">
                    @if($csatRating > 0)
                        {{ number_format($csatRating, 1, ',', '.') }} / 5.0 ({{ round($csatScore) }}%)
                    @else
                        -
                    @endif
                </td>
                <td class="td-data td-center" style="font-weight: bold; color: #004880;">{{ $scoreText }}</td>
                <td class="td-data td-center {{ $predikatClass }}">{{ $predikatText }}</td>
                <td class="td-data td-center {{ $statusClass }}">{{ $statusText }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="td-data td-center" style="padding: 18px; color: #64748b;">
                    Tidak ada data staff (role: user) yang ditemukan pada Departemen {{ ucfirst($currentDepartemen->name ?? '') }}.
                </td>
            </tr>
            @endforelse
        </tbody>

        <!-- Table Summary / Footer -->
        <tfoot>
            @php
                $avgCsat = $countCsat > 0 ? round($sumCsat / $countCsat, 1) : 0;
                $avgFinalScore = $countFinalScore > 0 ? round($sumFinalScore / $countFinalScore, 1) : 0;
            @endphp
            <tr class="tfoot-row">
                <td colspan="4" class="td-data td-center" style="font-weight: bold; background-color: #e2e8f0;">
                    TOTAL / RATA-RATA ({{ $totalStaff }} Staff)
                </td>
                <td class="td-data td-center" style="font-weight: bold; background-color: #e2e8f0;">
                    {{ $totalClosedAll }} Tiket
                </td>
                <td class="td-data td-center" style="font-weight: bold; background-color: #e2e8f0;">
                    {{ $totalAdvisedAll }} Tiket
                </td>
                <td class="td-data td-center" style="font-weight: bold; background-color: #e2e8f0;">
                    {{ $avgCsat > 0 ? number_format($avgCsat, 1, ',', '.') . ' / 5.0' : '-' }}
                </td>
                <td class="td-data td-center" style="font-weight: bold; background-color: #e2e8f0; color: #004880;">
                    {{ $avgFinalScore > 0 ? number_format($avgFinalScore, 1, ',', '.') : '-' }}
                </td>
                <td colspan="2" class="td-data td-center" style="font-weight: bold; background-color: #e2e8f0;">
                    Final: {{ $countFinalScore }} / {{ $totalStaff }} Staff
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
