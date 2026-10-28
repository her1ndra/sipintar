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
            j.nama_jabatan,
            COALESCE((
                SELECT GROUP_CONCAT(
                    DISTINCT CASE
                        WHEN penilai.nama_lengkap IS NULL THEN jabatan_penilai.nama_jabatan
                        ELSE CONCAT(penilai.nama_lengkap, \' (\', jabatan_penilai.nama_jabatan, \')\')
                    END
                    ORDER BY jabatan_penilai.id_jabatan
                    SEPARATOR \', \'
                )
                FROM wewenang_penilaian wp
                JOIN jabatan jabatan_penilai ON jabatan_penilai.id_jabatan = wp.id_jabatan_penilai
                LEFT JOIN pegawai penilai
                    ON penilai.id_jabatan = jabatan_penilai.id_jabatan
                   AND penilai.status_aktif = \'Aktif\'
                WHERE wp.id_jabatan_dinilai = p.id_jabatan
            ), \'-\') AS nama_pewawancara,
            h.kompetensi, h.isi_penilaian, h.jawaban
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
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
$pewawancara = $interview['nama_pewawancara'];
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
    $properties = '<w:tcW w:w="' . $width . '" w:type="dxa"/><w:vAlign w:val="top"/>';
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
    return '<w:tc><w:tcPr>' . $properties . '</w:tcPr><w:p><w:pPr><w:spacing w:after="100"/></w:pPr><w:r>'
        . $runProperties . $textRuns . '</w:r></w:p></w:tc>';
};

$columnWidths = [600, 1600, 1800, 1900, 4206];
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

$logoPath = __DIR__ . '/../../assets/img/logo.png';
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
$letterheadLines = '';
foreach ([
    ['MAHKAMAH AGUNG REPUBLIK INDONESIA', 20, true],
    ['DIREKTORAT JENDERAL BADAN PERADILAN UMUM', 18, true],
    ['PENGADILAN TINGGI YOGYAKARTA', 22, true],
    ['PENGADILAN NEGERI YOGYAKARTA', 26, true],
    ['Jalan Kapas Nomor 10, Semaki, Kecamatan Umbulharjo', 14, false],
    ['Kota Yogyakarta, D.I. Yogyakarta 55166. www.pn-yogyakarta.go.id, pn.yogya@gmail.com', 14, false],
] as [$text, $size, $bold]) {
    $properties = '<w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="' . $size . '"/>'
        . ($bold ? '<w:b/>' : '') . '</w:rPr>';
    $letterheadLines .= '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="0"/></w:pPr>'
        . '<w:r>' . $properties . '<w:t>' . $xmlText($text) . '</w:t></w:r></w:p>';
}
$letterhead = '<w:tbl><w:tblPr><w:tblW w:w="10106" w:type="dxa"/><w:tblLayout w:type="fixed"/>'
    . '<w:tblCellMar><w:top w:w="0" w:type="dxa"/><w:left w:w="0" w:type="dxa"/>'
    . '<w:bottom w:w="100" w:type="dxa"/><w:right w:w="0" w:type="dxa"/></w:tblCellMar>'
    . '<w:tblBorders><w:top w:val="nil"/><w:left w:val="nil"/><w:right w:val="nil"/>'
    . '<w:bottom w:val="single" w:sz="12" w:space="1" w:color="000000"/>'
    . '<w:insideH w:val="nil"/><w:insideV w:val="nil"/></w:tblBorders></w:tblPr>'
    . '<w:tblGrid><w:gridCol w:w="1600"/><w:gridCol w:w="8506"/></w:tblGrid><w:tr>'
    . '<w:tc><w:tcPr><w:tcW w:w="1600" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr>'
    . '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r>' . $logoDrawing . '</w:r></w:p></w:tc>'
    . '<w:tc><w:tcPr><w:tcW w:w="8506" w:type="dxa"/><w:vAlign w:val="center"/></w:tcPr>'
    . $letterheadLines . '</w:tc></w:tr></w:tbl>';

$grid = '';
foreach ($columnWidths as $width) {
    $grid .= '<w:gridCol w:w="' . $width . '"/>';
}
$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
    . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'
    . ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"'
    . ' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"'
    . ' xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><w:body>'
    . '<w:p><w:pPr><w:spacing w:after="160"/></w:pPr><w:r><w:t></w:t></w:r></w:p>'
    . $letterhead
    . '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="240"/></w:pPr>'
    . '<w:r><w:rPr><w:b/><w:sz w:val="30"/></w:rPr><w:t>DATA WAWANCARA</w:t></w:r></w:p>'
    . $paragraph('Hari/Tanggal:', $tanggalLabel)
    . $paragraph('Yang mewawancarai:', $pewawancara)
    . $paragraph('Yang diwawancarai:', $diwawancarai)
    . '<w:p><w:pPr><w:spacing w:after="100"/></w:pPr></w:p>'
    . '<w:tbl><w:tblPr><w:tblW w:w="10106" w:type="dxa"/><w:tblLayout w:type="fixed"/>'
    . '<w:tblCellMar><w:top w:w="100" w:type="dxa"/><w:left w:w="110" w:type="dxa"/>'
    . '<w:bottom w:w="100" w:type="dxa"/><w:right w:w="110" w:type="dxa"/></w:tblCellMar>'
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
    . '<Default Extension="png" ContentType="image/png"/>'
    . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
    . '</Types>';
$relationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
    . '</Relationships>';
$documentRelationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/logo.png"/>'
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
$archiveWritten = $archive->addFromString('word/_rels/document.xml.rels', $documentRelationships) && $archiveWritten;
$archiveWritten = $archive->addFile($logoPath, 'word/media/logo.png') && $archiveWritten;
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
