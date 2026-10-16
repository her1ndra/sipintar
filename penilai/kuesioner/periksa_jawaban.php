<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');

header('Content-Type: application/json; charset=utf-8');

$idWawancara = filter_input(INPUT_POST, 'id_wawancara', FILTER_VALIDATE_INT);
if (!$idWawancara || $idWawancara < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Data kuesioner tidak valid.']);
    exit;
}

$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = array_map('intval', getJabatanWewenang($pdo, (int) $user['id_jabatan']));
if (!$jabatanIds) {
    http_response_code(404);
    echo json_encode(['error' => 'Kuesioner tidak ditemukan.']);
    exit;
}

$placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
$accessStmt = $pdo->prepare(
    "SELECT 1
     FROM wawancara w
     JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     WHERE w.id_wawancara = ?
       AND (
           p.id_jabatan IN ($placeholders)
           OR k.id_jabatan_dinilai IN ($placeholders)
       )
     LIMIT 1"
);
$accessStmt->execute(array_merge([$idWawancara], $jabatanIds, $jabatanIds));
if (!$accessStmt->fetchColumn()) {
    http_response_code(404);
    echo json_encode(['error' => 'Kuesioner tidak ditemukan.']);
    exit;
}

$resultStmt = $pdo->prepare('SELECT id_hasil FROM hasil_kuesioner WHERE id_wawancara = ?');
$resultStmt->execute([$idWawancara]);
if (!$resultStmt->fetchColumn()) {
    http_response_code(404);
    echo json_encode(['error' => 'Jawaban kuesioner belum tersedia.']);
    exit;
}

$markReviewed = $pdo->prepare(
    'INSERT INTO pemeriksaan_jawaban_kuesioner (id_hasil, id_user_penilai, jawaban_hash)
     SELECT id_hasil, ?, SHA2(CONCAT(COALESCE(daftar_nilai, \'\'), \'|\', COALESCE(daftar_jawaban, \'\'), \'|\', COALESCE(file_bukti, \'\')), 256)
     FROM hasil_kuesioner
     WHERE id_wawancara = ?
     ON DUPLICATE KEY UPDATE
        jawaban_hash = VALUES(jawaban_hash),
        diperiksa_at = CURRENT_TIMESTAMP'
);
$markReviewed->execute([(int) $user['id_user'], $idWawancara]);

echo json_encode([
    'nama_penilai' => $user['nama'] ?: $user['nip'],
    'diperiksa_at' => date('d-m-Y H:i'),
], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
