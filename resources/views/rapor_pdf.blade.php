<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penilaian Kinerja - {{ $user->name }}</title>
    <style>
        @page {
            margin: 10px 14px;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 9.5px;
            line-height: 1.3;
            background-color: #eef2f7;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    {{-- Outer background wrapper --}}
    <div style="background-color: #eef2f7; padding: 6px;">

        {{-- Main white card --}}
        <div style="border: 1.5px solid #d6dce6; border-radius: 14px; padding: 12px 16px 8px 16px; background-color: #ffffff;">

            @php
                if (!function_exists('formatDurationPdf')) {
                    function formatDurationPdf($text, $showSub = true) {
                        if (!$text || $text === '-') {
                            return '<span style="color: #94a3b8;">-</span>';
                        }
                        if (!preg_match('/(\d+)\s*menit/i', $text, $m)) {
                            return e($text);
                        }
                        $totalM = (int)$m[1];
                        if ($totalM == 0) {
                            return '<span style="color: #94a3b8;">0 m</span>';
                        }
                        if ($totalM < 60) {
                            return $totalM . ' menit';
                        }
                        $readable = \App\Models\Ticket::formatMinutesToText($totalM);
                        $formattedM = number_format($totalM, 0, ',', '.') . ' m';
                        if ($showSub) {
                            return '<div style="line-height: 1.15; text-align: center;">'
                                . '<span style="font-weight: bold; color: #1e293b;">' . e($readable) . '</span>'
                                . '<div style="font-size: 7px; color: #94a3b8; margin-top: 1px;">(' . $formattedM . ')</div>'
                                . '</div>';
                        }
                        return e($readable);
                    }
                }

                $pdfAvatarSrc = null;
                if (!empty($user->profile_image)) {
                    $pPath = public_path('storage/' . $user->profile_image);
                    if (file_exists($pPath)) {
                        $ext = strtolower(pathinfo($pPath, PATHINFO_EXTENSION));
                        $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : (($ext === 'png') ? 'image/png' : 'image/jpeg');
                        $pdfAvatarSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($pPath));
                    } else {
                        $sPath = storage_path('app/public/' . $user->profile_image);
                        if (file_exists($sPath)) {
                            $ext = strtolower(pathinfo($sPath, PATHINFO_EXTENSION));
                            $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : (($ext === 'png') ? 'image/png' : 'image/jpeg');
                            $pdfAvatarSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sPath));
                        }
                    }
                }
                if (!$pdfAvatarSrc) {
                    $isFemale = in_array(strtolower(trim($user->name ?? '')), ['dyah kusuma', 'dewi', 'siti rahma', 'dewi anggraini', 'rina wulandari', 'ayu lestari']);
                    $defaultPath = public_path($isFemale ? 'img/avatar-female.png' : 'img/avatar-default.png');
                    if (file_exists($defaultPath)) {
                        $pdfAvatarSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($defaultPath));
                    }
                }
            @endphp

            {{-- ============================================= --}}
            {{-- HEADER: Avatar + Name  |  Document Title      --}}
            {{-- ============================================= --}}
            <table style="width: 100%; margin-bottom: 8px;">
                <tr>
                    <td style="width: 55%; vertical-align: middle;">
                        <table>
                            <tr>
                                <td style="vertical-align: middle; padding-right: 12px; width: 68px;">
                                    @if ($pdfAvatarSrc)
                                        <img src="{{ $pdfAvatarSrc }}" width="64" height="64" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid #cbd5e1; display: block;" />
                                    @else
                                        <div style="width: 64px; height: 64px; border-radius: 50%; background-color: #6366f1; text-align: center; line-height: 64px; color: #ffffff; font-weight: bold; font-size: 26px; letter-spacing: 0.5px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">
                                    <div style="font-size: 17px; font-weight: bold; color: #0f172a; line-height: 1.2;">{{ $user->name }}</div>
                                    <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $user->usertype ?? 'Customer Support' }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 45%; text-align: right; vertical-align: middle;">
                        <div style="font-size: 16px; font-weight: bold; color: #0f172a; line-height: 1.15;">Penilaian Kinerja</div>
                        <div style="font-size: 9.5px; color: #64748b; margin-top: 2px;">Probus System</div>
                        <div style="font-size: 9.5px; color: #94a3b8; margin-top: 1px;">
                            {{ $rapor ? \Carbon\Carbon::parse($rapor->periode)->translatedFormat('F Y') : \Carbon\Carbon::now()->translatedFormat('F Y') }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ============================================= --}}
            {{-- ROW 1: Stat Cards — Ticket CS & Komposisi Bobot --}}
            {{-- ============================================= --}}
            <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 7px;">
                <tr>
                    {{-- Left Card: Ringkasan Aktivitas Tiket CS --}}
                    <td style="width: 50%; vertical-align: top; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 8px 11px;">
                        <div style="font-size: 10.5px; font-weight: bold; color: #0f172a; margin-bottom: 5px;">
                            <span style="color: #2563eb; font-size: 10px;">&#9656;</span> Ringkasan Aktivitas Tiket Staff
                        </div>
                        <table style="width: 100%; font-size: 9px;">
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Tiket ditangani
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_ditangani ?? ($rapor->total_ticket ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #10b981;">&#10003;</span> Tiket selesai
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">
                                    {{ $tiket_selesai ?? ($rapor->total_ticket ?? 0) }}
                                    <span style="font-size: 8px; color: #059669; font-weight: normal;">({{ $completion_rate ?? 0 }}%)</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Tiket di advice
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_di_advice ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Rata-rata respon
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{!! formatDurationPdf($rapor->rata_rata_waktu ?? '-', false) !!}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Modul terbanyak
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #2563eb;">{{ $topHandledModuleName ?? '-' }} ({{ $topHandledModuleCount ?? 0 }})</td>
                            </tr>
                        </table>
                    </td>

                    {{-- Right Card: Komposisi Bobot & Nilai Akhir (Menggantikan Ringkasan Rating Lama) --}}
                    <td style="width: 50%; vertical-align: top; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 8px 11px;">
                        <div style="font-size: 10.5px; font-weight: bold; color: #0f172a; margin-bottom: 5px;">
                            <span style="color: #2563eb; font-size: 10px;">&#9656;</span> Komposisi Bobot & Nilai Akhir
                        </div>
                        <table style="width: 100%; font-size: 8.5px; border-collapse: collapse;">
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #2563eb; font-weight: bold;">[{{ $weights['ticket'] ?? 30 }}%]</span> Kinerja Tiket Staff
                                </td>
                                <td style="text-align: right; color: #64748b; padding: 2px 0;">
                                    Skor {{ number_format($scoreTicket, 0) }} &rarr; <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreTicket * (($weights['ticket'] ?? 30) / 100), 1) }}</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #059669; font-weight: bold;">[{{ $weights['soft_skill'] ?? 40 }}%]</span> Soft Skill
                                </td>
                                <td style="text-align: right; color: #64748b; padding: 2px 0;">
                                    Skor {{ number_format($scoreQuality, 0) }} &rarr; <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreQuality * (($weights['soft_skill'] ?? 40) / 100), 1) }}</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #7c3aed; font-weight: bold;">[{{ $weights['client_review'] ?? 30 }}%]</span> Kepuasan Klien
                                </td>
                                <td style="text-align: right; color: #64748b; padding: 2px 0;">
                                    <span style="color: #f59e0b;">&#9733;</span> {{ number_format($client_rating, 1) }} &rarr; <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreSurvey * (($weights['client_review'] ?? 30) / 100), 1) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #0f172a; font-weight: bold; padding: 3px 0 0 0;">
                                    Total Nilai Akhir ({{ (float)($weights['ticket'] ?? 30) + (float)($weights['soft_skill'] ?? 40) + (float)($weights['client_review'] ?? 30) }}%)
                                </td>
                                <td style="text-align: right; padding: 3px 0 0 0;">
                                    <span style="font-weight: bold; font-size: 12px; color: #0f172a;">{{ number_format($scoreTotal, 1) }}</span>
                                    <span style="background-color: {{ $scoreTotal >= 80 ? '#ecfdf5' : ($scoreTotal >= 70 ? '#eff6ff' : '#fffbeb') }}; color: {{ $scoreTotal >= 80 ? '#059669' : ($scoreTotal >= 70 ? '#2563eb' : '#d97706') }}; padding: 1px 5px; border-radius: 4px; font-weight: bold; font-size: 8px; border: 1px solid {{ $scoreTotal >= 80 ? '#a7f3d0' : ($scoreTotal >= 70 ? '#bfdbfe' : '#fde68a') }}; margin-left: 2px;">
                                        {{ $predikat }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- ============================================= --}}
            {{-- ROW 2: Detail Kinerja Tiket per Modul         --}}
            {{-- ============================================= --}}
            <div style="background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 8px 11px; margin-bottom: 7px;">
                <div style="font-size: 10.5px; font-weight: bold; color: #0f172a; margin-bottom: 4px;">
                    <span style="color: #2563eb; font-size: 10px;">&#9656;</span> Detail Kinerja Tiket per Modul
                </div>

                <table style="width: 100%; font-size: 8.5px;">
                    <tr style="color: #475569; font-weight: bold; border-bottom: 1.5px solid #cbd5e1;">
                        <td style="padding: 2.5px 0; width: 22%;">Modul</td>
                        <td style="text-align: center; width: 9%;">Bobot</td>
                        <td style="text-align: center; width: 13%;">Total Tiket</td>
                        <td style="text-align: center; width: 21%;">Lama Pengerjaan</td>
                        <td style="text-align: center; width: 21%;">Rata-rata Waktu</td>
                        <td style="text-align: right; width: 14%;">Skor Poin</td>
                    </tr>
                    @forelse($rapor && $rapor->moduleDetails ? $rapor->moduleDetails : [] as $md)
                    <tr>
                        <td style="color: #1e293b; font-weight: bold; padding: 2.5px 0; border-bottom: 1px solid #f1f5f9; width: 22%;">{{ optional($md->moduleSystem)->name ?? ('Modul #' . $md->module_id) }}</td>
                        <td style="text-align: center; color: #64748b; padding: 2.5px 0; border-bottom: 1px solid #f1f5f9; width: 9%;">{{ $md->bobot }}</td>
                        <td style="text-align: center; color: #0f172a; font-weight: bold; padding: 2.5px 0; border-bottom: 1px solid #f1f5f9; width: 13%;">{{ $md->total_ticket }}</td>
                        <td style="text-align: center; color: #64748b; padding: 2.5px 0; border-bottom: 1px solid #f1f5f9; width: 21%;">{!! formatDurationPdf($md->lama_waktu ?: ($md->rata_rata_waktu ?: '-')) !!}</td>
                        <td style="text-align: center; color: #64748b; padding: 2.5px 0; border-bottom: 1px solid #f1f5f9; width: 21%;">{!! formatDurationPdf($md->rata_rata_waktu ?? '-') !!}</td>
                        <td style="text-align: right; color: #2563eb; font-weight: bold; padding: 2.5px 0; border-bottom: 1px solid #f1f5f9; width: 14%;">{{ number_format($md->score, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 6px 0; font-style: italic;">Belum ada detail modul tiket.</td>
                    </tr>
                    @endforelse
                </table>

                {{-- Blue divider line --}}
                <div style="border-top: 2px solid #2563eb; margin: 3px 0 2px 0;"></div>

                {{-- Total Row --}}
                <table style="width: 100%; font-size: 9px; font-weight: bold;">
                    <tr>
                        <td style="color: #0f172a; width: 22%; padding: 2px 0;">Total</td>
                        <td style="text-align: center; color: #334155; width: 9%; padding: 2px 0;">Bobot: {{ $total_bobot ?? ($rapor && $rapor->moduleDetails ? $rapor->moduleDetails->sum('bobot') : 0) }}</td>
                        <td style="text-align: center; color: #2563eb; width: 13%; padding: 2px 0;">Jumlah: {{ $total_qty ?? ($rapor && $rapor->moduleDetails ? $rapor->moduleDetails->sum('total_ticket') : 0) }}</td>
                        <td style="text-align: center; color: #334155; width: 21%; padding: 2px 0;">{!! formatDurationPdf($rapor->lama_waktu ?: ($rapor->rata_rata_waktu ?: '-')) !!}</td>
                        <td style="text-align: center; color: #334155; width: 21%; padding: 2px 0;">{!! formatDurationPdf($rapor->rata_rata_waktu ?? '-') !!}</td>
                        <td style="text-align: right; color: #2563eb; width: 14%; padding: 2px 0;">Total: {{ number_format($raw_ticket_score ?? 0, 0, ',', '.') }} poin</td>
                    </tr>
                </table>

                {{-- Normalization formula & Benchmark strip --}}
                <table style="width: 100%; margin-top: 4px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 3px 6px; font-size: 8px;">
                    <tr>
                        <td style="color: #475569; vertical-align: middle;">
                            <span style="font-weight: bold; color: #1e293b;">Normalisasi:</span> 
                            (Total Skor &divide; Benchmark Tertinggi) &times; 100
                            @if($max_ticket_score > 0)
                                <span style="color: #64748b; margin-left: 4px;">[Benchmark: <strong>{{ number_format($max_ticket_score, 0) }}</strong> poin]</span>
                            @endif
                        </td>
                        <td style="text-align: right; vertical-align: middle; white-space: nowrap;">
                            <span style="color: #475569; font-weight: bold;">Nilai Tiket (30%): </span>
                            <span style="font-size: 12px; font-weight: bold; color: #2563eb;">{{ number_format($scoreTicket, 1) }}</span>
                            <span style="color: #94a3b8; font-size: 8px;">/ 100</span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 3: Penilaian Soft Skill (10 Kriteria)     --}}
            {{-- ============================================= --}}
            <div style="background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 7px 11px; margin-bottom: 7px;">
                <div style="font-size: 10.5px; font-weight: bold; color: #0f172a; margin-bottom: 4px;">
                    <span style="color: #2563eb; font-size: 10px;">&#9656;</span> Penilaian Soft Skill (Bobot 40%)
                </div>
                @php
                    $catScores = [];
                    if ($rapor && $rapor->details) {
                        foreach ($rapor->details as $d) {
                            if ($d->category) {
                                $catScores[$d->category->name] = (float)$d->score;
                                $catScores[$d->performance_category_id] = (float)$d->score;
                            }
                        }
                    }
                    $allPdfCats = $categories ?? \App\Models\PerformanceCategory::orderBy('id')->get();
                    $half = (int)ceil($allPdfCats->count() / 2);
                    $leftCats = $allPdfCats->slice(0, $half);
                    $rightCats = $allPdfCats->slice($half);
                @endphp
                <table style="width: 100%; border-collapse: collapse; font-size: 8.5px;">
                    <tr>
                        <td style="width: 48%; vertical-align: top; padding: 0;">
                            <table style="width: 100%; border-collapse: collapse;">
                                @foreach($leftCats as $cat)
                                @php
                                    $score = isset($catScores[$cat->id]) ? $catScores[$cat->id] : (isset($catScores[$cat->name]) ? $catScores[$cat->name] : (float)($rapor->performance_score ?? 0));
                                    $scale5 = round($score / 20, 1);
                                @endphp
                                <tr>
                                    <td style="color: #1e293b; padding: 1.5px 0;">{{ $cat->name }}</td>
                                    <td style="text-align: right; width: 45px; padding: 1.5px 0;">
                                        <span style="font-weight: bold; color: #0f172a; font-size: 9.5px;">{{ $scale5 }}</span>
                                        <span style="color: #94a3b8; font-size: 7.5px;"> / 5</span>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                        <td style="width: 4%; padding: 0;"></td>
                        <td style="width: 48%; vertical-align: top; padding: 0;">
                            <table style="width: 100%; border-collapse: collapse;">
                                @foreach($rightCats as $cat)
                                @php
                                    $score = isset($catScores[$cat->id]) ? $catScores[$cat->id] : (isset($catScores[$cat->name]) ? $catScores[$cat->name] : (float)($rapor->performance_score ?? 0));
                                    $scale5 = round($score / 20, 1);
                                @endphp
                                <tr>
                                    <td style="color: #1e293b; padding: 1.5px 0;">{{ $cat->name }}</td>
                                    <td style="text-align: right; width: 45px; padding: 1.5px 0;">
                                        <span style="font-weight: bold; color: #0f172a; font-size: 9.5px;">{{ $scale5 }}</span>
                                        <span style="color: #94a3b8; font-size: 7.5px;"> / 5</span>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 4: Catatan & Saran                        --}}
            {{-- ============================================= --}}
            <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 7px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <div style="font-size: 10px; font-weight: bold; color: #0f172a; margin-bottom: 2px;">Catatan dari Penilai</div>
                        <div style="background: #f8fafc; border: 1.2px solid #e2e8f0; border-radius: 8px; padding: 5px 8px; font-size: 8.5px; color: #475569; line-height: 1.35; min-height: 32px;">
                            {{ $rapor->catatan ?? 'Menunjukkan komitmen dan dedikasi tinggi dalam menyelesaikan keluhan tiket pelanggan serta aktif berkoordinasi dengan tim support.' }}
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <div style="font-size: 10px; font-weight: bold; color: #0f172a; margin-bottom: 2px;">Saran untuk Perbaikan</div>
                        <div style="background: #f8fafc; border: 1.2px solid #e2e8f0; border-radius: 8px; padding: 5px 8px; font-size: 8.5px; color: #475569; line-height: 1.35; min-height: 32px;">
                            {{ $rapor->saran ?? 'Tingkatkan pendokumentasian kendala berulang pada sistem dan perdalam pemahaman teknis alur transaksi modul.' }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ============================================= --}}
            {{-- ROW 5: 4 Score Cards at Bottom                --}}
            {{-- ============================================= --}}
            @php
                $nilaiTiket = round($scoreTicket ?? ($rapor->module_score ?? 0), 2);
                $nilaiSoftSkill = round($scoreQuality ?? ($rapor->performance_score ?? 0), 2);
                $nilaiKepuasan = round($scoreSurvey ?? ($rapor->review_score ?? 0), 2);
                $nilaiAkhir = round($scoreTotal ?? ($rapor->total_score ?? 0), 2);

                // SVG arc gauge calculations
                $gaugePercent = min(100, max(0, $nilaiAkhir));
                $arcR = 38;
                $cx = 50;
                $cy = 46;

                // Start point (left end of semi-circle, 180 degrees)
                $sx = $cx - $arcR;
                $sy = $cy;

                // End point (right end of semi-circle, 0 degrees)
                $ex = $cx + $arcR;
                $ey = $cy;

                // Progress arc end point
                $progressAngle = 180 - ($gaugePercent / 100) * 180;
                $pRad = deg2rad($progressAngle);
                $px = $cx + $arcR * cos($pRad);
                $py = $cy - $arcR * sin($pRad);
                $pLargeArc = ($gaugePercent > 50) ? 1 : 0;

                // Gauge color based on score
                if ($nilaiAkhir >= 80) {
                    $gaugeColor = '#059669';
                } elseif ($nilaiAkhir >= 70) {
                    $gaugeColor = '#2563eb';
                } elseif ($nilaiAkhir >= 60) {
                    $gaugeColor = '#d97706';
                } else {
                    $gaugeColor = '#ef4444';
                }
            @endphp

            <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0;">
                <tr>
                    {{-- ========== Card 1: Nilai Tiket ========== --}}
                    <td style="width: 24%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 8px 6px; vertical-align: top;">
                        <table style="width: 100%; margin-bottom: 4px;">
                            <tr>
                                <td style="width: 26px; vertical-align: middle;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: #dbeafe; text-align: center; line-height: 24px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" style="vertical-align: middle;">
                                            <rect x="3" y="3" width="18" height="18" rx="3" fill="none" stroke="#2563eb" stroke-width="2"/>
                                            <line x1="7" y1="8" x2="17" y2="8" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                                            <line x1="7" y1="12" x2="17" y2="12" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding-left: 4px;">
                                    <span style="font-size: 8.5px; font-weight: bold; color: #475569;">Kinerja Tiket</span>
                                </td>
                            </tr>
                        </table>
                        <div style="text-align: center; padding-top: 1px;">
                            <span style="font-size: 24px; font-weight: bold; color: #2563eb; line-height: 1;">{{ number_format($nilaiTiket, 0) }}</span>
                            <span style="font-size: 9.5px; font-weight: bold; color: #b0b8c4;">/ 100</span>
                            <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">Bobot Evaluasi: 30%</div>
                        </div>
                    </td>

                    {{-- ========== Card 2: Nilai Soft Skill ========== --}}
                    <td style="width: 24%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 8px 6px; vertical-align: top;">
                        <table style="width: 100%; margin-bottom: 4px;">
                            <tr>
                                <td style="width: 26px; vertical-align: middle;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: #d1fae5; text-align: center; line-height: 24px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" style="vertical-align: middle;">
                                            <path d="M12 3L4 7v5c0 5.25 3.4 10.15 8 11.25C16.6 22.15 20 17.25 20 12V7l-8-4z" fill="none" stroke="#059669" stroke-width="2" stroke-linejoin="round"/>
                                            <polyline points="9,12 11,14 15,10" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding-left: 4px;">
                                    <span style="font-size: 8.5px; font-weight: bold; color: #475569;">Soft Skill</span>
                                </td>
                            </tr>
                        </table>
                        <div style="text-align: center; padding-top: 1px;">
                            <span style="font-size: 24px; font-weight: bold; color: #059669; line-height: 1;">{{ number_format($nilaiSoftSkill, 0) }}</span>
                            <span style="font-size: 9.5px; font-weight: bold; color: #b0b8c4;">/ 100</span>
                            <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">Bobot Evaluasi: 40%</div>
                        </div>
                    </td>

                    {{-- ========== Card 3: Kepuasan Klien ========== --}}
                    <td style="width: 24%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 8px 6px; vertical-align: top;">
                        <table style="width: 100%; margin-bottom: 4px;">
                            <tr>
                                <td style="width: 26px; vertical-align: middle;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: #ede9fe; text-align: center; line-height: 24px;">
                                        <svg width="13" height="13" viewBox="0 0 24 24" style="vertical-align: middle;">
                                            <circle cx="12" cy="8" r="4" fill="none" stroke="#7c3aed" stroke-width="2"/>
                                            <path d="M4 20c0-3.3 3.6-6 8-6s8 2.7 8 6" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding-left: 4px;">
                                    <span style="font-size: 8.5px; font-weight: bold; color: #475569;">Kepuasan Klien</span>
                                </td>
                            </tr>
                        </table>
                        <div style="text-align: center; padding-top: 1px;">
                            <span style="font-size: 24px; font-weight: bold; color: #7c3aed; line-height: 1;">{{ number_format($nilaiKepuasan, 0) }}</span>
                            <span style="font-size: 9.5px; font-weight: bold; color: #b0b8c4;">/ 100</span>
                            <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">
                                <span style="color: #f59e0b;">&#9733;</span> {{ number_format($client_rating, 1) }} / 5.0 (30%)
                            </div>
                        </div>
                    </td>

                    {{-- ========== Card 4: Nilai Akhir (Speedometer Gauge) ========== --}}
                    <td style="width: 28%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 6px 5px 4px 5px; text-align: center; vertical-align: top;">
                        <div style="position: relative; width: 100px; height: 50px; margin: 0 auto;">
                            <svg width="100" height="50" viewBox="0 0 100 50" style="display: block; margin: 0 auto;">
                                {{-- Background arc track --}}
                                <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 0 1 {{ $ex }} {{ $ey }}"
                                      fill="none" stroke="#e2e8f0" stroke-width="8" stroke-linecap="round" />
                                {{-- Colored progress arc --}}
                                @if($gaugePercent > 0)
                                <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 {{ $pLargeArc }} 1 {{ $px }} {{ $py }}"
                                      fill="none" stroke="{{ $gaugeColor }}" stroke-width="8" stroke-linecap="round" />
                                @endif
                            </svg>
                        </div>
                        <div style="margin-top: -38px; text-align: center; position: relative;">
                            <div style="font-size: 6px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.7px;">NILAI AKHIR</div>
                            <div style="font-size: 19px; font-weight: bold; color: #0f172a; line-height: 1.1;">{{ number_format($nilaiAkhir, 1) }}</div>
                            <div style="display: inline-block; margin-top: 1px; padding: 1px 5px; font-size: 7.5px; font-weight: bold; border-radius: 3px; background: {{ $nilaiAkhir >= 80 ? '#ecfdf5' : ($nilaiAkhir >= 70 ? '#eff6ff' : '#fffbeb') }}; color: {{ $nilaiAkhir >= 80 ? '#059669' : ($nilaiAkhir >= 70 ? '#2563eb' : '#d97706') }};">
                                {{ $predikat }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
