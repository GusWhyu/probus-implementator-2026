<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tiket {{ ($module->id ?? '') === 'all' ? 'Keseluruhan' : 'Modul ' . $module->name }} - {{ $user->name }}</title>
    <style>
        @page {
            margin: 22px 38px 22px 38px;
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
            font-size: 10px;
            line-height: 1.35;
            background-color: #ffffff;
            width: 100%;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }
        .header-table {
            width: 100%;
            table-layout: fixed;
            margin-bottom: 14px;
            border-bottom: 2.5px solid #1e40af;
            padding-bottom: 10px;
        }
        .meta-card {
            background-color: #f8fafc;
            border: 1.5px solid #94a3b8;
            border-radius: 6px;
            padding: 8px 12px;
            width: 100%;
        }
        .meta-table {
            width: 100%;
            table-layout: fixed;
        }
        .meta-table td {
            padding: 3px 4px;
            font-size: 10px;
            word-wrap: break-word;
        }
        .kpi-cell {
            padding: 7px 6px;
            border-radius: 6px;
            text-align: center;
            width: 100%;
        }
        .data-table {
            width: 100%;
            table-layout: fixed;
            border: 1.5px solid #475569;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #1e40af;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            padding: 7px 4px;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            word-wrap: break-word;
            overflow: hidden;
        }
        .data-table td {
            padding: 6px 6px;
            border: 1px solid #94a3b8;
            vertical-align: top;
            font-size: 9.5px;
            line-height: 1.35;
            word-wrap: break-word;
            overflow: hidden;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2.5px 6px;
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
            padding: 5px 6px;
            border-radius: 4px;
            color: #14532d;
            font-size: 9px;
            line-height: 1.35;
            margin-top: 3px;
            word-wrap: break-word;
        }
        .deskripsi-box {
            color: #334155;
            font-size: 9px;
            line-height: 1.35;
            margin-top: 3px;
            word-wrap: break-word;
        }
        .footer-table {
            width: 100%;
            table-layout: fixed;
            margin-top: 12px;
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
                <table style="width: auto; table-layout: auto;">
                    <tr>
                        @if($probusLogoSrc)
                        <td style="padding-right: 12px; vertical-align: middle;">
                            <img src="{{ $probusLogoSrc }}" style="height: 42px;" alt="Logo Probus">
                        </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div style="font-size: 15px; font-weight: bold; color: #1e40af; letter-spacing: 0.5px;">PROBUS SYSTEM INDONESIA</div>
                            <div style="font-size: 9px; color: #64748b; font-weight: 500;">Hospitality Property Management System & IT Helpdesk Support</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: middle;">
                <div style="font-size: 14px; font-weight: bold; color: #0f172a; text-transform: uppercase;">
                    {{ ($module->id ?? '') === 'all' ? 'LAPORAN RIWAYAT TIKET KESELURUHAN' : 'LAPORAN RIWAYAT TIKET PER MODUL' }}
                </div>
                <div style="font-size: 10.5px; font-weight: bold; color: #1e40af; margin-top: 2px;">
                    MODUL: {{ strtoupper($module->name) }}
                    @if(!empty($module->bobot) && $module->bobot !== '-')
                        <span style="font-size: 9px; color: #64748b; font-weight: normal;">(Bobot: {{ $module->bobot }})</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Meta Information Cards -->
    <table style="width: 100%; table-layout: fixed; margin-bottom: 12px;">
        <tr>
            <!-- Info Staff -->
            <td style="width: 48%; vertical-align: top; padding-right: 8px;">
                <div class="meta-card">
                    <table class="meta-table">
                        <tr>
                            <td style="width: 110px; color: #475569; font-weight: bold;">Nama Staff</td>
                            <td style="width: 8px;">:</td>
                            <td style="font-weight: bold; color: #0f172a;">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td style="color: #475569; font-weight: bold;">Departemen</td>
                            <td>:</td>
                            <td>{{ ucfirst($user->departemen->name ?? 'Operasional') }} &bull; <span style="text-transform: capitalize;">{{ $user->usertype ?? 'Staff' }}</span></td>
                        </tr>
                        <tr>
                            <td style="color: #475569; font-weight: bold;">Periode Evaluasi</td>
                            <td>:</td>
                            <td style="font-weight: bold; color: #1e40af;">{{ $periodeText }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            
            <!-- KPI Summary Cards -->
            <td style="width: 52%; vertical-align: top; padding-left: 8px;">
                <table style="width: 100%; table-layout: fixed;">
                    <tr>
                        <td style="width: 25%; padding-right: 4px;">
                            <div class="kpi-cell" style="background-color: #f1f5f9; border: 1.5px solid #94a3b8;">
                                <div style="font-size: 8px; font-weight: bold; color: #475569; text-transform: uppercase;">Total Tiket</div>
                                <div style="font-size: 15px; font-weight: bold; color: #0f172a; margin-top: 2px;">{{ $totalCount }}</div>
                            </div>
                        </td>
                        <td style="width: 25%; padding: 0 2px;">
                            <div class="kpi-cell" style="background-color: #dcfce7; border: 1.5px solid #86efac;">
                                <div style="font-size: 8px; font-weight: bold; color: #15803d; text-transform: uppercase;">Selesai (Closed)</div>
                                <div style="font-size: 15px; font-weight: bold; color: #15803d; margin-top: 2px;">{{ $closedCount }}</div>
                            </div>
                        </td>
                        <td style="width: 25%; padding: 0 2px;">
                            <div class="kpi-cell" style="background-color: #fef3c7; border: 1.5px solid #fde68a;">
                                <div style="font-size: 8px; font-weight: bold; color: #b45309; text-transform: uppercase;">Proses (Progress)</div>
                                <div style="font-size: 15px; font-weight: bold; color: #b45309; margin-top: 2px;">{{ $progressCount }}</div>
                            </div>
                        </td>
                        <td style="width: 25%; padding-left: 4px;">
                            <div class="kpi-cell" style="background-color: #dbeafe; border: 1.5px solid #bfdbfe;">
                                <div style="font-size: 8px; font-weight: bold; color: #1d4ed8; text-transform: uppercase;">Baru (Open)</div>
                                <div style="font-size: 15px; font-weight: bold; color: #1d4ed8; margin-top: 2px;">{{ $openCount }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
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

    <!-- Signatures & Footer -->
    <table class="footer-table">
        <tr>
            <td style="width: 55%; vertical-align: top; font-size: 8.5px; color: #64748b;">
                <div>Dokumen ini dicetak secara otomatis oleh Probus Helpdesk Management System.</div>
                <div>Dicetak Oleh: <strong>{{ auth()->user()->name ?? 'User' }}</strong> pada {{ date('d/m/Y H:i') }} WITA.</div>
            </td>
            <td style="width: 45%; text-align: right; vertical-align: top;">
                <table style="width: 100%; table-layout: fixed; text-align: center;">
                    <tr>
                        <td style="width: 50%; padding-right: 15px; padding-left: 10px; text-align: center;">
                            <div style="font-size: 9px; color: #475569;">Staff Pelaksana,</div>
                            <div style="height: 42px;"></div>
                            <div style="font-weight: bold; border-top: 1.5px solid #475569; padding-top: 3px; font-size: 10px; color: #0f172a;">{{ $user->name }}</div>
                            <div style="font-size: 8.5px; color: #64748b;">Staff {{ ucfirst($user->departemen->name ?? 'Operasional') }}</div>
                        </td>
                        <td style="width: 50%; padding-left: 15px; padding-right: 5px; text-align: center;">
                            <div style="font-size: 9px; color: #475569;">Mengetahui,</div>
                            <div style="height: 42px;"></div>
                            <div style="font-weight: bold; border-top: 1.5px solid #475569; padding-top: 3px; font-size: 10px; color: #0f172a;">Supervisor / Head Dept</div>
                            <div style="font-size: 8.5px; color: #64748b;">Probus System</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
