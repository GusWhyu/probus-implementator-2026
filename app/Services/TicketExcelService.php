<?php

namespace App\Services;

use ZipArchive;
use Carbon\Carbon;

class TicketExcelService
{
    /**
     * Membuat file Excel .xlsx murni (OpenXML) dari koleksi tiket dengan desain premium.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $tickets
     * @param array $meta
     * @return string Path file sementara .xlsx
     */
        /**
     * Membuat file Excel .xlsx murni (OpenXML) khusus untuk riwayat pengerjaan tiket per modul.
     * Menampilkan detail apa yang dikerjakan (solusi/pengerjaan) dan deskripsi masalah.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $tickets
     * @param array $meta
     * @return string Path file sementara .xlsx
     */
    public static function createModuleTicketsXlsx($tickets, array $meta = [])
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tck_mod_xlsx_');
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
        $sheetTitle = 'Tiket ' . (!empty($meta['module_name']) ? mb_substr($meta['module_name'], 0, 20) : 'Modul');
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="' . self::xmlEscape($sheetTitle) . '" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = self::buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $sheetXml = self::buildModuleSheetXml($tickets, $meta);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();
        return $tempFile;
    }

public static function createXlsx($tickets, array $meta = [])
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tck_xlsx_');
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
    <sheet name="Daftar Tiket" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml
        $styles = self::buildStylesXml();
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. xl/worksheets/sheet1.xml
        $sheetXml = self::buildSheetXml($tickets, $meta);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();
        return $tempFile;
    }

    /**
     * Membangun file XML style (Fonts, Fills, Borders, CellXfs) dengan proporsi rapi dan elegan.
     */
    private static function buildStylesXml()
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="16">
    <!-- 0: Normal 10pt Slate Dark -->
    <font><name val="Calibri"/><sz val="10"/><color rgb="FF0F172A"/></font>
    <!-- 1: Bold White 10.5pt (Header) -->
    <font><name val="Calibri"/><sz val="10.5"/><b/><color rgb="FFFFFFFF"/></font>
    <!-- 2: Title 17pt Bold Deep Royal Blue -->
    <font><name val="Calibri"/><sz val="17"/><b/><color rgb="FF1E40AF"/></font>
    <!-- 3: Subtitle/Meta 10pt Slate Darker -->
    <font><name val="Calibri"/><sz val="10"/><color rgb="FF334155"/></font>
    <!-- 4: KPI Total Label 8.5pt Bold -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FF475569"/></font>
    <!-- 5: KPI Total Value 16pt Bold -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FF0F172A"/></font>
    <!-- 6: KPI Open Label 8.5pt Bold Blue -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FF1D4ED8"/></font>
    <!-- 7: KPI Open Value 16pt Bold Blue -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FF1D4ED8"/></font>
    <!-- 8: KPI Progress Label 8.5pt Bold Amber -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FFB45309"/></font>
    <!-- 9: KPI Progress Value 16pt Bold Amber -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FFB45309"/></font>
    <!-- 10: KPI Closed Label 8.5pt Bold Green -->
    <font><name val="Calibri"/><sz val="8.5"/><b/><color rgb="FF047857"/></font>
    <!-- 11: KPI Closed Value 16pt Bold Green -->
    <font><name val="Calibri"/><sz val="16"/><b/><color rgb="FF047857"/></font>
    <!-- 12: Status OPEN Bold Blue -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FF1D4ED8"/></font>
    <!-- 13: Status PROGRESS Bold Amber -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FFB45309"/></font>
    <!-- 14: Status CLOSED Bold Green -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FF047857"/></font>
    <!-- 15: Ticket Number Bold Dark -->
    <font><name val="Calibri"/><sz val="10"/><b/><color rgb="FF0F172A"/></font>
  </fonts>
  <fills count="8">
    <!-- 0: None -->
    <fill><patternFill patternType="none"/></fill>
    <!-- 1: Gray125 -->
    <fill><patternFill patternType="gray125"/></fill>
    <!-- 2: Table Header Vibrant Blue #1D4ED8 (Probus Primary Blue) -->
    <fill><patternFill patternType="solid"><fgColor rgb="FF1D4ED8"/></patternFill></fill>
    <!-- 3: Zebra Alternating Row #F8FAFC -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
    <!-- 4: Soft Blue Status OPEN #EFF6FF -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/></patternFill></fill>
    <!-- 5: Soft Amber Status PROGRESS #FEF3C7 -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>
    <!-- 6: Soft Green Status CLOSED #DCFCE7 -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/></patternFill></fill>
    <!-- 7: KPI Card Total Gray #F1F5F9 -->
    <fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>
  </fills>
  <borders count="6">
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
    <!-- 4: Card Open Border #BFDBFE -->
    <border>
      <left style="thin"><color rgb="FFBFDBFE"/></left>
      <right style="thin"><color rgb="FFBFDBFE"/></right>
      <top style="thin"><color rgb="FFBFDBFE"/></top>
      <bottom style="thin"><color rgb="FFBFDBFE"/></bottom>
    </border>
    <!-- 5: Card Amber Border #FDE68A -->
    <border>
      <left style="thin"><color rgb="FFFDE68A"/></left>
      <right style="thin"><color rgb="FFFDE68A"/></right>
      <top style="thin"><color rgb="FFFDE68A"/></top>
      <bottom style="thin"><color rgb="FFFDE68A"/></bottom>
    </border>
  </borders>
  <cellXfs count="22">
    <!-- 0: Data Left (White) -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 1: Data Center (White) -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 2: Data Left (Zebra #F8FAFC) -->
    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf>
    <!-- 3: Data Center (Zebra #F8FAFC) -->
    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 4: Table Header (Navy #0F172A, White Bold, Center, Wrapped) -->
    <xf numFmtId="0" fontId="1" fillId="2" borderId="2" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <!-- 5: Title (Bold 17pt Royal Blue, Center) -->
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 6: Subtitle / Meta info (10pt Slate, Center) -->
    <xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 7: KPI Total Label (Gray fill, border, center) -->
    <xf numFmtId="0" fontId="4" fillId="7" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 8: KPI Total Value (Bold 16pt, Gray fill, border, center) -->
    <xf numFmtId="0" fontId="5" fillId="7" borderId="3" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 9: KPI Open Label (Soft Blue fill, border, center) -->
    <xf numFmtId="0" fontId="6" fillId="4" borderId="4" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 10: KPI Open Value (Bold 16pt Blue, Soft Blue fill, border, center) -->
    <xf numFmtId="0" fontId="7" fillId="4" borderId="4" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 11: KPI Progress Label (Soft Amber fill, border, center) -->
    <xf numFmtId="0" fontId="8" fillId="5" borderId="5" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 12: KPI Progress Value (Bold 16pt Amber, Soft Amber fill, border, center) -->
    <xf numFmtId="0" fontId="9" fillId="5" borderId="5" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 13: KPI Closed Label (Soft Green fill, border, center) -->
    <xf numFmtId="0" fontId="10" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 14: KPI Closed Value (Bold 16pt Green, Soft Green fill, border, center) -->
    <xf numFmtId="0" fontId="11" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 15: Status OPEN (Blue fill, Blue text, Center, border) -->
    <xf numFmtId="0" fontId="12" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 16: Status PROGRESS (Amber fill, Amber text, Center, border) -->
    <xf numFmtId="0" fontId="13" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 17: Status CLOSED (Green fill, Green text, Center, border) -->
    <xf numFmtId="0" fontId="14" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 18: Ticket Number (Bold, Center, White) -->
    <xf numFmtId="0" fontId="15" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 19: Ticket Number Zebra (Bold, Center, Zebra) -->
    <xf numFmtId="0" fontId="15" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- 20: Data Left Wrapped (White, Top Alignment) -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>
    <!-- 21: Data Left Wrapped Zebra (#F8FAFC, Top Alignment) -->
    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>
  </cellXfs>
</styleSheet>';
    }

    /**
     * Sanitasi dan escape teks untuk XML
     */
    /**
     * Membersihkan teks HTML (dari rich text editor) agar menjadi teks murni dengan baris rapi untuk Excel
     */
    private static function cleanTextForExcel($text)
    {
        if (empty($text)) return '-';
        // Ubah tag break & penutup blok menjadi newline
        $str = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $str = preg_replace('/<\/(p|div|li|tr|h[1-6])>/i', "\n", $str);
        $str = str_replace('&nbsp;', ' ', $str);
        $str = html_entity_decode(strip_tags($str), ENT_QUOTES, 'UTF-8');
        // Bersihkan whitespace berlebih per baris
        $lines = explode("\n", $str);
        $cleanLines = [];
        foreach ($lines as $l) {
            $trimmed = trim(preg_replace('/[ \t]+/', ' ', $l));
            if ($trimmed !== '') {
                $cleanLines[] = $trimmed;
            }
        }
        $result = implode("\n", $cleanLines);
        return !empty($result) ? $result : '-';
    }

    /**
     * Estimasi jumlah baris teks pada lebar kolom tertentu untuk kalkulasi tinggi baris dinamis
     */
    private static function estimateLines($text, $colWidth)
    {
        if (empty($text) || $text === '-') return 1;
        $lines = explode("\n", $text);
        $total = 0;
        $charsPerLine = max(10, (int)($colWidth * 0.95));
        foreach ($lines as $line) {
            $len = mb_strlen($line);
            if ($len === 0) {
                $total += 1;
            } else {
                $total += (int)ceil($len / $charsPerLine);
            }
        }
        return max(1, $total);
    }

    private static function xmlEscape($str)
    {
        if ($str === null) return '';
        // Hapus karakter kontrol ASCII non-printable kecuali \t, \n, \r
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string)$str);
        return htmlspecialchars($clean, ENT_XML1, 'UTF-8');
    }

    /**
     * Konversi index kolom (1-based) menjadi huruf kolom Excel (1=>A, 2=>B, ..., 14=>N)
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
     * Membangun sheet1.xml lengkap dengan proporsi yang pas, kartu KPI luas dan rapi, 
     * tanpa kolom rating klien, freeze pane di baris header, dan auto-filter.
     */
    private static function buildSheetXml($tickets, array $meta = [])
    {
        $statusFilter = !empty($meta['status']) ? strtoupper($meta['status']) : 'SEMUA STATUS';
        $userFilter = !empty($meta['user_name']) ? $meta['user_name'] : 'Semua User';
        $systemFilter = !empty($meta['system_name']) ? $meta['system_name'] : 'Semua System';
        $moduleFilter = !empty($meta['module_name']) ? $meta['module_name'] : 'Semua Module';
        $searchFilter = !empty($meta['search']) ? '"' . $meta['search'] . '"' : null;
        $printedAt = !empty($meta['printed_at']) ? $meta['printed_at'] : date('d/m/Y H:i') . ' WITA';
        $printedBy = !empty($meta['printed_by']) ? $meta['printed_by'] : 'User';

        // Hitung statistik
        $totalCount = $tickets->count();
        $openCount = $tickets->where('status', 'OPEN')->count();
        $progressCount = $tickets->where('status', 'PROGRESS')->count();
        $closedCount = $tickets->where('status', 'CLOSED')->count();

        // 15 Kolom dengan lebar yang luas, proporsional, dan adaptif agar tidak ada teks terpotong
        $cols = [
            ['title' => 'No', 'width' => 8],
            ['title' => 'No. Tiket', 'width' => 21],
            ['title' => 'Judul Tiket', 'width' => 38],
            ['title' => 'Deskripsi Masalah', 'width' => 55],
            ['title' => 'Keterangan Solusi', 'width' => 55],
            ['title' => 'Client / Outlet', 'width' => 40],
            ['title' => 'Staff Yang Mengerjakan', 'width' => 28],
            ['title' => 'Advisor', 'width' => 22],
            ['title' => 'System', 'width' => 16],
            ['title' => 'Module', 'width' => 18],
            ['title' => 'Tipe Penanganan', 'width' => 18],
            ['title' => 'Status', 'width' => 15],
            ['title' => 'Lama Pengerjaan', 'width' => 22],
            ['title' => 'Waktu Masuk (Insert Time)', 'width' => 22],
            ['title' => 'Waktu Selesai (Ending Time)', 'width' => 22],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        
        // Freeze Panes pada baris 7 (Header tabel tetap terkunci saat scroll)
        $xml .= '  <sheetViews>' . "\n";
        $xml .= '    <sheetView tabSelected="1" workbookViewId="0">' . "\n";
        $xml .= '      <pane ySplit="7" topLeftCell="A8" activePane="bottomLeft" state="frozen"/>' . "\n";
        $xml .= '    </sheetView>' . "\n";
        $xml .= '  </sheetViews>' . "\n";

        // Lebar Kolom yang disesuaikan secara proporsional agar tidak ada teks / dropdown terpotong
        $xml .= '  <cols>' . "\n";
        foreach ($cols as $idx => $col) {
            $colNum = $idx + 1;
            $xml .= '    <col min="' . $colNum . '" max="' . $colNum . '" width="' . $col['width'] . '" customWidth="1"/>' . "\n";
        }
        $xml .= '  </cols>' . "\n";

        $xml .= '  <sheetData>' . "\n";

        // Row 1: Header Judul Laporan (Tinggi 36pt, Center & Merged A1:O1)
        $xml .= '    <row r="1" ht="36" customHeight="1">' . "\n";
        $xml .= '      <c r="A1" s="5" t="inlineStr"><is><t>' . self::xmlEscape('PROBUS SYSTEM - LAPORAN DAFTAR TIKET HELPDESK') . '</t></is></c>' . "\n";
        for ($colIdx = 2; $colIdx <= 15; $colIdx++) {
            $colLetter = self::colLetter($colIdx);
            $xml .= '      <c r="' . $colLetter . '1" s="5"/>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Row 2: Subtitle Info & Filter Terapan (Tinggi 22pt, Center & Merged A2:O2)
        $subInfo = "Filter Status: " . $statusFilter;
        if ($userFilter !== 'Semua User') $subInfo .= "   •   User: " . $userFilter;
        if ($systemFilter !== 'Semua System') $subInfo .= "   •   System: " . $systemFilter;
        if ($moduleFilter !== 'Semua Module') $subInfo .= "   •   Module: " . $moduleFilter;
        if ($searchFilter) $subInfo .= "   •   Kata Kunci: " . $searchFilter;
        $subInfo .= "   •   Dicetak Oleh: " . $printedBy . " (" . $printedAt . ")";

        $xml .= '    <row r="2" ht="22" customHeight="1">' . "\n";
        $xml .= '      <c r="A2" s="6" t="inlineStr"><is><t>' . self::xmlEscape($subInfo) . '</t></is></c>' . "\n";
        for ($colIdx = 2; $colIdx <= 15; $colIdx++) {
            $colLetter = self::colLetter($colIdx);
            $xml .= '      <c r="' . $colLetter . '2" s="6"/>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Row 3: Blank separator (Tinggi 8pt)
        $xml .= '    <row r="3" ht="8" customHeight="1"/>' . "\n";

        // Row 4: KPI Summary Cards - Label Row (Tinggi 20pt)
        // Card 1: A4..D4 (Total), Card 2: E4..G4 (Open), Card 3: H4..K4 (Progress), Card 4: L4..O4 (Closed)
        $xml .= '    <row r="4" ht="20" customHeight="1">' . "\n";
        // Total Card Header (A4..D4)
        $xml .= '      <c r="A4" s="7" t="inlineStr"><is><t>TOTAL TIKET TERFILTER</t></is></c>' . "\n";
        $xml .= '      <c r="B4" s="7"/>' . "\n";
        $xml .= '      <c r="C4" s="7"/>' . "\n";
        $xml .= '      <c r="D4" s="7"/>' . "\n";
        // Open Card Header (E4..G4)
        $xml .= '      <c r="E4" s="9" t="inlineStr"><is><t>TIKET OPEN</t></is></c>' . "\n";
        $xml .= '      <c r="F4" s="9"/>' . "\n";
        $xml .= '      <c r="G4" s="9"/>' . "\n";
        // Progress Card Header (H4..K4)
        $xml .= '      <c r="H4" s="11" t="inlineStr"><is><t>TIKET IN PROGRESS</t></is></c>' . "\n";
        $xml .= '      <c r="I4" s="11"/>' . "\n";
        $xml .= '      <c r="J4" s="11"/>' . "\n";
        $xml .= '      <c r="K4" s="11"/>' . "\n";
        // Closed Card Header (L4..O4)
        $xml .= '      <c r="L4" s="13" t="inlineStr"><is><t>TIKET CLOSED</t></is></c>' . "\n";
        $xml .= '      <c r="N4" s="13"/>' . "\n";
        $xml .= '      <c r="M4" s="13"/>' . "\n";
        $xml .= '      <c r="O4" s="13"/>' . "\n";
        $xml .= '    </row>' . "\n";

        // Row 5: KPI Summary Cards - Value Row (Tinggi 28pt)
        $xml .= '    <row r="5" ht="28" customHeight="1">' . "\n";
        // Total Card Value (A5..D5)
        $xml .= '      <c r="A5" s="8"><v>' . $totalCount . '</v></c>' . "\n";
        $xml .= '      <c r="B5" s="8"/>' . "\n";
        $xml .= '      <c r="C5" s="8"/>' . "\n";
        $xml .= '      <c r="D5" s="8"/>' . "\n";
        // Open Card Value (E5..G5)
        $xml .= '      <c r="E5" s="10"><v>' . $openCount . '</v></c>' . "\n";
        $xml .= '      <c r="F5" s="10"/>' . "\n";
        $xml .= '      <c r="G5" s="10"/>' . "\n";
        // Progress Card Value (H5..K5)
        $xml .= '      <c r="H5" s="12"><v>' . $progressCount . '</v></c>' . "\n";
        $xml .= '      <c r="I5" s="12"/>' . "\n";
        $xml .= '      <c r="J5" s="12"/>' . "\n";
        $xml .= '      <c r="K5" s="12"/>' . "\n";
        // Closed Card Value (L5..O5)
        $xml .= '      <c r="L5" s="14"><v>' . $closedCount . '</v></c>' . "\n";
        $xml .= '      <c r="M5" s="14"/>' . "\n";
        $xml .= '      <c r="N5" s="14"/>' . "\n";
        $xml .= '      <c r="O5" s="14"/>' . "\n";
        $xml .= '    </row>' . "\n";

        // Row 6: Blank separator (Tinggi 12pt)
        $xml .= '    <row r="6" ht="12" customHeight="1"/>' . "\n";

        // Row 7: Main Table Headers (Tinggi 32pt, Style 4: Dark Navy & White Bold)
        $headerRow = 7;
        $xml .= '    <row r="' . $headerRow . '" ht="32" customHeight="1">' . "\n";
        foreach ($cols as $idx => $col) {
            $colLetter = self::colLetter($idx + 1);
            $xml .= '      <c r="' . $colLetter . $headerRow . '" s="4" t="inlineStr"><is><t>' . self::xmlEscape($col['title']) . '</t></is></c>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Rows 8+: Data Rows (Tinggi 24pt, ruang vertikal nyaman dan rapi)
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($tickets as $t) {
            $isZebra = ($no % 2 === 0);
            $sText = $isZebra ? 2 : 0;
            $sWrap = $isZebra ? 21 : 20;
            $sCenter = $isZebra ? 3 : 1;
            $sTck = $isZebra ? 19 : 18;

            $status = strtoupper($t->status ?? 'OPEN');
            $sStatus = 15; // OPEN (Soft Blue)
            if ($status === 'PROGRESS') {
                $sStatus = 16; // PROGRESS (Soft Amber)
            } elseif ($status === 'CLOSED') {
                $sStatus = 17; // CLOSED (Soft Green)
            }

            // Bersihkan teks HTML dan decode entitas untuk Deskripsi & Solusi
            $deskripsi = self::cleanTextForExcel($t->description);
            $solusi = self::cleanTextForExcel($t->solusi);

            // Client / Property & Lokasi
            $prop = $t->client->nm_out ?? $t->client->name ?? '-';
            if (!empty($t->client->lokasi)) {
                $prop .= ' (' . $t->client->lokasi . ')';
            }

            // Staff Yang Mengerjakan
            $staff = $t->user->name ?? '-';

            // Advisor
            $advisor = $t->advisor->name ?? '-';

            // System & Module
            $sys = $t->kategoriSystem->name ?? $t->system ?? '-';
            $mod = $t->moduleSystem->name ?? '-';

            // Tipe Penanganan
            $tipe = $t->tipe_penanganan ?: '-';

            // Lama Pengerjaan (menggunakan accessor duration_text)
            $duration = $t->duration_text ?: '-';

            // Waktu Masuk (Insert Time) & Waktu Selesai (Ending Time)
            $insertTime = $t->created_at ? Carbon::parse($t->created_at)->format('d/m/Y H:i:s') : '-';
            $endingTime = ($status === 'CLOSED' && $t->closed_at) ? Carbon::parse($t->closed_at)->format('d/m/Y H:i:s') : '-';

            // Hitung estimasi tinggi baris dinamis agar seluruh baris teks tampil utuh
            $maxLines = max(
                self::estimateLines($t->title, 38),
                self::estimateLines($deskripsi, 55),
                self::estimateLines($solusi, 55),
                self::estimateLines($prop, 40)
            );
            $rowHeight = max(26, ($maxLines * 16) + 6);

            // ht menyediakan tinggi precalculated yang lega, dan tanpa customHeight="1" agar Excel juga bebas auto-fit penuh
            $xml .= '    <row r="' . $currentRow . '" ht="' . $rowHeight . '">' . "\n";
            // A: No
            $xml .= '      <c r="A' . $currentRow . '" s="' . $sCenter . '"><v>' . $no . '</v></c>' . "\n";
            // B: No. Tiket
            $xml .= '      <c r="B' . $currentRow . '" s="' . $sTck . '" t="inlineStr"><is><t>' . self::xmlEscape($t->ticket_number) . '</t></is></c>' . "\n";
            // C: Judul Tiket
            $xml .= '      <c r="C' . $currentRow . '" s="' . $sWrap . '" t="inlineStr"><is><t xml:space="preserve">' . self::xmlEscape($t->title) . '</t></is></c>' . "\n";
            // D: Deskripsi Masalah
            $xml .= '      <c r="D' . $currentRow . '" s="' . $sWrap . '" t="inlineStr"><is><t xml:space="preserve">' . self::xmlEscape($deskripsi) . '</t></is></c>' . "\n";
            // E: Keterangan Solusi
            $xml .= '      <c r="E' . $currentRow . '" s="' . $sWrap . '" t="inlineStr"><is><t xml:space="preserve">' . self::xmlEscape($solusi) . '</t></is></c>' . "\n";
            // F: Property / Pelanggan
            $xml .= '      <c r="F' . $currentRow . '" s="' . $sText . '" t="inlineStr"><is><t>' . self::xmlEscape($prop) . '</t></is></c>' . "\n";
            // G: Staff Yang Mengerjakan
            $xml .= '      <c r="G' . $currentRow . '" s="' . $sText . '" t="inlineStr"><is><t>' . self::xmlEscape($staff) . '</t></is></c>' . "\n";
            // H: Advisor
            $xml .= '      <c r="H' . $currentRow . '" s="' . $sText . '" t="inlineStr"><is><t>' . self::xmlEscape($advisor) . '</t></is></c>' . "\n";
            // I: System
            $xml .= '      <c r="I' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($sys) . '</t></is></c>' . "\n";
            // J: Module
            $xml .= '      <c r="J' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($mod) . '</t></is></c>' . "\n";
            // K: Tipe Penanganan
            $xml .= '      <c r="K' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($tipe) . '</t></is></c>' . "\n";
            // L: Status (with color badge)
            $xml .= '      <c r="L' . $currentRow . '" s="' . $sStatus . '" t="inlineStr"><is><t>' . self::xmlEscape($status) . '</t></is></c>' . "\n";
            // M: Lama Pengerjaan
            $xml .= '      <c r="M' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($duration) . '</t></is></c>' . "\n";
            // N: Waktu Masuk (Insert Time)
            $xml .= '      <c r="N' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($insertTime) . '</t></is></c>' . "\n";
            // O: Waktu Selesai (Ending Time)
            $xml .= '      <c r="O' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($endingTime) . '</t></is></c>' . "\n";
            $xml .= '    </row>' . "\n";

            $currentRow++;
            $no++;
        }

        $xml .= '  </sheetData>' . "\n";

        // Merge cells untuk Judul (A1:O1), Subtitle (A2:O2), dan KPI Cards (A4..O5)
        $xml .= '  <mergeCells count="10">' . "\n";
        $xml .= '    <mergeCell ref="A1:O1"/>' . "\n";
        $xml .= '    <mergeCell ref="A2:O2"/>' . "\n";
        $xml .= '    <mergeCell ref="A4:D4"/>' . "\n";
        $xml .= '    <mergeCell ref="A5:D5"/>' . "\n";
        $xml .= '    <mergeCell ref="E4:G4"/>' . "\n";
        $xml .= '    <mergeCell ref="E5:G5"/>' . "\n";
        $xml .= '    <mergeCell ref="H4:K4"/>' . "\n";
        $xml .= '    <mergeCell ref="H5:K5"/>' . "\n";
        $xml .= '    <mergeCell ref="L4:O4"/>' . "\n";
        $xml .= '    <mergeCell ref="L5:O5"/>' . "\n";
        $xml .= '  </mergeCells>' . "\n";

        $xml .= '</worksheet>';

        return $xml;
    }

    /**
     * Membangun sheet1.xml untuk riwayat tiket per modul dengan 12 kolom proporsional,
     * menampilkan kendala/deskripsi dan apa yang dikerjakan (solusi).
     */
    private static function buildModuleSheetXml($tickets, array $meta = [])
    {
        $staffName = !empty($meta['staff_name']) ? $meta['staff_name'] : 'Staff';
        $moduleName = !empty($meta['module_name']) ? $meta['module_name'] : 'Modul';
        $periodLabel = !empty($meta['period_label']) ? $meta['period_label'] : date('F Y');
        $printedAt = !empty($meta['printed_at']) ? $meta['printed_at'] : date('d/m/Y H:i') . ' WITA';
        $printedBy = !empty($meta['printed_by']) ? $meta['printed_by'] : 'User';

        $totalCount = $tickets->count();
        $openCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'OPEN'; })->count();
        $progressCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'PROGRESS'; })->count();
        $closedCount = $tickets->filter(function($t) { return strtoupper($t->status) === 'CLOSED'; })->count();

        // 12 Kolom
        $cols = [
            ['title' => 'No', 'width' => 6],
            ['title' => 'No. Tiket', 'width' => 20],
            ['title' => 'Judul Tiket', 'width' => 30],
            ['title' => 'Kendala / Deskripsi', 'width' => 40],
            ['title' => 'Apa Yang Dikerjakan (Solusi)', 'width' => 45],
            ['title' => 'Client / Outlet', 'width' => 30],
            ['title' => 'Staff Pelaksana', 'width' => 22],
            ['title' => 'Tipe Penanganan', 'width' => 18],
            ['title' => 'Status', 'width' => 15],
            ['title' => 'Lama Pengerjaan', 'width' => 20],
            ['title' => 'Waktu Masuk', 'width' => 20],
            ['title' => 'Waktu Selesai', 'width' => 20],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";

        // Freeze Panes pada baris 7 (Header tabel tetap terkunci saat scroll)
        $xml .= '  <sheetViews>' . "\n";
        $xml .= '    <sheetView tabSelected="1" workbookViewId="0">' . "\n";
        $xml .= '      <pane ySplit="7" topLeftCell="A8" activePane="bottomLeft" state="frozen"/>' . "\n";
        $xml .= '    </sheetView>' . "\n";
        $xml .= '  </sheetViews>' . "\n";

        // Lebar Kolom
        $xml .= '  <cols>' . "\n";
        foreach ($cols as $idx => $col) {
            $colNum = $idx + 1;
            $xml .= '    <col min="' . $colNum . '" max="' . $colNum . '" width="' . $col['width'] . '" customWidth="1"/>' . "\n";
        }
        $xml .= '  </cols>' . "\n";

        $xml .= '  <sheetData>' . "\n";

        $isAll = !empty($meta['is_all_modules']);
        $isAdvice = !empty($meta['is_advice']);
        // Row 1: Header Judul Laporan (Tinggi 36pt, Merged A1:L1)
        if ($isAdvice) {
            $titleText = 'PROBUS SYSTEM - RIWAYAT TIKET DI-ADVICE OLEH ADVISOR';
        } elseif ($isAll) {
            $titleText = 'PROBUS SYSTEM - RIWAYAT PENGERJAAN TIKET: SEMUA MODUL (TOTAL KESELURUHAN)';
        } else {
            $titleText = 'PROBUS SYSTEM - RIWAYAT PENGERJAAN TIKET MODUL: ' . mb_strtoupper($moduleName);
        }
        $xml .= '    <row r="1" ht="36" customHeight="1">' . "\n";
        $xml .= '      <c r="A1" s="5" t="inlineStr"><is><t>' . self::xmlEscape($titleText) . '</t></is></c>' . "\n";
        for ($colIdx = 2; $colIdx <= 12; $colIdx++) {
            $colLetter = self::colLetter($colIdx);
            $xml .= '      <c r="' . $colLetter . '1" s="5"/>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Row 2: Subtitle Info (Tinggi 22pt, Merged A2:L2)
        if ($isAdvice) {
            $subInfo = "Advisor: " . $staffName . "   •   Kategori: Tiket Di-Advice   •   Periode: " . $periodLabel . "   •   Dicetak Oleh: " . $printedBy . " (" . $printedAt . ")";
        } else {
            $subInfo = "Staff: " . $staffName . "   •   Modul: " . $moduleName . "   •   Periode: " . $periodLabel . "   •   Dicetak Oleh: " . $printedBy . " (" . $printedAt . ")";
        }
        $xml .= '    <row r="2" ht="22" customHeight="1">' . "\n";
        $xml .= '      <c r="A2" s="6" t="inlineStr"><is><t>' . self::xmlEscape($subInfo) . '</t></is></c>' . "\n";
        for ($colIdx = 2; $colIdx <= 12; $colIdx++) {
            $colLetter = self::colLetter($colIdx);
            $xml .= '      <c r="' . $colLetter . '2" s="6"/>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Row 3: Blank
        $xml .= '    <row r="3" ht="10" customHeight="1"/>' . "\n";

        // Row 4: KPI Summary Cards - Label Row (Tinggi 18pt)
        if ($isAdvice) {
            $kpiTotalLabel = 'TOTAL TIKET DI-ADVICE';
        } elseif ($isAll) {
            $kpiTotalLabel = 'TOTAL SELURUH TIKET';
        } else {
            $kpiTotalLabel = 'TOTAL TIKET MODUL';
        }
        $xml .= '    <row r="4" ht="18" customHeight="1">' . "\n";
        $xml .= '      <c r="A4" s="7" t="inlineStr"><is><t>' . $kpiTotalLabel . '</t></is></c>' . "\n";
        $xml .= '      <c r="B4" s="7"/>' . "\n";
        $xml .= '      <c r="C4" s="7"/>' . "\n";
        $xml .= '      <c r="D4" s="13" t="inlineStr"><is><t>TIKET SELESAI (CLOSED)</t></is></c>' . "\n";
        $xml .= '      <c r="E4" s="13"/>' . "\n";
        $xml .= '      <c r="F4" s="13"/>' . "\n";
        $xml .= '      <c r="G4" s="11" t="inlineStr"><is><t>DALAM PROSES (PROGRESS)</t></is></c>' . "\n";
        $xml .= '      <c r="H4" s="11"/>' . "\n";
        $xml .= '      <c r="I4" s="11"/>' . "\n";
        $xml .= '      <c r="J4" s="9" t="inlineStr"><is><t>TIKET BARU (OPEN)</t></is></c>' . "\n";
        $xml .= '      <c r="K4" s="9"/>' . "\n";
        $xml .= '      <c r="L4" s="9"/>' . "\n";
        $xml .= '    </row>' . "\n";

        // Row 5: KPI Summary Cards - Value Row (Tinggi 28pt)
        $xml .= '    <row r="5" ht="28" customHeight="1">' . "\n";
        $xml .= '      <c r="A5" s="8"><v>' . $totalCount . '</v></c>' . "\n";
        $xml .= '      <c r="B5" s="8"/>' . "\n";
        $xml .= '      <c r="C5" s="8"/>' . "\n";
        $xml .= '      <c r="D5" s="14"><v>' . $closedCount . '</v></c>' . "\n";
        $xml .= '      <c r="E5" s="14"/>' . "\n";
        $xml .= '      <c r="F5" s="14"/>' . "\n";
        $xml .= '      <c r="G5" s="12"><v>' . $progressCount . '</v></c>' . "\n";
        $xml .= '      <c r="H5" s="12"/>' . "\n";
        $xml .= '      <c r="I5" s="12"/>' . "\n";
        $xml .= '      <c r="J5" s="10"><v>' . $openCount . '</v></c>' . "\n";
        $xml .= '      <c r="K5" s="10"/>' . "\n";
        $xml .= '      <c r="L5" s="10"/>' . "\n";
        $xml .= '    </row>' . "\n";

        // Row 6: Blank separator
        $xml .= '    <row r="6" ht="12" customHeight="1"/>' . "\n";

        // Row 7: Main Table Headers (Tinggi 32pt)
        $headerRow = 7;
        $xml .= '    <row r="' . $headerRow . '" ht="32" customHeight="1">' . "\n";
        foreach ($cols as $idx => $col) {
            $colLetter = self::colLetter($idx + 1);
            $xml .= '      <c r="' . $colLetter . $headerRow . '" s="4" t="inlineStr"><is><t>' . self::xmlEscape($col['title']) . '</t></is></c>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Rows 8+: Data Rows
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($tickets as $t) {
            $isZebra = ($no % 2 === 0);
            $sText = $isZebra ? 2 : 0;
            $sWrap = $isZebra ? 21 : 20;
            $sCenter = $isZebra ? 3 : 1;
            $sTck = $isZebra ? 19 : 18;

            $status = strtoupper($t->status ?? 'OPEN');
            $sStatus = 15; // OPEN
            if ($status === 'PROGRESS') {
                $sStatus = 16;
            } elseif ($status === 'CLOSED') {
                $sStatus = 17;
            }

            $prop = $t->client->nm_out ?? $t->client->name ?? '-';
            if (!empty($t->client->lokasi)) {
                $prop .= ' (' . $t->client->lokasi . ')';
            }

            $staff = $t->user->name ?? $staffName;
            $tipe = $t->tipe_penanganan ?: '-';
            $duration = $t->duration_text ?: '-';
            $insertTime = $t->created_at ? Carbon::parse($t->created_at)->format('d/m/Y H:i') : '-';
            $endingTime = ($status === 'CLOSED' && $t->closed_at) ? Carbon::parse($t->closed_at)->format('d/m/Y H:i') : '-';
            $solusi = $t->solusi ? trim(strip_tags($t->solusi)) : '-';
            $deskripsi = $t->description ? trim(strip_tags($t->description)) : '-';

            $xml .= '    <row r="' . $currentRow . '" ht="32" customHeight="1">' . "\n";
            // A: No
            $xml .= '      <c r="A' . $currentRow . '" s="' . $sCenter . '"><v>' . $no . '</v></c>' . "\n";
            // B: No. Tiket
            $xml .= '      <c r="B' . $currentRow . '" s="' . $sTck . '" t="inlineStr"><is><t>' . self::xmlEscape($t->ticket_number) . '</t></is></c>' . "\n";
            // C: Judul Tiket
            $displayTitle = (($isAll || $isAdvice) && !empty($t->moduleSystem->name)) ? '[' . $t->moduleSystem->name . '] ' . $t->title : $t->title;
            $xml .= '      <c r="C' . $currentRow . '" s="' . $sWrap . '" t="inlineStr"><is><t>' . self::xmlEscape($displayTitle) . '</t></is></c>' . "\n";
            // D: Kendala / Deskripsi
            $xml .= '      <c r="D' . $currentRow . '" s="' . $sWrap . '" t="inlineStr"><is><t>' . self::xmlEscape($deskripsi) . '</t></is></c>' . "\n";
            // E: Apa Yang Dikerjakan (Solusi)
            $xml .= '      <c r="E' . $currentRow . '" s="' . $sWrap . '" t="inlineStr"><is><t>' . self::xmlEscape($solusi) . '</t></is></c>' . "\n";
            // F: Client / Outlet
            $xml .= '      <c r="F' . $currentRow . '" s="' . $sText . '" t="inlineStr"><is><t>' . self::xmlEscape($prop) . '</t></is></c>' . "\n";
            // G: Staff Pelaksana
            $xml .= '      <c r="G' . $currentRow . '" s="' . $sText . '" t="inlineStr"><is><t>' . self::xmlEscape($staff) . '</t></is></c>' . "\n";
            // H: Tipe Penanganan
            $xml .= '      <c r="H' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($tipe) . '</t></is></c>' . "\n";
            // I: Status
            $xml .= '      <c r="I' . $currentRow . '" s="' . $sStatus . '" t="inlineStr"><is><t>' . self::xmlEscape($status) . '</t></is></c>' . "\n";
            // J: Lama Pengerjaan
            $xml .= '      <c r="J' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($duration) . '</t></is></c>' . "\n";
            // K: Waktu Masuk
            $xml .= '      <c r="K' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($insertTime) . '</t></is></c>' . "\n";
            // L: Waktu Selesai
            $xml .= '      <c r="L' . $currentRow . '" s="' . $sCenter . '" t="inlineStr"><is><t>' . self::xmlEscape($endingTime) . '</t></is></c>' . "\n";
            $xml .= '    </row>' . "\n";

            $currentRow++;
            $no++;
        }

        $xml .= '  </sheetData>' . "\n";

        // Merge cells untuk Judul (A1:L1), Subtitle (A2:L2), dan KPI Cards
        $xml .= '  <mergeCells count="10">' . "\n";
        $xml .= '    <mergeCell ref="A1:L1"/>' . "\n";
        $xml .= '    <mergeCell ref="A2:L2"/>' . "\n";
        $xml .= '    <mergeCell ref="A4:C4"/>' . "\n";
        $xml .= '    <mergeCell ref="A5:C5"/>' . "\n";
        $xml .= '    <mergeCell ref="D4:F4"/>' . "\n";
        $xml .= '    <mergeCell ref="D5:F5"/>' . "\n";
        $xml .= '    <mergeCell ref="G4:I4"/>' . "\n";
        $xml .= '    <mergeCell ref="G5:I5"/>' . "\n";
        $xml .= '    <mergeCell ref="J4:L4"/>' . "\n";
        $xml .= '    <mergeCell ref="J5:L5"/>' . "\n";
        $xml .= '  </mergeCells>' . "\n";

        $xml .= '</worksheet>';

        return $xml;
    }
}

