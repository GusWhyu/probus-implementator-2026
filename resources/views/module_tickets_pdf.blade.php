<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tiket Modul {{ $module->name }} - {{ $user->name }}</title>
    <style>
        @page {
            margin: 18px 22px 18px 22px;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
        }
        body {
            color: #0f172a;
            font-size: 8.5px;
            line-height: 1.3;
            background-color: #ffffff;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .header-table {
            width: 100%;
            margin-bottom: 12px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 8px;
        }
        .meta-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 2.5px 6px;
            font-size: 8.5px;
        }
        .kpi-table {
            width: 100%;
            margin-bottom: 12px;
        }
        .kpi-cell {
            padding: 6px 10px;
            border-radius: 6px;
            text-align: center;
        }
        .data-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #1e40af;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-align: center;
            padding: 6px 4px;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 6px 5px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 8px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 7.5px;
            text-align: center;
        }
        .badge-closed {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
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
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .badge-online {
            background-color: #f3e8ff;
            color: #7e22ce;
            border: 1px solid #e9d5ff;
        }
        .solusi-box {
            background-color: #f0fdf4;
            border-left: 2.5px solid #22c55e;
            padding: 4px 6px;
            border-radius: 3px;
            color: #14532d;
            font-size: 7.8px;
            line-height: 1.25;
            margin-top: 2px;
        }
        .deskripsi-box {
            color: #334155;
            font-size: 7.8px;
            line-height: 1.25;
        }
        .footer-table {
            width: 100%;
            margin-top: 10px;
        }
        .sign-box {
            text-align: center;
            width: 200px;
        }
    </style>
</head>
<body>
    @php
        // Logo Probus
        $logoPath = public_path('img/logo.png');
        $probusLogoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $totalCount = $tickets->count();
        $closedCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'CLOSED'; })->count();
        $progressCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'PROGRESS'; })->count();
        $openCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'OPEN'; })->count();
    @endphp

    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <td style="width: 50%; vertical-align: middle;">
                <table style="width: auto;">
                    <tr>
                        @if($probusLogoSrc)
                        <td style="padding-right: 10px; vertical-align: middle;">
                            <img src="{{ $probusLogoSrc }}" style="height: 38px;" alt="Logo Probus">
                        </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div style="font-size: 13px; font-weight: bold; color: #1e40af; letter-spacing: 0.5px;">PROBUS SYSTEM INDONESIA</div>
                            <div style="font-size: 8px; color: #64748b; font-weight: 500;">Hospitality Property Management System & IT Helpdesk Support</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: middle;">
                <div style="font-size: 13px; font-weight: bold; color: #0f172a; text-transform: uppercase;">LAPORAN RIWAYAT TIKET PER MODUL</div>
                <div style="font-size: 9.5px; font-weight: bold; color: #2563eb; margin-top: 2px;">
                    MODUL: {{ strtoupper($module->name) }} <span style="font-size: 8px; color: #64748b; font-weight: normal;">(Bobot: {{ $module->bobot }})</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Meta Information Cards -->
    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <!-- Info Staff -->
            <td style="width: 48%; vertical-align: top; padding-right: 6px;">
                <div class="meta-card">
                    <table class="meta-table">
                        <tr>
                            <td style="width: 90px; color: #64748b; font-weight: bold;">Nama Staff</td>
                            <td style="width: 8px;">:</td>
                            <td style="font-weight: bold; color: #0f172a;">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: bold;">Departemen</td>
                            <td>:</td>
                            <td>{{ ucfirst($user->departemen->name ?? 'Operasional') }} &bull; <span style="text-transform: capitalize;">{{ $user->usertype ?? 'Staff' }}</span></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: bold;">Periode Evaluasi</td>
                            <td>:</td>
                            <td style="font-weight: bold; color: #1e40af;">{{ $periodeText }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            
            <!-- KPI Summary Cards -->
            <td style="width: 52%; vertical-align: top; padding-left: 6px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 25%; padding: 0 3px;">
                            <div class="kpi-cell" style="background-color: #f1f5f9; border: 1px solid #cbd5e1;">
                                <div style="font-size: 7px; font-weight: bold; color: #475569; text-transform: uppercase;">Total Tiket</div>
                                <div style="font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 2px;">{{ $totalCount }}</div>
                            </div>
                        </td>
                        <td style="width: 25%; padding: 0 3px;">
                            <div class="kpi-cell" style="background-color: #dcfce7; border: 1px solid #86efac;">
                                <div style="font-size: 7px; font-weight: bold; color: #15803d; text-transform: uppercase;">Selesai (Closed)</div>
                                <div style="font-size: 13px; font-weight: bold; color: #15803d; margin-top: 2px;">{{ $closedCount }}</div>
                            </div>
                        </td>
                        <td style="width: 25%; padding: 0 3px;">
                            <div class="kpi-cell" style="background-color: #fef3c7; border: 1px solid #fde68a;">
                                <div style="font-size: 7px; font-weight: bold; color: #b45309; text-transform: uppercase;">Proses (Progress)</div>
                                <div style="font-size: 13px; font-weight: bold; color: #b45309; margin-top: 2px;">{{ $progressCount }}</div>
                            </div>
                        </td>
                        <td style="width: 25%; padding: 0 3px;">
                            <div class="kpi-cell" style="background-color: #dbeafe; border: 1px solid #bfdbfe;">
                                <div style="font-size: 7px; font-weight: bold; color: #1d4ed8; text-transform: uppercase;">Baru (Open)</div>
                                <div style="font-size: 13px; font-weight: bold; color: #1d4ed8; margin-top: 2px;">{{ $openCount }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Tiket -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 14%;">No. Tiket & Status</th>
                <th style="width: 14%;">Client / Outlet</th>
                <th style="width: 9%;">Penanganan</th>
                <th style="width: 25%;">Kendala / Masalah Dilaporkan</th>
                <th style="width: 25%;">Apa Yang Dikerjakan (Solusi)</th>
                <th style="width: 10%;">Waktu & Durasi</th>
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
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; font-family: monospace; font-size: 8.5px; color: #0f172a;">{{ $t->ticket_number }}</div>
                        <div style="margin-top: 3px;">
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
                        <div style="font-weight: bold; color: #1e293b;">{{ $clientName }}</div>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $isOnline ? 'badge-online' : 'badge-tipe' }}">
                            {{ $tipe }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a; margin-bottom: 2px;">{{ $t->title }}</div>
                        @if($t->description)
                            <div class="deskripsi-box">{{ strip_tags($t->description) }}</div>
                        @else
                            <div style="color: #94a3b8; font-style: italic;">Tidak ada catatan tambahan</div>
                        @endif
                    </td>
                    <td>
                        @if($t->solusi)
                            <div class="solusi-box">
                                <strong>Pengerjaan:</strong><br>
                                {{ strip_tags($t->solusi) }}
                            </div>
                        @else
                            <div style="color: #94a3b8; font-style: italic; padding: 3px 0;">
                                @if($status === 'CLOSED')
                                    Telah diselesaikan (catatan solusi tidak dicantumkan)
                                @else
                                    Dalam proses penanganan teknis
                                @endif
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="font-weight: bold; color: #1e40af;">{{ $t->duration_text ?: '-' }}</div>
                        <div style="font-size: 7px; color: #64748b; margin-top: 2px;">
                            {{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') : '-' }}
                        </div>
                        @if($status === 'CLOSED' && $t->closed_at)
                            <div style="font-size: 6.8px; color: #15803d; margin-top: 1px;">
                                Selesai: {{ \Carbon\Carbon::parse($t->closed_at)->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 24px; color: #64748b; font-style: italic;">
                        Tidak ada riwayat tiket yang dikerjakan pada modul {{ $module->name }} untuk periode {{ $periodeText }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatures & Footer -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%; vertical-align: top; font-size: 7.5px; color: #64748b;">
                <div>Dokumen ini dicetak secara otomatis oleh Probus Helpdesk Management System.</div>
                <div>Dicetak Oleh: <strong>{{ auth()->user()->name ?? 'User' }}</strong> pada {{ date('d/m/Y H:i') }} WITA.</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <table style="float: right; text-align: center;">
                    <tr>
                        <td class="sign-box" style="padding-right: 25px;">
                            <div style="font-size: 8px; color: #475569;">Staff Pelaksana,</div>
                            <div style="height: 40px;"></div>
                            <div style="font-weight: bold; border-top: 1px solid #94a3b8; padding-top: 2px; font-size: 8.5px;">{{ $user->name }}</div>
                            <div style="font-size: 7.5px; color: #64748b;">Staff {{ ucfirst($user->departemen->name ?? 'Operasional') }}</div>
                        </td>
                        <td class="sign-box">
                            <div style="font-size: 8px; color: #475569;">Mengetahui,</div>
                            <div style="height: 40px;"></div>
                            <div style="font-weight: bold; border-top: 1px solid #94a3b8; padding-top: 2px; font-size: 8.5px;">Supervisor / Head Dept</div>
                            <div style="font-size: 7.5px; color: #64748b;">Probus System</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
