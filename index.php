<?php
require_once __DIR__ . '/config/config.php';

if (empty($_SESSION['id_user'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}
header('Location: ' . BASE_URL . ($_SESSION['role'] === 'Admin' ? '/admin/dashboard.php' : '/penilai/dashboard.php'));
exit;
