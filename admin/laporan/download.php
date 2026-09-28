<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
require_once __DIR__ . '/data.php';

[$mode, $year, $month, $start, $end] = laporanPeriode($_GET);
$data = laporanAmbilData(Database::getConnection(), $start, $end);
$safePeriod = preg_replace('/[^a-z0-9-]+/i', '-', strtolower($mode . '-' . $year . ($month ? '-' . $month : '')));

function wordSetCell(DOMDocument $document, DOMElement $cell, string $value, bool $numberPoints = false): void
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
    $lines = preg_split('/\r\n|\r|\n/', laporanNilai($value));
    $hasList = $numberPoints && count(array_filter($lines, static fn(string $line): bool => trim($line) !== '')) > 1;
    $pointNumber = 1;
    foreach ($lines as $index => $line) {
        if ($index > 0) {
            $run = $document->createElementNS($namespace, 'w:r');
            $run->appendChild($document->createElementNS($namespace, 'w:br'));
            $paragraph->appendChild($run);
        }
        if ($hasList && trim($line) !== '') {
            $line = preg_replace('/^\s*\d+[.)]\s*/u', '', $line);
            $line = $pointNumber++ . '. ' . $line;
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
            wordSetCell($document, $cell, (string) ($values[$index] ?? '-'), $index > 0);
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

$word = laporanWordTemplate(Database::getConnection(), $data, $start, $end);
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="laporan-tna-' . $safePeriod . '.docx"');
header('Content-Length: ' . strlen($word));
echo $word;
exit;
