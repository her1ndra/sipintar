<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$pegawaiList = $pdo->query(
    'SELECT p.id_pegawai, p.nama_lengkap, j.nama_jabatan
     FROM pegawai p
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
     WHERE p.status_aktif = "Aktif"
     ORDER BY j.id_jabatan, p.nama_lengkap'
)->fetchAll();
$diklatList = $pdo->query('SELECT id_diklat, nama_diklat FROM diklat ORDER BY nama_diklat')->fetchAll();
$gapList = $pdo->query(
    "SELECT g.id_kesenjangan, g.id_pegawai, g.gap_kompetensi, p.nama_lengkap
     FROM analisis_kesenjangan_kompetensi g
     JOIN pegawai p ON p.id_pegawai = g.id_pegawai
     ORDER BY p.nama_lengkap, g.created_at DESC"
)->fetchAll();
$gapByPegawai = [];
foreach ($gapList as $gap) {
    $pegawaiId = (int) $gap['id_pegawai'];
    if (!isset($gapByPegawai[$pegawaiId])) {
        $gapByPegawai[$pegawaiId] = [
            'id' => (int) $gap['id_kesenjangan'],
            'text' => $gap['gap_kompetensi'],
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $idDiklat = ($_POST['id_diklat'] ?? '') !== '' ? (int) $_POST['id_diklat'] : null;
    $idKesenjangan = ($_POST['id_kesenjangan'] ?? '') !== '' ? (int) $_POST['id_kesenjangan'] : null;
    $metode = trim($_POST['metode_pengembangan'] ?? '');
    $prioritas = $_POST['prioritas'] ?? 'Sedang';
    $tahun = (int) ($_POST['tahun_rencana'] ?? 0);
    $catatan = trim($_POST['catatan'] ?? '');
    $pegawaiStmt = $pdo->prepare('SELECT id_pegawai FROM pegawai WHERE id_pegawai = ? AND status_aktif = "Aktif"');
    $pegawaiStmt->execute([$idPegawai]);
    $diklatValid = $idDiklat === null;
    if ($idDiklat !== null) {
        $diklatStmt = $pdo->prepare('SELECT id_diklat FROM diklat WHERE id_diklat = ?');
        $diklatStmt->execute([$idDiklat]);
        $diklatValid = (bool) $diklatStmt->fetch();
    }
    $gapValid = $idKesenjangan === null;
    if ($idKesenjangan !== null) {
        $gapStmt = $pdo->prepare('SELECT id_kesenjangan, id_pegawai FROM analisis_kesenjangan_kompetensi WHERE id_kesenjangan = ?');
        $gapStmt->execute([$idKesenjangan]);
        $gap = $gapStmt->fetch();
        $gapValid = $gap && (int) $gap['id_pegawai'] === $idPegawai;
    }
    if (!$pegawaiStmt->fetch() || !$diklatValid || !$gapValid || $metode === '' || !in_array($prioritas, ['Tinggi', 'Sedang', 'Rendah'], true) || $tahun < 2000) {
        setFlash('error', 'Pegawai, diklat, prioritas, dan tahun rencana harus diisi dengan benar.');
    } else {
        $pdo->prepare(
            'INSERT INTO kebutuhan_diklat (id_pegawai, id_kesenjangan, id_diklat, metode_pengembangan, prioritas, tahun_rencana, catatan)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        )->execute([$idPegawai, $idKesenjangan, $idDiklat, $metode, $prioritas, $tahun, $catatan]);
        setFlash('success', 'Penentuan kebutuhan diklat berhasil disimpan.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Tentukan kebutuhan diklat';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:600px;">
  <div class="mb-3">
    <label class="form-label">Pegawai</label>
    <select name="id_pegawai" class="form-select" required>
      <option value="">-- pilih pegawai --</option>
      <?php foreach ($pegawaiList as $pegawai): ?>
      <option value="<?= (int) $pegawai['id_pegawai'] ?>"><?= htmlspecialchars($pegawai['nama_jabatan'] . ' - ' . $pegawai['nama_lengkap']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Gap kompetensi</label>
    <input type="hidden" name="id_kesenjangan" id="id_kesenjangan">
    <textarea id="gap_kompetensi" class="form-control" rows="3" readonly placeholder="Gap akan terisi setelah pegawai dipilih"></textarea>
    <div class="form-text">Gap kompetensi diambil otomatis dari data analisis pegawai.</div>
  </div>
  <div class="mb-3">
    <label class="form-label">Diklat</label>
    <select name="id_diklat" class="form-select">
      <option value="">-- belum ditentukan --</option>
      <?php foreach ($diklatList as $diklat): ?>
      <option value="<?= (int) $diklat['id_diklat'] ?>"><?= htmlspecialchars($diklat['nama_diklat']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Metode pengembangan</label>
    <select name="metode_pengembangan" class="form-select" required>
      <option value="">-- pilih metode --</option>
      <option value="Pelatihan klasikal">Pelatihan klasikal</option>
      <option value="Pelatihan daring">Pelatihan daring</option>
      <option value="Bimbingan teknis">Bimbingan teknis</option>
      <option value="Coaching">Coaching</option>
      <option value="Mentoring">Mentoring</option>
      <option value="Belajar mandiri">Belajar mandiri</option>
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
    <input type="number" name="tahun_rencana" class="form-control" min="2000" value="<?= date('Y') + 1 ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="catatan" class="form-control"></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
const gapByPegawai = <?= json_encode($gapByPegawai, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const pegawaiSelect = document.querySelector('select[name="id_pegawai"]');
const gapIdInput = document.getElementById('id_kesenjangan');
const gapText = document.getElementById('gap_kompetensi');

function updateGap() {
  const gap = gapByPegawai[pegawaiSelect.value];
  gapIdInput.value = gap ? gap.id : '';
  gapText.value = gap ? gap.text : '';
}

pegawaiSelect.addEventListener('change', updateGap);
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
