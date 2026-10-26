<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penilaian Kinerja - {{ $user->name }}</title>
    <style>
        @page {
            margin: 20px 24px 15px 24px;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;
        }
        body {
            font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 9.5px;
            line-height: 1.25;
            background-color: #ffffff;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
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
                    return '<div style="line-height: 1.1; text-align: center;">'
                        . '<span style="font-weight: bold; color: #1e293b;">' . e($readable) . '</span>'
                        . '<div style="font-size: 7px; color: #94a3b8;">(' . $formattedM . ')</div>'
                        . '</div>';
                }
                return e($readable);
            }
        }

        // Bobot dinamis
        $wTicket = isset($weights['ticket']) ? (float)$weights['ticket'] : 30.0;
        $wSoftSkill = isset($weights['soft_skill']) ? (float)$weights['soft_skill'] : 40.0;
        $wClient = isset($weights['client_review']) ? (float)$weights['client_review'] : 30.0;
        $wTotal = $wTicket + $wSoftSkill + $wClient;

        // Logo Probus
        $logoPath = public_path('img/logo.png');
        $probusLogoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        // Avatar User
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

        // Modul list
        $modulesList = $allModules ?? \App\Models\ModuleSystem::orderBy('id', 'asc')->get();
        $savedDetails = ($rapor && $rapor->moduleDetails) ? $rapor->moduleDetails->keyBy('module_id') : collect();
    @endphp

    {{-- ============================================================ --}}
    {{-- 1. KOP PERUSAHAAN (Logo di Kiri, Alamat & Kontak di Kanan)   --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; border-bottom: 2px solid #004880; padding-bottom: 8px; margin-bottom: 8px;">
        <tr>
            <td style="width: 45%; vertical-align: top;">
                @if ($probusLogoSrc)
                    <img src="{{ $probusLogoSrc }}" style="width: 62px; height: 62px; object-fit: contain; display: block;" />
                @endif
            </td>
            <td style="width: 55%; vertical-align: top; text-align: right; line-height: 1.35;">
                <div style="font-size: 13.5px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px;">CV. MITRA SOLUSI MANDIRI</div>
                <div style="font-size: 9px; color: #334155; margin-top: 1px;">JL PB. SUDIRMAN, PERTOKOAN SUDIRMAN AGUNG BLOK C NO. 51</div>
                <div style="font-size: 9px; color: #334155;">DENPASAR</div>
                <div style="font-size: 9px; color: #334155;">0361 -241 689</div>
                <div style="font-size: 9px; color: #004880; font-weight: bold;">support@probussystem.com</div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- 2. PROFILE, JABATAN, DEPARTEMEN (Bawah Kiri)                 --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; margin-bottom: 6px;">
        <tr>
            <td style="width: 100%; vertical-align: middle;">
                <table style="border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; padding-right: 10px; width: 44px;">
                            @if ($pdfAvatarSrc)
                                <img src="{{ $pdfAvatarSrc }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1.5px solid #cbd5e1; display: block;" />
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #004880; text-align: center; line-height: 40px; color: #ffffff; font-weight: bold; font-size: 16px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td style="vertical-align: middle; line-height: 1.3;">
                            <div style="font-size: 12.5px; font-weight: bold; color: #0f172a;">{{ $user->name }}</div>
                            <div style="font-size: 9.5px; color: #475569;">Jabatan: <span style="font-weight: bold; color: #1e293b;">{{ ucwords($user->usertype ?? 'Customer Support') }}</span></div>
                            <div style="font-size: 9.5px; color: #475569;">Departemen: <span style="font-weight: bold; color: #1e293b;">{{ optional($user->departemen)->name ?? 'Customer Support' }}</span></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- 3. JUDUL DI TENGAH: PENILAIAN KINERJA & PERIODE              --}}
    {{-- ============================================================ --}}
    <div style="text-align: center; margin: 4px 0 8px 0; border-top: 1px dashed #cbd5e1; padding-top: 5px;">
        <div style="font-size: 14px; font-weight: bold; color: #004880; letter-spacing: 0.8px; text-transform: uppercase;">PENILAIAN KINERJA</div>
        <div style="font-size: 9.5px; color: #475569; margin-top: 1px;">
            Periode: {{ !empty($periodInfo['label']) ? $periodInfo['label'] : ($rapor ? \Carbon\Carbon::parse($rapor->periode)->translatedFormat('F Y') : \Carbon\Carbon::now()->translatedFormat('F Y')) }} (Cut-off: {{ !empty($periodInfo['cutoff_label']) ? $periodInfo['cutoff_label'] : \Carbon\Carbon::now()->endOfMonth()->translatedFormat('d F Y') }})
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 4. DETAIL KINERJA TIKET PER MODUL (MEMUNCULKAN SEMUA MODUL)  --}}
    {{-- ============================================================ --}}
    <div style="margin-bottom: 7px; border: 1.2px solid #004880; border-radius: 4px; overflow: hidden;">
        <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 4px 8px; font-size: 10px;">
            Detail Kinerja Tiket per Modul
        </div>

        <table style="width: 100%; font-size: 9px; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #004880; color: #ffffff; font-weight: bold; border-top: 1px solid #1a5695;">
                    <th style="padding: 3.5px 6px; text-align: left; color: #ffffff; width: 22%;">Modul</th>
                    <th style="padding: 3.5px 6px; text-align: center; color: #ffffff; width: 9%;">Bobot</th>
                    <th style="padding: 3.5px 6px; text-align: center; color: #ffffff; width: 13%;">Total Tiket</th>
                    <th style="padding: 3.5px 6px; text-align: center; color: #ffffff; width: 21%;">Lama Pengerjaan</th>
                    <th style="padding: 3.5px 6px; text-align: center; color: #ffffff; width: 21%;">Rata-rata Waktu</th>
                    <th style="padding: 3.5px 6px; text-align: right; color: #ffffff; width: 14%;">Skor Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($modulesList as $mod)
                @php
                    $detail = $savedDetails->get($mod->id);
                    $mBobot = $detail ? (int)$detail->bobot : (int)($mod->bobot ?? 0);
                    $mTotal = $detail ? (int)$detail->total_ticket : 0;
                    $mLama = $detail ? ($detail->lama_waktu ?: '-') : '-';
                    $mRata = $detail ? ($detail->rata_rata_waktu ?: '-') : '-';
                    $mScore = $detail ? (float)$detail->score : 0;
                @endphp
                <tr style="border-bottom: 1px solid #f1f5f9; {{ $loop->iteration % 2 == 0 ? 'background-color: #f8fafc;' : 'background-color: #ffffff;' }}">
                    <td style="color: #1e293b; font-weight: bold; padding: 2.5px 6px; width: 22%;">{{ $mod->name }}</td>
                    <td style="text-align: center; color: #64748b; padding: 2.5px 6px; width: 9%;">{{ $mBobot }}</td>
                    <td style="text-align: center; color: #0f172a; font-weight: {{ $mTotal > 0 ? 'bold' : 'normal' }}; padding: 2.5px 6px; width: 13%;">{{ $mTotal }}</td>
                    <td style="text-align: center; color: #64748b; padding: 2.5px 6px; width: 21%;">{!! formatDurationPdf($mLama) !!}</td>
                    <td style="text-align: center; color: #64748b; padding: 2.5px 6px; width: 21%;">{!! formatDurationPdf($mRata) !!}</td>
                    <td style="text-align: right; color: {{ $mScore > 0 ? '#004880' : '#94a3b8' }}; font-weight: bold; padding: 2.5px 6px; width: 14%;">{{ number_format($mScore, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; border-top: 1.5px solid #004880; font-weight: bold;">
                    <td style="color: #0f172a; width: 22%; padding: 3px 6px;">Total</td>
                    <td style="text-align: center; color: #334155; width: 9%; padding: 3px 6px;">Bobot: {{ $modulesList->sum('bobot') }}</td>
                    <td style="text-align: center; color: #004880; width: 13%; padding: 3px 6px;">Jumlah: {{ $total_qty ?? ($savedDetails->sum('total_ticket')) }}</td>
                    <td style="text-align: center; color: #334155; width: 21%; padding: 3px 6px;">{!! formatDurationPdf(($rapor && $rapor->lama_waktu) ? $rapor->lama_waktu : (($rapor && $rapor->rata_rata_waktu) ? $rapor->rata_rata_waktu : '-')) !!}</td>
                    <td style="text-align: center; color: #334155; width: 21%; padding: 3px 6px;">{!! formatDurationPdf($rapor && $rapor->rata_rata_waktu ? $rapor->rata_rata_waktu : '-') !!}</td>
                    <td style="text-align: right; color: #004880; width: 14%; padding: 3px 6px;">Total: {{ number_format($raw_ticket_score ?? 0, 0, ',', '.') }} poin</td>
                </tr>
            </tfoot>
        </table>

        {{-- Normalization formula & Benchmark strip --}}
        <table style="width: 100%; background: #ffffff; border-top: 1px dashed #cbd5e1; padding: 3px 8px; font-size: 8.5px;">
            <tr>
                <td style="color: #475569; vertical-align: middle;">
                    <span style="font-weight: bold; color: #1e293b;">Normalisasi:</span> 
                    (Total Skor &divide; Benchmark Tertinggi) &times; 100
                    @if($max_ticket_score > 0)
                        <span style="color: #64748b; margin-left: 4px;">[Benchmark: <strong>{{ number_format($max_ticket_score, 0) }}</strong> poin]</span>
                    @endif
                </td>
                <td style="text-align: right; vertical-align: middle; white-space: nowrap;">
                    <span style="color: #475569; font-weight: bold;">Nilai Tiket ({{ number_format($wTicket, 0) }}%): </span>
                    <span style="font-size: 11px; font-weight: bold; color: #004880;">{{ number_format($scoreTicket, 1) }}</span>
                    <span style="color: #94a3b8; font-size: 8px;">/ 100</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ============================================================ --}}
    {{-- 5. PENILAIAN SOFT SKILL (BOBOT)                              --}}
    {{-- ============================================================ --}}
    <div style="margin-bottom: 7px; border: 1.2px solid #004880; border-radius: 4px; overflow: hidden;">
        <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 4px 8px; font-size: 10px;">
            Penilaian Soft Skill (Bobot {{ number_format($wSoftSkill, 0) }}%)
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
            <thead>
                <tr style="background-color: #004880; color: #ffffff; font-weight: bold; border-top: 1px solid #1a5695;">
                    <th style="padding: 3px 8px; text-align: left; color: #ffffff; width: 38%;">Kriteria Soft Skill</th>
                    <th style="padding: 3px 8px; text-align: right; color: #ffffff; width: 10%;">Nilai</th>
                    <th style="width: 4%; background-color: #004880;"></th>
                    <th style="padding: 3px 8px; text-align: left; color: #ffffff; width: 38%;">Kriteria Soft Skill</th>
                    <th style="padding: 3px 8px; text-align: right; color: #ffffff; width: 10%;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $leftArr = $leftCats->values();
                    $rightArr = $rightCats->values();
                    $maxRows = max($leftArr->count(), $rightArr->count());
                @endphp
                @for($i = 0; $i < $maxRows; $i++)
                @php
                    $lCat = $leftArr->get($i);
                    $rCat = $rightArr->get($i);
                    $lScore = $lCat ? (isset($catScores[$lCat->id]) ? $catScores[$lCat->id] : (isset($catScores[$lCat->name]) ? $catScores[$lCat->name] : (float)($rapor->performance_score ?? 0))) : 0;
                    $rScore = $rCat ? (isset($catScores[$rCat->id]) ? $catScores[$rCat->id] : (isset($catScores[$rCat->name]) ? $catScores[$rCat->name] : (float)($rapor->performance_score ?? 0))) : 0;
                @endphp
                <tr style="border-bottom: 1px solid #f1f5f9; {{ $i % 2 == 0 ? 'background-color: #f8fafc;' : 'background-color: #ffffff;' }}">
                    {{-- Left side --}}
                    @if($lCat)
                    <td style="color: #1e293b; padding: 2px 8px; width: 38%;">{{ $lCat->name }}</td>
                    <td style="text-align: right; width: 10%; padding: 2px 8px;">
                        <span style="font-weight: bold; color: #0f172a;">{{ round($lScore / 20, 1) }}</span>
                        <span style="color: #94a3b8; font-size: 8px;"> / 5</span>
                    </td>
                    @else
                    <td style="width: 38%;"></td>
                    <td style="width: 10%;"></td>
                    @endif

                    {{-- Separator column --}}
                    <td style="width: 4%; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; background-color: #ffffff;"></td>

                    {{-- Right side --}}
                    @if($rCat)
                    <td style="color: #1e293b; padding: 2px 8px; width: 38%;">{{ $rCat->name }}</td>
                    <td style="text-align: right; width: 10%; padding: 2px 8px;">
                        <span style="font-weight: bold; color: #0f172a;">{{ round($rScore / 20, 1) }}</span>
                        <span style="color: #94a3b8; font-size: 8px;"> / 5</span>
                    </td>
                    @else
                    <td style="width: 38%;"></td>
                    <td style="width: 10%;"></td>
                    @endif
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- ============================================================ --}}
    {{-- 6. RINGKASAN AKTIVITAS TIKET STAFF & KOMPOSISI BOBOT         --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 7px;">
        <tr>
            {{-- Kiri: Ringkasan Aktivitas Tiket Staff --}}
            <td style="width: 50%; vertical-align: top; border: 1.2px solid #004880; border-radius: 4px; padding: 0; overflow: hidden; background: #ffffff;">
                <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 4px 8px; font-size: 10px;">
                    Ringkasan Aktivitas Tiket Staff
                </div>
                <div style="padding: 4px 8px;">
                    <table style="width: 100%; font-size: 9px;">
                        <tr>
                            <td style="color: #475569; padding: 2px 0;">Tiket ditangani</td>
                            <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_ditangani ?? ($rapor->total_ticket ?? 0) }}</td>
                        </tr>
                        <tr>
                            <td style="color: #475569; padding: 2px 0;">Tiket selesai</td>
                            <td style="text-align: right; font-weight: bold; color: #0f172a;">
                                {{ $tiket_selesai ?? ($rapor->total_ticket ?? 0) }}
                                <span style="font-size: 8px; color: #059669; font-weight: normal;">({{ $completion_rate ?? 0 }}%)</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #475569; padding: 2px 0;">Tiket diproses</td>
                            <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_progress ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td style="color: #475569; padding: 2px 0;">Rata-rata respon</td>
                            <td style="text-align: right; font-weight: bold; color: #0f172a;">{!! formatDurationPdf($rapor && $rapor->rata_rata_waktu ? $rapor->rata_rata_waktu : '-', false) !!}</td>
                        </tr>
                        <tr>
                            <td style="color: #475569; padding: 2px 0;">Modul terbanyak</td>
                            <td style="text-align: right; font-weight: bold; color: #004880;">{{ $topHandledModuleName ?? '-' }} ({{ $topHandledModuleCount ?? 0 }})</td>
                        </tr>
                    </table>
                </div>
            </td>

            {{-- Kanan: Komposisi Bobot & Nilai Akhir --}}
            <td style="width: 50%; vertical-align: top; border: 1.2px solid #004880; border-radius: 4px; padding: 0; overflow: hidden; background: #ffffff;">
                <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 4px 8px; font-size: 10px;">
                    Komposisi Bobot &amp; Nilai Akhir
                </div>
                <div style="padding: 4px 8px;">
                    <table style="width: 100%; font-size: 9px; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="color: #475569; padding: 2px 0;"><span style="color: #004880; font-weight: bold;">[{{ number_format($wTicket, 0) }}%]</span> Kinerja Tiket Staff</td>
                            <td style="text-align: right; color: #64748b; padding: 2px 0;">Skor {{ number_format($scoreTicket, 0) }} : <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreTicket * ($wTicket / 100), 1) }}</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="color: #475569; padding: 2px 0;"><span style="color: #059669; font-weight: bold;">[{{ number_format($wSoftSkill, 0) }}%]</span> Soft Skill</td>
                            <td style="text-align: right; color: #64748b; padding: 2px 0;">Skor {{ number_format($scoreQuality, 0) }} : <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreQuality * ($wSoftSkill / 100), 1) }}</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="color: #475569; padding: 2px 0;"><span style="color: #7c3aed; font-weight: bold;">[{{ number_format($wClient, 0) }}%]</span> Kepuasan Klien</td>
                            <td style="text-align: right; color: #64748b; padding: 2px 0;">Rating {{ number_format($client_rating, 1) }} : <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreSurvey * ($wClient / 100), 1) }}</span></td>
                        </tr>
                        <tr>
                            <td style="color: #0f172a; font-weight: bold; padding: 3px 0 0 0;">Total Nilai Akhir ({{ number_format($wTotal, 0) }}%)</td>
                            <td style="text-align: right; padding: 3px 0 0 0;">
                                <span style="font-weight: bold; font-size: 11.5px; color: #0f172a;">{{ number_format($scoreTotal, 1) }}</span>
                                <span style="background-color: {{ $scoreTotal >= 80 ? '#ecfdf5' : ($scoreTotal >= 70 ? '#eff6ff' : '#fffbeb') }}; color: {{ $scoreTotal >= 80 ? '#059669' : ($scoreTotal >= 70 ? '#004880' : '#d97706') }}; padding: 1px 5px; border-radius: 3px; font-weight: bold; font-size: 8px; border: 1px solid {{ $scoreTotal >= 80 ? '#a7f3d0' : ($scoreTotal >= 70 ? '#bfdbfe' : '#fde68a') }}; margin-left: 2px;">
                                    {{ $predikat }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- 7. CATATAN DARI PENILAI & SARAN UNTUK PERBAIKAN              --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-bottom: 7px;">
        <tr>
            <td style="width: 50%; vertical-align: top; border: 1.2px solid #004880; border-radius: 4px; padding: 0; overflow: hidden; background: #ffffff;">
                <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 3px 8px; font-size: 9.5px;">
                    Catatan dari Penilai
                </div>
                <div style="padding: 4px 8px; font-size: 8.5px; color: #475569; line-height: 1.35; min-height: 24px;">
                    {{ $rapor->catatan ?? 'Menunjukkan komitmen dan dedikasi tinggi dalam menyelesaikan keluhan tiket pelanggan serta aktif berkoordinasi dengan tim support.' }}
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; border: 1.2px solid #004880; border-radius: 4px; padding: 0; overflow: hidden; background: #ffffff;">
                <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 3px 8px; font-size: 9.5px;">
                    Saran untuk Perbaikan
                </div>
                <div style="padding: 4px 8px; font-size: 8.5px; color: #475569; line-height: 1.35; min-height: 24px;">
                    {{ $rapor->saran ?? 'Tingkatkan pendokumentasian kendala berulang pada sistem dan perdalam pemahaman teknis alur transaksi modul.' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- 8. SEMUA CARD DI PALING BAWAH (4 CARDS)                      --}}
    {{-- ============================================================ --}}
    @php
        $nilaiTiket = round($scoreTicket ?? ($rapor->module_score ?? 0), 2);
        $nilaiSoftSkill = round($scoreQuality ?? ($rapor->performance_score ?? 0), 2);
        $nilaiKepuasan = round($scoreSurvey ?? ($rapor->review_score ?? 0), 2);
        $nilaiAkhir = round($scoreTotal ?? ($rapor->total_score ?? 0), 2);

        // SVG arc gauge calculations
        $gaugePercent = min(100, max(0, $nilaiAkhir));
        $arcR = 34;
        $cx = 45;
        $cy = 40;

        $sx = $cx - $arcR;
        $sy = $cy;
        $ex = $cx + $arcR;
        $ey = $cy;

        $progressAngle = 180 - ($gaugePercent / 100) * 180;
        $pRad = deg2rad($progressAngle);
        $px = $cx + $arcR * cos($pRad);
        $py = $cy - $arcR * sin($pRad);
        $pLargeArc = ($gaugePercent > 50) ? 1 : 0;

        if ($nilaiAkhir >= 80) {
            $gaugeColor = '#059669';
        } elseif ($nilaiAkhir >= 70) {
            $gaugeColor = '#004880';
        } elseif ($nilaiAkhir >= 60) {
            $gaugeColor = '#d97706';
        } else {
            $gaugeColor = '#ef4444';
        }
    @endphp

    <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0;">
        <tr>
            {{-- Card 1: Nilai Tiket --}}
            <td style="width: 24%; background: #ffffff; border: 1.2px solid #004880; border-radius: 4px; padding: 4px 6px; vertical-align: top;">
                <div style="font-size: 8.5px; font-weight: bold; color: #004880; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px; margin-bottom: 2px;">
                    Kinerja Tiket
                </div>
                <div style="text-align: center; padding-top: 1px;">
                    <span style="font-size: 20px; font-weight: bold; color: #004880; line-height: 1;">{{ number_format($nilaiTiket, 0) }}</span>
                    <span style="font-size: 8px; font-weight: bold; color: #94a3b8;">/ 100</span>
                    <div style="font-size: 7.5px; color: #64748b; margin-top: 1px;">Bobot: {{ number_format($wTicket, 0) }}%</div>
                </div>
            </td>

            {{-- Card 2: Nilai Soft Skill --}}
            <td style="width: 24%; background: #ffffff; border: 1.2px solid #004880; border-radius: 4px; padding: 4px 6px; vertical-align: top;">
                <div style="font-size: 8.5px; font-weight: bold; color: #004880; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px; margin-bottom: 2px;">
                    Soft Skill
                </div>
                <div style="text-align: center; padding-top: 1px;">
                    <span style="font-size: 20px; font-weight: bold; color: #059669; line-height: 1;">{{ number_format($nilaiSoftSkill, 0) }}</span>
                    <span style="font-size: 8px; font-weight: bold; color: #94a3b8;">/ 100</span>
                    <div style="font-size: 7.5px; color: #64748b; margin-top: 1px;">Bobot: {{ number_format($wSoftSkill, 0) }}%</div>
                </div>
            </td>

            {{-- Card 3: Kepuasan Klien --}}
            <td style="width: 24%; background: #ffffff; border: 1.2px solid #004880; border-radius: 4px; padding: 4px 6px; vertical-align: top;">
                <div style="font-size: 8.5px; font-weight: bold; color: #004880; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px; margin-bottom: 2px;">
                    Kepuasan Klien
                </div>
                <div style="text-align: center; padding-top: 1px;">
                    <span style="font-size: 20px; font-weight: bold; color: #7c3aed; line-height: 1;">{{ number_format($nilaiKepuasan, 0) }}</span>
                    <span style="font-size: 8px; font-weight: bold; color: #94a3b8;">/ 100</span>
                    <div style="font-size: 7.5px; color: #64748b; margin-top: 1px;">
                        Rating {{ number_format($client_rating, 1) }} / 5.0 ({{ number_format($wClient, 0) }}%)
                    </div>
                </div>
            </td>

            {{-- Card 4: Nilai Akhir (Speedometer Gauge) --}}
            <td style="width: 28%; background: #ffffff; border: 1.2px solid #004880; border-radius: 4px; padding: 3px 4px 2px 4px; text-align: center; vertical-align: top;">
                <div style="position: relative; width: 90px; height: 38px; margin: 0 auto;">
                    <svg width="90" height="38" viewBox="0 0 90 40" style="display: block; margin: 0 auto;">
                        {{-- Track --}}
                        <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 0 1 {{ $ex }} {{ $ey }}"
                              fill="none" stroke="#e2e8f0" stroke-width="6" stroke-linecap="round" />
                        {{-- Progress --}}
                        @if($gaugePercent > 0)
                        <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 {{ $pLargeArc }} 1 {{ $px }} {{ $py }}"
                              fill="none" stroke="{{ $gaugeColor }}" stroke-width="6" stroke-linecap="round" />
                        @endif
                    </svg>
                </div>
                <div style="margin-top: -26px; text-align: center; position: relative;">
                    <div style="font-size: 6px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">NILAI AKHIR</div>
                    <div style="font-size: 17px; font-weight: bold; color: #0f172a; line-height: 1.1;">{{ number_format($nilaiAkhir, 1) }}</div>
                    <div style="display: inline-block; margin-top: 1px; padding: 1px 5px; font-size: 7px; font-weight: bold; border-radius: 3px; background: {{ $nilaiAkhir >= 80 ? '#ecfdf5' : ($nilaiAkhir >= 70 ? '#eff6ff' : '#fffbeb') }}; color: {{ $nilaiAkhir >= 80 ? '#059669' : ($nilaiAkhir >= 70 ? '#004880' : '#d97706') }};">
                        {{ $predikat }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
