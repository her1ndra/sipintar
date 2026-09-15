<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM pegawai WHERE id_pegawai = ?');
$stmt->execute([$id]);
$pegawai = $stmt->fetch();
if (!$pegawai) {
    setFlash('error', 'Data pegawai tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama_lengkap']);
    $idJabatan = (int) $_POST['id_jabatan'];
    $status = $_POST['status_aktif'];

    $stmt = $pdo->prepare(
        'UPDATE pegawai SET nip = ?, nama_lengkap = ?, id_jabatan = ?, status_aktif = ? WHERE id_pegawai = ?'
    );
    $stmt->execute([$nip, $nama, $idJabatan, $status, $id]);
    setFlash('success', 'Data pegawai berhasil diperbarui.');
    header('Location: index.php');
    exit;
}
$pageTitle = 'Edit pegawai';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <div class="mb-3">
    <label class="form-label">NIP</label>
    <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($pegawai['nip']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Nama lengkap</label>
    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($pegawai['nama_lengkap']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Jabatan</label>
    <select name="id_jabatan" class="form-select" required>
      <?php foreach ($jabatanList as $j): ?>
      <option value="<?= $j['id_jabatan'] ?>" <?= $j['id_jabatan'] == $pegawai['id_jabatan'] ? 'selected' : '' ?>><?= htmlspecialchars($j['nama_jabatan']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Status</label>
    <select name="status_aktif" class="form-select">
      <option value="Aktif" <?= $pegawai['status_aktif'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
      <option value="Non-Aktif" <?= $pegawai['status_aktif'] === 'Non-Aktif' ? 'selected' : '' ?>>Non-Aktif</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
