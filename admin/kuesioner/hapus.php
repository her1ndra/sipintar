<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
require_once __DIR__ . '/../../includes/penilaian_delete.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

$idKuesioner = (int) ($_POST['id'] ?? 0);
$pdo = Database::getConnection();
$statement = $pdo->prepare('SELECT id_kuesioner FROM kuesioner WHERE id_kuesioner = ?');
$statement->execute([$idKuesioner]);
if (!$statement->fetchColumn()) {
    setFlash('error', 'Kuesioner tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$statement = $pdo->prepare('SELECT id_wawancara FROM wawancara WHERE id_kuesioner = ?');
$statement->execute([$idKuesioner]);
$sessionIds = array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
$filePaths = [];
$pdo->beginTransaction();
try {
    foreach ($sessionIds as $idWawancara) {
        $filePaths = array_merge($filePaths, hapusSesiPenilaian($pdo, $idWawancara));
    }
    $pdo->prepare('DELETE FROM kuesioner WHERE id_kuesioner = ?')->execute([$idKuesioner]);
    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    throw $exception;
}

hapusFileBuktiPenilaian($filePaths);
setFlash('success', 'Kuesioner beserta sesi dan hasil terkait berhasil dihapus.');
header('Location: index.php');
exit;