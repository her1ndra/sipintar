<?php
require_once __DIR__ . '/../config/config.php';

function loginGagal(string $pesan): void
{
    $_SESSION['login_error'] = $pesan;
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$nip = trim($_POST['nip'] ?? '');
$password = $_POST['password'] ?? '';

if ($nip === '' || $password === '') {
    loginGagal('NIP dan kata sandi wajib diisi.');
}

if (!ctype_digit($nip) || strlen($nip) > 50) {
    loginGagal('NIP hanya boleh berisi angka.');
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare(
    "SELECT u.id_user, u.nip, u.password, u.role, u.id_pegawai,
            p.nama_lengkap, p.id_jabatan, j.nama_jabatan
     FROM users u
     LEFT JOIN pegawai p ON u.id_pegawai = p.id_pegawai
     LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE u.nip = ? AND u.status_aktif = 1"
);
$stmt->execute([$nip]);
$row = $stmt->fetch();

if (!$row || !password_verify($password, $row['password'])) {
    loginGagal('NIP atau kata sandi salah.');
}

if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
    $pdo->prepare('UPDATE users SET password = ? WHERE id_user = ?')
        ->execute([password_hash($password, PASSWORD_DEFAULT), $row['id_user']]);
}

loginUser($row);

$pdo->prepare('UPDATE users SET last_login = NOW() WHERE id_user = ?')->execute([$row['id_user']]);
auditLog('login', 'Login berhasil');

header('Location: ' . BASE_URL . ($row['role'] === 'Admin' ? '/admin/dashboard.php' : '/penilai/dashboard.php'));
exit;
