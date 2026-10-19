<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penilaian Kinerja - {{ $user->name }}</title>
    <style>
        @page {
            margin: 12px 14px;
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
            font-size: 10px;
            line-height: 1.35;
            background-color: #eef2f7;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    {{-- Outer background wrapper --}}
    <div style="background-color: #eef2f7; padding: 8px;">

        {{-- Main white card --}}
        <div style="border: 1.5px solid #d6dce6; border-radius: 16px; padding: 14px 18px 10px 18px; background-color: #ffffff;">

            @php
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
            <table style="width: 100%; margin-bottom: 10px;">
                <tr>
                    <td style="width: 55%; vertical-align: middle;">
                        <table>
                            <tr>
                                <td style="vertical-align: middle; padding-right: 12px; width: 74px;">
                                    @if ($pdfAvatarSrc)
                                        <img src="{{ $pdfAvatarSrc }}" width="70" height="70" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid #cbd5e1; display: block;" />
                                    @else
                                        <div style="width: 70px; height: 70px; border-radius: 50%; background-color: #6366f1; text-align: center; line-height: 70px; color: #ffffff; font-weight: bold; font-size: 28px; letter-spacing: 0.5px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">
                                    <div style="font-size: 18px; font-weight: bold; color: #0f172a; line-height: 1.2;">{{ $user->name }}</div>
                                    <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">{{ $user->usertype ?? 'Customer Support' }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 45%; text-align: right; vertical-align: middle;">
                        <div style="font-size: 17px; font-weight: bold; color: #0f172a; line-height: 1.15;">Penilaian Kinerja</div>
                        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Probus System</div>
                        <div style="font-size: 10px; color: #94a3b8; margin-top: 1px;">
                            {{ $rapor ? \Carbon\Carbon::parse($rapor->periode)->translatedFormat('F Y') : \Carbon\Carbon::now()->translatedFormat('F Y') }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ============================================= --}}
            {{-- ROW 1: Stat Cards — Ticket & Rating           --}}
            {{-- ============================================= --}}
            <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 8px;">
                <tr>
                    {{-- Left Card: Ringkasan Statistik Ticket --}}
                    <td style="width: 50%; vertical-align: top; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 9px 12px;">
                        <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-bottom: 6px;">
                            <span style="color: #2563eb; font-size: 11px;">&#9656;</span> Ringkasan Statistik Ticket
                        </div>
                        <table style="width: 100%; font-size: 9.5px;">
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Tiket ditangani
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_ditangani ?? ($rapor->total_ticket ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#10003;</span> Tiket selesai
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_selesai ?? ($rapor->total_ticket ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Rata-rata respon
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $rapor->rata_rata_waktu ?? '12 menit' }}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 2px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Persentase tiket selesai
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">
                                    @php
                                        $ditangani = $tiket_ditangani ?? ($rapor->total_ticket ?? 0);
                                        $selesai = $tiket_selesai ?? ($rapor->total_ticket ?? 0);
                                    @endphp
                                    {{ $ditangani > 0 ? round(($selesai / $ditangani) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        </table>
                    </td>

                    {{-- Right Card: Ringkasan Statistik Rating --}}
                    <td style="width: 50%; vertical-align: top; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 9px 12px;">
                        <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-bottom: 6px;">
                            <span style="color: #2563eb; font-size: 11px;">&#9656;</span> Ringkasan Statistik Rating
                        </div>
                        <table style="width: 100%; font-size: 9.5px;">
                            <tr>
                                <td style="color: #475569; padding: 4px 0; vertical-align: middle;">
                                    <span style="color: #94a3b8;">&#9656;</span> Average Rating
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    @php
                                        $rScore = floatval($avg_rating ?? ($rapor ? round($rapor->total_score / 20, 1) : 4.0));
                                        $fullStars = floor($rScore);
                                    @endphp
                                    <span style="font-size: 13px; letter-spacing: 1px;">
                                        @for($s=1; $s<=5; $s++)
                                            @if($s <= $fullStars)
                                                <span style="color: #f59e0b;">&#9733;</span>
                                            @else
                                                <span style="color: #e2e8f0;">&#9733;</span>
                                            @endif
                                        @endfor
                                    </span>
                                    <span style="font-weight: bold; color: #0f172a; margin-left: 4px; font-size: 11px;">{{ $avg_rating ?? ($rapor ? number_format($rapor->total_score / 20, 1) : '4.0') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 4px 0;">
                                    <span style="color: #94a3b8;">&#9656;</span> Jumlah Rating
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a; font-size: 11px;">
                                    {{ ($tiket_selesai ?? 0) > 0 ? $tiket_selesai : ($tiket_ditangani ?? ($rapor->total_ticket ?? 0)) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- ============================================= --}}
            {{-- ROW 2: Detail Ticket                          --}}
            {{-- ============================================= --}}
            <div style="background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 9px 12px; margin-bottom: 8px;">
                <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-bottom: 5px;">
                    <span style="color: #2563eb; font-size: 11px;">&#9656;</span> Detail Ticket
                </div>

                <table style="width: 100%; font-size: 9px;">
                    <tr style="color: #64748b; font-weight: bold; border-bottom: 1.5px solid #cbd5e1;">
                        <td style="padding: 3px 0; width: 24%;">Modul</td>
                        <td style="text-align: center; width: 12%;">Beban</td>
                        <td style="text-align: center; width: 16%;">Total Ticket</td>
                        <td style="text-align: center; width: 16%;">Lama Waktu</td>
                        <td style="text-align: center; width: 16%;">Rata-rata Waktu</td>
                        <td style="text-align: right; width: 16%;">Rata-rata Skor</td>
                    </tr>
                    @forelse($rapor && $rapor->moduleDetails ? $rapor->moduleDetails : [] as $md)
                    <tr>
                        <td style="color: #1e293b; font-weight: bold; padding: 3px 0; border-bottom: 1px solid #f1f5f9;">{{ optional($md->moduleSystem)->name ?? ('Modul #' . $md->module_id) }}</td>
                        <td style="text-align: center; color: #64748b; padding: 3px 0; border-bottom: 1px solid #f1f5f9;">{{ $md->bobot }}</td>
                        <td style="text-align: center; color: #0f172a; font-weight: bold; padding: 3px 0; border-bottom: 1px solid #f1f5f9;">{{ $md->total_ticket }}</td>
                        <td style="text-align: center; color: #64748b; padding: 3px 0; border-bottom: 1px solid #f1f5f9;">{{ $md->lama_waktu ?: ($md->rata_rata_waktu ?: '-') }}</td>
                        <td style="text-align: center; color: #64748b; padding: 3px 0; border-bottom: 1px solid #f1f5f9;">{{ $md->rata_rata_waktu ?? '-' }}</td>
                        <td style="text-align: right; color: #2563eb; font-weight: bold; padding: 3px 0; border-bottom: 1px solid #f1f5f9;">{{ number_format($md->score, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 8px 0; font-style: italic;">Belum ada detail modul tiket.</td>
                    </tr>
                    @endforelse
                </table>

                {{-- Blue divider line --}}
                <div style="border-top: 2.5px solid #2563eb; margin: 4px 0 3px 0;"></div>

                {{-- Total Row --}}
                <table style="width: 100%; font-size: 9.5px; font-weight: bold;">
                    <tr>
                        <td style="color: #0f172a; width: 24%; padding: 2px 0;">Total</td>
                        <td style="text-align: center; color: #334155; width: 12%; padding: 2px 0;">Beban: {{ $total_bobot ?? ($rapor && $rapor->moduleDetails ? $rapor->moduleDetails->sum('bobot') : 0) }}</td>
                        <td style="text-align: center; color: #2563eb; width: 16%; padding: 2px 0;">Jumlah: {{ $total_qty ?? ($rapor && $rapor->moduleDetails ? $rapor->moduleDetails->sum('total_ticket') : 0) }}</td>
                        <td style="text-align: center; color: #334155; width: 16%; padding: 2px 0;">{{ $rapor->lama_waktu ?: ($rapor->rata_rata_waktu ?: '-') }}</td>
                        <td style="text-align: center; color: #334155; width: 16%; padding: 2px 0;">{{ $rapor->rata_rata_waktu ?? '-' }}</td>
                        <td style="text-align: right; color: #2563eb; width: 16%; padding: 2px 0;">Skor: {{ number_format($rapor->raw_ticket_score ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </table>

                {{-- Nilai Akhir Ticket --}}
                <div style="text-align: right; margin-top: 4px;">
                    <span style="font-size: 11px; font-weight: bold; color: #0f172a;">Nilai Akhir Ticket: </span>
                    <span style="font-size: 15px; font-weight: bold; color: #0f172a;">{{ $rapor->module_score ?? 0 }}</span>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 3: Penilaian Soft Skill (10 Kriteria)     --}}
            {{-- ============================================= --}}
            <div style="background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; margin-bottom: 8px;">
                <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-bottom: 5px;">
                    <span style="color: #2563eb; font-size: 11px;">&#9656;</span> Penilaian Soft Skill
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
                <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
                    <tr>
                        <td style="width: 48%; vertical-align: top; padding: 0;">
                            <table style="width: 100%; border-collapse: collapse;">
                                @foreach($leftCats as $cat)
                                @php
                                    $score = isset($catScores[$cat->id]) ? $catScores[$cat->id] : (isset($catScores[$cat->name]) ? $catScores[$cat->name] : (float)($rapor->performance_score ?? 0));
                                    $scale5 = round($score / 20, 1);
                                @endphp
                                <tr>
                                    <td style="color: #1e293b; padding: 2px 0;">{{ $cat->name }}</td>
                                    <td style="text-align: right; width: 45px; padding: 2px 0;">
                                        <span style="font-weight: bold; color: #0f172a; font-size: 10px;">{{ $scale5 }}</span>
                                        <span style="color: #94a3b8; font-size: 8px;"> / 5</span>
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
                                    <td style="color: #1e293b; padding: 2px 0;">{{ $cat->name }}</td>
                                    <td style="text-align: right; width: 45px; padding: 2px 0;">
                                        <span style="font-weight: bold; color: #0f172a; font-size: 10px;">{{ $scale5 }}</span>
                                        <span style="color: #94a3b8; font-size: 8px;"> / 5</span>
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
            <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 10px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <div style="font-size: 10.5px; font-weight: bold; color: #0f172a; margin-bottom: 3px;">Catatan dari Penilai</div>
                        <div style="background: #f8fafc; border: 1.2px solid #e2e8f0; border-radius: 8px; padding: 7px 9px; font-size: 9px; color: #475569; line-height: 1.45; min-height: 42px;">
                            {{ $rapor->catatan ?? 'Menunjukkan komitmen dan dedikasi tinggi dalam menyelesaikan keluhan tiket pelanggan serta aktif berkoordinasi dengan tim support.' }}
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <div style="font-size: 10.5px; font-weight: bold; color: #0f172a; margin-bottom: 3px;">Saran untuk Perbaikan</div>
                        <div style="background: #f8fafc; border: 1.2px solid #e2e8f0; border-radius: 8px; padding: 7px 9px; font-size: 9px; color: #475569; line-height: 1.45; min-height: 42px;">
                            {{ $rapor->saran ?? 'Tingkatkan pendokumentasian kendala berulang pada sistem dan perdalam pemahaman teknis alur transaksi modul.' }}
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ============================================= --}}
            {{-- ROW 5: 4 Score Cards at Bottom                --}}
            {{-- ============================================= --}}
            @php
                $nilaiTiket = round($rapor->module_score ?? 0, 2);
                $nilaiSoftSkill = round($rapor->performance_score ?? 0, 2);
                $nilaiKepuasan = round($rapor->review_score ?? 0, 2);
                $nilaiAkhir = round($rapor->total_score ?? 0, 2);

                // SVG arc gauge calculations
                $gaugePercent = min(100, max(0, $nilaiAkhir));
                $arcR = 42;
                $cx = 55;
                $cy = 50;

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
                    $gaugeColor = '#0d9488';
                } elseif ($nilaiAkhir >= 60) {
                    $gaugeColor = '#0891b2';
                } elseif ($nilaiAkhir >= 40) {
                    $gaugeColor = '#f59e0b';
                } else {
                    $gaugeColor = '#ef4444';
                }
            @endphp

            <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0;">
                <tr>
                    {{-- ========== Card 1: Nilai Tiket ========== --}}
                    <td style="width: 24%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px 10px 8px; vertical-align: top;">
                        {{-- Icon + Label row (side by side) --}}
                        <table style="width: 100%; margin-bottom: 6px;">
                            <tr>
                                <td style="width: 32px; vertical-align: middle;">
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #dbeafe; text-align: center; line-height: 30px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" style="vertical-align: middle; margin-top: 1px;">
                                            <rect x="3" y="3" width="18" height="18" rx="3" fill="none" stroke="#2563eb" stroke-width="2"/>
                                            <line x1="7" y1="8" x2="17" y2="8" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                                            <line x1="7" y1="12" x2="17" y2="12" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                                            <line x1="7" y1="16" x2="13" y2="16" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding-left: 4px;">
                                    <span style="font-size: 9px; font-weight: bold; color: #475569; letter-spacing: 0.2px;">Nilai Tiket</span>
                                </td>
                            </tr>
                        </table>
                        {{-- Score --}}
                        <div style="text-align: center; padding-top: 2px;">
                            <span style="font-size: 30px; font-weight: bold; color: #2563eb; line-height: 1;">{{ number_format($nilaiTiket, 0) }}</span>
                            <span style="font-size: 11px; font-weight: bold; color: #b0b8c4;">/ 100</span>
                        </div>
                    </td>

                    {{-- ========== Card 2: Nilai Soft Skill ========== --}}
                    <td style="width: 24%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px 10px 8px; vertical-align: top;">
                        {{-- Icon + Label row --}}
                        <table style="width: 100%; margin-bottom: 6px;">
                            <tr>
                                <td style="width: 32px; vertical-align: middle;">
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #d1fae5; text-align: center; line-height: 30px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" style="vertical-align: middle; margin-top: 1px;">
                                            <path d="M12 3L4 7v5c0 5.25 3.4 10.15 8 11.25C16.6 22.15 20 17.25 20 12V7l-8-4z" fill="none" stroke="#059669" stroke-width="2" stroke-linejoin="round"/>
                                            <polyline points="9,12 11,14 15,10" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding-left: 4px;">
                                    <span style="font-size: 9px; font-weight: bold; color: #475569; letter-spacing: 0.2px;">Nilai Soft Skill</span>
                                </td>
                            </tr>
                        </table>
                        {{-- Score --}}
                        <div style="text-align: center; padding-top: 2px;">
                            <span style="font-size: 30px; font-weight: bold; color: #059669; line-height: 1;">{{ number_format($nilaiSoftSkill, 0) }}</span>
                            <span style="font-size: 11px; font-weight: bold; color: #b0b8c4;">/ 100</span>
                        </div>
                    </td>

                    {{-- ========== Card 3: Kepuasan Klien ========== --}}
                    <td style="width: 24%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 12px; padding: 10px 8px 10px 8px; vertical-align: top;">
                        {{-- Icon + Label row --}}
                        <table style="width: 100%; margin-bottom: 6px;">
                            <tr>
                                <td style="width: 32px; vertical-align: middle;">
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #ede9fe; text-align: center; line-height: 30px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" style="vertical-align: middle; margin-top: 1px;">
                                            <circle cx="12" cy="8" r="4" fill="none" stroke="#7c3aed" stroke-width="2"/>
                                            <path d="M4 20c0-3.3 3.6-6 8-6s8 2.7 8 6" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding-left: 4px;">
                                    <span style="font-size: 9px; font-weight: bold; color: #475569; letter-spacing: 0.2px;">Kepuasan Klien</span>
                                </td>
                            </tr>
                        </table>
                        {{-- Score --}}
                        <div style="text-align: center; padding-top: 2px;">
                            <span style="font-size: 30px; font-weight: bold; color: #7c3aed; line-height: 1;">{{ number_format($nilaiKepuasan, 0) }}</span>
                            <span style="font-size: 11px; font-weight: bold; color: #b0b8c4;">/ 100</span>
                        </div>
                    </td>

                    {{-- ========== Card 4: Nilai Akhir (Speedometer Gauge) ========== --}}
                    <td style="width: 28%; background: #ffffff; border: 1.2px solid #e2e8f0; border-radius: 12px; padding: 6px 6px 4px 6px; text-align: center; vertical-align: top;">
                        <div style="position: relative; width: 110px; height: 58px; margin: 0 auto;">
                            <svg width="110" height="58" viewBox="0 0 110 58" style="display: block; margin: 0 auto;">
                                {{-- Background arc track (full semicircle) --}}
                                <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 0 1 {{ $ex }} {{ $ey }}"
                                      fill="none" stroke="#e2e8f0" stroke-width="10" stroke-linecap="round" />
                                {{-- Colored progress arc --}}
                                @if($gaugePercent > 0)
                                <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 {{ $pLargeArc }} 1 {{ $px }} {{ $py }}"
                                      fill="none" stroke="{{ $gaugeColor }}" stroke-width="10" stroke-linecap="round" />
                                @endif
                            </svg>
                        </div>
                        <div style="margin-top: -42px; text-align: center; position: relative;">
                            <div style="font-size: 6.5px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px;">NILAI AKHIR</div>
                            <div style="font-size: 22px; font-weight: bold; color: #0f172a; line-height: 1.15;">{{ number_format($nilaiAkhir, 1) }}</div>
                            <div style="font-size: 8px; font-weight: bold; color: #b0b8c4; margin-top: -1px;">/ 100</div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
