<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
require_once __DIR__ . '/data.php';

$format = ($_GET['format'] ?? 'pdf') === 'word' ? 'word' : 'pdf';
[$mode, $year, $month, $start, $end, $periodLabel] = laporanPeriode($_GET);
$data = laporanAmbilData(Database::getConnection(), $start, $end);
$safePeriod = preg_replace('/[^a-z0-9-]+/i', '-', strtolower($mode . '-' . $year . ($month ? '-' . $month : '')));

$sections = [
    'analisis_tugas' => ['Jabatan', 'Pegawai', 'Tugas', 'Kegiatan', 'Kompetensi sementara', 'Kompetensi jabatan', 'Tanggal input'],
    'gap' => ['Jabatan', 'Pegawai', 'Kompetensi jabatan', 'Kompetensi saat ini', 'Gap kompetensi', 'Dampak', 'Tanggal input'],
    'diklat' => ['Pegawai', 'Jabatan', 'Diklat', 'Metode pengembangan', 'Prioritas', 'Status', 'Tahun rencana', 'Catatan', 'Tanggal input'],
    'kuesioner' => ['Judul', 'Kompetensi', 'Tahun periode', 'Status', 'Jabatan dinilai', 'Tanggal input'],
    'wawancara' => ['Pegawai', 'Jabatan', 'Tanggal wawancara', 'Status', 'Catatan', 'Tanggal input'],
    'hasil_wawancara' => ['Pegawai', 'Kompetensi', 'Nilai', 'Status', 'Penilaian', 'Tanggal input'],
    'sertifikat' => ['Pegawai', 'Nama sertifikat', 'Penyelenggara', 'Tanggal terbit', 'Tanggal kadaluarsa', 'Tanggal input'],
    'kegiatan_hakim' => ['Pegawai', 'Jenis kegiatan', 'Nama kegiatan', 'Penyelenggara', 'Peran/topik', 'Tanggal mulai', 'Tanggal selesai', 'Lokasi', 'Tanggal input'],
];

function laporanBaris(string $key, array $row): array
{
    $tanggal = laporanNilai($row['created_at'] ?? null);
    switch ($key) {
        case 'analisis_tugas':
            $values = [$row['nama_jabatan'], $row['nama_lengkap'], $row['tugas'], $row['kegiatan'], $row['kompetensi_sementara_jabatan'], $row['kompetensi_jabatan'], $tanggal];
            break;
        case 'gap':
            $values = [$row['nama_jabatan'], $row['nama_lengkap'], $row['kompetensi_jabatan'], $row['kompetensi_pegawai_saat_ini'], $row['gap_kompetensi'], $row['dampak'], $tanggal];
            break;
        case 'diklat':
            $values = [$row['nama_lengkap'], $row['nama_jabatan'], $row['nama_diklat'], $row['metode_pengembangan'], $row['prioritas'], $row['status'], $row['tahun_rencana'], $row['catatan'], $tanggal];
            break;
        case 'kuesioner':
            $values = [$row['judul_kuesioner'], $row['kompetensi'], $row['tahun_periode'], $row['status'], $row['nama_jabatan'], $tanggal];
            break;
        case 'wawancara':
            $values = [$row['nama_lengkap'], $row['nama_jabatan'], $row['tanggal_wawancara'], $row['status'], $row['catatan'], $tanggal];
            break;
        case 'hasil_wawancara':
            $values = [$row['nama_lengkap'], $row['kompetensi'], $row['nilai'], $row['status_kompetensi'], $row['isi_penilaian'], $tanggal];
            break;
        case 'sertifikat':
            $values = [$row['nama_lengkap'], $row['nama_sertifikat'], $row['penyelenggara'], $row['tanggal_terbit'], $row['tanggal_kadaluarsa'], $tanggal];
            break;
        case 'kegiatan_hakim':
            $values = [$row['nama_lengkap'], $row['jenis_kegiatan'], $row['nama_kegiatan'], $row['penyelenggara'], $row['peran_topik'], $row['tanggal_mulai'], $row['tanggal_selesai'], $row['lokasi'], $tanggal];
            break;
        default:
            $values = [];
    }
    return array_map('laporanNilai', $values);
}

