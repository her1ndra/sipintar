<?php

function downloadKuesionerWord(int $idKuesioner, array $rows, array $penulis): void
{
    if (!class_exists(ZipArchive::class)) {
        throw new RuntimeException('Ekstensi ZipArchive diperlukan untuk membuat dokumen Word.');
    }

    $xmlText = static function (?string $value): string {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value ?? '');
        return htmlspecialchars($value ?? '', ENT_XML1 | ENT_COMPAT, 'UTF-8');
    };
    $run = static function (string $text, int $size, bool $bold = false, bool $preserveSpace = false) use ($xmlText): string {
        $properties = '<w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="' . $size . '"/>';
        if ($bold) {
            $properties .= '<w:b/>';
        }
        $properties .= '</w:rPr>';
        $space = $preserveSpace ? ' xml:space="preserve"' : '';
        return '<w:r>' . $properties . '<w:t' . $space . '>' . $xmlText($text) . '</w:t></w:r>';
    };
    $paragraph = static function (
        string $content,
        string $alignment = 'left',
        int $before = 0,
        int $after = 0,
        ?int $line = null,
        ?int $leftIndent = null,
        ?int $rightIndent = null,
        bool $bottomRule = false
    ): string {
        $properties = '<w:pPr><w:jc w:val="' . $alignment . '"/>';
        if ($before || $after || $line !== null) {
            $properties .= '<w:spacing w:before="' . $before . '" w:after="' . $after . '"';
            if ($line !== null) {
                $properties .= ' w:line="' . $line . '" w:lineRule="exact"';
            }
            $properties .= '/>';
        }
        if ($leftIndent !== null || $rightIndent !== null) {
            $properties .= '<w:ind'
                . ($leftIndent !== null ? ' w:left="' . $leftIndent . '"' : '')
                . ($rightIndent !== null ? ' w:right="' . $rightIndent . '"' : '')
                . '/>';
        }
        if ($bottomRule) {
            $properties .= '<w:pBdr><w:bottom w:val="single" w:sz="14" w:space="1" w:color="000000"/></w:pBdr>';
        }
        return '<w:p>' . $properties . '</w:pPr>' . $content . '</w:p>';
    };
    $tableCell = static function (string $content, int $width, string $verticalAlignment = 'top'): string {
        return '<w:tc><w:tcPr><w:tcW w:w="' . $width . '" w:type="dxa"/>'
            . '<w:vAlign w:val="' . $verticalAlignment . '"/></w:tcPr>' . $content . '</w:tc>';
    };

    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $date = new DateTimeImmutable('today');
    $dateLabel = 'Yogyakarta, ' . $date->format('j') . ' '
        . $months[(int) $date->format('n')] . ' ' . $date->format('Y');

    $logoPath = __DIR__ . '/../assets/img/logo.png';
    $logoInfo = is_file($logoPath) ? getimagesize($logoPath) : false;
    if ($logoInfo === false || $logoInfo['mime'] !== 'image/png') {
        throw new RuntimeException('Logo kop surat tidak dapat dibaca sebagai PNG.');
    }
    $logoWidth = 787400;
    $logoDrawing = '<w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0">'
        . '<wp:extent cx="' . $logoWidth . '" cy="' . $logoWidth . '"/>'
        . '<wp:effectExtent l="0" t="0" r="0" b="0"/><wp:docPr id="1" name="Logo Pengadilan Negeri Yogyakarta"/>'
        . '<wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1"/></wp:cNvGraphicFramePr>'
        . '<a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
        . '<pic:pic><pic:nvPicPr><pic:cNvPr id="0" name="logo.png"/>'
        . '<pic:cNvPicPr><a:picLocks noChangeAspect="1"/></pic:cNvPicPr></pic:nvPicPr>'
        . '<pic:blipFill><a:blip r:embed="rId2"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>'
        . '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $logoWidth . '" cy="' . $logoWidth . '"/></a:xfrm>'
        . '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic>'
        . '</a:graphicData></a:graphic></wp:inline></w:drawing>';
    $logoParagraph = $paragraph($logoDrawing, 'center', 0, 0);
    $letterheadLines = [
        ['MAHKAMAH AGUNG REPUBLIK INDONESIA', 20, true, 280],
        ['DIREKTORAT JENDERAL BADAN PERADILAN UMUM', 18, true, 280],
        ['PENGADILAN TINGGI YOGYAKARTA', 22, true, 300],
        ['PENGADILAN NEGERI YOGYAKARTA', 26, true, 300],
        ['Jalan Kapas Nomor 10, Semaki, Kecamatan Umbulharjo', 14, false, 280],
        ['Kota Yogyakarta, D.I. Yogyakarta 55166. www.pn-yogyakarta.go.id, pn.yogya@gmail.com', 14, false, 240],
    ];
    $letterhead = '';
    foreach ($letterheadLines as [$text, $size, $bold, $lineHeight]) {
        $letterhead .= $paragraph(
            $run($text, $size, $bold),
            'center',
            0,
            0,
            $lineHeight
        );
    }
    $headerTable = '<w:tbl><w:tblPr><w:tblW w:w="10706" w:type="dxa"/>'
        . '<w:tblBorders>'
        . '<w:top w:val="nil"/><w:left w:val="nil"/><w:right w:val="nil"/>'
        . '<w:bottom w:val="single" w:sz="6" w:space="1" w:color="000000"/>'
        . '<w:insideH w:val="nil"/><w:insideV w:val="nil"/></w:tblBorders>'
        . '<w:tblLayout w:type="fixed"/>'
        . '<w:tblCellMar><w:top w:w="0" w:type="dxa"/><w:left w:w="0" w:type="dxa"/>'
        . '<w:bottom w:w="100" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar></w:tblPr>'
        . '<w:tblGrid><w:gridCol w:w="1600"/><w:gridCol w:w="9106"/></w:tblGrid><w:tr>'
        . $tableCell($logoParagraph, 1600)
        . $tableCell($letterhead, 9106)
        . '</w:tr></w:tbl>';

    $metadata = '';
    foreach ([
        'Nama pegawai: ' . (string) ($rows[0]['nama_pegawai'] ?? ''),
        'Jabatan: ' . (string) ($rows[0]['nama_jabatan'] ?? ''),
        'Kompetensi jabatan: ' . (string) ($rows[0]['kompetensi'] ?? ''),
    ] as $line) {
        $metadata .= $paragraph($run($line, 20), 'left', 0, 0, 340, 500);
    }

    $questionRows = '';
    foreach ($rows as $row) {
        $questionText = (string) ($row['teks_pertanyaan'] ?? '');
        $lines = [];
        foreach (preg_split('/\r\n|\r|\n/', $questionText) as $sourceLine) {
            $lines = array_merge($lines, explode("\n", wordwrap($sourceLine, 94, "\n", true)));
        }
        $questionRuns = [];
        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $questionRuns[] = '<w:br/>';
                $line = '   ' . $line;
            } else {
                $line = ' ' . $line;
            }
            $questionRuns[] = '<w:t xml:space="preserve">' . $xmlText($line) . '</w:t>';
        }
        $questionContent = '<w:p><w:pPr><w:spacing w:after="160" w:line="240" w:lineRule="exact"/>'
            . '</w:pPr><w:r><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="20"/></w:rPr>'
            . implode('', $questionRuns) . '</w:r></w:p>';
        $numberContent = $paragraph($run((string) ($row['nomor_urut'] ?? ''), 20, true), 'left', 0, 160, 240);
        $questionRows .= '<w:tr><w:trPr><w:cantSplit/></w:trPr>'
            . $tableCell($numberContent, 260)
            . $tableCell($questionContent, 9440)
            . '</w:tr>';
    }
    $questionsTable = '<w:tbl><w:tblPr><w:tblW w:w="9700" w:type="dxa"/>'
        . '<w:tblInd w:w="500" w:type="dxa"/>'
        . '<w:tblBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/>'
        . '<w:right w:val="nil"/><w:insideH w:val="nil"/><w:insideV w:val="nil"/></w:tblBorders>'
        . '<w:tblLayout w:type="fixed"/><w:tblCellMar><w:top w:w="0" w:type="dxa"/>'
        . '<w:left w:w="0" w:type="dxa"/><w:bottom w:w="0" w:type="dxa"/>'
        . '<w:right w:w="0" w:type="dxa"/></w:tblCellMar></w:tblPr>'
        . '<w:tblGrid><w:gridCol w:w="260"/><w:gridCol w:w="9440"/></w:tblGrid>' . $questionRows . '</w:tbl>';

    $title = $paragraph($run('KUESIONER', 26, true), 'center', 200, 240);
    $questionHeading = $paragraph($run('Daftar Pertanyaan', 22, true), 'left', 300, 0, null, 500, 500, true);
    $signature = static function (string $label, string $position, string $name, int $width) use ($paragraph, $run): string {
        $body = $paragraph($run($label, 20), 'left', 0, 0, 360)
            . $paragraph($run($position, 20), 'left', 0, 0, 360)
            . $paragraph($run($name, 20, true), 'left', 1600, 0);
        return '<w:tc><w:tcPr><w:tcW w:w="' . $width . '" w:type="dxa"/><w:vAlign w:val="top"/></w:tcPr>'
            . $body . '</w:tc>';
    };
    $signatureTable = '<w:tbl><w:tblPr><w:tblW w:w="10000" w:type="dxa"/>'
        . '<w:tblInd w:w="700" w:type="dxa"/>'
        . '<w:tblBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/>'
        . '<w:right w:val="nil"/><w:insideH w:val="nil"/><w:insideV w:val="nil"/></w:tblBorders>'
        . '<w:tblLayout w:type="fixed"/><w:tblCellMar><w:top w:w="0" w:type="dxa"/>'
        . '<w:left w:w="0" w:type="dxa"/><w:bottom w:w="0" w:type="dxa"/>'
        . '<w:right w:w="0" w:type="dxa"/></w:tblCellMar></w:tblPr>'
        . '<w:tblGrid><w:gridCol w:w="5500"/><w:gridCol w:w="4500"/></w:tblGrid><w:tr>'
        . $signature('Mengetahui,', trim((string) ($penulis['nama_jabatan'] ?? '')), trim((string) ($penulis['nama'] ?? '')) ?: 'Nama Penilai', 5500)
        . $signature('Yang Mengisi,', trim((string) ($rows[0]['nama_jabatan'] ?? '')), trim((string) ($rows[0]['nama_pegawai'] ?? '')) ?: 'Nama Pegawai', 4500)
        . '</w:tr></w:tbl>';

    $documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
        . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'
        . ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"'
        . ' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"'
        . ' xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><w:body>'
        . '<w:p><w:pPr><w:spacing w:before="400" w:after="0"/></w:pPr><w:r><w:t></w:t></w:r></w:p>'
        . $headerTable
        . $paragraph($run($dateLabel, 20), 'right', 280, 0, null, 0, 0)
        . $title
        . $metadata
        . $questionHeading
        . '<w:p><w:pPr><w:spacing w:after="160"/></w:pPr><w:r><w:t></w:t></w:r></w:p>'
        . $questionsTable
        . $paragraph($run('Jawaban', 22, true), 'left', 360, 0, null, 500)
        . '<w:sectPr><w:footerReference w:type="default" r:id="rId3"/>'
        . '<w:pgSz w:w="11906" w:h="16838"/>'
        . '<w:pgMar w:top="0" w:right="600" w:bottom="4900" w:left="600" w:header="0" w:footer="3600"/>'
        . '</w:sectPr>'
        . '</w:body></w:document>';
    $footerXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
        . $signatureTable . '</w:ftr>';
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Default Extension="png" ContentType="image/png"/>'
        . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
        . '<Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>'
        . '</Types>';
    $relationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
        . '</Relationships>';
    $documentRelationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/logo.png"/>'
        . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/>'
        . '</Relationships>';

    $temporaryFile = tempnam(sys_get_temp_dir(), 'sipintar-kuesioner-');
    if ($temporaryFile === false) {
        throw new RuntimeException('Dokumen kuesioner gagal dibuat.');
    }
    $archive = new ZipArchive();
    if ($archive->open($temporaryFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        unlink($temporaryFile);
        throw new RuntimeException('Dokumen kuesioner gagal dibuat.');
    }
    $written = $archive->addFromString('[Content_Types].xml', $contentTypes);
    $written = $archive->addFromString('_rels/.rels', $relationships) && $written;
    $written = $archive->addFromString('word/document.xml', $documentXml) && $written;
    $written = $archive->addFromString('word/footer1.xml', $footerXml) && $written;
    $written = $archive->addFromString('word/_rels/document.xml.rels', $documentRelationships) && $written;
    $written = $archive->addFile($logoPath, 'word/media/logo.png') && $written;
    $closed = $archive->close();
    if (!$written || !$closed) {
        unlink($temporaryFile);
        throw new RuntimeException('Dokumen kuesioner gagal dibuat.');
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="kuesioner-' . $idKuesioner . '.docx"');
    header('Content-Length: ' . filesize($temporaryFile));
    readfile($temporaryFile);
    unlink($temporaryFile);
    exit;
}
