<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$user = currentUser();
if (!in_array($user['nama_jabatan'], ['Ketua', 'Wakil Ketua'], true)) {
    http_response_code(403);
    die('Menu ini khusus untuk Ketua/Wakil Ketua sebagai penilai Hakim.');
}
$pdo = Database::getConnection();
$hakimList = $pdo->query(
    "SELECT p.id_pegawai, p.nama_lengkap FROM pegawai p
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE j.nama_jabatan = 'Hakim' ORDER BY p.nama_lengkap"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) $_POST['id_pegawai'];
    $jenis = $_POST['jenis_kegiatan'];
    $nama = trim($_POST['nama_kegiatan']);
    $penyelenggara = trim($_POST['penyelenggara']);
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'] ?: null;

    $pdo->prepare(
        'INSERT INTO kegiatan_hakim (id_pegawai, jenis_kegiatan, nama_kegiatan, penyelenggara, tanggal_mulai, tanggal_selesai)
         VALUES (?, ?, ?, ?, ?, ?)'
    )->execute([$idPegawai, $jenis, $nama, $penyelenggara, $mulai, $selesai]);
    setFlash('success', 'Kegiatan hakim berhasil dicatat.');
    header('Location: index.php');
    exit;
}
$pageTitle = 'Catat kegiatan hakim';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <div class="mb-3">
    <label class="form-label">Hakim</label>
    <select name="id_pegawai" class="form-select" required>
      <option value="">-- pilih hakim --</option>
      <?php foreach ($hakimList as $h): ?>
      <option value="<?= $h['id_pegawai'] ?>"><?= htmlspecialchars($h['nama_lengkap']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Jenis kegiatan</label>
    <select name="jenis_kegiatan" class="form-select" required>
      <option value="Narasumber">Narasumber</option>
      <option value="Bimtek/Pelatihan">Bimtek/Pelatihan</option>
      <option value="Pengajar">Pengajar</option>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Nama kegiatan</label>
    <input type="text" name="nama_kegiatan" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Penyelenggara</label>
    <input type="text" name="penyelenggara" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal mulai</label>
    <input type="date" name="tanggal_mulai" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal selesai</label>
    <input type="date" name="tanggal_selesai" class="form-control">
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
