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
$stmt = $pdo->prepare("SELECT id_kuesioner, judul_kuesioner FROM kuesioner WHERE id_user_pembuat = ? AND status = 'Aktif'");
$stmt->execute([$user['id_user']]);
$kuesionerList = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) $_POST['id_pegawai'];
    $idKuesioner = (int) $_POST['id_kuesioner'];
    $tanggal = $_POST['tanggal_wawancara'];

    $pdo->prepare(
        'INSERT INTO wawancara (id_pegawai, id_kuesioner, id_user_penilai, tanggal_wawancara) VALUES (?, ?, ?, ?)'
    )->execute([$idPegawai, $idKuesioner, $user['id_user'], $tanggal]);
    setFlash('success', 'Jadwal wawancara berhasil dibuat.');
    header('Location: index.php');
    exit;
}
$pageTitle = 'Jadwalkan wawancara';
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
    <label class="form-label">Kuesioner</label>
    <select name="id_kuesioner" class="form-select" required>
      <option value="">-- pilih kuesioner --</option>
      <?php foreach ($kuesionerList as $k): ?>
      <option value="<?= $k['id_kuesioner'] ?>"><?= htmlspecialchars($k['judul_kuesioner']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal wawancara</label>
    <input type="date" name="tanggal_wawancara" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
