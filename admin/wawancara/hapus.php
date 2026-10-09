<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
require_once __DIR__ . '/../../includes/penilaian_delete.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

$idWawancara = (int) ($_POST['id'] ?? 0);
$pdo = Database::getConnection();
$statement = $pdo->prepare('SELECT id_wawancara FROM wawancara WHERE id_wawancara = ? AND id_kuesioner IS NULL');
$statement->execute([$idWawancara]);
if (!$statement->fetchColumn()) {
    setFlash('error', 'Data wawancara tidak ditemukan.');
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
setFlash('success', 'Data wawancara beserta hasil dan bukti terkait berhasil dihapus.');
header('Location: index.php');
exit;