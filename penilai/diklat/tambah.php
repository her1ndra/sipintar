<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);

$pegawaiList = [];
if ($jabatanIds) {
    $placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
    $stmt = $pdo->prepare("SELECT id_pegawai, nama_lengkap FROM pegawai WHERE id_jabatan IN ($placeholders) ORDER BY nama_lengkap");
    $stmt->execute($jabatanIds);
    $pegawaiList = $stmt->fetchAll();
}
$diklatList = $pdo->query('SELECT id_diklat, nama_diklat FROM diklat ORDER BY nama_diklat')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) $_POST['id_pegawai'];
    $idDiklat = $_POST['id_diklat'] !== '' ? (int) $_POST['id_diklat'] : null;
    $prioritas = $_POST['prioritas'];
    $tahun = (int) $_POST['tahun_rencana'];
    $catatan = trim($_POST['catatan']);

    $pdo->prepare(
        'INSERT INTO kebutuhan_diklat (id_pegawai, id_diklat, prioritas, tahun_rencana, catatan) VALUES (?, ?, ?, ?, ?)'
    )->execute([$idPegawai, $idDiklat, $prioritas, $tahun, $catatan]);
    setFlash('success', 'Usulan kebutuhan diklat berhasil disimpan.');
    header('Location: index.php');
    exit;
}
$pageTitle = 'Ajukan kebutuhan diklat';
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
    <label class="form-label">Diklat (opsional, dari katalog)</label>
    <select name="id_diklat" class="form-select">
      <option value="">-- belum ditentukan --</option>
      <?php foreach ($diklatList as $d): ?>
      <option value="<?= $d['id_diklat'] ?>"><?= htmlspecialchars($d['nama_diklat']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Prioritas</label>
    <select name="prioritas" class="form-select">
      <option value="Tinggi">Tinggi</option>
      <option value="Sedang" selected>Sedang</option>
      <option value="Rendah">Rendah</option>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Tahun rencana</label>
    <input type="number" name="tahun_rencana" class="form-control" value="<?= date('Y') + 1 ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="catatan" class="form-control"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
