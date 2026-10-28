<?php
require_once __DIR__ . '/../config/config.php';
requireRole('Admin');
requirePost('wawancara/index.php');

$jenis = $_POST['jenis'] ?? '';
$idWawancara = filter_var($_POST['id_wawancara'] ?? null, FILTER_VALIDATE_INT);
$dibaca = $_POST['dibaca'] ?? null;

if (!in_array($jenis, ['wawancara', 'kuesioner'], true)
    || $idWawancara === false
    || $idWawancara < 1
    || !in_array($dibaca, ['0', '1'], true)) {
    setFlash('error', 'Status baca tidak valid.');
    header('Location: wawancara/index.php');
    exit;
}

$redirect = $jenis === 'kuesioner' ? 'kuesioner/index.php' : 'wawancara/index.php';
$pdo = Database::getConnection();
$statement = $pdo->prepare(
    'SELECT COUNT(*)
     FROM wawancara
     WHERE id_wawancara = ? AND ' . ($jenis === 'kuesioner' ? 'id_kuesioner IS NOT NULL' : 'id_kuesioner IS NULL')
);
$statement->execute([$idWawancara]);
if ((int) $statement->fetchColumn() !== 1) {
    setFlash('error', 'Data tidak ditemukan.');
    header('Location: ' . $redirect);
    exit;
}

$idUser = (int) $_SESSION['id_user'];
if ($dibaca === '1') {
    $pdo->prepare(
        'INSERT INTO status_baca_admin (id_wawancara, id_user_admin)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE dibaca_at = CURRENT_TIMESTAMP'
    )->execute([$idWawancara, $idUser]);
} else {
    $pdo->prepare(
        'DELETE FROM status_baca_admin
         WHERE id_wawancara = ? AND id_user_admin = ?'
    )->execute([$idWawancara, $idUser]);
}

auditLog('ubah_status_baca', $jenis . ' #' . $idWawancara . ($dibaca === '1' ? ' dibaca' : ' belum dibaca'));
setFlash('success', 'Status baca berhasil diperbarui.');
header('Location: ' . $redirect);
exit;