function wordSetCell(DOMDocument $document, DOMElement $cell, string $value): void
{
    $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    $properties = null;
    foreach (iterator_to_array($cell->childNodes) as $child) {
        if ($child instanceof DOMElement && $child->localName === 'tcPr') {
            $properties = $child;
            break;
        }
    }
    while ($cell->firstChild) {
        $cell->removeChild($cell->firstChild);
    }
    if ($properties) {
        $cell->appendChild($properties);
    }

    $paragraph = $document->createElementNS($namespace, 'w:p');
    $run = null;
    foreach (preg_split('/\r\n|\r|\n/', laporanNilai($value)) as $index => $line) {
        if ($index > 0) {
            $run = $document->createElementNS($namespace, 'w:r');
            $run->appendChild($document->createElementNS($namespace, 'w:br'));
            $paragraph->appendChild($run);
        }
        $run = $document->createElementNS($namespace, 'w:r');
        $text = $document->createElementNS($namespace, 'w:t');
        $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
        $text->appendChild($document->createTextNode($line));
        $run->appendChild($text);
        $paragraph->appendChild($run);
    }
    $cell->appendChild($paragraph);
}

function wordSetParagraph(DOMDocument $document, DOMElement $paragraph, string $value): void
{
    $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    $properties = null;
    foreach (iterator_to_array($paragraph->childNodes) as $child) {
        if ($child instanceof DOMElement && $child->localName === 'pPr') {
            $properties = $child;
            break;
        }
    }
    while ($paragraph->firstChild) {
        $paragraph->removeChild($paragraph->firstChild);
    }
    if ($properties) {
        $paragraph->appendChild($properties);
    }
    if ($value === '') {
        return;
    }
    $run = $document->createElementNS($namespace, 'w:r');
    $text = $document->createElementNS($namespace, 'w:t');
    $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
    $text->appendChild($document->createTextNode($value));
    $run->appendChild($text);
    $paragraph->appendChild($run);
}

function laporanTanggalDownload(): string
{
    $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    $tanggal = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
    return 'Yogyakarta, ' . (int) $tanggal->format('j') . ' ' . $bulan[(int) $tanggal->format('n')] . ' ' . $tanggal->format('Y');
}

function wordFillTable(DOMDocument $document, DOMElement $table, array $rows): void
{
    $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('w', $namespace);
    $tableRows = $xpath->query('./w:tr', $table);
    if (!$tableRows || $tableRows->length === 0) {
        return;
    }
    $prototype = $tableRows->item(1) ?: $tableRows->item(0);
    for ($index = $tableRows->length - 1; $index >= 1; $index--) {
        $table->removeChild($tableRows->item($index));
    }
    if (!$rows) {
        $rows = [array_fill(0, $xpath->query('./w:tc', $prototype)->length, 'Tidak ada data pada periode ini.')];
    }
    foreach ($rows as $values) {
        $row = $prototype->cloneNode(true);
        $cells = $xpath->query('./w:tc', $row);
        foreach ($cells as $index => $cell) {
            wordSetCell($document, $cell, (string) ($values[$index] ?? '-'));
        }
        $table->appendChild($row);
    }
}

