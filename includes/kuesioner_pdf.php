<?php

function downloadKuesionerPdf(int $idKuesioner, array $rows, array $penulis): void
{
    $toPdfText = static function (string $value): string {
        return mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
    };
    $escapePdfText = static function (string $value): string {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    };
    $drawText = static function (string $text, float $x, float $y, int $size = 11, bool $bold = false) use ($toPdfText, $escapePdfText): string {
        $font = $bold ? 'F2' : 'F1';
        return 'BT /' . $font . ' ' . $size . ' Tf ' . $x . ' ' . $y . ' Td (' . $escapePdfText($toPdfText($text)) . ") Tj ET\n";
    };
    $drawCenteredText = static function (string $text, float $y, int $size = 11, bool $bold = false) use ($toPdfText, $drawText): string {
        $width = strlen($toPdfText($text)) * $size * 0.5;
        return $drawText($text, max(40, (595 - $width) / 2), $y, $size, $bold);
    };
    $drawRightSignatureText = static function (string $text, float $y, int $size = 11, bool $bold = false) use ($toPdfText, $drawText): string {
        $width = strlen($toPdfText($text)) * $size * 0.5;
        return $drawText($text, 340 + max(0, (190 - $width) / 2), $y, $size, $bold);
    };
    $drawRule = static function (float $x1, float $y, float $x2): string {
        return '0.7 w ' . $x1 . ' ' . $y . ' m ' . $x2 . ' ' . $y . " l S\n";
    };

    $questionLines = [];
    foreach ($rows as $row) {
        $wrappedLines = explode("\n", wordwrap($toPdfText($row['teks_pertanyaan']), 82, "\n", true));
        $questionLines[] = $row['nomor_urut'] . '.  ' . array_shift($wrappedLines);
        foreach ($wrappedLines as $line) {
            $questionLines[] = '    ' . $line;
        }
    }

    $metadataLines = [];
    foreach ([
        'Judul: ' . $rows[0]['judul_kuesioner'],
        'Tahun periode: ' . $rows[0]['tahun_periode'],
        'Kompetensi / jabatan: ' . $rows[0]['kompetensi'],
    ] as $metadata) {
        foreach (explode("\n", wordwrap($toPdfText($metadata), 84, "\n", true)) as $line) {
            $metadataLines[] = $line;
        }
    }
    $questionStartY = 715 - (count($metadataLines) * 16) - 38;
    $firstPageCapacity = max(1, min(20, (int) floor(($questionStartY - 285) / 15) + 1));
    $pages = [array_slice($questionLines, 0, $firstPageCapacity)];
    for ($offset = $firstPageCapacity; $offset < count($questionLines); $offset += 30) {
        $pages[] = array_slice($questionLines, $offset, 30);
    }

    $writerName = trim((string) ($penulis['nama'] ?? ''));
    $writerPosition = trim((string) ($penulis['nama_jabatan'] ?? ''));
    $objects = [
        '<< /Type /Catalog /Pages 2 0 R >>',
        '',
    ];
    $pageReferences = [];

    foreach ($pages as $pageIndex => $pageLines) {
        $pageObject = 3 + ($pageIndex * 2);
        $contentObject = $pageObject + 1;
        $pageReferences[] = $pageObject . ' 0 R';
        $stream = '';

        if ($pageIndex === 0) {
            $stream .= $drawCenteredText('MAHKAMAH AGUNG REPUBLIK INDONESIA', 808, 10);
            $stream .= $drawCenteredText('PENGADILAN NEGERI YOGYAKARTA', 788, 15, true);
            $stream .= $drawRule(48, 775, 547);
            $stream .= $drawRule(48, 772, 547);
            $stream .= $drawCenteredText('KUESIONER', 744, 14, true);
            $metadataY = 715;
            foreach ($metadataLines as $line) {
                $stream .= $drawText($line, 55, $metadataY);
                $metadataY -= 16;
            }
            $stream .= $drawText('Daftar Pertanyaan', 55, $questionStartY, 11, true);
            $stream .= $drawRule(55, $questionStartY - 9, 540);
            $questionY = $questionStartY - 30;
        } else {
            $stream .= $drawCenteredText('PENGADILAN NEGERI YOGYAKARTA', 808, 12, true);
            $stream .= $drawRule(48, 795, 547);
            $stream .= $drawCenteredText('KUESIONER (LANJUTAN)', 773, 12, true);
            $questionY = 744;
        }

        foreach ($pageLines as $line) {
            $stream .= $drawText($line, 58, $questionY, 10);
            $questionY -= 15;
        }

        if ($pageIndex === count($pages) - 1) {
            $stream .= $drawText('Yogyakarta, ' . date('d-m-Y'), 350, 185, 10);
            $stream .= $drawText('Mengetahui,', 105, 164, 10);
            $stream .= $drawText('Yang Membuat,', 385, 164, 10);
            $stream .= $drawRule(65, 91, 255);
            $stream .= $drawRule(340, 91, 530);
            $stream .= $drawText('Jabatan: ____________________', 65, 74, 9);
            $stream .= $drawText('Nama: ______________________', 65, 58, 9);
            $stream .= $drawRightSignatureText($writerPosition !== '' ? $writerPosition : 'Jabatan', 74, 9);
            $stream .= $drawRightSignatureText($writerName !== '' ? $writerName : 'Nama', 58, 10, true);
        }

        $stream .= "BT ET";
        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 ' . (3 + (count($pages) * 2)) . ' 0 R /F2 ' . (4 + (count($pages) * 2)) . ' 0 R >> >> /Contents ' . $contentObject . ' 0 R >>';
        $objects[] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
    }

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
