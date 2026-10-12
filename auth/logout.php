<?php
require_once __DIR__ . '/../config/config.php';

// Logout hanya lewat POST + token CSRF (mencegah logout paksa lewat link/gambar dari situs lain).
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

if (!empty($_SESSION['id_user'])) {
    auditLog('logout');
}
logoutUser();
header('Location: ' . BASE_URL . '/auth/login.php');
exit;
