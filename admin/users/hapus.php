<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

$idUser = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($idUser <= 0) {
    setFlash('error', 'Akun tidak valid.');
    header('Location: index.php');
    exit;
}

$pdo = Database::getConnection();
$accountStmt = $pdo->prepare(
    'SELECT u.id_user, u.nip, u.role,
            (SELECT COUNT(*) FROM kuesioner k WHERE k.id_user_pembuat = u.id_user) AS jumlah_kuesioner,
            (SELECT COUNT(*) FROM penilaian_kompetensi p WHERE p.id_user_penilai = u.id_user) AS jumlah_penilaian,
            (SELECT COUNT(*) FROM wawancara w WHERE w.id_user_penilai = u.id_user) AS jumlah_wawancara
     FROM users u
     WHERE u.id_user = ?'
);
$accountStmt->execute([$idUser]);
$account = $accountStmt->fetch();

if (!$account) {
    setFlash('error', 'Akun tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ((int) currentUser()['id_user'] === $idUser) {
    setFlash('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
    header('Location: index.php');
    exit;
}

if ($account['role'] === 'Admin') {
    $adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Admin'")->fetchColumn();
    if ($adminCount <= 1) {
        setFlash('error', 'Akun Admin terakhir tidak dapat dihapus.');
        header('Location: index.php');
        exit;
    }
}

if ((int) $account['jumlah_kuesioner'] > 0 || (int) $account['jumlah_penilaian'] > 0 || (int) $account['jumlah_wawancara'] > 0) {
    setFlash('error', 'Akun memiliki data kuesioner, penilaian, atau wawancara. Pindahkan atau hapus data terkait terlebih dahulu.');
    header('Location: index.php');
    exit;
}

try {
    $delete = $pdo->prepare('DELETE FROM users WHERE id_user = ?');
    $delete->execute([$idUser]);
    setFlash('success', 'Akun user berhasil dihapus.');
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        setFlash('error', 'Akun masih digunakan oleh data lain dan tidak dapat dihapus.');
    } else {
        throw $exception;
    }
}

header('Location: index.php');
exit;
