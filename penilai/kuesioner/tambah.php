<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
$jabatanList = [];
if ($jabatanIds) {
    $placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
    $stmt = $pdo->prepare("SELECT id_jabatan, nama_jabatan FROM jabatan WHERE id_jabatan IN ($placeholders)");
    $stmt->execute($jabatanIds);
    $jabatanList = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJabatan = (int) $_POST['id_jabatan_dinilai'];
    $judul = trim($_POST['judul_kuesioner']);
    $tahun = (int) $_POST['tahun_periode'];

    if (!in_array($idJabatan, $jabatanIds, true)) {
        setFlash('error', 'Anda tidak berwenang membuat kuesioner untuk jabatan tersebut.');
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO kuesioner (id_user_pembuat, id_jabatan_dinilai, judul_kuesioner, tahun_periode)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$user['id_user'], $idJabatan, $judul, $tahun]);
        setFlash('success', 'Kuesioner berhasil dibuat. Silakan tambahkan pertanyaan.');
        header('Location: pertanyaan.php?id=' . $pdo->lastInsertId());
        exit;
    }
}
$pageTitle = 'Buat kuesioner';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <div class="mb-3">
    <label class="form-label">Jabatan yang dinilai</label>
    <select name="id_jabatan_dinilai" class="form-select" required>
      <option value="">-- pilih jabatan --</option>
      <?php foreach ($jabatanList as $j): ?>
      <option value="<?= $j['id_jabatan'] ?>"><?= htmlspecialchars($j['nama_jabatan']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Judul kuesioner</label>
    <input type="text" name="judul_kuesioner" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Tahun periode</label>
    <input type="number" name="tahun_periode" class="form-control" value="<?= date('Y') ?>" required>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
