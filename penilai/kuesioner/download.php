<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');

$idKuesioner = (int) ($_GET['id'] ?? 0);
$format = strtolower((string) ($_GET['format'] ?? 'pdf'));
$user = currentUser();
$pdo = Database::getConnection();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);

$placeholders = $jabatanIds ? implode(',', array_fill(0, count($jabatanIds), '?')) : 'NULL';
$stmt = $pdo->prepare(
    "SELECT k.judul_kuesioner, k.tahun_periode, k.kompetensi,
            p_target.nama_lengkap AS nama_pegawai, j_target.nama_jabatan,
            q.nomor_urut, q.teks_pertanyaan
     FROM kuesioner k
    JOIN pertanyaan_kuesioner q ON q.id_kuesioner = k.id_kuesioner
    LEFT JOIN pegawai p_target ON p_target.id_pegawai = k.id_jabatan_dinilai
    LEFT JOIN jabatan j_target ON j_target.id_jabatan = p_target.id_jabatan
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
if ($format === 'word') {
    require_once __DIR__ . '/../../includes/kuesioner_word.php';
    downloadKuesionerWord($idKuesioner, $rows, $user);
}
http_response_code(400);
exit('Format unduhan kuesioner tidak valid.');
