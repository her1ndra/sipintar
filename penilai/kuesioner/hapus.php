<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
require_once __DIR__ . '/../../includes/penilaian_delete.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

$idWawancara = (int) ($_POST['id'] ?? 0);
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
if (!$jabatanIds) {
    http_response_code(403);
    exit('Tidak memiliki wewenang untuk menghapus sesi kuesioner.');
}
$placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
$statement = $pdo->prepare(
    "SELECT w.id_wawancara
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     LEFT JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
     LEFT JOIN pegawai p_target ON p_target.id_pegawai = k.id_jabatan_dinilai
     WHERE w.id_wawancara = ? AND w.id_kuesioner IS NOT NULL
       AND (p.id_jabatan IN ($placeholders) OR p_target.id_jabatan IN ($placeholders)
            OR k.id_jabatan_dinilai IN ($placeholders))"
);
$statement->execute(array_merge([$idWawancara], $jabatanIds, $jabatanIds, $jabatanIds));
if (!$statement->fetchColumn()) {
    setFlash('error', 'Sesi kuesioner tidak ditemukan atau di luar wewenang Anda.');
    header('Location: index.php');
    exit;
}

$pdo->beginTransaction();
try {
    $filePaths = hapusSesiPenilaian($pdo, $idWawancara);
    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $exception;
}

hapusFileBuktiPenilaian($filePaths);
setFlash('success', 'Sesi kuesioner beserta hasil dan bukti terkait berhasil dihapus.');
header('Location: index.php');
exit;