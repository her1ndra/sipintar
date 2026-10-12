<?php
/**
 * Salin file ini menjadi config/local.php lalu isi sesuai server.
 * config/local.php TIDAK boleh di-commit (sudah ada di .gitignore).
 * Semua nilai juga bisa diberikan lewat environment variable SIPINTAR_<NAMA>,
 * contoh: SIPINTAR_DB_PASS.
 */
return [
    'env'          => 'production',   // 'development' hanya untuk komputer pengembang
    'db_host'      => 'localhost',
    'db_port'      => '3306',
    'db_name'      => 'si_pintar',
    'db_user'      => 'si_pintar_app', // gunakan user khusus, BUKAN root
    'db_pass'      => 'GANTI_DENGAN_PASSWORD_KUAT',
    'base_url'     => 'sipintar',              // mis. '/si-pintar' bila di subfolder
    'session_idle' => 7200,            // detik
    'trust_proxy'  => false,           // true bila di belakang reverse proxy HTTPS
];
