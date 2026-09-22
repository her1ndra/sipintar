<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('DELETE FROM kebutuhan_diklat WHERE id_kebutuhan = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Kebutuhan diklat berhasil dihapus.');
}
header('Location: index.php');
exit;
