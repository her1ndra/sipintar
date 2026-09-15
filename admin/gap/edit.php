<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$id = (int) ($_GET['id'] ?? $_POST['id_kesenjangan'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();
$pegawaiList = $pdo->query('SELECT id_pegawai, id_jabatan, nama_lengkap FROM pegawai ORDER BY nama_lengkap')->fetchAll();
$stmt = $pdo->prepare('SELECT * FROM analisis_kesenjangan_kompetensi WHERE id_kesenjangan = ?');
$stmt->execute([$id]);
$gap = $stmt->fetch();

if (!$gap) {
    setFlash('error', 'Data analisis kesenjangan tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJabatan = (int) ($_POST['id_jabatan'] ?? 0);
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $kompetensiJabatan = trim($_POST['kompetensi_jabatan'] ?? '');
    $kompetensiPegawai = trim($_POST['kompetensi_pegawai_saat_ini'] ?? '');
    $gapKompetensi = trim($_POST['gap_kompetensi'] ?? '');
    $dampak = trim($_POST['dampak'] ?? '');

    if ($idJabatan <= 0 || $idPegawai <= 0 || $kompetensiJabatan === '' || $kompetensiPegawai === '' || $gapKompetensi === '' || $dampak === '') {
        setFlash('error', 'Semua data analisis kesenjangan wajib diisi.');
        $gap = array_merge($gap, $_POST);
    } else {
        $update = $pdo->prepare(
            'UPDATE analisis_kesenjangan_kompetensi SET id_jabatan = ?, id_pegawai = ?, kompetensi_jabatan = ?, kompetensi_pegawai_saat_ini = ?, gap_kompetensi = ?, dampak = ?
             WHERE id_kesenjangan = ?'
        );
        $update->execute([$idJabatan, $idPegawai, $kompetensiJabatan, $kompetensiPegawai, $gapKompetensi, $dampak, $id]);
        setFlash('success', 'Analisis kesenjangan berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Edit analisis kesenjangan';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?><div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:800px;">
  <input type="hidden" name="id_kesenjangan" value="<?= (int) $id ?>">
  <div class="mb-3"><label class="form-label" for="id_jabatan">Nama jabatan</label><select name="id_jabatan" id="id_jabatan" class="form-select" required><option value="">-- pilih jabatan --</option><?php foreach ($jabatanList as $jabatan): ?><option value="<?= $jabatan['id_jabatan'] ?>" <?= ((int) $gap['id_jabatan'] === (int) $jabatan['id_jabatan']) ? 'selected' : '' ?>><?= htmlspecialchars($jabatan['nama_jabatan']) ?></option><?php endforeach; ?></select></div>
  <div class="mb-3"><label class="form-label" for="id_pegawai">Pegawai</label><select name="id_pegawai" id="id_pegawai" class="form-select" required><option value="">-- pilih pegawai --</option><?php foreach ($pegawaiList as $pegawai): ?><option value="<?= $pegawai['id_pegawai'] ?>" data-jabatan="<?= $pegawai['id_jabatan'] ?>" <?= ((int) $gap['id_pegawai'] === (int) $pegawai['id_pegawai']) ? 'selected' : '' ?>><?= htmlspecialchars($pegawai['nama_lengkap']) ?></option><?php endforeach; ?></select></div>
  <div class="mb-3"><label class="form-label" for="kompetensi_jabatan">Kompetensi jabatan</label><textarea name="kompetensi_jabatan" id="kompetensi_jabatan" class="form-control" rows="3" required><?= htmlspecialchars($gap['kompetensi_jabatan']) ?></textarea></div>
  <div class="mb-3"><label class="form-label" for="kompetensi_pegawai_saat_ini">Kompetensi pegawai saat ini</label><textarea name="kompetensi_pegawai_saat_ini" id="kompetensi_pegawai_saat_ini" class="form-control" rows="3" required><?= htmlspecialchars($gap['kompetensi_pegawai_saat_ini']) ?></textarea></div>
  <div class="mb-3"><label class="form-label" for="gap_kompetensi">Gap kompetensi</label><textarea name="gap_kompetensi" id="gap_kompetensi" class="form-control" rows="3" required><?= htmlspecialchars($gap['gap_kompetensi']) ?></textarea></div>
  <div class="mb-3"><label class="form-label" for="dampak">Dampak</label><textarea name="dampak" id="dampak" class="form-control" rows="3" required><?= htmlspecialchars($gap['dampak']) ?></textarea></div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button> <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
    var jabatanInput = document.getElementById('id_jabatan');
    var pegawaiInput = document.getElementById('id_pegawai');
    function filterPegawai() {
        Array.from(pegawaiInput.options).forEach(function (option) {
            option.hidden = option.dataset.jabatan ? option.dataset.jabatan !== jabatanInput.value : false;
        });
    }
    jabatanInput.addEventListener('change', function () { pegawaiInput.value = ''; filterPegawai(); });
    filterPegawai();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
