<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJabatan = (int) ($_POST['id_jabatan'] ?? 0);
    $tugas = trim($_POST['tugas'] ?? '');
    $kegiatan = trim($_POST['kegiatan'] ?? '');
    $kompetensiSementara = trim($_POST['kompetensi_sementara_jabatan'] ?? '');
    $kompetensiJabatan = trim($_POST['kompetensi_jabatan'] ?? '');

    if ($idJabatan <= 0 || $tugas === '' || $kegiatan === '' || $kompetensiJabatan === '') {
        setFlash('error', 'Jabatan, tugas, kegiatan, dan kompetensi jabatan wajib diisi.');
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO analisis_tugas
            (id_jabatan, tugas, kegiatan, kompetensi_sementara_jabatan, kompetensi_jabatan)
           VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $idJabatan,
            $tugas,
            $kegiatan,
            $kompetensiSementara ?: null,
            $kompetensiJabatan,
        ]);
        setFlash('success', 'Analisis tugas berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Tambah analisis tugas';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:800px;">
  <div class="mb-3">
    <label class="form-label" for="id_jabatan">Nama jabatan</label>
    <select name="id_jabatan" id="id_jabatan" class="form-select" required>
      <option value="">-- pilih jabatan --</option>
      <?php foreach ($jabatanList as $jabatan): ?>
      <option value="<?= $jabatan['id_jabatan'] ?>" <?= ((int) ($_POST['id_jabatan'] ?? 0) === (int) $jabatan['id_jabatan']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($jabatan['nama_jabatan']) ?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label" for="tugas">Tugas</label>
    <textarea name="tugas" id="tugas" class="form-control" rows="4" required><?= htmlspecialchars($_POST['tugas'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kegiatan">Kegiatan</label>
    <textarea name="kegiatan" id="kegiatan" class="form-control" rows="5" required><?= htmlspecialchars($_POST['kegiatan'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_sementara_jabatan">Kompetensi sementara jabatan</label>
    <textarea name="kompetensi_sementara_jabatan" id="kompetensi_sementara_jabatan" class="form-control" rows="4"><?= htmlspecialchars($_POST['kompetensi_sementara_jabatan'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_jabatan">Kompetensi jabatan</label>
    <textarea name="kompetensi_jabatan" id="kompetensi_jabatan" class="form-control" rows="4" required><?= htmlspecialchars($_POST['kompetensi_jabatan'] ?? '') ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
