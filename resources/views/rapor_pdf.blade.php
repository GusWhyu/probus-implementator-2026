<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Kinerja CS - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e293b;
            font-size: 24px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            color: #64748b;
            margin: 0;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            padding: 8px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            width: 150px;
        }
        .score-box {
            text-align: center;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .score-title {
            font-size: 16px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .score-value {
            font-size: 48px;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
        }
        .score-status {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            color: {{ ($rapor->total_score ?? 0) >= 80 ? '#10b981' : (($rapor->total_score ?? 0) >= 70 ? '#f59e0b' : '#ef4444') }};
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #cbd5e1;
        }
        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .notes-section {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .notes-title {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            color: #64748b;
            font-size: 12px;
        }
        .signature-box {
            float: right;
            text-align: center;
            width: 200px;
            margin-top: 40px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapor Kinerja Customer Service</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($rapor->periode ?? now())->translatedFormat('F Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Lengkap</td>
            <td>: {{ $user->name }}</td>
            <td class="info-label">Evaluator</td>
            <td>: {{ $rapor->reviewer->name ?? 'Tim Penilai' }}</td>
        </tr>
        <tr>
            <td class="info-label">Posisi / Departemen</td>
            <td>: Customer Service</td>
            <td class="info-label">Tanggal Cetak</td>
            <td>: {{ date('d F Y') }}</td>
        </tr>
    </table>

    <div class="score-box">
        <div class="score-title">Nilai Akhir Keseluruhan</div>
        <div class="score-value">{{ $rapor->total_score ?? 0 }}</div>
        <div class="score-status">
            {{ ($rapor->total_score ?? 0) >= 80 ? 'SANGAT BAIK' : (($rapor->total_score ?? 0) >= 70 ? 'BAIK' : 'CUKUP/KURANG') }}
        </div>
    </div>

    <h3 style="color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">Rincian Komponen Penilaian</h3>
    <table class="details-table">
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="text-center">Skor (1-100)</th>
                <th class="text-center">Bobot</th>
                <th class="text-right">Nilai Tertimbang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Penyelesaian Tiket & Produktivitas</td>
                <td class="text-center">{{ ($rapor->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket")?->score ?? 0) ?? 0 }}</td>
                <td class="text-center">30%</td>
                <td class="text-right">{{ (($rapor->details->firstWhere("category.name", "Produktivitas & Resolusi Tiket")?->score ?? 0) ?? 0) * 0.3 }}</td>
            </tr>
            <tr>
                <td>Kualitas Penyelesaian CS</td>
                <td class="text-center">{{ ($rapor->details->firstWhere("category.name", "Kualitas Solusi")?->score ?? 0) ?? 0 }}</td>
                <td class="text-center">40%</td>
                <td class="text-right">{{ (($rapor->details->firstWhere("category.name", "Kualitas Solusi")?->score ?? 0) ?? 0) * 0.4 }}</td>
            </tr>
            <tr>
                <td>Kepuasan Klien</td>
                <td class="text-center">{{ ($rapor->details->firstWhere("category.name", "Kepuasan Klien (Survey)")?->score ?? 0) ?? 0 }}</td>
                <td class="text-center">30%</td>
                <td class="text-right">{{ (($rapor->details->firstWhere("category.name", "Kepuasan Klien (Survey)")?->score ?? 0) ?? 0) * 0.3 }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="2" class="text-right">TOTAL NILAI AKHIR</td>
                <td class="text-center">100%</td>
                <td class="text-right" style="color: #059669;">{{ $rapor->total_score ?? 0 }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="notes-section">
        <div class="notes-title">Catatan Penilaian:</div>
        <div>
            {!! nl2br(e($rapor->catatan ?? 'Tidak ada catatan.')) !!}
        </div>
    </div>

    <div class="notes-section" style="border-left-color: #f59e0b;">
        <div class="notes-title">Saran & Rekomendasi:</div>
        <div>
            {!! nl2br(e($rapor->saran ?? 'Tidak ada saran/rekomendasi.')) !!}
        </div>
    </div>

    <div class="signature-box">
        <p>Disetujui Oleh,</p>
        <div class="signature-line">
            {{ $rapor->reviewer->name ?? 'Manajemen' }}
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} PT. Probus System. Dokumen ini digenerate secara otomatis.</p>
    </div>
</body>
</html>
