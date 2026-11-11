<?php

namespace App\Helpers;

class DocxParser
{
    public static function parse($filePath)
    {
        if (!file_exists($filePath)) {
            return ['html' => '', 'image_count' => 0];
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return ['html' => '', 'image_count' => 0];
        }

        // 1. Read relationships to find media images
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

        // 2. Extract images to public/uploads/editor/
        $imageMap = [];
        $targetDir = public_path('uploads/editor');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $imgCount = 0;
        foreach ($relations as $rId => $target) {
            // Target path inside zip is usually media/image1.png or word/media/image1.png
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

        // 3. Read main document XML
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

    private static function convertXmlToHtml($xmlString, $imageMap)
    {
        $dom = new \DOMDocument();
        // Suppress warnings for custom Word XML namespaces
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
                $pContent = self::parseParagraph($node, $imageMap);
                if (!empty(trim(strip_tags($pContent, '<img>')))) {
                    $output .= $pContent;
                }
            } elseif ($node->nodeName === 'w:tbl') {
                $output .= self::parseTable($node, $imageMap);
            }
        }

        return $output;
    }

    private static function parseParagraph($pNode, $imageMap)
    {
        // Check if paragraph is a heading
        $isHeading = false;
        $headingLevel = 2;

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
        }

        $text = '';
        foreach ($pNode->childNodes as $child) {
            if ($child->nodeName === 'w:r') {
                $text .= self::parseRun($child, $imageMap);
            } elseif ($child->nodeName === 'w:drawing') {
                $text .= self::parseDrawing($child, $imageMap);
            }
        }

        if (empty(trim(strip_tags($text, '<img>')))) {
            return '';
        }

        if ($isHeading) {
            return '<h' . $headingLevel . '>' . $text . '</h' . $headingLevel . '>';
        }

        return '<p>' . $text . '</p>';
    }

    private static function parseRun($rNode, $imageMap)
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
            } elseif ($child->nodeName === 'w:drawing') {
                $text .= self::parseDrawing($child, $imageMap);
            } elseif ($child->nodeName === 'w:br') {
                $text .= '<br/>';
            }
        }

        if ($isUnderline) $text = '<u>' . $text . '</u>';
        if ($isItalic) $text = '<em>' . $text . '</em>';
        if ($isBold) $text = '<strong>' . $text . '</strong>';

        return $text;
    }

    private static function parseDrawing($drawingNode, $imageMap)
    {
        // Look for a:blip with r:embed="rIdX"
        $blips = $drawingNode->getElementsByTagName('blip');
        foreach ($blips as $blip) {
            foreach ($blip->attributes as $attr) {
                if ($attr->localName === 'embed' && isset($imageMap[$attr->value])) {
                    $src = $imageMap[$attr->value];
                    if (!preg_match('/^(https?:\/\/|\/)/i', $src)) {
                        $src = '/' . ltrim($src, '/');
                    }
                    return '<p><img src="' . $src . '" alt="Gambar Panduan" style="max-width: 100%; height: auto; border-radius: 8px; margin: 12px 0;" /></p>';
                }
            }
        }
        return '';
    }

    private static function parseTable($tblNode, $imageMap)
    {
        $html = '<table style="width: 100%; border-collapse: collapse; margin: 16px 0;" border="1"><tbody>';

        $rows = $tblNode->getElementsByTagName('tr');
        $isFirstRow = true;

        foreach ($rows as $tr) {
            $html .= '<tr>';
            $cells = $tr->getElementsByTagName('tc');
            foreach ($cells as $tc) {
                $cellText = '';
                $pList = $tc->getElementsByTagName('p');
                foreach ($pList as $p) {
                    $cellText .= self::parseParagraph($p, $imageMap);
                }
                if ($isFirstRow) {
                    $html .= '<th style="padding: 10px 14px; background-color: #0b1a36; color: #ffffff; font-weight: bold; border: 1px solid #475569;">' . ($cellText ?: '&nbsp;') . '</th>';
                } else {
                    $html .= '<td style="padding: 10px 14px; border: 1px solid #475569;">' . ($cellText ?: '&nbsp;') . '</td>';
                }
            }
            $html .= '</tr>';
            $isFirstRow = false;
        }

        $html .= '</tbody></table><p></p>';
        return $html;
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
