<?php

namespace App\Helpers;

class DocxParser
{
    /**
     * Parse file Word (.docx) menjadi HTML bersih yang siap dipakai pada TinyMCE & portal baca.
     */
    public static function parse($filePath)
    {
        if (!file_exists($filePath)) {
            return ['html' => '', 'image_count' => 0];
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return ['html' => '', 'image_count' => 0];
        }

        // 1. Baca relasi dokumen untuk menemukan media gambar
        $relations = [];
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        if ($relsXml) {
            try {
                $xml = simplexml_load_string($relsXml);
                if ($xml) {
                    foreach ($xml->Relationship as $rel) {
                        $id = (string) $rel['Id'];
                        $target = (string) $rel['Target'];
                        $type = (string) $rel['Type'];
                        if (strpos($type, 'image') !== false || preg_match('/\.(png|jpe?g|gif|webp|bmp)$/i', $target)) {
                            $relations[$id] = $target;
                        }
                    }
                }
            } catch (\Exception $e) {}
        }

        // 2. Ekstrak gambar ke direktori public/uploads/editor/
        $imageMap = [];
        $targetDir = public_path('uploads/editor');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $imgCount = 0;
        foreach ($relations as $rId => $target) {
            $zipEntry = 'word/' . ltrim(str_replace('../', '', $target), '/');
            if (!$zip->locateName($zipEntry)) {
                $zipEntry = ltrim($target, '/');
            }

            $imgData = $zip->getFromName($zipEntry);
            if ($imgData) {
                $ext = pathinfo($target, PATHINFO_EXTENSION);
                if (!$ext) $ext = 'png';
                $newFilename = 'word_img_' . time() . '_' . substr(md5($zipEntry . microtime()), 0, 8) . '.' . strtolower($ext);
                $fullPath = $targetDir . DIRECTORY_SEPARATOR . $newFilename;
                file_put_contents($fullPath, $imgData);
                $imageMap[$rId] = '/uploads/editor/' . $newFilename;
                $imgCount++;
            }
        }

        // 3. Baca XML konten utama dokumen
        $docXml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$docXml) {
            return ['html' => '', 'image_count' => 0];
        }

        $html = self::convertXmlToHtml($docXml, $imageMap);

