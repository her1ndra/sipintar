<?php
/**
 * Bootstrap aplikasi: konfigurasi, error handling, session, keamanan dasar.
 * Nilai konfigurasi dibaca dari (urutan prioritas):
 *   1. environment variable  SIPINTAR_<NAMA>   (mis. SIPINTAR_DB_PASS)
 *   2. config/local.php      (tidak di-commit; lihat local.example.php)
 *   3. nilai default pada pemanggilan appConfig()
 */

$GLOBALS['__sipintar_local'] = [];
$__localFile = __DIR__ . '/local.php';
if (is_file($__localFile)) {
    $__local = require $__localFile;
    if (is_array($__local)) {
        $GLOBALS['__sipintar_local'] = $__local;
    }
}
unset($__localFile, $__local);

function appConfig(string $key, $default = null)
{
    $env = getenv('SIPINTAR_' . strtoupper($key));
    if ($env !== false && $env !== '') {
        return $env;
    }
    return $GLOBALS['__sipintar_local'][$key] ?? $default;
}

// Default aman: production. Set 'env' => 'development' di config/local.php saat pengembangan.
define('APP_ENV', appConfig('env', 'production'));
define('APP_DEBUG', APP_ENV === 'development');

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('display_startup_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

date_default_timezone_set('Asia/Jakarta');

// BASE_URL harus berupa path dari root web, mis. '/sipintar'; kosong untuk root.
$__baseUrl = trim((string) appConfig('base_url', 'sipintar'), '/');
define('BASE_URL', $__baseUrl === '' ? '' : '/' . $__baseUrl);
unset($__baseUrl);
define('APP_NAME', 'SI PINTAR - Pengembangan Kompetensi Aparatur');

// Jabatan yang boleh mengakses modul Kegiatan Hakim (selain Admin).
const JABATAN_KEGIATAN_HAKIM = ['Ketua', 'Wakil Ketua'];

// Batas waktu tidak aktif (detik) sebelum sesi berakhir. Default 2 jam.
define('SESSION_IDLE_SECONDS', (int) appConfig('session_idle', 7200));

// --- Session yang diperkeras -------------------------------------------------
$__https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (appConfig('trust_proxy', false) && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
define('APP_HTTPS', $__https);

if (session_status() === PHP_SESSION_NONE) {
    session_name('SIPINTARSESSID');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => BASE_URL === '' ? '/' : BASE_URL . '/',
        'secure'   => $__https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
unset($__https);

if (PHP_SAPI !== 'cli') {
    header_remove('X-Powered-By');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    if (APP_HTTPS) {
        header('Strict-Transport-Security: max-age=15552000');
    }
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';

// Semua request POST wajib membawa token CSRF yang valid.
csrfEnforce();
