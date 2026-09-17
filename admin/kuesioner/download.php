<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

$idKuesioner = (int) ($_GET['id'] ?? 0);
$format = $_GET['format'] ?? 'pdf';
$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    'SELECT k.judul_kuesioner, k.tahun_periode, j.nama_jabatan,
            q.nomor_urut, q.teks_pertanyaan
     FROM kuesioner k
     JOIN jabatan j ON j.id_jabatan = k.id_jabatan_dinilai
     JOIN pertanyaan_kuesioner q ON q.id_kuesioner = k.id_kuesioner
     WHERE k.id_kuesioner = ?
     ORDER BY q.nomor_urut'
);
$stmt->execute([$idKuesioner]);
$rows = $stmt->fetchAll();

if (!$rows) {
    setFlash('error', 'Kuesioner atau pertanyaan tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($format === 'pdf') {
    $pdfText = static function (string $value): string {
        return preg_replace('/[^\x20-\x7E]/', '?', $value);
    };
    $lines = [
        'KUESIONER',
        'Judul: ' . $pdfText($rows[0]['judul_kuesioner']),
        'Tahun periode: ' . $rows[0]['tahun_periode'],
        'Kompetensi / jabatan: ' . $pdfText($rows[0]['nama_jabatan']),
        '',
        'No.  Pertanyaan',
    ];
    foreach ($rows as $row) {
        $question = wordwrap($pdfText($row['teks_pertanyaan']), 88, "\n", true);
        $questionLines = explode("\n", $question);
        $lines[] = $row['nomor_urut'] . '.    ' . array_shift($questionLines);
        foreach ($questionLines as $line) {
            $lines[] = '      ' . $line;
        }
    }
    $pages = array_chunk($lines, 45);
    $objects = [
        '<< /Type /Catalog /Pages 2 0 R >>',
        '',
    ];
    $pageReferences = [];
    foreach ($pages as $pageIndex => $pageLines) {
        $pageObject = 3 + ($pageIndex * 2);
        $contentObject = $pageObject + 1;
        $pageReferences[] = $pageObject . ' 0 R';
        $stream = "BT\n/F1 11 Tf\n50 790 Td\n";
        foreach ($pageLines as $line) {
            $stream .= '(' . str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line) . ") Tj\n0 -16 Td\n";
        }
        $stream .= "ET";
        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 ' . (3 + (count($pages) * 2)) . ' 0 R >> >> /Contents ' . $contentObject . ' 0 R >>';
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
    header('Content-Disposition: attachment; filename="kuesioner-' . $idKuesioner . '.pdf"');
    echo $pdf;
    exit;
}
$filename = 'kuesioner-' . $idKuesioner . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo "\xEF\xBB\xBF";
$output = fopen('php://output', 'wb');
fputcsv($output, ['Judul kuesioner', $rows[0]['judul_kuesioner']], ';');
fputcsv($output, ['Tahun periode', $rows[0]['tahun_periode']], ';');
fputcsv($output, ['Kompetensi / jabatan', $rows[0]['nama_jabatan']], ';');
fputcsv($output, []);
fputcsv($output, ['No', 'Pertanyaan'], ';');
foreach ($rows as $row) {
    fputcsv($output, [$row['nomor_urut'], $row['teks_pertanyaan']], ';');
}
fclose($output);
exit;
