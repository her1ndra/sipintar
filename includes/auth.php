<?php
/**
 * Middleware otorisasi sederhana berbasis session.
 * Role aplikasi hanya dua: Admin dan Penilai.
 */

function requireLogin(): void
{
    if (empty($_SESSION['id_user'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }

    // Sesi berakhir bila tidak aktif terlalu lama.
    $last = (int) ($_SESSION['last_activity'] ?? 0);
    if ($last > 0 && (time() - $last) > SESSION_IDLE_SECONDS) {
        logoutUser();
        session_start();
        $_SESSION['login_error'] = 'Sesi berakhir karena tidak aktif. Silakan masuk kembali.';
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
    $_SESSION['last_activity'] = time();

    // Akun yang dinonaktifkan / diubah role-nya langsung kehilangan akses.
    $stmt = Database::getConnection()->prepare('SELECT role, status_aktif FROM users WHERE id_user = ?');
    $stmt->execute([(int) $_SESSION['id_user']]);
    $row = $stmt->fetch();
    if (!$row || (int) $row['status_aktif'] !== 1 || $row['role'] !== ($_SESSION['role'] ?? null)) {
        logoutUser();
        session_start();
        $_SESSION['login_error'] = 'Akun Anda tidak aktif atau telah berubah. Silakan masuk kembali.';
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

function requireRole(string $role): void
{
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        die('Akses ditolak. Halaman ini khusus untuk role ' . htmlspecialchars($role) . '.');
    }
}

function currentUser(): array
{
    return [
        'id_user'      => $_SESSION['id_user'] ?? null,
        'nip'          => $_SESSION['nip'] ?? null,
        'role'         => $_SESSION['role'] ?? null,
        'id_pegawai'   => $_SESSION['id_pegawai'] ?? null,
        'nama'         => $_SESSION['nama'] ?? null,
        'id_jabatan'   => $_SESSION['id_jabatan'] ?? null,
        'nama_jabatan' => $_SESSION['nama_jabatan'] ?? null,
    ];
}

/**
 * Mengambil daftar id_jabatan yang berhak dinilai oleh sebuah jabatan penilai,
 * berdasarkan tabel wewenang_penilaian.
 */
function getJabatanWewenang(PDO $pdo, int $idJabatanPenilai): array
{
    $stmt = $pdo->prepare(
        'SELECT id_jabatan_dinilai FROM wewenang_penilaian WHERE id_jabatan_penilai = ?'
    );
    $stmt->execute([$idJabatanPenilai]);
    return array_column($stmt->fetchAll(), 'id_jabatan_dinilai');
}

/**
 * Admin, atau Penilai dengan jabatan Ketua / Wakil Ketua.
 */
function canAccessKegiatanHakim(?array $user = null): bool
{
    $user = $user ?? currentUser();
    if (($user['role'] ?? null) === 'Admin') {
        return true;
    }
    return ($user['role'] ?? null) === 'Penilai'
        && in_array($user['nama_jabatan'] ?? '', JABATAN_KEGIATAN_HAKIM, true);
}

function requireKegiatanHakimAccess(): void
{
    requireLogin();
    if (!canAccessKegiatanHakim()) {
        http_response_code(403);
        die('Akses kegiatan hakim hanya untuk Admin, Ketua, atau Wakil Ketua.');
    }
}

/**
 * Apakah penilai dengan jabatan tertentu berwenang menilai pegawai ini?
 */
function penilaiBerwenangAtasPegawai(PDO $pdo, int $idJabatanPenilai, int $idPegawai): bool
{
    $stmt = $pdo->prepare(
        'SELECT 1 FROM pegawai p
         JOIN wewenang_penilaian w ON w.id_jabatan_dinilai = p.id_jabatan
         WHERE p.id_pegawai = ? AND w.id_jabatan_penilai = ? LIMIT 1'
    );
    $stmt->execute([$idPegawai, $idJabatanPenilai]);
    return (bool) $stmt->fetchColumn();
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