function laporanTemplateRows(PDO $pdo, array $data, DateTimeImmutable $start, DateTimeImmutable $end): array
{
    $params = [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
    $query = static function (string $sql) use ($pdo, $params): array {
        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    };
    $analisis = array_map(static function (array $row): array {
        $nama = laporanNilai($row['nama_jabatan']);
        if (laporanNilai($row['nama_lengkap']) !== '-') {
            $nama .= "\n(" . $row['nama_lengkap'] . ')';
        }
        return [$nama, $row['tugas'], $row['kegiatan'], $row['kompetensi_sementara_jabatan'], $row['kompetensi_jabatan']];
    }, $data['analisis_tugas']);
    $wawancaraRows = $query("SELECT p.nama_lengkap, j.nama_jabatan, x.status,
            GROUP_CONCAT(DISTINCT h.kompetensi ORDER BY h.id_hasil SEPARATOR '\\n') AS kompetensi,
            GROUP_CONCAT(h.isi_penilaian ORDER BY h.id_hasil SEPARATOR '\\n') AS pertanyaan,
            GROUP_CONCAT(DISTINCT h.file_bukti ORDER BY h.file_bukti SEPARATOR '\\n') AS bukti,
            GROUP_CONCAT(DISTINCT h.status_kompetensi ORDER BY h.status_kompetensi SEPARATOR '\\n') AS hasil
            FROM wawancara x JOIN pegawai p ON p.id_pegawai = x.id_pegawai
            JOIN jabatan j ON j.id_jabatan = p.id_jabatan
            LEFT JOIN hasil_wawancara h ON h.id_wawancara = x.id_wawancara
            WHERE x.created_at >= ? AND x.created_at < ?
            GROUP BY x.id_wawancara ORDER BY p.nama_lengkap");
    $wawancara = array_map(static function (array $row): array {
        return [$row['nama_jabatan'] . "\n(" . $row['nama_lengkap'] . ')', $row['kompetensi'],
            $row['pertanyaan'], $row['bukti'] ?: '-', $row['hasil'] ?: $row['status']];
    }, $wawancaraRows);
    $kuesionerRows = $query("SELECT x.kompetensi, x.status, j.nama_jabatan,
            GROUP_CONCAT(CONCAT(q.nomor_urut, '. ', q.teks_pertanyaan) ORDER BY q.nomor_urut SEPARATOR '\\n') AS pertanyaan
            FROM kuesioner x JOIN jabatan j ON j.id_jabatan = x.id_jabatan_dinilai
            LEFT JOIN pertanyaan_kuesioner q ON q.id_kuesioner = x.id_kuesioner
            WHERE x.created_at >= ? AND x.created_at < ?
            GROUP BY x.id_kuesioner ORDER BY x.created_at");
    $kuesioner = array_map(static function (array $row): array {
        return [$row['nama_jabatan'], $row['kompetensi'], $row['pertanyaan'] ?: '-', 'Jawaban tertulis', $row['status']];
    }, $kuesionerRows);
    $gap = array_map(static function (array $row): array {
        return [$row['nama_jabatan'] . "\n(" . $row['nama_lengkap'] . ')', $row['kompetensi_jabatan'],
            $row['kompetensi_pegawai_saat_ini'], $row['gap_kompetensi'], $row['dampak']];
    }, $data['gap']);
    $diklat = array_map(static function (array $row): array {
        return [$row['nama_jabatan'], $row['catatan'] ?: '-', $row['nama_diklat'], $row['metode_pengembangan'], $row['prioritas']];
    }, $data['diklat']);

    return [1 => $analisis, 2 => $wawancara, 3 => $kuesioner, 4 => $gap, 5 => $diklat];
}

function laporanWordTemplate(PDO $pdo, array $data, DateTimeImmutable $start, DateTimeImmutable $end): string
{
    $template = __DIR__ . '/../../database/DOKUMEN TRAINING NEED ANALYSIS 2026.docx';
    if (!is_file($template)) {
        throw new RuntimeException('Template dokumen TNA 2026 tidak ditemukan.');
    }
    $zip = new ZipArchive();
    if ($zip->open($template) !== true) {
        throw new RuntimeException('Template dokumen TNA 2026 tidak dapat dibuka.');
    }
    $document = new DOMDocument();
    $document->preserveWhiteSpace = false;
    $document->loadXML($zip->getFromName('word/document.xml'));
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    foreach ($xpath->query('//w:t') as $textNode) {
        if (trim($textNode->nodeValue) === 'DAFTARN ISI') {
            $textNode->nodeValue = str_replace('DAFTARN ISI', 'DAFTAR ISI', $textNode->nodeValue);
        }
    }
    $tables = $xpath->query('//w:tbl');
    $templateRows = laporanTemplateRows($pdo, $data, $start, $end);
    foreach ($templateRows as $tableNumber => $rows) {
        if ($tables->item($tableNumber - 1)) {
            wordFillTable($document, $tables->item($tableNumber - 1), $rows);
        }
    }
    foreach ($xpath->query('//w:body/w:p') as $paragraph) {
        $text = '';
        foreach ($xpath->query('.//w:t', $paragraph) as $part) {
            $text .= $part->nodeValue;
        }
        if (preg_match('/^Yogyakarta,\s*\d+\s+\w+\s*\d{4}$/u', trim($text))) {
            wordSetParagraph($document, $paragraph, laporanTanggalDownload());
        } elseif (trim($text) === 'Ketua') {
            wordSetParagraph($document, $paragraph, '');
        }
    }
    $output = tempnam(sys_get_temp_dir(), 'tna-word-');
    $zip->close();
    copy($template, $output);
    $result = new ZipArchive();
    $result->open($output);
    $result->addFromString('word/document.xml', $document->saveXML());
    $result->close();
    $content = file_get_contents($output);
    unlink($output);
    return $content;
}

if ($format === 'word') {
    $word = laporanWordTemplate(Database::getConnection(), $data, $start, $end);
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="laporan-tna-' . $safePeriod . '.docx"');
    header('Content-Length: ' . strlen($word));
    echo $word;
    exit;
}

function laporanPdfFromWord(PDO $pdo, array $data, DateTimeImmutable $start, DateTimeImmutable $end): string
{
    $autoload = __DIR__ . '/../../vendor/autoload.php';
    if (!is_file($autoload)) {
        throw new RuntimeException('Library konversi PDF belum tersedia.');
    }
    require_once $autoload;
    $word = laporanWordTemplate($pdo, $data, $start, $end);
    $docxPath = tempnam(sys_get_temp_dir(), 'tna-pdf-source-') . '.docx';
    $pdfPath = tempnam(sys_get_temp_dir(), 'tna-pdf-result-') . '.pdf';
    file_put_contents($docxPath, $word);
    try {
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(__DIR__ . '/../../vendor/dompdf/dompdf');
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($docxPath, 'Word2007');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $writer->save($pdfPath);
        $pdf = file_get_contents($pdfPath);
    } finally {
        @unlink($docxPath);
        @unlink($pdfPath);
    }
    if ($pdf === false || $pdf === '') {
        throw new RuntimeException('PDF hasil konversi kosong.');
    }
    return $pdf;
}

try {
    $pdf = laporanPdfFromWord(Database::getConnection(), $data, $start, $end);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="laporan-tna-' . $safePeriod . '.pdf"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
} catch (Throwable $exception) {
    // Fallback ke renderer tabel manual jika converter tidak mendukung elemen template tertentu.
}

function laporanPdfText(string $value): string
{
    $value = preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $value));
    return iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $value) ?: $value;
}

