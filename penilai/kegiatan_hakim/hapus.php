<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'Admin' && ($user['role'] !== 'Penilai' || $user['nama_jabatan'] !== 'Ketua')) {
    http_response_code(403);
    die('Akses kegiatan hakim hanya untuk Admin atau Ketua.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan.');
}

$id = (int) ($_POST['id'] ?? 0);
$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    "SELECT kh.file_bukti FROM kegiatan_hakim kh
     JOIN pegawai p ON kh.id_pegawai = p.id_pegawai
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE kh.id_kegiatan = ? AND j.nama_jabatan LIKE '%Hakim%'"
);
$stmt->execute([$id]);
$filePath = $stmt->fetchColumn();

if ($filePath === false) {
    setFlash('error', 'Data kegiatan hakim tidak ditemukan.');
} else {
    $pdo->prepare('DELETE FROM kegiatan_hakim WHERE id_kegiatan = ?')->execute([$id]);
    if ($filePath) {
        $file = __DIR__ . '/../../' . ltrim($filePath, '/');
        if (is_file($file)) {
            unlink($file);
        }
    }
    setFlash('success', 'Kegiatan hakim berhasil dihapus.');
}

header('Location: index.php');
exit;