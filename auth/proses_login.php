<?php
require_once __DIR__ . '/../config/config.php';

$nip = trim($_POST['nip'] ?? '');
$password = $_POST['password'] ?? '';

if ($nip === '' || $password === '') {
    $_SESSION['login_error'] = 'NIP dan kata sandi wajib diisi.';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

if (!ctype_digit($nip)) {
    $_SESSION['login_error'] = 'NIP hanya boleh berisi angka.';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
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
    $_SESSION['login_error'] = 'NIP atau kata sandi salah.';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$_SESSION['id_user']      = $row['id_user'];
$_SESSION['nip']          = $row['nip'];
$_SESSION['role']         = $row['role'];
$_SESSION['id_pegawai']   = $row['id_pegawai'];
$_SESSION['nama']         = $row['nama_lengkap'];
$_SESSION['id_jabatan']   = $row['id_jabatan'];
$_SESSION['nama_jabatan'] = $row['nama_jabatan'];

$pdo->prepare('UPDATE users SET last_login = NOW() WHERE id_user = ?')->execute([$row['id_user']]);

header('Location: ' . BASE_URL . ($row['role'] === 'Admin' ? '/admin/dashboard.php' : '/penilai/dashboard.php'));
exit;
