<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penilaian Kinerja - {{ $user->name }}</title>
    <style>
        @page {
            margin: 45px 0px 40px 0px;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;
        }
        body {
            font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            font-size: 10.5px;
            line-height: 1.35;
            background-color: #ffffff;
        }
        .page-container {
            padding: 0 44px;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>
    <div class="page-container">
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
                        . '<div style="font-size: 8.5px; color: #94a3b8;">(' . $formattedM . ')</div>'
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
    <table style="width: 100%; border-bottom: 2px solid #004880; padding-bottom: 8px; margin-bottom: 12px;">
        <tr>
            <td style="width: 45%; vertical-align: top;">
                @if ($probusLogoSrc)
                    <img src="{{ $probusLogoSrc }}" style="width: 68px; height: 68px; object-fit: contain; display: block;" />
                @endif
            </td>
            <td style="width: 55%; vertical-align: top; text-align: right; line-height: 1.35;">
                <div style="font-size: 15px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px;">CV. MITRA SOLUSI MANDIRI</div>
                <div style="font-size: 9.5px; color: #334155; margin-top: 1px;">JL PB. SUDIRMAN, PERTOKOAN SUDIRMAN AGUNG BLOK C NO. 51</div>
                <div style="font-size: 9.5px; color: #334155;">DENPASAR</div>
                <div style="font-size: 9.5px; color: #334155;">0361 -241 689</div>
                <div style="font-size: 9.5px; color: #004880; font-weight: bold;">support@probussystem.com</div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- 2. PROFILE USER LEBIH BESAR & INFORMATIF                     --}}
    {{-- ============================================================ --}}
    <div style="margin-bottom: 12px; border: 1.2px solid #004880; border-radius: 5px; overflow: hidden; background: #ffffff;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                {{-- Panel Avatar di Kiri --}}
                <td style="width: 82px; vertical-align: middle; text-align: center; background-color: #f8fafc; border-right: 1.2px solid #cbd5e1; padding: 10px 8px;">
                    @if ($pdfAvatarSrc)
                        <img src="{{ $pdfAvatarSrc }}" style="width: 62px; height: 62px; border-radius: 50%; object-fit: cover; border: 2.5px solid #004880; display: block; margin: 0 auto;" />
                    @else
                        <div style="width: 62px; height: 62px; border-radius: 50%; background-color: #004880; text-align: center; line-height: 62px; color: #ffffff; font-weight: bold; font-size: 24px; margin: 0 auto;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </td>

                {{-- Detail Informasi Staff --}}
                <td style="vertical-align: middle; padding: 8px 14px; background-color: #ffffff;">
                    {{-- Baris Atas: Nama Staff & Badge Status --}}
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="vertical-align: middle;">
                                <span style="font-size: 15.5px; font-weight: bold; color: #004880; letter-spacing: 0.3px;">
                                    {{ ucwords(strtolower($user->name)) }}
                                </span>
                            </td>
                            <td style="text-align: right; vertical-align: middle;">
                                <span style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; letter-spacing: 0.4px;">
                                    STAFF AKTIF
                                </span>
                            </td>
                        </tr>
                    </table>

                    {{-- Garis Pembatas Halus --}}
                    <div style="border-bottom: 1px solid #e2e8f0; margin: 5px 0 6px 0;"></div>

                    {{-- Grid 2 Kolom Simetris --}}
                    <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                        <tr>
                            {{-- Kolom Kiri (48%) --}}
                            <td style="width: 48%; vertical-align: top; padding: 0;">
                                <table style="width: 100%; border-collapse: collapse; line-height: 1.55;">
                                    <tr>
                                        <td style="color: #64748b; width: 78px; white-space: nowrap;">Jabatan</td>
                                        <td style="color: #94a3b8; width: 12px; text-align: center;">:</td>
                                        <td style="color: #0f172a; font-weight: bold;">{{ ucwords($user->usertype ?? 'Customer Support') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; width: 78px; white-space: nowrap;">Departemen</td>
                                        <td style="color: #94a3b8; width: 12px; text-align: center;">:</td>
                                        <td style="color: #0f172a; font-weight: bold;">{{ optional($user->departemen)->name ?? 'Customer Support' }}</td>
                                    </tr>
                                </table>
                            </td>

                            {{-- Spacer Tengah (4%) --}}
                            <td style="width: 4%;"></td>

                            {{-- Kolom Kanan (48%) --}}
                            <td style="width: 48%; vertical-align: top; padding: 0;">
                                <table style="width: 100%; border-collapse: collapse; line-height: 1.55;">
                                    <tr>
                                        <td style="color: #64748b; width: 78px; white-space: nowrap;">Email</td>
                                        <td style="color: #94a3b8; width: 12px; text-align: center;">:</td>
                                        <td style="color: #004880; font-weight: bold;">{{ $user->email ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; width: 78px; white-space: nowrap;">Status Staff</td>
                                        <td style="color: #94a3b8; width: 12px; text-align: center;">:</td>
                                        <td style="color: #059669; font-weight: bold;">Aktif</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ============================================================ --}}
    {{-- 3. JUDUL DI TENGAH: PENILAIAN KINERJA & PERIODE              --}}
    {{-- ============================================================ --}}
    <div style="text-align: center; margin: 6px 0 12px 0; border-top: 1px dashed #cbd5e1; padding-top: 6px;">
        <div style="font-size: 15px; font-weight: bold; color: #004880; letter-spacing: 0.8px; text-transform: uppercase;">PENILAIAN KINERJA</div>
        <div style="font-size: 11px; color: #475569; margin-top: 2px;">
            Periode: {{ !empty($periodInfo['label']) ? $periodInfo['label'] : ($rapor ? \Carbon\Carbon::parse($rapor->periode)->translatedFormat('F Y') : \Carbon\Carbon::now()->translatedFormat('F Y')) }} (Cut-off: {{ !empty($periodInfo['cutoff_label']) ? $periodInfo['cutoff_label'] : \Carbon\Carbon::now()->endOfMonth()->translatedFormat('d F Y') }})
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ============================================================ --}}
    {{-- 4. DETAIL KINERJA TIKET PER MODUL (MEMUNCULKAN SEMUA MODUL)  --}}
    {{-- ============================================================ --}}
    <div style="margin-bottom: 12px; border: 1.2px solid #004880; border-radius: 4px; overflow: hidden;">
        <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 5.5px 10px; font-size: 11.5px; text-align: center; letter-spacing: 0.3px; border-bottom: 1.5px solid #93c5fd;">
            Detail Kinerja Tiket per Modul
        </div>

        <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #004880; color: #ffffff; font-weight: bold;">
                    <th style="padding: 5px 8px; text-align: left; color: #ffffff; width: 22%; border: 1px solid #1a5695; border-top: none;">Modul</th>
                    <th style="padding: 5px 8px; text-align: center; color: #ffffff; width: 9%; border: 1px solid #1a5695; border-top: none;">Bobot</th>
                    <th style="padding: 5px 8px; text-align: center; color: #ffffff; width: 13%; border: 1px solid #1a5695; border-top: none;">Total Tiket</th>
                    <th style="padding: 5px 8px; text-align: center; color: #ffffff; width: 21%; border: 1px solid #1a5695; border-top: none;">Lama Pengerjaan</th>
                    <th style="padding: 5px 8px; text-align: center; color: #ffffff; width: 21%; border: 1px solid #1a5695; border-top: none;">Rata-rata Waktu</th>
                    <th style="padding: 5px 8px; text-align: right; color: #ffffff; width: 14%; border: 1px solid #1a5695; border-top: none;">Skor Poin</th>
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
                <tr style="{{ $loop->iteration % 2 == 0 ? 'background-color: #f8fafc;' : 'background-color: #ffffff;' }}">
                    <td style="color: #1e293b; font-weight: bold; padding: 4px 8px; width: 22%; border: 1px solid #cbd5e1;">{{ $mod->name }}</td>
                    <td style="text-align: center; color: #64748b; padding: 4px 8px; width: 9%; border: 1px solid #cbd5e1;">{{ $mBobot }}</td>
                    <td style="text-align: center; color: #0f172a; font-weight: {{ $mTotal > 0 ? 'bold' : 'normal' }}; padding: 4px 8px; width: 13%; border: 1px solid #cbd5e1;">{{ $mTotal }}</td>
                    <td style="text-align: center; color: #64748b; padding: 4px 8px; width: 21%; border: 1px solid #cbd5e1;">{!! formatDurationPdf($mLama) !!}</td>
                    <td style="text-align: center; color: #64748b; padding: 4px 8px; width: 21%; border: 1px solid #cbd5e1;">{!! formatDurationPdf($mRata) !!}</td>
                    <td style="text-align: right; color: {{ $mScore > 0 ? '#004880' : '#94a3b8' }}; font-weight: bold; padding: 4px 8px; width: 14%; border: 1px solid #cbd5e1;">{{ number_format($mScore, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td style="color: #0f172a; width: 22%; padding: 4.5px 8px; border: 1px solid #cbd5e1; border-top: 1.5px solid #004880;">Total</td>
                    <td style="text-align: center; color: #334155; width: 9%; padding: 4.5px 8px; border: 1px solid #cbd5e1; border-top: 1.5px solid #004880;">Bobot: {{ $modulesList->sum('bobot') }}</td>
                    <td style="text-align: center; color: #004880; width: 13%; padding: 4.5px 8px; border: 1px solid #cbd5e1; border-top: 1.5px solid #004880;">Jumlah: {{ $total_qty ?? ($savedDetails->sum('total_ticket')) }}</td>
                    <td style="text-align: center; color: #334155; width: 21%; padding: 4.5px 8px; border: 1px solid #cbd5e1; border-top: 1.5px solid #004880;">{!! formatDurationPdf(($rapor && $rapor->lama_waktu) ? $rapor->lama_waktu : (($rapor && $rapor->rata_rata_waktu) ? $rapor->rata_rata_waktu : '-')) !!}</td>
                    <td style="text-align: center; color: #334155; width: 21%; padding: 4.5px 8px; border: 1px solid #cbd5e1; border-top: 1.5px solid #004880;">{!! formatDurationPdf($rapor && $rapor->rata_rata_waktu ? $rapor->rata_rata_waktu : '-') !!}</td>
                    <td style="text-align: right; color: #004880; width: 14%; padding: 4.5px 8px; border: 1px solid #cbd5e1; border-top: 1.5px solid #004880;">Total: {{ number_format($raw_ticket_score ?? 0, 0, ',', '.') }} poin</td>
                </tr>
            </tfoot>
        </table>

        {{-- Normalization formula & Benchmark strip --}}
        <table style="width: 100%; background: #ffffff; border-top: 1px solid #cbd5e1; padding: 5px 10px; font-size: 10px;">
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
                    <span style="font-size: 12.5px; font-weight: bold; color: #004880;">{{ number_format($scoreTicket, 1) }}</span>
                    <span style="color: #94a3b8; font-size: 9.5px;">/ 100</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- ============================================================ --}}
    {{-- 5. PENILAIAN SOFT SKILL                                      --}}
    {{-- ============================================================ --}}
    <div style="margin-bottom: 12px; border: 1.2px solid #004880; border-radius: 4px; overflow: hidden;">
        <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 5.5px 10px; font-size: 11.5px; text-align: center; letter-spacing: 0.3px; border-bottom: 1.5px solid #93c5fd;">
            Penilaian Soft Skill
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

        <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
            <thead>
                <tr style="background-color: #004880; color: #ffffff; font-weight: bold;">
                    <th style="padding: 4.5px 10px; text-align: left; color: #ffffff; width: 38%; border: 1px solid #1a5695; border-top: none;">Kriteria Soft Skill</th>
                    <th style="padding: 4.5px 10px; text-align: right; color: #ffffff; width: 10%; border: 1px solid #1a5695; border-top: none;">Nilai</th>
                    <th style="width: 4%; background-color: #004880; border: 1px solid #1a5695; border-top: none;"></th>
                    <th style="padding: 4.5px 10px; text-align: left; color: #ffffff; width: 38%; border: 1px solid #1a5695; border-top: none;">Kriteria Soft Skill</th>
                    <th style="padding: 4.5px 10px; text-align: right; color: #ffffff; width: 10%; border: 1px solid #1a5695; border-top: none;">Nilai</th>
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
                <tr style="{{ $i % 2 == 0 ? 'background-color: #f8fafc;' : 'background-color: #ffffff;' }}">
                    {{-- Left side --}}
                    @if($lCat)
                    <td style="color: #1e293b; padding: 4px 10px; width: 38%; border: 1px solid #cbd5e1; border-top: none;">{{ $lCat->name }}</td>
                    <td style="text-align: right; width: 10%; padding: 4px 10px; border: 1px solid #cbd5e1; border-top: none;">
                        <span style="font-weight: bold; color: #0f172a; font-size: 11px;">{{ round($lScore / 20, 1) }}</span>
                        <span style="color: #94a3b8; font-size: 9px;"> / 5</span>
                    </td>
                    @else
                    <td style="width: 38%; border: 1px solid #cbd5e1; border-top: none;"></td>
                    <td style="width: 10%; border: 1px solid #cbd5e1; border-top: none;"></td>
                    @endif

                    {{-- Separator column --}}
                    <td style="width: 4%; border: 1px solid #cbd5e1; border-top: none; background-color: #f1f5f9;"></td>

                    {{-- Right side --}}
                    @if($rCat)
                    <td style="color: #1e293b; padding: 4px 10px; width: 38%; border: 1px solid #cbd5e1; border-top: none;">{{ $rCat->name }}</td>
                    <td style="text-align: right; width: 10%; padding: 4px 10px; border: 1px solid #cbd5e1; border-top: none;">
                        <span style="font-weight: bold; color: #0f172a; font-size: 11px;">{{ round($rScore / 20, 1) }}</span>
                        <span style="color: #94a3b8; font-size: 9px;"> / 5</span>
                    </td>
                    @else
                    <td style="width: 38%; border: 1px solid #cbd5e1; border-top: none;"></td>
                    <td style="width: 10%; border: 1px solid #cbd5e1; border-top: none;"></td>
                    @endif
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- ============================================================ --}}
    {{-- 6. RINGKASAN AKTIVITAS TIKET STAFF & KOMPOSISI BOBOT         --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            {{-- Kiri: Ringkasan Aktivitas Tiket Staff --}}
            <td style="width: 49%; vertical-align: top; padding: 0;">
                <div style="border: 1.2px solid #004880; border-radius: 4px; overflow: hidden; background: #ffffff;">
                    <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 5.5px 10px; font-size: 11.5px; text-align: center;">
                        Ringkasan Aktivitas Tiket Staff
                    </div>
                    <div style="padding: 7px 10px;">
                        <table style="width: 100%; font-size: 10.5px; border-collapse: collapse;">
                            <tr>
                                <td style="color: #475569; padding: 3.5px 0;">Tiket ditangani</td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_ditangani ?? ($rapor->total_ticket ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 3.5px 0;">Tiket selesai</td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">
                                    {{ $tiket_selesai ?? ($rapor->total_ticket ?? 0) }}
                                    <span style="font-size: 9px; color: #059669; font-weight: normal;">({{ $completion_rate ?? 0 }}%)</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 3.5px 0;">Tiket diproses</td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ $tiket_progress ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 3.5px 0;">Rata-rata respon</td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">{!! formatDurationPdf($rapor && $rapor->rata_rata_waktu ? $rapor->rata_rata_waktu : '-', false) !!}</td>
                            </tr>
                            <tr>
                                <td style="color: #475569; padding: 3.5px 0;">Modul terbanyak</td>
                                <td style="text-align: right; font-weight: bold; color: #004880;">{{ $topHandledModuleName ?? '-' }} ({{ $topHandledModuleCount ?? 0 }})</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>

            {{-- Spacer tengah --}}
            <td style="width: 2%;"></td>

            {{-- Kanan: Komposisi Bobot & Nilai Akhir --}}
            <td style="width: 49%; vertical-align: top; padding: 0;">
                <div style="border: 1.2px solid #004880; border-radius: 4px; overflow: hidden; background: #ffffff;">
                    <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 5.5px 10px; font-size: 11.5px; text-align: center;">
                        Komposisi Bobot &amp; Nilai Akhir
                    </div>
                    <div style="padding: 7px 10px;">
                        <table style="width: 100%; font-size: 10.5px; border-collapse: collapse;">
                            <tr style="border-bottom: 1px solid #cbd5e1;">
                                <td style="color: #475569; padding: 3.5px 0;"><span style="color: #004880; font-weight: bold;">[{{ number_format($wTicket, 0) }}%]</span> Kinerja Tiket Staff</td>
                                <td style="text-align: right; color: #64748b; padding: 3.5px 0;">Skor {{ number_format($scoreTicket, 0) }} : <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreTicket * ($wTicket / 100), 1) }}</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #cbd5e1;">
                                <td style="color: #475569; padding: 3.5px 0;"><span style="color: #059669; font-weight: bold;">[{{ number_format($wSoftSkill, 0) }}%]</span> Soft Skill</td>
                                <td style="text-align: right; color: #64748b; padding: 3.5px 0;">Skor {{ number_format($scoreQuality, 0) }} : <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreQuality * ($wSoftSkill / 100), 1) }}</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #cbd5e1;">
                                <td style="color: #475569; padding: 3.5px 0;"><span style="color: #7c3aed; font-weight: bold;">[{{ number_format($wClient, 0) }}%]</span> Kepuasan Klien</td>
                                <td style="text-align: right; color: #64748b; padding: 3.5px 0;">Rating {{ number_format($client_rating, 1) }} : <span style="font-weight: bold; color: #0f172a;">{{ number_format($scoreSurvey * ($wClient / 100), 1) }}</span></td>
                            </tr>
                            <tr>
                                <td style="color: #0f172a; font-weight: bold; padding: 4.5px 0 0 0;">Total Nilai Akhir ({{ number_format($wTotal, 0) }}%)</td>
                                <td style="text-align: right; padding: 4.5px 0 0 0;">
                                    <span style="font-weight: bold; font-size: 13.5px; color: #0f172a;">{{ number_format($scoreTotal, 1) }}</span>
                                    <span style="background-color: {{ $scoreTotal >= 80 ? '#ecfdf5' : ($scoreTotal >= 70 ? '#eff6ff' : '#fffbeb') }}; color: {{ $scoreTotal >= 80 ? '#059669' : ($scoreTotal >= 70 ? '#004880' : '#d97706') }}; padding: 1.5px 6px; border-radius: 3px; font-weight: bold; font-size: 9.5px; border: 1px solid {{ $scoreTotal >= 80 ? '#a7f3d0' : ($scoreTotal >= 70 ? '#bfdbfe' : '#fde68a') }}; margin-left: 3px;">
                                        {{ $predikat }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- 7. CATATAN DARI PENILAI & SARAN UNTUK PERBAIKAN              --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="width: 49%; vertical-align: top; padding: 0;">
                <div style="border: 1.2px solid #004880; border-radius: 4px; overflow: hidden; background: #ffffff;">
                    <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 5px 10px; font-size: 11px; text-align: center;">
                        Catatan dari Penilai
                    </div>
                    <div style="padding: 7px 10px; font-size: 10px; color: #475569; line-height: 1.45; min-height: 32px;">
                        {{ $rapor->catatan ?? 'Menunjukkan komitmen dan dedikasi tinggi dalam menyelesaikan keluhan tiket pelanggan serta aktif berkoordinasi dengan tim support.' }}
                    </div>
                </div>
            </td>

            {{-- Spacer tengah --}}
            <td style="width: 2%;"></td>

            <td style="width: 49%; vertical-align: top; padding: 0;">
                <div style="border: 1.2px solid #004880; border-radius: 4px; overflow: hidden; background: #ffffff;">
                    <div style="background-color: #004880; color: #ffffff; font-weight: bold; padding: 5px 10px; font-size: 11px; text-align: center;">
                        Saran untuk Perbaikan
                    </div>
                    <div style="padding: 7px 10px; font-size: 10px; color: #475569; line-height: 1.45; min-height: 32px;">
                        {{ $rapor->saran ?? 'Tingkatkan pendokumentasian kendala berulang pada sistem dan perdalam pemahaman teknis alur transaksi modul.' }}
                    </div>
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
        $arcR = 38;
        $cx = 50;
        $cy = 44;

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

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            {{-- Card 1: Nilai Tiket --}}
            <td style="width: 23.5%; vertical-align: top; padding: 0;">
                <div style="background: #ffffff; border: 1.2px solid #004880; border-radius: 5px; padding: 7px 10px;">
                    <div style="font-size: 10px; font-weight: bold; color: #004880; border-bottom: 1.5px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 4px; text-align: center;">
                        Kinerja Tiket
                    </div>
                    <div style="text-align: center; padding-top: 3px;">
                        <span style="font-size: 25px; font-weight: bold; color: #004880; line-height: 1;">{{ number_format($nilaiTiket, 0) }}</span>
                        <span style="font-size: 10px; font-weight: bold; color: #94a3b8;">/ 100</span>
                        <div style="font-size: 9px; color: #64748b; margin-top: 3px;">Bobot: {{ number_format($wTicket, 0) }}%</div>
                    </div>
                </div>
            </td>

            <td style="width: 2%;"></td>

            {{-- Card 2: Nilai Soft Skill --}}
            <td style="width: 23.5%; vertical-align: top; padding: 0;">
                <div style="background: #ffffff; border: 1.2px solid #004880; border-radius: 5px; padding: 7px 10px;">
                    <div style="font-size: 10px; font-weight: bold; color: #004880; border-bottom: 1.5px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 4px; text-align: center;">
                        Soft Skill
                    </div>
                    <div style="text-align: center; padding-top: 3px;">
                        <span style="font-size: 25px; font-weight: bold; color: #059669; line-height: 1;">{{ number_format($nilaiSoftSkill, 0) }}</span>
                        <span style="font-size: 10px; font-weight: bold; color: #94a3b8;">/ 100</span>
                        <div style="font-size: 9px; color: #64748b; margin-top: 3px;">Bobot: {{ number_format($wSoftSkill, 0) }}%</div>
                    </div>
                </div>
            </td>

            <td style="width: 2%;"></td>

            {{-- Card 3: Kepuasan Klien --}}
            <td style="width: 23.5%; vertical-align: top; padding: 0;">
                <div style="background: #ffffff; border: 1.2px solid #004880; border-radius: 5px; padding: 7px 10px;">
                    <div style="font-size: 10px; font-weight: bold; color: #004880; border-bottom: 1.5px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 4px; text-align: center;">
                        Kepuasan Klien
                    </div>
                    <div style="text-align: center; padding-top: 3px;">
                        <span style="font-size: 25px; font-weight: bold; color: #7c3aed; line-height: 1;">{{ number_format($nilaiKepuasan, 0) }}</span>
                        <span style="font-size: 10px; font-weight: bold; color: #94a3b8;">/ 100</span>
                        <div style="font-size: 9px; color: #64748b; margin-top: 3px;">
                            Rating {{ number_format($client_rating, 1) }} / 5.0 ({{ number_format($wClient, 0) }}%)
                        </div>
                    </div>
                </div>
            </td>

            <td style="width: 2%;"></td>

            {{-- Card 4: Nilai Akhir (Speedometer Gauge) --}}
            <td style="width: 23.5%; vertical-align: top; padding: 0;">
                <div style="background: #ffffff; border: 1.2px solid #004880; border-radius: 5px; padding: 6px 8px 4px 8px; text-align: center;">
                    <div style="position: relative; width: 100px; height: 44px; margin: 0 auto;">
                        <svg width="100" height="44" viewBox="0 0 100 46" style="display: block; margin: 0 auto;">
                            {{-- Track --}}
                            <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 0 1 {{ $ex }} {{ $ey }}"
                                  fill="none" stroke="#e2e8f0" stroke-width="6.5" stroke-linecap="round" />
                            {{-- Progress --}}
                            @if($gaugePercent > 0)
                            <path d="M {{ $sx }} {{ $sy }} A {{ $arcR }} {{ $arcR }} 0 {{ $pLargeArc }} 1 {{ $px }} {{ $py }}"
                                  fill="none" stroke="{{ $gaugeColor }}" stroke-width="6.5" stroke-linecap="round" />
                            @endif
                        </svg>
                    </div>
                    <div style="margin-top: -30px; text-align: center; position: relative;">
                        <div style="font-size: 7.5px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">NILAI AKHIR</div>
                        <div style="font-size: 21px; font-weight: bold; color: #0f172a; line-height: 1.1;">{{ number_format($nilaiAkhir, 1) }}</div>
                        <div style="display: inline-block; margin-top: 2px; padding: 2px 7px; font-size: 8.5px; font-weight: bold; border-radius: 3px; background: {{ $nilaiAkhir >= 80 ? '#ecfdf5' : ($nilaiAkhir >= 70 ? '#eff6ff' : '#fffbeb') }}; color: {{ $nilaiAkhir >= 80 ? '#059669' : ($nilaiAkhir >= 70 ? '#004880' : '#d97706') }};">
                            {{ $predikat }}
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ============================================================ --}}
    {{-- 9. TANDA TANGAN (Staff Pelaksana & Supervisor)              --}}
    {{-- ============================================================ --}}
    <table style="width: 100%; table-layout: fixed; margin-top: 18px; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top; font-size: 8.5px; color: #64748b; line-height: 1.4;">
                <div>Dokumen ini dicetak secara otomatis oleh Probus Helpdesk Management System.</div>
                <div>Dicetak Oleh: <strong>{{ auth()->user()->name ?? 'User' }}</strong> pada {{ date('d/m/Y H:i') }} WITA.</div>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <table style="width: 100%; table-layout: fixed; text-align: center; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; padding-right: 12px; padding-left: 8px; text-align: center; vertical-align: top;">
                            <div style="font-size: 9.5px; color: #475569;">Staff Pelaksana,</div>
                            <div style="height: 38px;"></div>
                            <div style="font-weight: bold; border-top: 1.5px solid #475569; padding-top: 3px; font-size: 10.5px; color: #0f172a;">{{ $user->name }}</div>
                            <div style="font-size: 9px; color: #64748b;">Staff {{ ucfirst($user->departemen->name ?? 'Operasional') }}</div>
                        </td>
                        <td style="width: 50%; padding-left: 12px; padding-right: 8px; text-align: center; vertical-align: top;">
                            <div style="font-size: 9.5px; color: #475569;">Mengetahui,</div>
                            <div style="height: 38px;"></div>
                            <div style="font-weight: bold; border-top: 1.5px solid #475569; padding-top: 3px; font-size: 10.5px; color: #0f172a;">Supervisor / Head Dept</div>
                            <div style="font-size: 9px; color: #64748b;">Probus System</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </div>
</body>
</html>
