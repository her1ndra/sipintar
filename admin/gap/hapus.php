<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('DELETE FROM analisis_kesenjangan_kompetensi WHERE id_kesenjangan = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Analisis kesenjangan berhasil dihapus.');
}

header('Location: index.php');
exit;
