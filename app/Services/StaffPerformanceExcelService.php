<?php

namespace App\Services;

use ZipArchive;
use Carbon\Carbon;

class StaffPerformanceExcelService
{
    /**
     * Membuat file Excel .xlsx murni (OpenXML) dari data kinerja staf dengan desain modern, rapi, dan konsisten.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $users
     * @param array $meta
     * @return string Path file sementara .xlsx
     */
    public static function createXlsx($users, array $meta = [])
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'perf_xlsx_');
        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Gagal membuat file arsip Excel sementara.");
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Kinerja Staff" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = self::buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $sheetXml = self::buildSheetXml($users, $meta);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();
        return $tempFile;
    }

    /**
     * Membangun file XML style (Fonts, Fills, Borders, CellXfs) dengan proporsi rapi, elegan, dan warna Probus.
     */
    private static function buildStylesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="20">
    <!-- 0: Normal 10pt Slate Dark -->
    <font><name val="Calibri"/><sz val="10"/><color rgb="FF0F172A"/></font>
    <!-- 1: Bold White 10.5pt (Header) -->
    <font><name val="Calibri"/><sz val="10.5"/><b/><color rgb="FFFFFFFF"/></font>
    <!-- 2: Title 17pt Bold Deep Royal Blue -->
    <font><name val="Calibri"/><sz val="17"/><b/><color rgb="FF1E40AF"/></font>
    <!-- 3: Subtitle/Meta 10pt Slate Darker -->
    <font><name val="Calibri"/><sz val="10"/><color rgb="FF334155"/></font>
    <!-- 4: KPI Total Label 8.5pt Bold Slate -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FF475569"/></font>
    <!-- 5: KPI Total Value 16pt Bold -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FF0F172A"/></font>
    <!-- 6: KPI Green Label 8.5pt Bold Green -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FF047857"/></font>
    <!-- 7: KPI Green Value 16pt Bold Green -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FF047857"/></font>
    <!-- 8: KPI Amber Label 8.5pt Bold Amber -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FFB45309"/></font>
    <!-- 9: KPI Amber Value 16pt Bold Amber -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FFB45309"/></font>
    <!-- 10: KPI Blue Label 8.5pt Bold Blue -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FF1D4ED8"/></font>
    <!-- 11: KPI Blue Value 16pt Bold Blue -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FF1D4ED8"/></font>
    <!-- 12: Badge Bold Green -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FF047857"/></font>
    <!-- 13: Badge Bold Blue -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FF1D4ED8"/></font>
    <!-- 14: Badge Bold Amber -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FFB45309"/></font>
    <!-- 15: Badge Bold Red -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FFB91C1C"/></font>
    <!-- 16: Badge Muted Slate -->
    <font><name val="Calibri"/><sz val="10"/><color rgb="FF64748B"/></font>
    <!-- 17: Staff Name Bold Dark -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FF0F172A"/></font>
    <!-- 18: Staff Email Slate -->
    <font><name val="Calibri"/><sz val="9.5"/><color rgb="FF475569"/></font>
    <!-- 19: Score Bold 10.5pt -->
    <font><name val="Calibri"/><sz val="10.5"/><b/><color rgb="FF0F172A"/></font>
  </fonts>
  <fills count="9">
    <!-- 0: None -->
    <fill><patternFill patternType="none"/></fill>
    <!-- 1: Gray125 -->
    <fill><patternFill patternType="gray125"/></fill>
    <!-- 2: Table Header Royal Blue #1D4ED8 (Probus Light Mode Primary Blue) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FF1D4ED8"/></patternFill></fill>
    <!-- 3: Zebra Alternating Row #F8FAFC -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
    <!-- 4: Soft Gray #F1F5F9 (Card Total & Belum Diproses) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>
    <!-- 5: Soft Green #DCFCE7 (Card Final & Sangat Baik) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/></patternFill></fill>
    <!-- 6: Soft Amber #FEF3C7 (Card Draft & Draft) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>
    <!-- 7: Soft Blue #EFF6FF (Card Avg & Baik) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/></patternFill></fill>
    <!-- 8: Soft Red #FEE2E2 (Cukup) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/></patternFill></fill>
  </fills>
  <borders count="7">
    <!-- 0: None -->
    <border><left/><right/><top/><bottom/></border>
    <!-- 1: Thin Slate Border #CBD5E1 (Data cells) -->
    <border>
      <left style="thin"><color rgb="FFCBD5E1"/></left>
      <right style="thin"><color rgb="FFCBD5E1"/></right>
      <top style="thin"><color rgb="FFCBD5E1"/></top>
      <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
    </border>
    <!-- 2: Header Border #3B82F6 / #1E3A8A -->
    <border>
      <left style="thin"><color rgb="FF3B82F6"/></left>
      <right style="thin"><color rgb="FF3B82F6"/></right>
      <top style="thin"><color rgb="FF3B82F6"/></top>
      <bottom style="medium"><color rgb="FF1E3A8A"/></bottom>
    </border>
    <!-- 3: Card Total Border #CBD5E1 -->
    <border>
      <left style="thin"><color rgb="FFCBD5E1"/></left>
      <right style="thin"><color rgb="FFCBD5E1"/></right>
      <top style="thin"><color rgb="FFCBD5E1"/></top>
      <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
    </border>
    <!-- 4: Card Green Border #86EFAC -->
    <border>
      <left style="thin"><color rgb="FF86EFAC"/></left>
      <right style="thin"><color rgb="FF86EFAC"/></right>
      <top style="thin"><color rgb="FF86EFAC"/></top>
      <bottom style="thin"><color rgb="FF86EFAC"/></bottom>
    </border>
    <!-- 5: Card Amber Border #FDE68A -->
    <border>
      <left style="thin"><color rgb="FFFDE68A"/></left>
      <right style="thin"><color rgb="FFFDE68A"/></right>
      <top style="thin"><color rgb="FFFDE68A"/></top>
      <bottom style="thin"><color rgb="FFFDE68A"/></bottom>
    </border>
    <!-- 6: Card Blue Border #BFDBFE -->
    <border>
      <left style="thin"><color rgb="FFBFDBFE"/></left>
      <right style="thin"><color rgb="FFBFDBFE"/></right>
      <top style="thin"><color rgb="FFBFDBFE"/></top>
      <bottom style="thin"><color rgb="FFBFDBFE"/></bottom>
    </border>
  </borders>
  <cellXfs count="26">
    <!-- 0: Data Left (White) -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 1: Data Center (White) -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 2: Data Left (Zebra #F8FAFC) -->
    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 3: Data Center (Zebra #F8FAFC) -->
    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 4: Table Header (Royal Blue #1D4ED8, White Bold, Center, Wrapped) -->
    <xf numFmtId="0" fontId="1" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <!-- 5: Title (Bold 17pt Royal Blue, Center) -->
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 6: Subtitle / Meta info (10pt Slate, Center) -->
    <xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 7: KPI Total Label (Gray fill, border, center) -->
    <xf numFmtId="0" fontId="4" fillId="4" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 8: KPI Total Value (Bold 16pt, Gray fill, border, center) -->
    <xf numFmtId="0" fontId="5" fillId="4" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 9: KPI Final Label (Soft Green fill, border, center) -->
    <xf numFmtId="0" fontId="6" fillId="5" borderId="4" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 10: KPI Final Value (Bold 16pt Green, Soft Green fill, border, center) -->
    <xf numFmtId="0" fontId="7" fillId="5" borderId="4" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 11: KPI Draft Label (Soft Amber fill, border, center) -->
    <xf numFmtId="0" fontId="8" fillId="6" borderId="5" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 12: KPI Draft Value (Bold 16pt Amber, Soft Amber fill, border, center) -->
    <xf numFmtId="0" fontId="9" fillId="6" borderId="5" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 13: KPI Avg Label (Soft Blue fill, border, center) -->
    <xf numFmtId="0" fontId="10" fillId="7" borderId="6" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 14: KPI Avg Value (Bold 16pt Blue, Soft Blue fill, border, center) -->
    <xf numFmtId="0" fontId="11" fillId="7" borderId="6" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 15: Staff Name Left White (Bold Dark) -->
    <xf numFmtId="0" fontId="17" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 16: Staff Name Left Zebra (Bold Dark) -->
    <xf numFmtId="0" fontId="17" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 17: Email Left White (Slate) -->
    <xf numFmtId="0" fontId="18" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 18: Email Left Zebra (Slate) -->
    <xf numFmtId="0" fontId="18" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 19: Badge Green (Soft Green fill, Green text, border 1, center) -->
    <xf numFmtId="0" fontId="12" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 20: Badge Blue (Soft Blue fill, Blue text, border 1, center) -->
    <xf numFmtId="0" fontId="13" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 21: Badge Amber (Soft Amber fill, Amber text, border 1, center) -->
    <xf numFmtId="0" fontId="14" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 22: Badge Red (Soft Red fill, Red text, border 1, center) -->
    <xf numFmtId="0" fontId="15" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 23: Badge Gray (Soft Gray fill, Muted Slate text, border 1, center) -->
    <xf numFmtId="0" fontId="16" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 24: Score Bold Center White (10.5pt Bold) -->
    <xf numFmtId="0" fontId="19" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 25: Score Bold Center Zebra (10.5pt Bold) -->
    <xf numFmtId="0" fontId="19" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  </cellXfs>
</styleSheet>';
    }

    /**
     * Sanitasi dan escape teks untuk XML
     */
    private static function xmlEscape($str)
    {
        if ($str === null) return '';
        // Hapus karakter kontrol ASCII non-printable kecuali \t, \n, \r
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string)$str);
        return htmlspecialchars($clean, ENT_XML1, 'UTF-8');
    }

    /**
     * Konversi index kolom (1-based) menjadi huruf kolom Excel (1=>A, 2=>B, ..., 10=>J)
     */
    private static function colLetter($index)
    {
        $letters = '';
        while ($index > 0) {
            $rem = ($index - 1) % 26;
            $letters = chr(65 + $rem) . $letters;
            $index = intdiv($index - 1, 26);
        }
        return $letters;
    }

    /**
     * Membangun sheet1.xml lengkap dengan proporsi yang pas, 4 kartu KPI, 
     * auto-filter, freeze pane pada baris header, dan footer akumulasi.
     */
    private static function buildSheetXml($users, array $meta = [])
    {
        $month = (int)($meta['month'] ?? date('m'));
        $year = (int)($meta['year'] ?? date('Y'));
        $periodDate = Carbon::createFromDate($year, $month, 1);
        $periodLabel = $meta['period_label'] ?? $periodDate->locale('id')->translatedFormat('F Y');
        $cutoffLabel = $meta['cutoff_label'] ?? $periodDate->copy()->endOfMonth()->locale('id')->translatedFormat('d F Y');
        $deptName = $meta['departemen_name'] ?? 'Operasional';
        $printedBy = $meta['printed_by'] ?? 'User';
        $printedAt = $meta['printed_at'] ?? (date('d/m/Y H:i') . ' WITA');

        $monthClosedTickets = $meta['monthClosedTickets'] ?? [];
        $monthAdvisedTickets = $meta['monthAdvisedTickets'] ?? [];
        $monthSurveys = $meta['monthSurveys'] ?? [];

        // Hitung statistik keseluruhan untuk KPI summary cards
        $totalStaff = $users->count();
        $sudahDinilaiCount = 0;
        $belumDinilaiCount = 0;
        $totalClosedAll = 0;
        $totalAdvisedAll = 0;
        $sumFinalScore = 0;
        $countFinalScore = 0;
        $sumCsat = 0;
        $countCsat = 0;

        foreach ($users as $u) {
            $penilaian = $u->performanceReviews->first();
            $isFinal = $penilaian && strtoupper($penilaian->status) === 'FINAL';

            if ($isFinal) {
                $sudahDinilaiCount++;
                $scoreVal = (float)$penilaian->total_score;
                $sumFinalScore += $scoreVal;
                $countFinalScore++;
            } else {
                $belumDinilaiCount++;
            }

            $closed = $penilaian ? (int)$penilaian->total_ticket : (int)($monthClosedTickets[$u->id] ?? 0);
            $advised = (int)($monthAdvisedTickets[$u->id] ?? ($penilaian->total_ticket_yang_di_advice ?? 0));
            $totalClosedAll += $closed;
            $totalAdvisedAll += $advised;

            $csatRating = 0;
            if ($penilaian && $penilaian->review_score > 0) {
                $csatRating = round(((float)$penilaian->review_score) / 20, 1);
            } elseif (isset($monthSurveys[$u->id]) && $monthSurveys[$u->id] > 0) {
                $csatRating = round((float)$monthSurveys[$u->id], 1);
            } elseif ($closed > 0) {
                $csatRating = 4.5;
            }

            if ($csatRating > 0) {
                $sumCsat += $csatRating;
                $countCsat++;
            }
        }

        $avgFinalScore = $countFinalScore > 0 ? round($sumFinalScore / $countFinalScore, 1) : 0;
        $avgCsat = $countCsat > 0 ? round($sumCsat / $countCsat, 1) : 0;
        $persenFinal = $totalStaff > 0 ? round(($sudahDinilaiCount / $totalStaff) * 100, 1) : 0;

        // 10 Kolom dengan lebar proporsional dan luas
        $cols = [
            ['title' => 'No', 'width' => 8],
            ['title' => 'Nama Staff', 'width' => 26],
            ['title' => 'Email', 'width' => 30],
            ['title' => 'Periode', 'width' => 15],
            ['title' => 'Tiket Selesai', 'width' => 17],
            ['title' => 'Tiket Di-Advice', 'width' => 17],
            ['title' => 'Kepuasan Klien', 'width' => 20],
            ['title' => 'Nilai Akhir', 'width' => 16],
            ['title' => 'Predikat', 'width' => 18],
            ['title' => 'Status Penilaian', 'width' => 24],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";

        // Freeze Panes pada baris 7 (Header tabel terkunci saat scroll)
        $xml .= '  <sheetViews>' . "\n";
        $xml .= '    <sheetView tabSelected="1" workbookViewId="0">' . "\n";
        $xml .= '      <pane ySplit="7" topLeftCell="A8" activePane="bottomLeft" state="frozen"/>' . "\n";
        $xml .= '    </sheetView>' . "\n";
        $xml .= '  </sheetViews>' . "\n";

        // Lebar kolom yang disetel secara presisi
        $xml .= '  <cols>' . "\n";
        foreach ($cols as $idx => $col) {
            $colNum = $idx + 1;
            $xml .= '    <col min="' . $colNum . '" max="' . $colNum . '" width="' . $col['width'] . '" customWidth="1"/>' . "\n";
        }
        $xml .= '  </cols>' . "\n";

        $xml .= '  <sheetData>' . "\n";

        // Row 1: Header Judul Laporan (Tinggi 36pt, Center & Merged A1:J1)
        $xml .= '    <row r="1" ht="36" customHeight="1">' . "\n";
        $xml .= '      <c r="A1" s="5" t="inlineStr"><is><t>' . self::xmlEscape('PROBUS SYSTEM - LAPORAN KINERJA STAFF') . '</t></is></c>' . "\n";
        for ($colIdx = 2; $colIdx <= 10; $colIdx++) {
            $colLetter = self::colLetter($colIdx);
            $xml .= '      <c r="' . $colLetter . '1" s="5"/>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Row 2: Subtitle Info Departemen & Periode (Tinggi 22pt, Center & Merged A2:J2)
        $subInfo = "Departemen: " . $deptName . "   •   Periode: " . $periodLabel . "   •   Batas Cut-off: " . $cutoffLabel;
        if (!empty($meta['status_filter']) && $meta['status_filter'] !== 'Semua Status') {
            $subInfo .= "   •   Filter: " . $meta['status_filter'];
        }
        if (!empty($meta['search'])) {
            $subInfo .= '   •   Kata Kunci: "' . $meta['search'] . '"';
        }
        $subInfo .= "   •   Dicetak Oleh: " . $printedBy . " (" . $printedAt . ")";
        $xml .= '    <row r="2" ht="22" customHeight="1">' . "\n";
        $xml .= '      <c r="A2" s="6" t="inlineStr"><is><t>' . self::xmlEscape($subInfo) . '</t></is></c>' . "\n";
        for ($colIdx = 2; $colIdx <= 10; $colIdx++) {
            $colLetter = self::colLetter($colIdx);
            $xml .= '      <c r="' . $colLetter . '2" s="6"/>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Row 3: Blank separator (Tinggi 8pt)
        $xml .= '    <row r="3" ht="8" customHeight="1"/>' . "\n";

        // Row 4: KPI Summary Cards - Label Row (Tinggi 20pt)
        // Card 1: A4:B4 (Total Staff)
        // Card 2: C4:D4 (Sudah Dinilai)
        // Card 3: E4:G4 (Belum Dinilai / Draft)
        // Card 4: H4:J4 (Rata-Rata Nilai Akhir)
        $xml .= '    <row r="4" ht="20" customHeight="1">' . "\n";
        // Card 1 Total (A4..B4)
        $xml .= '      <c r="A4" s="7" t="inlineStr"><is><t>TOTAL STAFF</t></is></c>' . "\n";
        $xml .= '      <c r="B4" s="7"/>' . "\n";
        // Card 2 Final (C4..D4)
        $xml .= '      <c r="C4" s="9" t="inlineStr"><is><t>SUDAH DINILAI (FINAL)</t></is></c>' . "\n";
        $xml .= '      <c r="D4" s="9"/>' . "\n";
        // Card 3 Draft / Belum (E4..G4)
        $xml .= '      <c r="E4" s="11" t="inlineStr"><is><t>BELUM DINILAI / DRAFT</t></is></c>' . "\n";
        $xml .= '      <c r="F4" s="11"/>' . "\n";
        $xml .= '      <c r="G4" s="11"/>' . "\n";
        // Card 4: Total Tiket Selesai (H4..J4)
        $xml .= '      <c r="H4" s="13" t="inlineStr"><is><t>TOTAL TIKET SELESAI</t></is></c>' . "\n";
        $xml .= '      <c r="I4" s="13"/>' . "\n";
        $xml .= '      <c r="J4" s="13"/>' . "\n";
        $xml .= '    </row>' . "\n";

        // Row 5: KPI Summary Cards - Value Row (Tinggi 28pt)
        $xml .= '    <row r="5" ht="28" customHeight="1">' . "\n";
        // Card 1 Total Value (A5..B5)
        $xml .= '      <c r="A5" s="8"><v>' . $totalStaff . '</v></c>' . "\n";
        $xml .= '      <c r="B5" s="8"/>' . "\n";
        // Card 2 Final Value (C5..D5)
        $xml .= '      <c r="C5" s="10" t="inlineStr"><is><t>' . $sudahDinilaiCount . ' Staff (' . $persenFinal . '%)</t></is></c>' . "\n";
        $xml .= '      <c r="D5" s="10"/>' . "\n";
        // Card 3 Draft Value (E5..G5)
        $xml .= '      <c r="E5" s="12" t="inlineStr"><is><t>' . $belumDinilaiCount . ' Staff</t></is></c>' . "\n";
        $xml .= '      <c r="F5" s="12"/>' . "\n";
        $xml .= '      <c r="G5" s="12"/>' . "\n";
        // Card 4 Total Tiket Selesai Value (H5..J5)
        $xml .= '      <c r="H5" s="14" t="inlineStr"><is><t>' . $totalClosedAll . ' Tiket</t></is></c>' . "\n";
        $xml .= '      <c r="I5" s="14"/>' . "\n";
        $xml .= '      <c r="J5" s="14"/>' . "\n";
        $xml .= '    </row>' . "\n";

        // Row 6: Blank separator (Tinggi 12pt)
        $xml .= '    <row r="6" ht="12" customHeight="1"/>' . "\n";

        // Row 7: Main Table Headers (Tinggi 32pt, Royal Blue #1D4ED8 & Bold White)
        $headerRow = 7;
        $xml .= '    <row r="' . $headerRow . '" ht="32" customHeight="1">' . "\n";
        foreach ($cols as $idx => $col) {
            $colLetter = self::colLetter($idx + 1);
            $xml .= '      <c r="' . $colLetter . $headerRow . '" s="4" t="inlineStr"><is><t>' . self::xmlEscape($col['title']) . '</t></is></c>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Rows 8+: Data Rows (Tinggi 24pt)
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($users as $u) {
            $isZebra = ($no % 2 === 0);
            $sCenter = $isZebra ? 3 : 1;
            $sName = $isZebra ? 16 : 15;
            $sEmail = $isZebra ? 18 : 17;
            $sScore = $isZebra ? 25 : 24;

            $penilaian = $u->performanceReviews->first();
            $isFinal = $penilaian && strtoupper($penilaian->status) === 'FINAL';
            $isDraft = $penilaian && strtoupper($penilaian->status) === 'DRAFT';

            $ticketClosedCount = $penilaian ? (int)$penilaian->total_ticket : (int)($monthClosedTickets[$u->id] ?? 0);
            $ticketAdviceCount = (int)($monthAdvisedTickets[$u->id] ?? ($penilaian->total_ticket_yang_di_advice ?? 0));

            // CSAT
            $csatRating = 0;
            $csatScore = 0;
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

            $csatText = $csatRating > 0 ? (number_format($csatRating, 1, ',', '.') . ' / 5.0 (' . round($csatScore) . '%)') : '-';

            // Nilai Akhir & Predikat
            $scoreText = '-';
            $predikatText = '-';
            $sPredikat = $sCenter; // Default center

            if ($isFinal) {
                $scoreVal = (float)$penilaian->total_score;
                $scoreText = number_format($scoreVal, 1, ',', '.');
                if ($scoreVal >= 80) {
                    $predikatText = 'Sangat Baik';
                    $sPredikat = 19; // Soft Green
                } elseif ($scoreVal >= 70) {
                    $predikatText = 'Baik';
                    $sPredikat = 20; // Soft Blue
                } else {
                    $predikatText = 'Cukup';
                    $sPredikat = 22; // Soft Red
                }
            } elseif ($isDraft) {
                $scoreVal = (float)($penilaian->module_score ?? 0);
                $scoreText = 'Draft (' . number_format($scoreVal, 0) . ')';
                $predikatText = 'Draft';
                $sPredikat = 21; // Soft Amber
            }

            // Status Penilaian
            $statusText = 'Belum Diproses';
            $sStatus = 23; // Soft Gray
            if ($isFinal) {
                $statusText = 'Sudah Dinilai (Final)';
                $sStatus = 19; // Soft Green
            } elseif ($isDraft) {
                $statusText = 'Terproses (Draft)';
                $sStatus = 21; // Soft Amber
            }

            $xml .= '    <row r="' . $currentRow . '" ht="24" customHeight="1">' . "\n";
            // A: No
            $xml .= '      <c r="A' . $currentRow . '" s="' . $sCenter . '"><v>' . $no . '</v></c>' . "\n";
            // B: Nama Staff
            $xml .= '      <c r="B' . $currentRow . '" s="' . $sName . '" t="inlineStr"><is><t>' . self::xmlEscape($u->name) . '</t></is></c>' . "\n";
            // C: Email
            $xml .= '      <c r="C' . $currentRow . '" s="' . $sEmail . '" t="inlineStr"><is><t>' . self::xmlEscape($u->email) . '</t></is></c>' . "\n";
            // D: Periode
            $xml .= '      <c r="D' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($periodDate->locale('id')->translatedFormat('M Y')) . '</t></is></c>' . "\n";
            // E: Tiket Selesai
            $xml .= '      <c r="E' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . $ticketClosedCount . ' Tiket</t></is></c>' . "\n";
            // F: Tiket Di-Advice
            $xml .= '      <c r="F' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . $ticketAdviceCount . ' Tiket</t></is></c>' . "\n";
            // G: Kepuasan Klien
            $xml .= '      <c r="G' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($csatText) . '</t></is></c>' . "\n";
            // H: Nilai Akhir
            $xml .= '      <c r="H' . $currentRow . '" s="' . $sScore . '" t="inlineStr"><is><t>' . self::xmlEscape($scoreText) . '</t></is></c>' . "\n";
            // I: Predikat
            $xml .= '      <c r="I' . $currentRow . '" s="' . $sPredikat . '" t="inlineStr"><is><t>' . self::xmlEscape($predikatText) . '</t></is></c>' . "\n";
            // J: Status Penilaian
            $xml .= '      <c r="J' . $currentRow . '" s="' . $sStatus . '" t="inlineStr"><is><t>' . self::xmlEscape($statusText) . '</t></is></c>' . "\n";
            $xml .= '    </row>' . "\n";

            $currentRow++;
            $no++;
        }

        $xml .= '  </sheetData>' . "\n";

        // Merge cells untuk Judul, Subtitle, dan 4 KPI Cards
        $xml .= '  <mergeCells count="10">' . "\n";
        $xml .= '    <mergeCell ref="A1:J1"/>' . "\n";
        $xml .= '    <mergeCell ref="A2:J2"/>' . "\n";
        $xml .= '    <mergeCell ref="A4:B4"/>' . "\n";
        $xml .= '    <mergeCell ref="A5:B5"/>' . "\n";
        $xml .= '    <mergeCell ref="C4:D4"/>' . "\n";
        $xml .= '    <mergeCell ref="C5:D5"/>' . "\n";
        $xml .= '    <mergeCell ref="E4:G4"/>' . "\n";
        $xml .= '    <mergeCell ref="E5:G5"/>' . "\n";
        $xml .= '    <mergeCell ref="H4:J4"/>' . "\n";
        $xml .= '    <mergeCell ref="H5:J5"/>' . "\n";
        $xml .= '  </mergeCells>' . "\n";

        $xml .= '</worksheet>';

        return $xml;
    }
}
