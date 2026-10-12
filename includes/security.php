<?php
/**
 * Utilitas keamanan: escape output, CSRF, pembatasan percobaan login,
 * manajemen sesi login/logout, dan audit log.
 */

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// --- CSRF --------------------------------------------------------------------

function csrfToken(): string
{
    if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrfField(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrfToken()) . '">';
}

function csrfEnforce(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }
    $sent  = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $known = $_SESSION['_csrf'] ?? '';
    if (is_string($sent) && is_string($known) && $known !== '' && hash_equals($known, $sent)) {
        return;
    }
    http_response_code(419);
    $login = BASE_URL . '/auth/login.php';
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Sesi formulir tidak valid</title></head>'
        . '<body style="font-family:sans-serif;max-width:560px;margin:80px auto;padding:0 16px">'
        . '<h2>Permintaan ditolak</h2>'
        . '<p>Token keamanan formulir tidak valid atau sudah kedaluwarsa. Kemungkinan penyebab: halaman dibuka terlalu lama, '
        . 'sesi sudah berakhir, atau ukuran file yang diunggah melebihi batas server.</p>'
        . '<p><a href="javascript:history.back()">Kembali</a> &middot; <a href="' . e($login) . '">Ke halaman masuk</a></p>'
        . '</body></html>';
    exit;
}

function requirePost(string $redirect = 'index.php'): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        header('Location: ' . $redirect);
        exit;
    }
}

// --- Sesi login / logout -----------------------------------------------------

function clientIp(): string
{
    if (appConfig('trust_proxy', false) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $first = trim(explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        if (filter_var($first, FILTER_VALIDATE_IP)) {
            return $first;
        }
    }
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function loginUser(array $row): void
{
    // Cegah session fixation: ID sesi baru setelah autentikasi berhasil.
    session_regenerate_id(true);
    $_SESSION = [];
    $_SESSION['id_user']       = (int) $row['id_user'];
    $_SESSION['nip']           = $row['nip'];
    $_SESSION['role']          = $row['role'];
    $_SESSION['id_pegawai']    = $row['id_pegawai'];
    $_SESSION['nama']          = $row['nama_lengkap'];
    $_SESSION['id_jabatan']    = $row['id_jabatan'];
    $_SESSION['nama_jabatan']  = $row['nama_jabatan'];
    $_SESSION['login_time']    = time();
    $_SESSION['last_activity'] = time();
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'] ?? 'Lax',
        ]);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

// --- Audit log (tabel audit_log) ---------------------------------------------

function auditLog(string $aksi, ?string $keterangan = null, ?string $nipOverride = null): void
{
    try {
        $pdo = Database::getConnection();
        $pdo->prepare('INSERT INTO audit_log (id_user, nip, aksi, keterangan, ip) VALUES (?, ?, ?, ?, ?)')
            ->execute([
                $_SESSION['id_user'] ?? null,
                $nipOverride ?? ($_SESSION['nip'] ?? null),
                substr($aksi, 0, 50),
                $keterangan === null ? null : mb_substr($keterangan, 0, 255),
                clientIp(),
            ]);
    } catch (Throwable $e) {
        error_log('SI PINTAR: audit_log gagal ditulis: ' . $e->getMessage());
    }
}

// --- URL file upload (selalu lewat file.php yang mengecek login & hak akses) --

function fileUrl(?string $path): ?string
{
    if ($path === null || $path === '') {
        return null;
    }
    return BASE_URL . '/file.php?f=' . rawurlencode(ltrim(str_replace('\\', '/', $path), '/'));
}

// --- Hapus data (POST + CSRF + audit + tangani relasi FK) ---------------------

function hapusDenganAman(string $sql, int $id, string $pesanSukses, string $aksi, string $keterangan, string $redirect = 'index.php'): void
{
    requirePost($redirect);
    if ($id <= 0) {
        setFlash('error', 'Data tidak valid.');
        header('Location: ' . $redirect);
        exit;
    }
    try {
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            auditLog($aksi, $keterangan . ' #' . $id);
            setFlash('success', $pesanSukses);
        } else {
            setFlash('error', 'Data tidak ditemukan.');
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            setFlash('error', 'Data masih dipakai oleh data lain sehingga tidak dapat dihapus.');
        } else {
            throw $e;
        }
    }
    header('Location: ' . $redirect);
    exit;
}

/**
 * Hapus file upload dengan aman: hanya path yang berada di folder upload yang dikenal.
 */
function hapusFileUpload(?string $path): void
{
    if ($path === null || $path === '') {
        return;
    }
    $rel = ltrim(str_replace('\\', '/', $path), '/');
    $aman = '#^(?:assets/uploads/(?:sertifikat|kegiatan_hakim|kuesioner|wawancara)/[A-Za-z0-9._-]+'
          . '|uploads/sertifikat/[0-9]+/[A-Za-z0-9._-]+)$#D';
    if (!preg_match($aman, $rel) || strpos($rel, '..') !== false) {
        return;
    }
    $full = realpath(__DIR__ . '/../' . $rel);
    if ($full !== false && is_file($full)) {
        @unlink($full);
    }
}
