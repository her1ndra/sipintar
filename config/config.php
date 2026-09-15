<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

// Ubah jika aplikasi diletakkan di subfolder, misal '/si-pintar'
define('BASE_URL', '');
define('APP_NAME', 'SI PINTAR - Pengembangan Kompetensi Aparatur');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/auth.php';
