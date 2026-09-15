<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
// Hanya pegawai berjabatan Ketua/Wakil Ketua/Panitera/Sekretaris yang relevan jadi Penilai
$pegawaiList = $pdo->query(
    "SELECT p.id_pegawai, p.nama_lengkap, j.nama_jabatan
     FROM pegawai p JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE j.is_penilai = 1
     ORDER BY p.nama_lengkap"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $idPegawai = $_POST['id_pegawai'] !== '' ? (int) $_POST['id_pegawai'] : null;

    if ($role === 'Penilai' && !$idPegawai) {
        setFlash('error', 'Role Penilai wajib dikaitkan dengan data pegawai.');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (id_pegawai, nip, password, role) VALUES (?, ?, ?, ?)'
        );
          $stmt->execute([$idPegawai, $nip, $hash, $role]);
        setFlash('success', 'Akun user berhasil dibuat.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Tambah akun user';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" id="role" class="form-select" required
      onchange="document.getElementById('pegawaiWrap').style.display = this.value === 'Penilai' ? 'block' : 'none'">
      <option value="Admin">Admin</option>
      <option value="Penilai">Penilai</option>
    </select>
  </div>
  <div class="mb-3" id="pegawaiWrap">
    <label class="form-label">Pegawai (khusus role Penilai)</label>
    <select name="id_pegawai" class="form-select">
      <option value="">-- tidak terkait pegawai --</option>
      <?php foreach ($pegawaiList as $p): ?>
      <option value="<?= $p['id_pegawai'] ?>"><?= htmlspecialchars($p['nama_lengkap']) ?> (<?= htmlspecialchars($p['nama_jabatan']) ?>)</option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">NIP</label>
    <input type="text" name="nip" class="form-control" inputmode="numeric" pattern="[0-9]+" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Kata sandi</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
