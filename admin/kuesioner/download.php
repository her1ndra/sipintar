<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

$idKuesioner = (int) ($_GET['id'] ?? 0);
$format = strtolower((string) ($_GET['format'] ?? 'pdf'));
$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    'SELECT k.judul_kuesioner, k.tahun_periode, k.kompetensi,
            p.nama_lengkap AS nama_pegawai, j.nama_jabatan,
            COALESCE((
                SELECT GROUP_CONCAT(
                    DISTINCT jabatan_penilai.nama_jabatan
                    ORDER BY jabatan_penilai.id_jabatan
                    SEPARATOR \', \'
                )
                FROM wewenang_penilaian wp
                JOIN jabatan jabatan_penilai ON jabatan_penilai.id_jabatan = wp.id_jabatan_penilai
                WHERE wp.id_jabatan_dinilai = p.id_jabatan
            ), \'\') AS jabatan_penilai,
            COALESCE((
                SELECT GROUP_CONCAT(
                    DISTINCT penilai.nama_lengkap
                    ORDER BY penilai.nama_lengkap
                    SEPARATOR \', \'
                )
                FROM wewenang_penilaian wp
                JOIN pegawai penilai
                    ON penilai.id_jabatan = wp.id_jabatan_penilai
                   AND penilai.status_aktif = \'Aktif\'
                WHERE wp.id_jabatan_dinilai = p.id_jabatan
            ), \'\') AS nama_penilai,
            q.nomor_urut, q.teks_pertanyaan
     FROM kuesioner k
     JOIN pegawai p ON p.id_pegawai = k.id_jabatan_dinilai
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
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
    downloadKuesionerPdf($idKuesioner, $rows, [
        'nama' => $rows[0]['nama_penilai'],
        'nama_jabatan' => $rows[0]['jabatan_penilai'],
    ]);
}
if ($format === 'word') {
    require_once __DIR__ . '/../../includes/kuesioner_word.php';
    downloadKuesionerWord($idKuesioner, $rows, [
        'nama' => $rows[0]['nama_penilai'],
        'nama_jabatan' => $rows[0]['jabatan_penilai'],
    ]);
}
http_response_code(400);
exit('Format unduhan kuesioner tidak valid.');