        return [
            'html' => $html,
            'image_count' => $imgCount,
        ];
    }

    /**
     * Mengkonversi struktur XML Word menjadi format HTML semantic yang rapi
     */
    private static function convertXmlToHtml($xmlString, $imageMap)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($xmlString);
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            return '';
        }

        $output = '';

        foreach ($body->childNodes as $node) {
            if ($node->nodeName === 'w:p') {
                $pContent = self::parseParagraph($node, $imageMap, false);
                if (!empty(trim(strip_tags($pContent, '<img>')))) {
                    $output .= $pContent;
                }
            } elseif ($node->nodeName === 'w:tbl') {
                $output .= self::parseTable($node, $imageMap);
            }
        }

        return $output;
    }

    /**
     * Parse paragraf (<w:p>) dengan pendeteksian heading, perataan teks, dan pemisahan kata
     */
    private static function parseParagraph($pNode, $imageMap, $isInTable = false)
    {
        $isHeading = false;
        $headingLevel = 2;
        $textAlign = '';

        $pPr = self::getFirstChildByTagName($pNode, 'w:pPr');
        if ($pPr) {
            $pStyle = self::getFirstChildByTagName($pPr, 'w:pStyle');
            if ($pStyle && $pStyle->hasAttribute('w:val')) {
                $val = strtolower($pStyle->getAttribute('w:val'));
                if (strpos($val, 'heading1') !== false || strpos($val, 'heading 1') !== false) {
                    $isHeading = true;
                    $headingLevel = 1;
                } elseif (strpos($val, 'heading2') !== false || strpos($val, 'heading 2') !== false) {
                    $isHeading = true;
                    $headingLevel = 2;
                } elseif (strpos($val, 'heading3') !== false || strpos($val, 'heading 3') !== false) {
                    $isHeading = true;
                    $headingLevel = 3;
                }
            }

            $jc = self::getFirstChildByTagName($pPr, 'w:jc');
            if ($jc && $jc->hasAttribute('w:val')) {
                $jcVal = strtolower($jc->getAttribute('w:val'));
                if ($jcVal === 'center') {
                    $textAlign = 'center';
                } elseif ($jcVal === 'right') {
                    $textAlign = 'right';
                } elseif ($jcVal === 'both' || $jcVal === 'justify') {
                    $textAlign = 'justify';
                }
            }
        }

        $text = '';
        foreach ($pNode->childNodes as $child) {
            if ($child->nodeName === 'w:r') {
                $text .= self::parseRun($child, $imageMap, $isInTable);
            } elseif ($child->nodeName === 'w:drawing') {
                $text .= self::parseDrawing($child, $imageMap, $isInTable);
            }
        }

        // 1. Bersihkan dan gabungkan tag inline bersebelahan (misal </strong><strong>)
        $text = self::cleanAndMergeInlineTags($text);

        // 2. Normalisasi spasi bahasa Indonesia yang menempel dari Word XML
        $text = self::normalizeTextWithTags($text);

        if (empty(trim(strip_tags($text, '<img>')))) {
            return '';
        }

        // Paragraf dalam tabel tidak perlu dibungkus tag <p> terpisah agar padding rapi
        if ($isInTable) {
            return $text;
        }

        $isOnlyImage = (strpos($text, '<img') !== false && empty(trim(strip_tags($text))));
        if ($isOnlyImage) {
            $textAlign = 'center';
        }

        $alignStyle = $textAlign ? ' style="text-align: ' . $textAlign . '; margin: 16px 0;"' : ' style="margin: 12px 0;"';

        if ($isHeading) {
            return '<h' . $headingLevel . ($textAlign ? ' style="text-align: ' . $textAlign . ';"' : '') . '>' . $text . '</h' . $headingLevel . '>';
        }

        return '<p' . $alignStyle . '>' . $text . '</p>';
    }

    /**
     * Parse text run (<w:r>) termasuk formatting tebal, miring, garis bawah, dan tabulasi
     */
    private static function parseRun($rNode, $imageMap, $isInTable = false)
    {
        $text = '';
        $isBold = false;
        $isItalic = false;
        $isUnderline = false;

        $rPr = self::getFirstChildByTagName($rNode, 'w:rPr');
        if ($rPr) {
            if (self::getFirstChildByTagName($rPr, 'w:b')) $isBold = true;
            if (self::getFirstChildByTagName($rPr, 'w:i')) $isItalic = true;
            if (self::getFirstChildByTagName($rPr, 'w:u')) $isUnderline = true;
        }

        foreach ($rNode->childNodes as $child) {
            if ($child->nodeName === 'w:t') {
                $text .= htmlspecialchars($child->nodeValue, ENT_QUOTES, 'UTF-8');
            } elseif ($child->nodeName === 'w:tab') {
                $text .= ' ';
            } elseif ($child->nodeName === 'w:drawing') {
                $text .= self::parseDrawing($child, $imageMap, $isInTable);
            } elseif ($child->nodeName === 'w:br') {
                $text .= '<br/>';
            }
        }

        if ($isUnderline) $text = '<u>' . $text . '</u>';
        if ($isItalic) $text = '<em>' . $text . '</em>';
        if ($isBold) $text = '<strong>' . $text . '</strong>';

        return $text;
    }

    /**
     * Parse elemen gambar (<w:drawing>) dengan penyesuaian ukuran jika di dalam atau luar tabel
     */
    private static function parseDrawing($drawingNode, $imageMap, $isInTable = false)
    {
        $blips = $drawingNode->getElementsByTagName('blip');
        foreach ($blips as $blip) {
            foreach ($blip->attributes as $attr) {
                if ($attr->localName === 'embed' && isset($imageMap[$attr->value])) {
                    $src = $imageMap[$attr->value];
                    if (!preg_match('/^(https?:\/\/|\/)/i', $src)) {
                        $src = '/' . ltrim($src, '/');
                    }
                    if ($isInTable) {
                        return '<img src="' . $src . '" alt="Icon" style="max-height: 40px; max-width: 100%; height: auto; border-radius: 4px; display: inline-block; vertical-align: middle; margin: 2px auto;" />';
                    }
                    return '<img src="' . $src . '" alt="Gambar Panduan" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; margin: 8px 0;" />';
                }
            }
        }
        return '';
    }

    /**
     * Parse tabel (<w:tbl>) dengan mempertahankan proporsi lebar kolom Word, perataan, dan template visual
     */
    private static function parseTable($tblNode, $imageMap)
    {
        // 1. Hitung persentase lebar tiap kolom dari gridCol atau tcW baris pertama
        $colWidths = [];
        $gridCols = $tblNode->getElementsByTagName('gridCol');
        if ($gridCols && $gridCols->length > 0) {
            $totalGridW = 0;
            foreach ($gridCols as $gc) {
                $w = (int) $gc->getAttribute('w:w');
                $colWidths[] = $w;
                $totalGridW += $w;
            }
            if ($totalGridW > 0) {
                foreach ($colWidths as $k => $w) {
                    $colWidths[$k] = round(($w / $totalGridW) * 100);
                }
            }
        }

        $rows = $tblNode->getElementsByTagName('tr');
        if (empty($colWidths) && $rows->length > 0) {
            $firstRow = $rows->item(0);
            $cells = $firstRow->getElementsByTagName('tc');
            $widths = [];
            $totalW = 0;
            foreach ($cells as $tc) {
                $tcPr = self::getFirstChildByTagName($tc, 'w:tcPr');
                $tcW = $tcPr ? self::getFirstChildByTagName($tcPr, 'w:tcW') : null;
                $w = $tcW ? (int) $tcW->getAttribute('w:w') : 1000;
                $widths[] = $w;
                $totalW += $w;
            }
            if ($totalW > 0) {
                foreach ($widths as $w) {
                    $colWidths[] = round(($w / $totalW) * 100);
                }
            }
        }

        $html = '<table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13.5px; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden;" border="1"><tbody>';

        $isFirstRow = true;

        foreach ($rows as $tr) {
            $html .= '<tr>';
            $cells = $tr->getElementsByTagName('tc');
            $colIndex = 0;

            foreach ($cells as $tc) {
                $colPct = isset($colWidths[$colIndex]) ? $colWidths[$colIndex] : null;

                // Vertikal alignment
                $tcPr = self::getFirstChildByTagName($tc, 'w:tcPr');
                $vAlign = 'middle';
                if ($tcPr) {
                    $vaNode = self::getFirstChildByTagName($tcPr, 'w:vAlign');
                    if ($vaNode && $vaNode->hasAttribute('w:val')) {
                        $v = strtolower($vaNode->getAttribute('w:val'));
                        if ($v === 'top') $vAlign = 'top';
                        elseif ($v === 'bottom') $vAlign = 'bottom';
                        elseif ($v === 'center') $vAlign = 'middle';
                    }
                }

                // Horizontal alignment
                $hAlign = 'left';
                $pList = $tc->getElementsByTagName('p');
                $cellFragments = [];

                foreach ($pList as $p) {
                    $pPr = self::getFirstChildByTagName($p, 'w:pPr');
                    if ($pPr) {
                        $jc = self::getFirstChildByTagName($pPr, 'w:jc');
                        if ($jc && $jc->hasAttribute('w:val')) {
                            $j = strtolower($jc->getAttribute('w:val'));
                            if ($j === 'center') $hAlign = 'center';
                            elseif ($j === 'right') $hAlign = 'right';
                        }
                    }
                    $fragment = self::parseParagraph($p, $imageMap, true);
                    if (!empty(trim(strip_tags($fragment, '<img>')))) {
                        $cellFragments[] = $fragment;
                    }
                }

                $cellContent = implode('<br/>', $cellFragments);
                if (empty(trim(strip_tags($cellContent, '<img>')))) {
                    $cellContent = '&nbsp;';
                }

                $widthStyle = $colPct ? 'width: ' . $colPct . '%; ' : '';
                $alignStyle = 'text-align: ' . $hAlign . '; vertical-align: ' . $vAlign . ';';

                if ($isFirstRow) {
                    $html .= '<th style="' . $widthStyle . 'padding: 10px 14px; background-color: #f1f5f9; color: #0f172a; font-weight: 700; border: 1px solid #cbd5e1; ' . $alignStyle . '">' . $cellContent . '</th>';
                } else {
                    $html .= '<td style="' . $widthStyle . 'padding: 10px 14px; border: 1px solid #cbd5e1; ' . $alignStyle . '">' . $cellContent . '</td>';
                }

                $colIndex++;
            }
            $html .= '</tr>';
            $isFirstRow = false;
        }

        $html .= '</tbody></table><p></p>';
        return $html;
    }

    /**
     * Membersihkan dan menggabungkan tag formatting inline yang bertetangga
     */
    private static function cleanAndMergeInlineTags($html)
    {
        // Hapus tag pembungkus kosong
        $html = preg_replace('/<(strong|b|em|i|u)>\s*<\/\1>/ui', '', $html);

        // Gabungkan tag inline identik yang berdampingan
        for ($i = 0; $i < 3; $i++) {
            $html = preg_replace('/<\/(strong|b|em|i|u)>\s*<\1>/ui', '', $html);
        }

        return $html;
    }

    /**
     * Menerapkan normalisasi teks tanpa mengganggu atribut tag HTML
     */
    private static function normalizeTextWithTags($html)
    {
        $parts = preg_split('/(<[^>]+>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        foreach ($parts as $part) {
            if (empty($part)) continue;
            if ($part[0] === '<') {
                $result .= $part;
            } else {
                $result .= self::normalizeIndonesianSpacing($part);
            }
        }
        return $result;
    }

    /**
     * Memperbaiki dan menormalkan kata-kata bahasa Indonesia yang menyatu akibat Word run / OCR
     */
    public static function normalizeIndonesianSpacing($text)
    {
        if (!$text) return $text;

        $dictionary = [
            'Digunakanuntukmengaturrentangwaktupaket' => 'Digunakan untuk mengatur rentang waktu paket',
            'digunakanuntukmengaturrentangwaktupaket' => 'digunakan untuk mengatur rentang waktu paket',
            'Digunakanuntukmerubahtampilanwarna'     => 'Digunakan untuk merubah tampilan warna',
            'digunakanuntukmerubahtampilanwarna'     => 'digunakan untuk merubah tampilan warna',
            'Digunakanuntukmemasukkanpassword'       => 'Digunakan untuk memasukkan password',
            'digunakanuntukmemasukkanpassword'       => 'digunakan untuk memasukkan password',
            'Digunakanuntukmelakukanclosing'         => 'Digunakan untuk melakukan closing',
            'digunakanuntukmelakukanclosing'         => 'digunakan untuk melakukan closing',
            'Digunakanuntukmenjalankan'              => 'Digunakan untuk menjalankan',
            'digunakanuntukmenjalankan'              => 'digunakan untuk menjalankan',
            'Digunakanuntukmerefresh'                => 'Digunakan untuk merefresh',
            'digunakanuntukmerefresh'                => 'digunakan untuk merefresh',
            'Digunakanuntukmelakukan'                => 'Digunakan untuk melakukan',
            'digunakanuntukmelakukan'                => 'digunakan untuk melakukan',
            'Digunakanuntukmengatur'                 => 'Digunakan untuk mengatur',
            'digunakanuntukmengatur'                 => 'digunakan untuk mengatur',
            'Digunakanuntukmemilih'                  => 'Digunakan untuk memilih',
            'digunakanuntukmemilih'                  => 'digunakan untuk memilih',
            'Digunakanuntuk'                         => 'Digunakan untuk',
            'digunakanuntuk'                         => 'digunakan untuk',
            'Menampilkannamakomputer'                => 'Menampilkan nama komputer',
            'menampilkannamakomputer'                => 'menampilkan nama komputer',
            'kasirdannamakasir'                      => 'kasir dan nama kasir',
            'kasirdannama'                           => 'kasir dan nama',
            'dannamakasir'                           => 'dan nama kasir',
            'dannama'                                => 'dan nama',
            'namakomputer'                           => 'nama komputer',
            'namakasir'                              => 'nama kasir',
            'transaksipembayaran'                    => 'transaksi pembayaran',
            'transaksiyang'                          => 'transaksi yang',
            'sepertitransaksi'                       => 'seperti transaksi',
            'informasitable'                         => 'informasi table',
            'informasitambahan'                      => 'informasi tambahan',
            'informasihalaman'                       => 'informasi halaman',
            'inginditampilkan'                       => 'ingin ditampilkan',
            'keterangandari'                         => 'keterangan dari',
            'rentangwaktupaket'                      => 'rentang waktu paket',
            'rentangwaktu'                           => 'rentang waktu',
            'waktupaket'                             => 'waktu paket',
            'waktukerja'                             => 'waktu kerja',
            'tampilanwarna'                          => 'tampilan warna',
            'merubahtampilan'                        => 'merubah tampilan',
            'memasukkanpassword'                     => 'memasukkan password',
            'mengaturrentangwaktu'                   => 'mengatur rentang waktu',
            'mengaturrentang'                        => 'mengatur rentang',
            'jumlahtamuyang'                         => 'jumlah tamu yang',
            'jumlahtamu'                             => 'jumlah tamu',
            'tabletamu'                              => 'table tamu',
            'tamuyang'                               => 'tamu yang',
            'tamumelakukan'                          => 'tamu melakukan',
            'pemesananpada'                          => 'pemesanan pada',
            'halamanproduk'                          => 'halaman produk',
            'TransactionRestauran'                   => 'Transaction Restauran',
            'DeleiveryPrices'                        => 'Delivery Prices',
            'memnyimpandata'                         => 'menyimpan data',
            'pengclosingngan'                        => 'peng-closing-an',
            'untukmerubah'                           => 'untuk merubah',
            'untukmenjalankan'                       => 'untuk menjalankan',
            'untukmemilih'                           => 'untuk memilih',
            'untukmemasukkan'                        => 'untuk memasukkan',
            'untukmengatur'                          => 'untuk mengatur',
            'untukmencetak'                          => 'untuk mencetak',
            'untukmelakukan'                         => 'untuk melakukan',
            'untukmembuat'                           => 'untuk membuat',
            'untukmenyimpan'                         => 'untuk menyimpan',
            'untukmenghapus'                         => 'untuk menghapus',
            'untukmenampilkan'                       => 'untuk menampilkan',
            'untukmengaktifkan'                      => 'untuk mengaktifkan',
            'untukmasuk'                             => 'untuk masuk',
            'untukkeluar'                            => 'untuk keluar',
            'untukpindah'                            => 'untuk pindah',
            'untukmenutup'                           => 'untuk menutup',
            'untukmemberikan'                        => 'untuk memberikan',
            'untukmembatalkan'                       => 'untuk membatalkan',
            'untuktamu'                              => 'untuk tamu',
        ];

        uksort($dictionary, function($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($dictionary as $search => $replace) {
            $text = str_ireplace($search, $replace, $text);
        }

        // Generic regex rules untuk pola-pola umum
        $text = preg_replace('/\buntuk(me[a-z]{3,}|meng[a-z]{3,}|mem[a-z]{3,}|men[a-z]{3,}|ber[a-z]{3,}|ter[a-z]{3,}|di[a-z]{3,})\b/ui', 'untuk $1', $text);
        $text = preg_replace('/\bdigunakan(untuk)\b/ui', 'digunakan $1', $text);
        $text = preg_replace('/\bmenampilkan(nama|informasi|data|detail|status|jumlah)\b/ui', 'menampilkan $1', $text);
        $text = preg_replace('/\btransaksi(yang|pembayaran|pemesanan|penjualan)\b/ui', 'transaksi $1', $text);
        $text = preg_replace('/\btamu(yang|melakukan)\b/ui', 'tamu $1', $text);
        $text = preg_replace('/\b(table|tabel)(tamu)\b/ui', '$1 $2', $text);
        $text = preg_replace('/\bjumlah(tamu)\b/ui', 'jumlah $1', $text);
        $text = preg_replace('/\bpemesanan(pada)\b/ui', 'pemesanan $1', $text);

        // Hapus multiple whitespace berturutan
        $text = preg_replace('/[ \t]{2,}/', ' ', $text);

        return $text;
    }

    private static function getFirstChildByTagName($node, $tagName)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeName === $tagName) {
                return $child;
            }
        }
        return null;
    }
}
