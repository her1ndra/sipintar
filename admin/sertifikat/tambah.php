<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$pegawaiList = $pdo->query('SELECT id_pegawai, nama_lengkap FROM pegawai ORDER BY nama_lengkap')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) $_POST['id_pegawai'];
    $nama = trim($_POST['nama_sertifikat']);
    $penyelenggara = trim($_POST['penyelenggara']);
    $terbit = $_POST['tanggal_terbit'] ?: null;
    $kadaluarsa = $_POST['tanggal_kadaluarsa'] ?: null;
  $filePath = null;

  if (!empty($_FILES['file_sertifikat']['name'])) {
    $file = $_FILES['file_sertifikat'];
    $allowedTypes = [
      'application/pdf' => 'pdf',
      'image/jpeg' => 'jpg',
      'image/png' => 'png',
    ];
    $maxFileSize = 5 * 1024 * 1024;
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);

    if ($file['error'] !== UPLOAD_ERR_OK || !isset($allowedTypes[$mimeType]) || $file['size'] > $maxFileSize) {
      setFlash('error', 'File harus berupa PDF, JPG, atau PNG dengan ukuran maksimal 5 MB.');
    } else {
      $uploadDirectory = __DIR__ . '/../../assets/uploads/sertifikat';
      if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
      }

      $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
      $filePath = 'assets/uploads/sertifikat/' . $fileName;
      move_uploaded_file($file['tmp_name'], __DIR__ . '/../../' . $filePath);
    }
  }

  if ($filePath !== null || empty($_FILES['file_sertifikat']['name'])) {
    $stmt = $pdo->prepare(
      'INSERT INTO sertifikat_pegawai (id_pegawai, nama_sertifikat, penyelenggara, tanggal_terbit, tanggal_kadaluarsa, file_sertifikat, dibuat_oleh)
       VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$idPegawai, $nama, $penyelenggara, $terbit, $kadaluarsa, $filePath, currentUser()['id_user']]);
    setFlash('success', 'Sertifikat berhasil ditambahkan.');
    header('Location: index.php');
    exit;
  }
}
$pageTitle = 'Tambah sertifikat';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
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
    <label class="form-label">Nama sertifikat</label>
    <input type="text" name="nama_sertifikat" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Penyelenggara</label>
    <input type="text" name="penyelenggara" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal terbit</label>
    <input type="date" name="tanggal_terbit" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal kadaluarsa</label>
    <input type="date" name="tanggal_kadaluarsa" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">File sertifikat</label>
    <input type="file" name="file_sertifikat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    <small class="text-muted">Format PDF, JPG, atau PNG. Maksimal 5 MB.</small>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
