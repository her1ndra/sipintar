<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

$id = (int) ($_GET['id'] ?? 0);
$pdo = Database::getConnection();
$stmt = $pdo->prepare('SELECT file_sertifikat FROM sertifikat_pegawai WHERE id_sertifikat = ?');
$stmt->execute([$id]);
$sertifikat = $stmt->fetch();

if ($sertifikat) {
    $delete = $pdo->prepare('DELETE FROM sertifikat_pegawai WHERE id_sertifikat = ?');
    $delete->execute([$id]);

    if (!empty($sertifikat['file_sertifikat'])) {
        $filePath = __DIR__ . '/../../' . $sertifikat['file_sertifikat'];
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    setFlash('success', 'Sertifikat berhasil dihapus.');
}

header('Location: index.php');
exit;
