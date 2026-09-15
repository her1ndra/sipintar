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
        'username'     => $_SESSION['username'] ?? null,
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
