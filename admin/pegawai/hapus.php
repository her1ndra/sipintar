<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$id = (int) ($_GET['id'] ?? 0);
$pdo->prepare('DELETE FROM pegawai WHERE id_pegawai = ?')->execute([$id]);
setFlash('success', 'Data pegawai berhasil dihapus.');
header('Location: index.php');
exit;
