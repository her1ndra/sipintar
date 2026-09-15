<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$id = (int) ($_GET['id'] ?? $_POST['id_analisis_tugas'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();
$stmt = $pdo->prepare('SELECT * FROM analisis_tugas WHERE id_analisis_tugas = ?');
$stmt->execute([$id]);
$analisis = $stmt->fetch();

if (!$analisis) {
    setFlash('error', 'Data analisis tugas tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJabatan = (int) ($_POST['id_jabatan'] ?? 0);
    $tugas = trim($_POST['tugas'] ?? '');
    $kegiatan = trim($_POST['kegiatan'] ?? '');
    $kompetensiSementara = trim($_POST['kompetensi_sementara_jabatan'] ?? '');
    $kompetensiJabatan = trim($_POST['kompetensi_jabatan'] ?? '');

    if ($idJabatan <= 0 || $tugas === '' || $kegiatan === '' || $kompetensiJabatan === '') {
        setFlash('error', 'Jabatan, tugas, kegiatan, dan kompetensi jabatan wajib diisi.');
        $analisis = array_merge($analisis, $_POST);
    } else {
        $update = $pdo->prepare(
            'UPDATE analisis_tugas
             SET id_jabatan = ?, tugas = ?, kegiatan = ?, kompetensi_sementara_jabatan = ?, kompetensi_jabatan = ?
             WHERE id_analisis_tugas = ?'
        );
        $update->execute([
            $idJabatan,
            $tugas,
            $kegiatan,
            $kompetensiSementara ?: null,
            $kompetensiJabatan,
            $id,
        ]);
        setFlash('success', 'Analisis tugas berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Edit analisis tugas';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:800px;">
  <input type="hidden" name="id_analisis_tugas" value="<?= (int) $id ?>">
  <div class="mb-3">
    <label class="form-label" for="id_jabatan">Nama jabatan</label>
    <select name="id_jabatan" id="id_jabatan" class="form-select" required>
      <option value="">-- pilih jabatan --</option>
      <?php foreach ($jabatanList as $jabatan): ?>
      <option value="<?= $jabatan['id_jabatan'] ?>" <?= ((int) ($analisis['id_jabatan'] ?? 0) === (int) $jabatan['id_jabatan']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($jabatan['nama_jabatan']) ?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label" for="tugas">Tugas</label>
    <textarea name="tugas" id="tugas" class="form-control" rows="4" required><?= htmlspecialchars($analisis['tugas'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kegiatan">Kegiatan</label>
    <textarea name="kegiatan" id="kegiatan" class="form-control" rows="5" required><?= htmlspecialchars($analisis['kegiatan'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_sementara_jabatan">Kompetensi sementara jabatan</label>
    <textarea name="kompetensi_sementara_jabatan" id="kompetensi_sementara_jabatan" class="form-control" rows="4"><?= htmlspecialchars($analisis['kompetensi_sementara_jabatan'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_jabatan">Kompetensi jabatan</label>
    <textarea name="kompetensi_jabatan" id="kompetensi_jabatan" class="form-control" rows="4" required><?= htmlspecialchars($analisis['kompetensi_jabatan'] ?? '') ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
