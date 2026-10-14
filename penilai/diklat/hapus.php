<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
requirePost('index.php');

$idKebutuhan = (int) ($_POST['id'] ?? 0);
if ($idKebutuhan <= 0) {
    setFlash('error', 'Data kebutuhan diklat tidak valid.');
    header('Location: index.php');
    exit;
}

$user = currentUser();
$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    'DELETE kd FROM kebutuhan_diklat kd
     JOIN pegawai p ON p.id_pegawai = kd.id_pegawai
     JOIN wewenang_penilaian w
       ON w.id_jabatan_dinilai = p.id_jabatan
      AND w.id_jabatan_penilai = ?
     WHERE kd.id_kebutuhan = ?'
);
$stmt->execute([(int) $user['id_jabatan'], $idKebutuhan]);

if ($stmt->rowCount() > 0) {
    auditLog('hapus_kebutuhan_diklat', 'Hapus kebutuhan diklat #' . $idKebutuhan);
    setFlash('success', 'Kebutuhan diklat berhasil dihapus.');
} else {
    setFlash('error', 'Kebutuhan diklat tidak ditemukan atau di luar wewenang Anda.');
}

header('Location: index.php');
exit;
