<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');

$idKuesioner = (int) ($_GET['id'] ?? 0);
$format = $_GET['format'] ?? 'pdf';
$user = currentUser();
$pdo = Database::getConnection();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);

$placeholders = $jabatanIds ? implode(',', array_fill(0, count($jabatanIds), '?')) : 'NULL';
$stmt = $pdo->prepare(
    "SELECT k.judul_kuesioner, k.tahun_periode, k.kompetensi,
            q.nomor_urut, q.teks_pertanyaan
     FROM kuesioner k
    JOIN pertanyaan_kuesioner q ON q.id_kuesioner = k.id_kuesioner
    LEFT JOIN pegawai p_target ON p_target.id_pegawai = k.id_jabatan_dinilai
    WHERE k.id_kuesioner = ?
      AND (p_target.id_jabatan IN ($placeholders) OR k.id_jabatan_dinilai IN ($placeholders))
    ORDER BY q.nomor_urut"
);
$stmt->execute(array_merge([$idKuesioner], $jabatanIds, $jabatanIds));
$rows = $stmt->fetchAll();

if (!$rows) {
    setFlash('error', 'Kuesioner atau pertanyaan tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($format === 'pdf') {
    require_once __DIR__ . '/../../includes/kuesioner_pdf.php';
    downloadKuesionerPdf($idKuesioner, $rows, $user);
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