function laporanPdfEscape(string $value): string
{
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
}

function laporanPdfWrap(string $value, int $characters): array
{
    $value = laporanPdfText($value);
    $parts = preg_split('/\r\n|\r|\n/', $value);
    $lines = [];
    foreach ($parts as $part) {
        $wrapped = wordwrap($part === '' ? ' ' : $part, $characters, "\n", true);
        foreach (explode("\n", $wrapped) as $line) {
            $lines[] = $line === '' ? ' ' : $line;
        }
    }
    return $lines ?: [' '];
}

function laporanPdfBuildPages(array $data, array $sections, string $periodLabel, DateTimeImmutable $start, DateTimeImmutable $end): array
{
    $pageWidth = 842;
    $pageHeight = 595;
    $left = 28;
    $right = 28;
    $top = 555;
    $bottom = 28;
    $fontSize = 6.5;
    $lineHeight = 8;
    $tableWidth = $pageWidth - $left - $right;
    $streams = [];
    $stream = "BT\n/F1 15 Tf\n" . $left . ' ' . $top . " Td\n(DOKUMEN TRAINING NEED ANALYSIS 2026) Tj\n/F1 9 Tf\n0 -16 Td\n(Laporan " . laporanPdfEscape(laporanPdfText($periodLabel)) . ") Tj\n0 -12 Td\n(Periode data: " . $start->format('d/m/Y') . ' - ' . $end->modify('-1 day')->format('d/m/Y') . ") Tj\nET\n";
    $y = 505;

    $newPage = static function () use (&$streams, &$stream, &$y, $pageWidth, $pageHeight, $left, $top, $periodLabel): void {
        $streams[] = $stream;
        $stream = "BT\n/F1 8 Tf\n" . $left . ' ' . $top . " Td\n(DOKUMEN TRAINING NEED ANALYSIS 2026 - " . laporanPdfEscape(laporanPdfText($periodLabel)) . ") Tj\nET\n";
        $y = $top - 22;
    };

    foreach ($sections as $key => $headers) {
        $columnCount = count($headers);
        $columnWidth = $tableWidth / $columnCount;
        $sectionTitle = strtoupper(laporanLabel($key)) . ' (' . count($data[$key]) . ')';
        if ($y < $bottom + 45) {
            $newPage();
        }
        $stream .= 'BT /F1 10 Tf ' . $left . ' ' . $y . ' Td (' . laporanPdfEscape(laporanPdfText($sectionTitle)) . ") Tj ET\n";
        $y -= 15;
        $headerLines = array_map(static fn (string $header): array => laporanPdfWrap($header, 16), $headers);
        $headerHeight = max(array_map('count', $headerLines)) * $lineHeight + 7;
        $stream .= $left . ' ' . ($y - $headerHeight) . ' ' . $tableWidth . ' ' . $headerHeight . " re S\n";
        foreach ($headers as $index => $header) {
            $x = $left + ($index * $columnWidth);
            if ($index > 0) {
                $stream .= $x . ' ' . ($y - $headerHeight) . ' m ' . $x . ' ' . $y . " l S\n";
            }
            foreach ($headerLines[$index] as $lineIndex => $line) {
                $textY = $y - 9 - ($lineIndex * $lineHeight);
                $stream .= 'BT /F1 ' . $fontSize . ' Tf ' . ($x + 3) . ' ' . $textY . ' Td (' . laporanPdfEscape($line) . ") Tj ET\n";
            }
        }
        $y -= $headerHeight;
        foreach ($data[$key] as $row) {
            $values = laporanBaris($key, $row);
            $cellLines = [];
            $rowLines = 1;
            foreach ($values as $index => $value) {
                $cellLines[$index] = laporanPdfWrap($value, max(8, (int) floor($columnWidth / 3.8)));
                $rowLines = max($rowLines, count($cellLines[$index]));
            }
            $rowHeight = ($rowLines * $lineHeight) + 7;
            if ($y - $rowHeight < $bottom) {
                $newPage();
                $y -= 5;
            }
            $stream .= $left . ' ' . ($y - $rowHeight) . ' ' . $tableWidth . ' ' . $rowHeight . " re S\n";
            foreach ($values as $index => $value) {
                $x = $left + ($index * $columnWidth);
                if ($index > 0) {
                    $stream .= $x . ' ' . ($y - $rowHeight) . ' m ' . $x . ' ' . $y . " l S\n";
                }
                foreach ($cellLines[$index] as $lineIndex => $line) {
                    $textY = $y - 9 - ($lineIndex * $lineHeight);
                    $stream .= 'BT /F1 ' . $fontSize . ' Tf ' . ($x + 3) . ' ' . $textY . ' Td (' . laporanPdfEscape($line) . ") Tj ET\n";
                }
            }
            $y -= $rowHeight;
        }
        if (!$data[$key]) {
            $emptyHeight = 18;
            $stream .= $left . ' ' . ($y - $emptyHeight) . ' ' . $tableWidth . ' ' . $emptyHeight . " re S\n";
            $stream .= 'BT /F1 ' . $fontSize . ' Tf ' . ($left + 3) . ' ' . ($y - 11) . " Td (Tidak ada data pada periode ini.) Tj ET\n";
            $y -= $emptyHeight;
        }
        $y -= 18;
    }
    $streams[] = $stream;
    return $streams;
}

$pages = laporanPdfBuildPages($data, $sections, $periodLabel, $start, $end);
$objects = ['<< /Type /Catalog /Pages 2 0 R >>', ''];
$pageReferences = [];
foreach ($pages as $pageIndex => $pageLines) {
    $pageObject = 3 + ($pageIndex * 2);
    $contentObject = $pageObject + 1;
    $pageReferences[] = $pageObject . ' 0 R';
    $stream = $pageLines;
    $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 ' . (3 + (count($pages) * 2)) . ' 0 R >> >> /Contents ' . $contentObject . ' 0 R >>';
    $objects[] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
}
$fontObject = 3 + (count($pages) * 2);
$objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
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
header('Content-Disposition: attachment; filename="laporan-tna-' . $safePeriod . '.pdf"');
echo $pdf;