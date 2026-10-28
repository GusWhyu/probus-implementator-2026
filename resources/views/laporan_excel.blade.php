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
            font-size: 10pt;
            color: #0f172a;
        }
        .kop-company {
            font-size: 15pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 0.5px;
        }
        .kop-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
        }
        .kop-meta {
            font-size: 9.5pt;
            color: #334155;
        }
        .kop-date {
            font-size: 9pt;
            color: #64748b;
            font-style: italic;
        }
        /* Header Blue seperti pada Light Mode Web (#1D4ED8 / Blue-700) */
        .th-blue {
            background-color: #1D4ED8;
            color: #ffffff;
            font-weight: bold;
            font-size: 9.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #1e40af;
            height: 40px;
            padding: 8px 6px;
            line-height: 1.25;
            mso-height-source: userset;
        }
        .td-data {
            font-size: 10pt;
            vertical-align: middle;
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            height: 28px;
            mso-height-source: userset;
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
        .tfoot-row td {
            background-color: #eff6ff;
            font-weight: bold;
            border-top: 2.5pt solid #1d4ed8;
            border-bottom: 2.5pt double #1d4ed8;
            border-left: 1px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
            height: 32px;
            vertical-align: middle;
            padding: 6px 10px;
        }
    </style>
</head>
<body>
    @php
        $periodDate = \Carbon\Carbon::createFromDate($year, $month, 1);
        $totalStaff = $users->count();
        $totalClosedAll = $users->sum(function($u) use ($monthClosedTickets) {
            $pen = $u->performanceReviews->first();
            return (int)($monthClosedTickets[$u->id] ?? ($pen ? $pen->total_ticket : 0));
        });
        $totalAdvisedAll = 0;
        $sumFinalScore = 0;
        $countFinalScore = 0;
        $sumCsat = 0;
        $countCsat = 0;

        // Hitung statistik untuk KPI Summary Cards di atas tabel
        $sudahDinilaiCount = $users->filter(fn($u) => $u->performanceReviews->first() && strtoupper($u->performanceReviews->first()->status) === 'FINAL')->count();
        $belumDinilaiCount = $totalStaff - $sudahDinilaiCount;
        $persenDinilai = $totalStaff > 0 ? round(($sudahDinilaiCount / $totalStaff) * 100, 1) : 0;
    @endphp

    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <!-- Accent Top Bar -->
        <tr style="height: 4px;">
            <td colspan="10" style="background-color: #1D4ED8; height: 4px; padding: 0;"></td>
        </tr>
        <tr style="height: 10px;"><td colspan="10" style="height: 10px;"></td></tr>

        <!-- Header Perusahaan & Laporan -->
        <tr>
            <td colspan="10" class="kop-company" style="padding-bottom: 2px;">CV. MITRA SOLUSI MANDIRI - PROBUS SYSTEM</td>
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

        <!-- Mini KPI Dashboard Cards (Ringkasan Eksekutif di Atas Tabel) -->
        <tr style="height: 8px;"><td colspan="10" style="height: 8px;"></td></tr>
        <tr>
            <td colspan="2" style="background-color: #EFF6FF; border: 1px solid #BFDBFE; text-align: center; vertical-align: middle; padding: 9px 8px;">
                <span style="font-size: 8pt; font-weight: bold; color: #1D4ED8; text-transform: uppercase;">TOTAL STAFF</span><br>
                <span style="font-size: 15pt; font-weight: bold; color: #1E3A8A;">{{ $totalStaff }}</span>
                <span style="font-size: 8.5pt; color: #64748B;"> Staff</span>
            </td>
            <td colspan="3" style="background-color: #ECFDF5; border: 1px solid #A7F3D0; text-align: center; vertical-align: middle; padding: 9px 8px;">
                <span style="font-size: 8pt; font-weight: bold; color: #047857; text-transform: uppercase;">SUDAH DINILAI (FINAL)</span><br>
                <span style="font-size: 15pt; font-weight: bold; color: #065F46;">{{ $sudahDinilaiCount }}</span>
                <span style="font-size: 8.5pt; color: #059669;"> Staff ({{ $persenDinilai }}%)</span>
            </td>
            <td colspan="2" style="background-color: #FFFBEB; border: 1px solid #FDE68A; text-align: center; vertical-align: middle; padding: 9px 8px;">
                <span style="font-size: 8pt; font-weight: bold; color: #B45309; text-transform: uppercase;">BELUM DINILAI</span><br>
                <span style="font-size: 15pt; font-weight: bold; color: #92400E;">{{ $belumDinilaiCount }}</span>
                <span style="font-size: 8.5pt; color: #D97706;"> Staff</span>
            </td>
            <td colspan="3" style="background-color: #EFF6FF; border: 1px solid #BFDBFE; text-align: center; vertical-align: middle; padding: 9px 8px;">
                <span style="font-size: 8pt; font-weight: bold; color: #1D4ED8; text-transform: uppercase;">TOTAL TIKET SELESAI</span><br>
                <span style="font-size: 15pt; font-weight: bold; color: #1E3A8A;">{{ $totalClosedAll }}</span>
                <span style="font-size: 8.5pt; color: #1D4ED8;"> Tiket</span>
            </td>
        </tr>
        <tr style="height: 12px;"><td colspan="10" style="height: 12px;"></td></tr>

        <!-- Table Header (Primary Blue Light Mode #1D4ED8) -->
        <thead>
            <tr>
                <th class="th-blue" style="width: 45px;">NO</th>
                <th class="th-blue" style="width: 190px; text-align: left; padding-left: 12px;">NAMA STAFF</th>
                <th class="th-blue" style="width: 220px; text-align: left; padding-left: 12px;">EMAIL</th>
                <th class="th-blue" style="width: 100px;">PERIODE</th>
                <th class="th-blue" style="width: 130px;">TIKET SELESAI</th>
                <th class="th-blue" style="width: 130px;">TIKET DI-ADVICE</th>
                <th class="th-blue" style="width: 150px;">KEPUASAN KLIEN</th>
                <th class="th-blue" style="width: 115px;">NILAI AKHIR</th>
                <th class="th-blue" style="width: 125px;">PREDIKAT</th>
                <th class="th-blue" style="width: 180px;">STATUS PENILAIAN</th>
            </tr>
        </thead>

        <!-- Table Body -->
        <tbody>
            @forelse($users as $index => $u)
            @php
                $penilaian = $u->performanceReviews->first();
                $isFinal = $penilaian && strtoupper($penilaian->status) === 'FINAL';
                $isDraft = $penilaian && strtoupper($penilaian->status) === 'DRAFT';

                $ticketClosedCount = (int)($monthClosedTickets[$u->id] ?? ($penilaian ? $penilaian->total_ticket : 0));
                $ticketAdviceCount = (int)($monthAdvisedTickets[$u->id] ?? ($penilaian ? $penilaian->total_ticket_yang_di_advice : 0));

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

                // Nilai & Predikat dengan style inline agar ter-render sempurna di Excel
                $scoreText = '-';
                $scoreColor = '#94a3b8';
                $predikatText = '-';
                $predikatStyle = 'color: #94a3b8;';

                if ($isFinal) {
                    $scoreVal = (float)$penilaian->total_score;
                    $scoreText = number_format($scoreVal, 1, ',', '.');
                    $sumFinalScore += $scoreVal;
                    $countFinalScore++;

                    if ($scoreVal >= 90) {
                        $scoreColor = '#047857';
                        $predikatText = 'Sangat Memuaskan';
                        $predikatStyle = 'background-color: #DCFCE7; color: #047857; font-weight: bold; border-color: #86EFAC;';
                    } elseif ($scoreVal >= 80) {
                        $scoreColor = '#1D4ED8';
                        $predikatText = 'Memuaskan';
                        $predikatStyle = 'background-color: #DBEAFE; color: #1D4ED8; font-weight: bold; border-color: #93C5FD;';
                    } elseif ($scoreVal >= 70) {
                        $scoreColor = '#0284C7';
                        $predikatText = 'Cukup Memuaskan';
                        $predikatStyle = 'background-color: #E0F2FE; color: #0369A1; font-weight: bold; border-color: #BAE6FD;';
                    } elseif ($scoreVal >= 60) {
                        $scoreColor = '#D97706';
                        $predikatText = 'Perlu Peningkatan';
                        $predikatStyle = 'background-color: #FEF3C7; color: #B45309; font-weight: bold; border-color: #FCD34D;';
                    } elseif ($scoreVal >= 50) {
                        $scoreColor = '#EA580C';
                        $predikatText = 'Perlu Pembinaan';
                        $predikatStyle = 'background-color: #FFEDD5; color: #C2410C; font-weight: bold; border-color: #FDBA74;';
                    } else {
                        $scoreColor = '#E11D48';
                        $predikatText = 'Perlu Perhatian Khusus';
                        $predikatStyle = 'background-color: #FFE4E6; color: #BE123C; font-weight: bold; border-color: #FDA4AF;';
                    }
                } elseif ($isDraft) {
                    $scoreText = 'Draft (' . ($penilaian->module_score ?? 0) . ')';
                    $scoreColor = '#B45309';
                    $predikatText = 'Draft';
                    $predikatStyle = 'background-color: #FEF3C7; color: #B45309; font-weight: bold; border-color: #FCD34D;';
                }

                // Status Penilaian dengan style inline
                $statusText = 'Belum Diproses';
                $statusStyle = 'background-color: #F1F5F9; color: #64748B; font-weight: 500;';
                if ($isFinal) {
                    $statusText = 'Sudah Dinilai (Final)';
                    $statusStyle = 'background-color: #DCFCE7; color: #047857; font-weight: bold;';
                } elseif ($isDraft) {
                    $statusText = 'Terproses (Draft)';
                    $statusStyle = 'background-color: #FEF3C7; color: #B45309; font-weight: bold;';
                }

                $rowBg = ($index % 2 == 1) ? '#F8FAFC' : '#FFFFFF';
            @endphp
            <tr style="background-color: {{ $rowBg }};">
                <td class="td-data td-center" style="color: #64748b;">{{ $index + 1 }}</td>
                <td class="td-data td-left" style="font-weight: bold; color: #0f172a; padding-left: 12px;">{{ $u->name }}</td>
                <td class="td-data td-left" style="color: #475569; padding-left: 12px; font-size: 9.5pt;">{{ $u->email }}</td>
                <td class="td-data td-center" style="color: #334155;">{{ $periodDate->locale('id')->translatedFormat('M Y') }}</td>
                
                {{-- Tiket Selesai --}}
                <td class="td-data td-center" style="{{ $ticketClosedCount > 0 ? 'font-weight: bold; color: #047857;' : 'color: #94a3b8;' }}">
                    {{ $ticketClosedCount }} Tiket
                </td>

                {{-- Tiket Di-Advice --}}
                <td class="td-data td-center" style="{{ $ticketAdviceCount > 0 ? 'font-weight: bold; color: #4338CA;' : 'color: #94a3b8;' }}">
                    {{ $ticketAdviceCount }} Tiket
                </td>

                {{-- Kepuasan Klien --}}
                <td class="td-data td-center" style="{{ $csatRating > 0 ? 'font-weight: bold; color: #B45309;' : 'color: #94a3b8;' }}">
                    @if($csatRating > 0)
                        {{ number_format($csatRating, 1, ',', '.') }} / 5.0 ({{ round($csatScore) }}%)
                    @else
                        -
                    @endif
                </td>

                {{-- Nilai Akhir --}}
                <td class="td-data td-center" style="font-weight: bold; font-size: 10.5pt; color: {{ $scoreColor }};">
                    {{ $scoreText }}
                </td>

                {{-- Predikat --}}
                <td class="td-data td-center" style="{{ $predikatStyle }}">
                    {{ $predikatText }}
                </td>

                {{-- Status Penilaian --}}
                <td class="td-data td-center" style="{{ $statusStyle }}">
                    {{ $statusText }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="td-data td-center" style="padding: 20px; color: #64748b;">
                    Tidak ada data staff (role: user) yang ditemukan pada Departemen {{ ucfirst($currentDepartemen->name ?? '') }}.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Ruang Pengesahan / Tanda Tangan Cetak -->
    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; margin-top: 25px;">
        <tr style="height: 20px;"><td colspan="10" style="height: 20px;"></td></tr>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 9.5pt; color: #475569; vertical-align: top;">
                Dibuat & Dievaluasi Oleh,<br>
                <strong>Supervisor / Tim Evaluator</strong><br><br><br><br><br>
                <strong>( ___________________________ )</strong><br>
                <span style="font-size: 8.5pt; color: #64748b;">Tanggal: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</span>
            </td>
            <td colspan="2"></td>
            <td colspan="4" style="text-align: center; font-size: 9.5pt; color: #475569; vertical-align: top;">
                Mengetahui & Menyetujui,<br>
                <strong>Kepala Departemen / Manajemen</strong><br><br><br><br><br>
                <strong>( ___________________________ )</strong><br>
                <span style="font-size: 8.5pt; color: #64748b;">CV. Mitra Solusi Mandiri</span>
            </td>
        </tr>
    </table>
</body>
</html>
