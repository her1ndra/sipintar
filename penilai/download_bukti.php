<?php
require_once __DIR__ . '/../config/config.php';
requireRole('Penilai');
require_once __DIR__ . '/../includes/penilaian_bukti_word.php';

$idWawancara = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$jenis = (string) ($_GET['jenis'] ?? '');
if (!$idWawancara || $idWawancara < 1 || !in_array($jenis, ['kuesioner', 'wawancara'], true)) {
    http_response_code(400);
    exit('Data unduhan tidak valid.');
}

$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
downloadBuktiPenilaianWord($pdo, $idWawancara, $jenis, $jabatanIds);
