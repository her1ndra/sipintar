<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

$idWawancara = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idWawancara || $idWawancara < 1) {
    setFlash('error', 'Wawancara tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$pdo = Database::getConnection();
$statement = $pdo->prepare(
    'SELECT w.tanggal_wawancara, p.nama_lengkap AS nama_di_wawancarai,
            j.nama_jabatan, penilai.nama_lengkap AS nama_pewawancara,
            u.NIP AS nip_pewawancara, h.kompetensi, h.isi_penilaian, h.jawaban
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
     LEFT JOIN users u ON u.id_user = w.id_user_penilai
     LEFT JOIN pegawai penilai ON penilai.id_pegawai = u.id_pegawai
     LEFT JOIN hasil_wawancara h ON h.id_wawancara = w.id_wawancara
     WHERE w.id_wawancara = ? AND w.id_kuesioner IS NULL
     ORDER BY h.id_hasil'
);
$statement->execute([$idWawancara]);
$rows = $statement->fetchAll();

if (!$rows) {
    setFlash('error', 'Wawancara tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$interview = $rows[0];
$tanggal = new DateTimeImmutable($interview['tanggal_wawancara']);
$hari = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
][$tanggal->format('l')];
$tanggalLabel = $hari . ', ' . $tanggal->format('d-m-Y');
$pewawancara = $interview['nama_pewawancara']
    ?: ($interview['nip_pewawancara'] ? 'NIP ' . $interview['nip_pewawancara'] : '-');
$diwawancarai = $interview['nama_di_wawancarai'] . ' (' . $interview['nama_jabatan'] . ')';

$xmlText = static function (?string $value): string {
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value ?? '-');
    return htmlspecialchars($value ?? '-', ENT_XML1 | ENT_COMPAT, 'UTF-8');
};
$paragraph = static function (string $label, string $value) use ($xmlText): string {
    return '<w:p><w:pPr><w:spacing w:after="100"/></w:pPr><w:r><w:rPr><w:b/></w:rPr><w:t>'
        . $xmlText($label) . '</w:t></w:r><w:r><w:t xml:space="preserve"> '
        . $xmlText($value) . '</w:t></w:r></w:p>';
};
$tableCell = static function (string $value, int $width, bool $header = false) use ($xmlText): string {
    $properties = '<w:tcW w:w="' . $width . '" w:type="dxa"/>';
    if ($header) {
        $properties .= '<w:shd w:fill="D9EAF7"/>';
    }
    $runProperties = $header ? '<w:rPr><w:b/></w:rPr>' : '';
    $textRuns = '';
    foreach (preg_split('/\r\n|\r|\n/', $value) as $index => $line) {
        if ($index > 0) {
            $textRuns .= '<w:br/>';
        }
        $textRuns .= '<w:t xml:space="preserve">' . $xmlText($line) . '</w:t>';
    }
    return '<w:tc><w:tcPr>' . $properties . '</w:tcPr><w:p><w:pPr><w:spacing w:after="80"/></w:pPr><w:r>'
        . $runProperties . $textRuns . '</w:r></w:p></w:tc>';
};

$columnWidths = [500, 1600, 1800, 2600, 2500];
$tableRow = static function (array $values, bool $header = false) use ($columnWidths, $tableCell): string {
    $cells = '';
    foreach ($columnWidths as $index => $width) {
        $cells .= $tableCell((string) ($values[$index] ?? '-'), $width, $header);
    }
    $repeatHeader = $header ? '<w:trPr><w:tblHeader/></w:trPr>' : '';
    return '<w:tr>' . $repeatHeader . $cells . '</w:tr>';
};

$tableRows = $tableRow(['No', 'Nama', 'Kompetensi Jabatan', 'Daftar Pertanyaan', 'Jawaban'], true);
$questionNumber = 0;
foreach ($rows as $row) {
    if ($row['isi_penilaian'] === null) {
        continue;
    }
    $questionNumber++;
    $tableRows .= $tableRow([
        (string) $questionNumber,
        $row['nama_di_wawancarai'],
        $row['kompetensi'],
        $row['isi_penilaian'],
        $row['jawaban'] ?? '-',
    ]);
}
if ($questionNumber === 0) {
    $tableRows .= $tableRow(['-', '-', '-', 'Belum ada pertanyaan.', '-']);
}

$grid = '';
foreach ($columnWidths as $width) {
    $grid .= '<w:gridCol w:w="' . $width . '"/>';
}
$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'
    . '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="240"/></w:pPr>'
    . '<w:r><w:rPr><w:b/><w:sz w:val="30"/></w:rPr><w:t>DATA WAWANCARA</w:t></w:r></w:p>'
    . $paragraph('Hari/Tanggal:', $tanggalLabel)
    . $paragraph('Yang mewawancarai:', $pewawancara)
    . $paragraph('Yang diwawancarai:', $diwawancarai)
    . '<w:p><w:pPr><w:spacing w:after="100"/></w:pPr></w:p>'
    . '<w:tbl><w:tblPr><w:tblW w:w="9000" w:type="dxa"/><w:tblLayout w:type="fixed"/>'
    . '<w:tblBorders><w:top w:val="single" w:sz="4" w:color="808080"/>'
    . '<w:left w:val="single" w:sz="4" w:color="808080"/><w:bottom w:val="single" w:sz="4" w:color="808080"/>'
    . '<w:right w:val="single" w:sz="4" w:color="808080"/><w:insideH w:val="single" w:sz="4" w:color="808080"/>'
    . '<w:insideV w:val="single" w:sz="4" w:color="808080"/></w:tblBorders></w:tblPr>'
    . '<w:tblGrid>' . $grid . '</w:tblGrid>' . $tableRows . '</w:tbl>'
    . '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="900" w:right="900" w:bottom="900" w:left="900"/></w:sectPr>'
    . '</w:body></w:document>';
$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
    . '</Types>';
$relationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
    . '</Relationships>';

$temporaryFile = tempnam(sys_get_temp_dir(), 'sipintar-wawancara-');
if ($temporaryFile === false) {
    http_response_code(500);
    exit('Dokumen wawancara gagal dibuat.');
}
$archive = new ZipArchive();
if ($archive->open($temporaryFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    unlink($temporaryFile);
    http_response_code(500);
    exit('Dokumen wawancara gagal dibuat.');
}
$archiveWritten = $archive->addFromString('[Content_Types].xml', $contentTypes);
$archiveWritten = $archive->addFromString('_rels/.rels', $relationships) && $archiveWritten;
$archiveWritten = $archive->addFromString('word/document.xml', $documentXml) && $archiveWritten;
$archiveClosed = $archive->close();
if (!$archiveWritten || !$archiveClosed) {
    unlink($temporaryFile);
    http_response_code(500);
    exit('Dokumen wawancara gagal dibuat.');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="wawancara-' . (int) $idWawancara . '.docx"');
header('Content-Length: ' . filesize($temporaryFile));
readfile($temporaryFile);
unlink($temporaryFile);
exit;
