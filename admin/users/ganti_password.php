<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$idUser = (int) ($_GET['id'] ?? $_POST['id_user'] ?? 0);

if ($idUser <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT u.id_user, u.nip, u.role, p.nama_lengkap
     FROM users u
     LEFT JOIN pegawai p ON u.id_pegawai = p.id_pegawai
     WHERE u.id_user = ?'
);
$stmt->execute([$idUser]);
$akun = $stmt->fetch();

if (!$akun) {
    setFlash('error', 'Akun tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $konfirmasiPassword = $_POST['konfirmasi_password'] ?? '';

    if ($password === '' || $konfirmasiPassword === '') {
        setFlash('error', 'Password baru dan konfirmasi password wajib diisi.');
    } elseif ($password !== $konfirmasiPassword) {
        setFlash('error', 'Konfirmasi password tidak sama.');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare('UPDATE users SET password = ? WHERE id_user = ?');
        $update->execute([$hash, $idUser]);
        setFlash('success', 'Password akun berhasil diubah.');
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Ganti password akun';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <input type="hidden" name="id_user" value="<?= (int) $akun['id_user'] ?>">
  <div class="mb-3">
    <label class="form-label">NIP</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($akun['nip']) ?>" readonly>
  </div>
  <div class="mb-3">
    <label class="form-label">Nama pegawai</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($akun['nama_lengkap'] ?? '-') ?>" readonly>
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">Password baru</label>
    <input type="password" name="password" id="password" class="form-control" required autofocus>
  </div>
  <div class="mb-3">
    <label class="form-label" for="konfirmasi_password">Konfirmasi password baru</label>
    <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Simpan password</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
