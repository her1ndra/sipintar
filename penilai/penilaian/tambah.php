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
$kompetensiList = $pdo->query('SELECT id_kompetensi, nama_kompetensi FROM kompetensi ORDER BY nama_kompetensi')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) $_POST['id_pegawai'];
    $idKompetensi = (int) $_POST['id_kompetensi'];
    $status = $_POST['status_kompetensi'];
    $periode = (int) $_POST['periode_penilaian'];
    $tanggal = $_POST['tanggal_penilaian'];
    $catatan = trim($_POST['catatan']);

    $stmt = $pdo->prepare(
        'INSERT INTO penilaian_kompetensi
         (id_pegawai, id_kompetensi, id_user_penilai, periode_penilaian, status_kompetensi, tanggal_penilaian, catatan)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$idPegawai, $idKompetensi, $user['id_user'], $periode, $status, $tanggal, $catatan]);
    setFlash('success', 'Penilaian kompetensi berhasil disimpan. Jangan lupa upload bukti dokumentasi.');
    header('Location: index.php');
    exit;
}
$pageTitle = 'Input penilaian kompetensi';
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
    <label class="form-label">Kompetensi</label>
    <select name="id_kompetensi" class="form-select" required>
      <option value="">-- pilih kompetensi --</option>
      <?php foreach ($kompetensiList as $k): ?>
      <option value="<?= $k['id_kompetensi'] ?>"><?= htmlspecialchars($k['nama_kompetensi']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Status kompetensi</label>
    <select name="status_kompetensi" class="form-select" required>
      <option value="Kompeten">Kompeten</option>
      <option value="Cukup">Cukup</option>
      <option value="Tidak Kompeten">Tidak Kompeten</option>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Periode (tahun)</label>
    <input type="number" name="periode_penilaian" class="form-control" value="<?= date('Y') ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal penilaian</label>
    <input type="date" name="tanggal_penilaian" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="catatan" class="form-control"></textarea>
  </div>
  <p class="text-muted small">Upload bukti dokumentasi dilakukan setelah data penilaian ini tersimpan
  (lihat tabel <code>bukti_dokumentasi</code> - form upload menyusul di iterasi berikutnya).</p>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
