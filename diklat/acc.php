<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$user = currentUser();
$role = $user['role'];
if (!in_array($role, ['Admin', 'Penilai'], true)) {
    http_response_code(403);
    die('Akses ditolak.');
}

$redirect = BASE_URL . '/' . ($role === 'Admin' ? 'admin' : 'penilai') . '/diklat/index.php';
requirePost($redirect);

$idKebutuhan = (int) ($_POST['id'] ?? 0);
$statusTujuan = $_POST['status'] ?? '';
if ($idKebutuhan <= 0) {
    setFlash('error', 'Data kebutuhan diklat tidak valid.');
    header('Location: ' . $redirect);
    exit;
}

$statusDiizinkan = [
    'Disetujui' => ['Diusulkan', 'Ditolak'],
    'Ditolak' => ['Diusulkan', 'Disetujui'],
    'Terlaksana' => ['Disetujui'],
    'Diusulkan' => ['Ditolak'],
];
if (!isset($statusDiizinkan[$statusTujuan])) {
    setFlash('error', 'Status kebutuhan diklat tidak valid.');
    header('Location: ' . $redirect);
    exit;
}

$pdo = Database::getConnection();
$statusLama = $statusDiizinkan[$statusTujuan];
$placeholderLama = implode(',', array_fill(0, count($statusLama), '?'));
if ($role === 'Admin') {
    $stmt = $pdo->prepare(
        "UPDATE kebutuhan_diklat
         SET status = ?
         WHERE id_kebutuhan = ? AND status IN ($placeholderLama)"
    );
    $stmt->execute(array_merge([$statusTujuan, $idKebutuhan], $statusLama));
} else {
    $stmt = $pdo->prepare(
        "UPDATE kebutuhan_diklat kd
         JOIN pegawai p ON p.id_pegawai = kd.id_pegawai
         JOIN wewenang_penilaian w
           ON w.id_jabatan_dinilai = p.id_jabatan
          AND w.id_jabatan_penilai = ?
         SET kd.status = ?
         WHERE kd.id_kebutuhan = ? AND kd.status IN ($placeholderLama)"
    );
    $stmt->execute(array_merge([(int) $user['id_jabatan'], $statusTujuan, $idKebutuhan], $statusLama));
}

if ($stmt->rowCount() > 0) {
    auditLog('ubah_status_kebutuhan_diklat', 'Status kebutuhan diklat #' . $idKebutuhan . ' menjadi ' . $statusTujuan);
    setFlash('success', 'Status kebutuhan diklat berhasil diubah menjadi ' . $statusTujuan . '.');
} else {
    setFlash('error', 'Usulan tidak ditemukan atau tidak dapat diubah ke status tersebut.');
}

header('Location: ' . $redirect);
exit;
