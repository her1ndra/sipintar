<?php
require_once __DIR__ . '/../../config/config.php';
requireKegiatanHakimAccess();
$user = currentUser();
$pdo = Database::getConnection();
$jenisKegiatan = $pdo->query('SELECT nama_jenis FROM jenis_kegiatan_hakim ORDER BY nama_jenis')->fetchAll(PDO::FETCH_COLUMN);
$hakimList = $pdo->query(
    "SELECT p.id_pegawai, p.nama_lengkap FROM pegawai p
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE j.nama_jabatan LIKE '%Hakim%' ORDER BY p.nama_lengkap"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $jenisPilihan = $_POST['jenis_kegiatan'] ?? '';
    $jenis = $jenisPilihan === '__lainnya__'
        ? trim($_POST['jenis_kegiatan_baru'] ?? '')
        : $jenisPilihan;
    $nama = trim($_POST['nama_kegiatan'] ?? '');
    $penyelenggara = trim($_POST['penyelenggara'] ?? '');
    $mulai = $_POST['tanggal_mulai'] ?? '';
    $selesai = ($_POST['tanggal_selesai'] ?? '') ?: null;
    $tempat = trim($_POST['tempat'] ?? '');
    $filePath = null;
    $canSave = true;

    if (($jenisPilihan === '__lainnya__' && ($jenis === '' || strlen($jenis) > 100))
        || ($jenisPilihan !== '__lainnya__' && !in_array($jenis, $jenisKegiatan, true))) {
        setFlash('error', 'Jenis kegiatan wajib dipilih atau diisi maksimal 100 karakter.');
        $canSave = false;
    }

    $stmt = $pdo->prepare(
        "SELECT p.id_pegawai
         FROM pegawai p
         JOIN jabatan j ON p.id_jabatan = j.id_jabatan
         WHERE p.id_pegawai = ? AND j.nama_jabatan LIKE '%Hakim%'"
    );
    $stmt->execute([$idPegawai]);

    if (!$stmt->fetchColumn()) {
        setFlash('error', 'Pegawai yang dipilih harus memiliki jabatan Hakim.');
        $canSave = false;
    }

    $file = $_FILES['surat_tugas'] ?? null;
    if ($canSave && $file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = simpanUploadAman($file, 'kegiatan_hakim', [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'image/jpeg' => 'jpg',
        ]);
        if ($upload['status'] === 'ok') {
            $filePath = $upload['path'];
        } else {
            setFlash('error', 'Surat tugas harus berupa PDF, Word, atau JPG dengan ukuran maksimal 5 MB.');
            $canSave = false;
        }
    }

    if ($canSave && (!$file || $file['error'] === UPLOAD_ERR_NO_FILE || $filePath !== null)) {
        $pdo->beginTransaction();
        $pdo->prepare('INSERT IGNORE INTO jenis_kegiatan_hakim (nama_jenis) VALUES (?)')->execute([$jenis]);
        $pdo->prepare(
            'INSERT INTO kegiatan_hakim (id_pegawai, jenis_kegiatan, nama_kegiatan, penyelenggara, tanggal_mulai, tanggal_selesai, lokasi, file_bukti)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([$idPegawai, $jenis, $nama, $penyelenggara, $mulai, $selesai, $tempat, $filePath]);
        $pdo->commit();
        setFlash('success', 'Kegiatan hakim berhasil dicatat.');
        header('Location: index.php');
        exit;
    } else {
        hapusFileUpload($filePath);
    }
}
$pageTitle = 'Catat kegiatan hakim';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;"><?= csrfField() ?>
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
    <select name="jenis_kegiatan" id="jenis_kegiatan" class="form-select" required>
      <option value="">-- pilih jenis kegiatan --</option>
      <?php foreach ($jenisKegiatan as $jenisPilihan): ?>
      <option value="<?= htmlspecialchars($jenisPilihan) ?>" <?= ($_POST['jenis_kegiatan'] ?? '') === $jenisPilihan ? 'selected' : '' ?>><?= htmlspecialchars($jenisPilihan) ?></option>
      <?php endforeach; ?>
      <option value="__lainnya__" <?= ($_POST['jenis_kegiatan'] ?? '') === '__lainnya__' ? 'selected' : '' ?>>Lainnya</option>
    </select>
  </div>
  <div class="mb-3 <?= ($_POST['jenis_kegiatan'] ?? '') === '__lainnya__' ? '' : 'd-none' ?>" id="jenis-kegiatan-baru-wrapper">
    <label class="form-label" for="jenis_kegiatan_baru">Jenis kegiatan lainnya</label>
    <input type="text" name="jenis_kegiatan_baru" id="jenis_kegiatan_baru" class="form-control" maxlength="100" value="<?= htmlspecialchars($_POST['jenis_kegiatan_baru'] ?? '') ?>">
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
  <div class="mb-3">
    <label class="form-label">Tempat</label>
    <input type="text" name="tempat" class="form-control" maxlength="150">
  </div>
  <div class="mb-3">
    <label class="form-label">Surat tugas</label>
    <input type="file" name="surat_tugas" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg">
    <small class="text-muted">Format PDF, Word, atau JPG. Maksimal 5 MB.</small>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
const jenisKegiatanSelect = document.getElementById('jenis_kegiatan');
const jenisKegiatanBaruWrapper = document.getElementById('jenis-kegiatan-baru-wrapper');
const jenisKegiatanBaruInput = document.getElementById('jenis_kegiatan_baru');

function toggleJenisKegiatanBaru() {
  const isLainnya = jenisKegiatanSelect.value === '__lainnya__';
  jenisKegiatanBaruWrapper.classList.toggle('d-none', !isLainnya);
  jenisKegiatanBaruInput.required = isLainnya;
}

jenisKegiatanSelect.addEventListener('change', toggleJenisKegiatanBaru);
toggleJenisKegiatanBaru();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
