<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$pegawaiList = $pdo->query('SELECT id_pegawai, nama_lengkap FROM pegawai ORDER BY nama_lengkap')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) $_POST['id_pegawai'];
    $nama = trim($_POST['nama_sertifikat']);
    $penyelenggara = trim($_POST['penyelenggara']);
    $terbit = $_POST['tanggal_terbit'] ?: null;
    $kadaluarsa = $_POST['tanggal_kadaluarsa'] ?: null;

    $stmt = $pdo->prepare(
        'INSERT INTO sertifikat_pegawai (id_pegawai, nama_sertifikat, penyelenggara, tanggal_terbit, tanggal_kadaluarsa, dibuat_oleh)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$idPegawai, $nama, $penyelenggara, $terbit, $kadaluarsa, currentUser()['id_user']]);
    setFlash('success', 'Sertifikat berhasil ditambahkan.');
    header('Location: index.php');
    exit;
}
$pageTitle = 'Tambah sertifikat';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <div class="mb-3">
    <label class="form-label">Pegawai</label>
    <select name="id_pegawai" class="form-select" required>
      <option value="">-- pilih pegawai --</option>
      <?php foreach ($pegawaiList as $p): ?>
      <option value="<?= $p['id_pegawai'] ?>"><?= htmlspecialchars($p['nama_lengkap']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Nama sertifikat</label>
    <input type="text" name="nama_sertifikat" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Penyelenggara</label>
    <input type="text" name="penyelenggara" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal terbit</label>
    <input type="date" name="tanggal_terbit" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal kadaluarsa</label>
    <input type="date" name="tanggal_kadaluarsa" class="form-control">
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
