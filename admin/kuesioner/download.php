<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

$idKuesioner = (int) ($_GET['id'] ?? 0);
$format = $_GET['format'] ?? 'pdf';
$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    'SELECT k.judul_kuesioner, k.tahun_periode, k.kompetensi,
            q.nomor_urut, q.teks_pertanyaan
     FROM kuesioner k
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
    require_once __DIR__ . '/../../includes/kuesioner_pdf.php';
    downloadKuesionerPdf($idKuesioner, $rows, currentUser());
}
$filename = 'kuesioner-' . $idKuesioner . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo "\xEF\xBB\xBF";
$output = fopen('php://output', 'wb');
fputcsv($output, ['Judul kuesioner', $rows[0]['judul_kuesioner']], ';');
fputcsv($output, ['Tahun periode', $rows[0]['tahun_periode']], ';');
fputcsv($output, ['Kompetensi / jabatan', $rows[0]['kompetensi']], ';');
fputcsv($output, []);
fputcsv($output, ['No', 'Pertanyaan'], ';');
foreach ($rows as $row) {
    fputcsv($output, [$row['nomor_urut'], $row['teks_pertanyaan']], ';');
}
fclose($output);
exit;
