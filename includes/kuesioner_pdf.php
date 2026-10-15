<?php

function downloadKuesionerPdf(int $idKuesioner, array $rows, array $penulis): void
{
    $toPdfText = static function (string $value): string {
        return mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
    };
    $escapePdfText = static function (string $value): string {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    };
    $drawText = static function (string $text, float $x, float $y, int $size = 10, bool $bold = false) use ($toPdfText, $escapePdfText): string {
        $font = $bold ? 'F2' : 'F1';
        return 'BT /' . $font . ' ' . $size . ' Tf ' . $x . ' ' . $y . ' Td ('
            . $escapePdfText($toPdfText($text)) . ") Tj ET\n";
    };
    $drawCenteredText = static function (string $text, float $y, int $size = 10, bool $bold = false) use ($toPdfText, $drawText): string {
        $width = strlen($toPdfText($text)) * $size * 0.5;
        return $drawText($text, max(40, (595 - $width) / 2), $y, $size, $bold);
    };
    $measurePdfText = static function (string $text, int $size, bool $bold) use ($toPdfText): float {
        $encodedText = $toPdfText($text);
        $widths = [
            ' ' => 278, '!' => 278, '"' => 355, '#' => 556, '$' => 556, '%' => 889,
            '&' => 667, "'" => 191, '(' => 333, ')' => 333, '*' => 389, '+' => 584,
            ',' => 278, '-' => 333, '.' => 278, '/' => 278, ':' => 278, ';' => 278,
            '<' => 584, '=' => 584, '>' => 584, '?' => 556, '@' => 1015, '[' => 278,
            '\\' => 278, ']' => 278, '^' => 469, '_' => 556, '`' => 333, '{' => 334,
            '|' => 260, '}' => 334, '~' => 584,
        ];
        foreach ([
            556 => '0123456789abdeghnopquL',
            667 => 'ABEKPSVXY',
            722 => 'CDHNRw',
            611 => 'FTZ',
            778 => 'GOQ',
            278 => 'Ift',
            500 => 'Jcksvxyz',
            833 => 'Mm',
            944 => 'W',
            222 => 'ijl',
            333 => 'r',
        ] as $width => $characters) {
            foreach (str_split($characters) as $character) {
                $widths[$character] = $width;
            }
        }
        $widthUnits = 0;
        foreach (str_split($encodedText) as $character) {
            $widthUnits += $widths[$character] ?? 556;
        }
        return ($widthUnits * $size) / 1000;
    };
    $drawCenteredTextAt = static function (string $text, float $center, float $y, int $size = 10, bool $bold = false) use ($measurePdfText, $drawText): string {
        $width = $measurePdfText($text, $size, $bold);
        return $drawText($text, $center - ($width / 2), $y, $size, $bold);
    };
    $drawRightText = static function (string $text, float $right, float $y, int $size = 10, bool $bold = false) use ($measurePdfText, $drawText): string {
        $width = $measurePdfText($text, $size, $bold);
        return $drawText($text, $right - $width, $y, $size, $bold);
    };
    $drawRule = static function (float $x1, float $y, float $x2): string {
        return '0.7 w ' . $x1 . ' ' . $y . ' m ' . $x2 . ' ' . $y . " l S\n";
    };
    $wrap = static function (string $value, int $width) use ($toPdfText): array {
        $value = $toPdfText($value);
        $lines = [];
        foreach (preg_split('/\r\n|\r|\n/', $value) as $paragraph) {
            $lines = array_merge($lines, explode("\n", wordwrap($paragraph, $width, "\n", true)));
        }
        return $lines ?: [''];
    };
    $metadataLines = [];
    foreach ([
        'Nama pegawai: ' . (string) $rows[0]['nama_pegawai'],
        'Jabatan: ' . (string) $rows[0]['nama_jabatan'],
        'Kompetensi jabatan: ' . (string) $rows[0]['kompetensi'],
    ] as $metadata) {
        $metadataLines = array_merge($metadataLines, $wrap($metadata, 85));
    }
    $metadataStartY = 665;
    $firstTableTop = $metadataStartY - ((count($metadataLines) - 1) * 17) - 20;

    $logoPath = __DIR__ . '/../assets/img/logo.png';
    $logoFile = file_get_contents($logoPath);
    if ($logoFile === false || substr($logoFile, 0, 8) !== "\x89PNG\r\n\x1a\n") {
        throw new RuntimeException('Logo kop surat tidak dapat dibaca.');
    }
    $logoWidth = 0;
    $logoHeight = 0;
    $bitDepth = null;
    $colorType = null;
    $interlace = null;
    $compressedImage = '';
    for ($offset = 8, $fileLength = strlen($logoFile); $offset + 12 <= $fileLength;) {
        $chunkLength = unpack('N', substr($logoFile, $offset, 4))[1];
        $chunkType = substr($logoFile, $offset + 4, 4);
        $chunkData = substr($logoFile, $offset + 8, $chunkLength);
        if ($chunkType === 'IHDR') {
            $header = unpack('Nwidth/Nheight/CbitDepth/CcolorType/Ccompression/Cfilter/Cinterlace', $chunkData);
            $logoWidth = $header['width'];
            $logoHeight = $header['height'];
            $bitDepth = $header['bitDepth'];
            $colorType = $header['colorType'];
            $interlace = $header['interlace'];
        } elseif ($chunkType === 'IDAT') {
            $compressedImage .= $chunkData;
        } elseif ($chunkType === 'IEND') {
            break;
        }
        $offset += 12 + $chunkLength;
    }
    if ($logoWidth < 1 || $logoHeight < 1 || $bitDepth !== 8 || $colorType !== 6 || $interlace !== 0) {
        throw new RuntimeException('Format logo kop surat tidak didukung.');
    }
    $pngData = gzuncompress($compressedImage);
    $sourceStride = $logoWidth * 4;
    if ($pngData === false || strlen($pngData) !== ($sourceStride + 1) * $logoHeight) {
        throw new RuntimeException('Data gambar logo kop surat tidak valid.');
    }
    $rgbImage = '';
    $alphaImage = '';
    $previousRow = array_fill(0, $sourceStride, 0);
    $sourceOffset = 0;
    for ($rowIndex = 0; $rowIndex < $logoHeight; $rowIndex++) {
        $filter = ord($pngData[$sourceOffset++]);
        $filteredRow = array_values(unpack('C*', substr($pngData, $sourceOffset, $sourceStride)));
        $sourceOffset += $sourceStride;
        $row = [];
        foreach ($filteredRow as $byteIndex => $value) {
            $left = $byteIndex >= 4 ? $row[$byteIndex - 4] : 0;
            $up = $previousRow[$byteIndex];
            $upperLeft = $byteIndex >= 4 ? $previousRow[$byteIndex - 4] : 0;
            if ($filter === 1) {
                $value += $left;
            } elseif ($filter === 2) {
                $value += $up;
            } elseif ($filter === 3) {
                $value += (int) floor(($left + $up) / 2);
            } elseif ($filter === 4) {
                $predictor = $left + $up - $upperLeft;
                $leftDistance = abs($predictor - $left);
                $upDistance = abs($predictor - $up);
                $upperLeftDistance = abs($predictor - $upperLeft);
                $value += $leftDistance <= $upDistance && $leftDistance <= $upperLeftDistance
                    ? $left
                    : ($upDistance <= $upperLeftDistance ? $up : $upperLeft);
            } elseif ($filter !== 0) {
                throw new RuntimeException('Filter gambar logo kop surat tidak didukung.');
            }
            $row[$byteIndex] = $value & 0xFF;
        }
        $rgbRow = "\0";
        $alphaRow = "\0";
        for ($pixelOffset = 0; $pixelOffset < $sourceStride; $pixelOffset += 4) {
            $rgbRow .= chr($row[$pixelOffset]) . chr($row[$pixelOffset + 1]) . chr($row[$pixelOffset + 2]);
            $alphaRow .= chr($row[$pixelOffset + 3]);
        }
        $rgbImage .= $rgbRow;
        $alphaImage .= $alphaRow;
        $previousRow = $row;
    }
    $compressedRgb = gzcompress($rgbImage);
    $compressedAlpha = gzcompress($alphaImage);
    if ($compressedRgb === false || $compressedAlpha === false) {
        throw new RuntimeException('Logo kop surat gagal disiapkan.');
    }

    $questionRows = [];
    foreach ($rows as $row) {
        $questionRows[] = [
            'number' => (string) $row['nomor_urut'],
            'lines' => $wrap((string) $row['teks_pertanyaan'], 94),
            'height' => 0,
        ];
    }

    $pages = [];
    $pageTop = 0;
    foreach ($questionRows as $questionRow) {
        $questionRow['height'] = count($questionRow['lines']) * 14 + 8;
        if (!$pages || $pageTop - $questionRow['height'] < 245) {
            $pages[] = ['type' => 'questions', 'rows' => []];
            $pageTop = count($pages) === 1 ? $firstTableTop : 730;
        }
        $pages[count($pages) - 1]['rows'][] = $questionRow;
        $pageTop -= $questionRow['height'];
    }
    if (!$pages) {
        $pages[] = ['type' => 'questions', 'rows' => []];
    }
    $answerPageIndex = count($pages) - 1;
    $answerHeadingY = $pageTop - ($answerPageIndex === 0 ? 52 : 18);
    if ($answerHeadingY < 245) {
        $pages[] = ['type' => 'answers', 'rows' => []];
        $answerPageIndex++;
    }

    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $date = new DateTimeImmutable('today');
    $dateLabel = 'Yogyakarta, ' . $date->format('j') . ' '
        . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
    $writerName = trim((string) ($penulis['nama'] ?? ''));
    $writerPosition = trim((string) ($penulis['nama_jabatan'] ?? ''));
    $employeeName = trim((string) ($rows[0]['nama_pegawai'] ?? ''));
    $employeePosition = trim((string) ($rows[0]['nama_jabatan'] ?? ''));

    $objects = ['<< /Type /Catalog /Pages 2 0 R >>', ''];
    $pageReferences = [];
    foreach ($pages as $pageIndex => $page) {
        $pageRows = $page['rows'];
        $pageObject = 3 + ($pageIndex * 2);
        $contentObject = $pageObject + 1;
        $pageReferences[] = $pageObject . ' 0 R';
        $stream = '';

        if ($pageIndex === 0) {
            $stream .= 'q 62 0 0 62 30 760 cm /Im1 Do Q' . "\n";
            $stream .= $drawCenteredTextAt('MAHKAMAH AGUNG REPUBLIK INDONESIA', 337.5, 820, 10, true);
            $stream .= $drawCenteredTextAt('DIREKTORAT JENDERAL BADAN PERADILAN UMUM', 337.5, 806, 9, true);
            $stream .= $drawCenteredTextAt('PENGADILAN TINGGI YOGYAKARTA', 337.5, 791, 11, true);
            $stream .= $drawCenteredTextAt('PENGADILAN NEGERI YOGYAKARTA', 337.5, 776, 13, true);
            $stream .= $drawCenteredTextAt('Jalan Kapas Nomor 10, Semaki, Kecamatan Umbulharjo', 337.5, 762, 7);
            $stream .= $drawCenteredTextAt('Kota Yogyakarta, D.I. Yogyakarta 55166. www.pn-yogyakarta.go.id, pn.yogya@gmail.com', 337.5, 750, 7);
            $stream .= $drawRule(30, 741, 565);
            $stream .= $drawRightText($dateLabel, 565, 719, 10);
            $stream .= $drawCenteredText('KUESIONER', 695, 13, true);
            foreach ($metadataLines as $lineIndex => $line) {
                $stream .= $drawText($line, 55, $metadataStartY - ($lineIndex * 17));
            }
            $questionTop = $firstTableTop;
        } else {
            $stream .= $drawCenteredText('PENGADILAN NEGERI YOGYAKARTA', 808, 12, true);
            $stream .= $drawRule(48, 795, 547);
            if ($page['type'] === 'questions') {
                $stream .= $drawCenteredText('DAFTAR PERTANYAAN (LANJUTAN)', 775, 12, true);
                $questionTop = 755;
            } else {
                $questionTop = 770;
            }
        }

        if ($page['type'] === 'questions') {
            if ($pageIndex === 0) {
                $stream .= $drawText('Daftar Pertanyaan', 55, $questionTop - 14, 11, true);
                $stream .= $drawRule(55, $questionTop - 25, 540);
                $questionTop -= 34;
            }
            foreach ($pageRows as $questionRow) {
                $stream .= $drawText($questionRow['number'] . '.', 55, $questionTop - 12, 10, true);
                foreach ($questionRow['lines'] as $lineIndex => $line) {
                    $prefix = $lineIndex === 0 ? ' ' : '   ';
                    $stream .= $drawText($prefix . $line, 68, $questionTop - 12 - ($lineIndex * 12), 10);
                }
                $questionTop -= $questionRow['height'];
            }
            if ($pageIndex === $answerPageIndex) {
                $stream .= $drawText('Jawaban', 55, $questionTop - 18, 11, true);
            }
        } elseif ($page['type'] === 'answers') {
            $stream .= $drawText('Jawaban', 55, $questionTop - 18, 11, true);
        }

        if ($pageIndex === count($pages) - 1) {
            $stream .= $drawText('Mengetahui,', 65, 188, 10);
            $stream .= $drawText('Yang Mengisi,', 340, 188, 10);
            if ($writerPosition !== '') {
                $stream .= $drawText($writerPosition, 65, 170, 10);
            }
            if ($employeePosition !== '') {
                $stream .= $drawText($employeePosition, 340, 170, 10);
            }
            $stream .= $drawText($writerName !== '' ? $writerName : 'Nama Penilai', 65, 72, 10, true);
            $stream .= $drawText($employeeName !== '' ? $employeeName : 'Nama Pegawai', 340, 72, 10, true);
        }

        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 '
            . (5 + (count($pages) * 2)) . ' 0 R /F2 ' . (6 + (count($pages) * 2))
            . ' 0 R >> /XObject << /Im1 ' . (4 + (count($pages) * 2))
            . ' 0 R >> >> /Contents ' . $contentObject . ' 0 R >>';
        $objects[] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
    }

    $maskObject = 3 + (count($pages) * 2);
    $logoObject = $maskObject + 1;
    $objects[] = '<< /Type /XObject /Subtype /Image /Width ' . $logoWidth
        . ' /Height ' . $logoHeight . ' /ColorSpace /DeviceGray /BitsPerComponent 8'
        . ' /Filter /FlateDecode /DecodeParms << /Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns '
        . $logoWidth . ' >> /Length ' . strlen($compressedAlpha) . " >>\nstream\n"
        . $compressedAlpha . "\nendstream";
    $objects[] = '<< /Type /XObject /Subtype /Image /Width ' . $logoWidth
        . ' /Height ' . $logoHeight . ' /ColorSpace /DeviceRGB /BitsPerComponent 8'
        . ' /Filter /FlateDecode /DecodeParms << /Predictor 15 /Colors 3 /BitsPerComponent 8 /Columns '
        . $logoWidth . ' >> /SMask ' . $maskObject . ' 0 R /Length ' . strlen($compressedRgb)
        . " >>\nstream\n" . $compressedRgb . "\nendstream";
    $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
    $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
    $objects[1] = '<< /Type /Pages /Kids [' . implode(' ', $pageReferences) . '] /Count ' . count($pages) . ' >>';

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $number => $object) {
        $offsets[$number + 1] = strlen($pdf);
        $pdf .= ($number + 1) . " 0 obj\n" . $object . "\nendobj\n";
    }
    $xref = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
    for ($index = 1; $index <= count($objects); $index++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
    }
    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="kuesioner-' . $idKuesioner . '.pdf"');
    echo $pdf;
    exit;
}
