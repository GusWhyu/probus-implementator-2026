<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tiket {{ ($module->id ?? '') === 'all' ? 'Keseluruhan' : 'Modul ' . $module->name }} - {{ $user->name }}</title>
    <style>
        @page {
            margin: 26px 44px 24px 44px;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
        }
        body {
            color: #0f172a;
            font-size: 9.5px;
            line-height: 1.35;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        table {
            border-collapse: collapse;
        }
        .data-table {
            width: 100%;
            table-layout: fixed;
            border: 1.5px solid #475569;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .data-table th {
            background-color: #1e40af;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-align: center;
            padding: 5.5px 4px;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            word-wrap: break-word;
            overflow: hidden;
        }
        .data-table td {
            padding: 4px 5px;
            border: 1px solid #94a3b8;
            vertical-align: top;
            font-size: 9px;
            line-height: 1.3;
            word-wrap: break-word;
            overflow: hidden;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8px;
            text-align: center;
            letter-spacing: 0.3px;
        }
        .badge-closed {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-progress {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-open {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .badge-tipe {
            background-color: #f1f5f9;
            color: #1e293b;
            border: 1px solid #94a3b8;
        }
        .badge-online {
            background-color: #f3e8ff;
            color: #7e22ce;
            border: 1px solid #d8b4fe;
        }
        .solusi-box {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-left: 3.5px solid #16a34a;
            padding: 4px 6px;
            border-radius: 4px;
            color: #14532d;
            font-size: 9px;
            line-height: 1.3;
            margin-top: 2px;
            word-wrap: break-word;
        }
        .deskripsi-box {
            color: #334155;
            font-size: 8.5px;
            line-height: 1.3;
            margin-top: 2px;
            word-wrap: break-word;
        }
        .footer-table {
            width: 100%;
            table-layout: fixed;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    @php
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

        $totalCount = $tickets->count();
        $closedCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'CLOSED'; })->count();
        $progressCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'PROGRESS'; })->count();
        $openCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'OPEN'; })->count();
    @endphp

    {{-- 1. KOP PERUSAHAAN (Sama persis dengan rapor_pdf) --}}
    <table style="width: 100%; border-bottom: 2px solid #004880; padding-bottom: 6px; margin-bottom: 8px;">
        <tr>
            <td style="width: 45%; vertical-align: top;">
                @if ($probusLogoSrc)
                    <img src="{{ $probusLogoSrc }}" style="width: 54px; height: 54px; object-fit: contain; display: block;" />
                @endif
            </td>
            <td style="width: 55%; vertical-align: top; text-align: right; line-height: 1.35; font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;">
                <div style="font-size: 14.5px; font-weight: bold; color: #0f172a; letter-spacing: 0.5px;">CV. MITRA SOLUSI MANDIRI</div>
                <div style="font-size: 9px; color: #334155; margin-top: 1px;">JL PB. SUDIRMAN, PERTOKOAN SUDIRMAN AGUNG BLOK C NO. 51</div>
                <div style="font-size: 9px; color: #334155;">DENPASAR &bull; 0361 -241 689</div>
                <div style="font-size: 9px; color: #004880; font-weight: bold;">support@probussystem.com</div>
            </td>
        </tr>
    </table>

    {{-- 2. PROFILE USER (Sama persis dengan rapor_pdf) --}}
    <div style="margin-bottom: 7px; border: 1.2px solid #004880; border-radius: 5px; overflow: hidden; background: #ffffff; font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                {{-- Panel Avatar di Kiri --}}
                <td style="width: 82px; vertical-align: middle; text-align: center; background-color: #f8fafc; border-right: 1.2px solid #cbd5e1; padding: 8px 6px;">
                    @if ($pdfAvatarSrc)
                        <img src="{{ $pdfAvatarSrc }}" style="width: 62px; height: 62px; border-radius: 50%; object-fit: cover; border: 2.2px solid #004880; display: block; margin: 0 auto;" />
                    @else
                        <div style="width: 62px; height: 62px; border-radius: 50%; background-color: #004880; text-align: center; line-height: 62px; color: #ffffff; font-weight: bold; font-size: 24px; margin: 0 auto;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </td>

                {{-- Detail Informasi Staff --}}
                <td style="vertical-align: middle; padding: 7px 14px; background-color: #ffffff;">
                    {{-- Baris Atas: Nama Staff & Badge Status --}}
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="vertical-align: middle;">
                                <span style="font-size: 15.5px; font-weight: bold; color: #004880; letter-spacing: 0.3px; font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;">
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
                    <div style="border-bottom: 1px solid #e2e8f0; margin: 4px 0 5px 0;"></div>

                    {{-- Grid 2 Kolom Simetris --}}
                    <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; font-family: 'Times New Roman', Times, 'DejaVu Serif', serif;">
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

    {{-- 3. JUDUL DI TENGAH: RIWAYAT TIKET & PERIODE (Di Bawah Profile User) --}}
    <div style="text-align: center; margin: 2px 0 7px 0; border-top: 1px dashed #cbd5e1; padding-top: 4px;">
        <div style="font-size: 13px; font-weight: bold; color: #004880; letter-spacing: 0.8px; text-transform: uppercase;">
            {{ ($module->id ?? '') === 'all' ? 'LAPORAN RIWAYAT TIKET KESELURUHAN' : 'LAPORAN RIWAYAT TIKET PER MODUL' }}
        </div>
        <div style="font-size: 9.5px; color: #475569; margin-top: 1.5px;">
            MODUL: <strong style="color: #004880;">{{ strtoupper($module->name) }}</strong>
            @if(!empty($module->bobot) && $module->bobot !== '-')
                <span style="color: #64748b; font-weight: normal;">(Bobot: {{ $module->bobot }})</span>
            @endif
            &bull; Periode: <strong style="color: #0f172a;">{{ $periodeText }}</strong>
        </div>
    </div>

    {{-- 4. KPI SUMMARY CARDS (Proporsional, Elegan, Lebar & Tidak Mepet) --}}
    <table style="width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 9px 0; margin-bottom: 8px;">
        <tr>
            <td style="width: 25%; padding: 0;">
                <div style="background-color: #f5f3ff; border: 1.5px solid #c4b5fd; border-radius: 6px; padding: 5px 8px; text-align: center;">
                    <div style="font-size: 8px; font-weight: bold; color: #6b21a8; text-transform: uppercase; letter-spacing: 0.4px;">TOTAL TICKETS</div>
                    <div style="font-size: 16px; font-weight: bold; color: #6b21a8; margin-top: 1px; line-height: 1.1;">{{ $totalCount }}</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0;">
                <div style="background-color: #ecfdf5; border: 1.5px solid #6ee7b7; border-radius: 6px; padding: 5px 8px; text-align: center;">
                    <div style="font-size: 8px; font-weight: bold; color: #047857; text-transform: uppercase; letter-spacing: 0.4px;">CLOSED</div>
                    <div style="font-size: 16px; font-weight: bold; color: #047857; margin-top: 1px; line-height: 1.1;">{{ $closedCount }}</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0;">
                <div style="background-color: #fffbeb; border: 1.5px solid #fcd34d; border-radius: 6px; padding: 5px 8px; text-align: center;">
                    <div style="font-size: 8px; font-weight: bold; color: #b45309; text-transform: uppercase; letter-spacing: 0.4px;">PROGRESS</div>
                    <div style="font-size: 16px; font-weight: bold; color: #b45309; margin-top: 1px; line-height: 1.1;">{{ $progressCount }}</div>
                </div>
            </td>
            <td style="width: 25%; padding: 0;">
                <div style="background-color: #eff6ff; border: 1.5px solid #93c5fd; border-radius: 6px; padding: 5px 8px; text-align: center;">
                    <div style="font-size: 8px; font-weight: bold; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.4px;">OPEN</div>
                    <div style="font-size: 16px; font-weight: bold; color: #1d4ed8; margin-top: 1px; line-height: 1.1;">{{ $openCount }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Tiket (Grid Jelas, Rapi & Proporsional) -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 14%;">NO. TIKET & STATUS</th>
                <th style="width: 15%;">CLIENT / OUTLET</th>
                <th style="width: 8.5%;">PENANGANAN</th>
                <th style="width: 24.5%;">KENDALA / MASALAH DILAPORKAN</th>
                <th style="width: 23%;">APA YANG DIKERJAKAN (SOLUSI)</th>
                <th style="width: 11%;">WAKTU & DURASI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $index => $t)
                @php
                    $status = strtoupper($t->status ?? 'OPEN');
                    $tipe = $t->tipe_penanganan ?: '-';
                    $clientName = $t->client->nm_out ?? $t->client->name ?? '-';
                    if (!empty($t->client->lokasi)) {
                        $clientName .= ' (' . $t->client->lokasi . ')';
                    }
                    $isOnline = strtolower($tipe) === 'online';
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold; font-size: 10px;">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; font-family: monospace; font-size: 10px; color: #0f172a;">{{ $t->ticket_number }}</div>
                        @if(($module->id ?? '') === 'all' && !empty($t->moduleSystem->name))
                            <div style="font-size: 8px; color: #1e40af; font-weight: bold; margin-top: 1px;">
                                [{{ $t->moduleSystem->name }}]
                            </div>
                        @endif
                        <div style="margin-top: 4px;">
                            @if($status === 'CLOSED')
                                <span class="badge badge-closed">CLOSED</span>
                            @elseif($status === 'PROGRESS')
                                <span class="badge badge-progress">PROGRESS</span>
                            @else
                                <span class="badge badge-open">OPEN</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a; font-size: 10px;">{{ $clientName }}</div>
                        @php
                            $advName = $t->advisor ? $t->advisor->name : ($t->pendingAdvisor ? $t->pendingAdvisor->name : null);
                        @endphp
                        @if($advName)
                            <div style="font-size: 8px; color: #b45309; font-weight: bold; margin-top: 2px;">
                                Adv: {{ $advName }}
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $isOnline ? 'badge-online' : 'badge-tipe' }}">
                            {{ $tipe }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a; font-size: 10px; margin-bottom: 3px;">{{ $t->title }}</div>
                        @if($t->description)
                            <div class="deskripsi-box">{{ strip_tags($t->description) }}</div>
                        @else
                            <div style="color: #94a3b8; font-style: italic; font-size: 9px;">Tidak ada catatan tambahan</div>
                        @endif
                    </td>
                    <td>
                        @if($t->solusi)
                            <div class="solusi-box">
                                <strong style="color: #15803d; font-size: 9px;">Solusi / Pengerjaan:</strong><br>
                                {{ strip_tags($t->solusi) }}
                            </div>
                        @else
                            <div style="color: #94a3b8; font-style: italic; font-size: 9px; padding: 2px 0;">
                                @if($status === 'CLOSED')
                                    Telah diselesaikan (catatan solusi tidak dicantumkan)
                                @else
                                    Dalam proses penanganan teknis
                                @endif
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="font-weight: bold; color: #1e40af; font-size: 10px;">{{ $t->duration_text ?: '-' }}</div>
                        <div style="font-size: 8px; color: #475569; margin-top: 2px;">
                            {{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') : '-' }}
                        </div>
                        @if($status === 'CLOSED' && $t->closed_at)
                            <div style="font-size: 8px; color: #15803d; font-weight: bold; margin-top: 2px;">
                                Selesai: {{ \Carbon\Carbon::parse($t->closed_at)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 28px; color: #64748b; font-style: italic; font-size: 11px;">
                        Tidak ada riwayat tiket yang dikerjakan pada modul {{ $module->name }} untuk periode {{ $periodeText }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Note (Tanpa Tanda Tangan) -->
    <table style="width: 100%; table-layout: fixed; margin-top: 10px; border-top: 1px solid #cbd5e1; padding-top: 5px;">
        <tr>
            <td style="font-size: 8px; color: #64748b; vertical-align: middle;">
                Dokumen ini dicetak secara otomatis oleh Probus Helpdesk Management System.
                Dicetak Oleh: <strong>{{ auth()->user()->name ?? 'User' }}</strong> pada {{ date('d/m/Y H:i') }} WITA.
            </td>
            <td style="font-size: 8px; color: #94a3b8; text-align: right; vertical-align: middle;">
                Probus System Indonesia &bull; IT Helpdesk Support
            </td>
        </tr>
    </table>
</body>
</html>
