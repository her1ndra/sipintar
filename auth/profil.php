<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = Database::getConnection();
$idUser = (int) currentUser()['id_user'];
$stmt = $pdo->prepare(
    'SELECT u.id_user, u.nip, u.password, u.role, u.id_pegawai, p.nama_lengkap
     FROM users u
     LEFT JOIN pegawai p ON p.id_pegawai = u.id_pegawai
     WHERE u.id_user = ? AND u.status_aktif = 1'
);
$stmt->execute([$idUser]);
$profile = $stmt->fetch();

if (!$profile) {
    header('Location: ' . BASE_URL . '/auth/logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['password_saat_ini'] ?? '';
    $name = trim($_POST['nama_lengkap'] ?? '');
    $newPassword = $_POST['password_baru'] ?? '';
    $confirmPassword = $_POST['konfirmasi_password'] ?? '';
    $error = null;

    if ($currentPassword === '' || !password_verify($currentPassword, $profile['password'])) {
        $error = 'Kata sandi saat ini tidak sesuai.';
    } elseif ($profile['id_pegawai'] !== null && $name === '') {
        $error = 'Nama lengkap wajib diisi.';
    } elseif ($newPassword !== '' && strlen($newPassword) < 8) {
        $error = 'Kata sandi baru minimal 8 karakter.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Konfirmasi kata sandi baru tidak sama.';
    }

    if ($error !== null) {
        setFlash('error', $error);
    } else {
        try {
            $pdo->beginTransaction();
            if ($profile['id_pegawai'] !== null) {
                $updateName = $pdo->prepare('UPDATE pegawai SET nama_lengkap = ? WHERE id_pegawai = ?');
                $updateName->execute([$name, $profile['id_pegawai']]);
                $_SESSION['nama'] = $name;
            }
            if ($newPassword !== '') {
                $updatePassword = $pdo->prepare('UPDATE users SET password = ? WHERE id_user = ?');
                $updatePassword->execute([password_hash($newPassword, PASSWORD_DEFAULT), $idUser]);
            }
            $pdo->commit();
            setFlash('success', 'Profil berhasil diperbarui.');
            header('Location: profil.php');
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }
}

$pageTitle = 'Ganti profil';
require_once __DIR__ . '/../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width: 600px;">
  <div class="mb-3">
    <label class="form-label" for="nip">NIP</label>
    <input type="text" id="nip" class="form-control" value="<?= htmlspecialchars($profile['nip']) ?>" readonly>
  </div>
  <?php if ($profile['id_pegawai'] !== null): ?>
  <div class="mb-3">
    <label class="form-label" for="nama_lengkap">Nama lengkap</label>
    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? $profile['nama_lengkap']) ?>" maxlength="150" required>
  </div>
  <?php endif; ?>
  <div class="mb-3">
    <label class="form-label" for="password_saat_ini">Kata sandi saat ini</label>
    <input type="password" name="password_saat_ini" id="password_saat_ini" class="form-control" autocomplete="current-password" required>
  </div>
  <hr>
  <div class="mb-3">
    <label class="form-label" for="password_baru">Kata sandi baru</label>
    <input type="password" name="password_baru" id="password_baru" class="form-control" autocomplete="new-password" minlength="8">
    <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti kata sandi.</small>
  </div>
  <div class="mb-3">
    <label class="form-label" for="konfirmasi_password">Konfirmasi kata sandi baru</label>
    <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control" autocomplete="new-password">
  </div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
  <a href="<?= BASE_URL ?>/<?= $profile['role'] === 'Admin' ? 'admin/dashboard.php' : 'penilai/dashboard.php' ?>" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
