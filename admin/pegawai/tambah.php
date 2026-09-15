<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama_lengkap']);
    $idJabatan = (int) $_POST['id_jabatan'];

    if ($nip === '' || $nama === '' || $idJabatan <= 0) {
        setFlash('error', 'Semua field wajib diisi.');
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO pegawai (nip, nama_lengkap, id_jabatan, dibuat_oleh) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$nip, $nama, $idJabatan, currentUser()['id_user']]);
        setFlash('success', 'Data pegawai berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Tambah pegawai';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <div class="mb-3">
    <label class="form-label">NIP</label>
    <input type="text" name="nip" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Nama lengkap</label>
    <input type="text" name="nama_lengkap" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Jabatan</label>
    <select name="id_jabatan" class="form-select" required>
      <option value="">-- pilih jabatan --</option>
      <?php foreach ($jabatanList as $j): ?>
      <option value="<?= $j['id_jabatan'] ?>"><?= htmlspecialchars($j['nama_jabatan']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
