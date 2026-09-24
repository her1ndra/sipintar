<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'Admin' && ($user['role'] !== 'Penilai' || $user['nama_jabatan'] !== 'Ketua')) {
    http_response_code(403);
    die('Akses kegiatan hakim hanya untuk Admin atau Ketua.');
}
$pdo = Database::getConnection();
$hakimList = $pdo->query(
    "SELECT p.id_pegawai, p.nama_lengkap FROM pegawai p
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE j.nama_jabatan LIKE '%Hakim%' ORDER BY p.nama_lengkap"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) $_POST['id_pegawai'];
    $jenis = $_POST['jenis_kegiatan'];
    $nama = trim($_POST['nama_kegiatan']);
    $penyelenggara = trim($_POST['penyelenggara']);
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'] ?: null;
    $tempat = trim($_POST['tempat']);
    $filePath = null;
    $canSave = true;

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
        $extensions = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'image/jpeg' => 'jpg',
        ];
        if (
            $file['error'] !== UPLOAD_ERR_OK
            || $file['size'] > 5 * 1024 * 1024
        ) {
            setFlash('error', 'Surat tugas harus berupa PDF, Word, atau JPG dengan ukuran maksimal 5 MB.');
            $canSave = false;
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
            if (!isset($extensions[$mime])) {
                setFlash('error', 'Surat tugas harus berupa PDF, Word, atau JPG dengan ukuran maksimal 5 MB.');
                $canSave = false;
            }
        }

        if ($canSave) {
            $directory = __DIR__ . '/../../assets/uploads/kegiatan_hakim';
            if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
                setFlash('error', 'Folder penyimpanan surat tugas tidak dapat dibuat.');
                $canSave = false;
            } else {
                $filePath = 'assets/uploads/kegiatan_hakim/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
                if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../' . $filePath)) {
                    $filePath = null;
                    setFlash('error', 'Surat tugas gagal diunggah.');
                    $canSave = false;
                }
            }
        }
    }

    if ($canSave && (!$file || $file['error'] === UPLOAD_ERR_NO_FILE || $filePath !== null)) {
        $pdo->prepare(
            'INSERT INTO kegiatan_hakim (id_pegawai, jenis_kegiatan, nama_kegiatan, penyelenggara, tanggal_mulai, tanggal_selesai, lokasi, file_bukti)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([$idPegawai, $jenis, $nama, $penyelenggara, $mulai, $selesai, $tempat, $filePath]);
        setFlash('success', 'Kegiatan hakim berhasil dicatat.');
        header('Location: index.php');
        exit;
    } else {
        if ($filePath !== null && is_file(__DIR__ . '/../../' . $filePath)) {
            unlink(__DIR__ . '/../../' . $filePath);
        }
    }
}
$pageTitle = 'Catat kegiatan hakim';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
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
    <select name="jenis_kegiatan" class="form-select" required>
      <option value="Narasumber">Narasumber</option>
      <option value="Bimtek/Pelatihan">Bimtek/Pelatihan</option>
      <option value="Pengajar">Pengajar</option>
    </select>
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
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
