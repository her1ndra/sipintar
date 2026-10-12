<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
requirePost();

$id = (int) ($_POST['id'] ?? 0);
$pdo = Database::getConnection();
$stmt = $pdo->prepare('SELECT id_pegawai, file_sertifikat FROM sertifikat_pegawai WHERE id_sertifikat = ?');
$stmt->execute([$id]);
$sertifikat = $stmt->fetch();
$redirect = 'index.php';

if ($sertifikat) {
    $redirect .= '?id_pegawai=' . (int) $sertifikat['id_pegawai'];
    $pdo->prepare('DELETE FROM sertifikat_pegawai WHERE id_sertifikat = ?')->execute([$id]);
    hapusFileUpload($sertifikat['file_sertifikat']);
    auditLog('hapus_sertifikat', 'Hapus sertifikat #' . $id);
    setFlash('success', 'Sertifikat berhasil dihapus.');
}

header('Location: ' . $redirect);
exit;
