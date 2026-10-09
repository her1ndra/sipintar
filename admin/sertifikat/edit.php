<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$idSertifikat = (int) ($_GET['id'] ?? $_POST['id_sertifikat'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT s.*, p.nama_lengkap FROM sertifikat_pegawai s
     JOIN pegawai p ON p.id_pegawai = s.id_pegawai
     WHERE s.id_sertifikat = ?'
);
$stmt->execute([$idSertifikat]);
$sertifikat = $stmt->fetch();

if (!$sertifikat) {
    setFlash('error', 'Sertifikat tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_sertifikat'] ?? '');
    $penyelenggara = trim($_POST['penyelenggara'] ?? '');
    $terbit = $_POST['tanggal_terbit'] ?: null;
    $filePath = $sertifikat['file_sertifikat'];
    $uploadedPath = null;
    $error = null;

    if ($nama === '') {
        $error = 'Nama sertifikat wajib diisi.';
    } elseif (!empty($_FILES['file_sertifikat']['name'])) {
        $file = $_FILES['file_sertifikat'];
        $allowedTypes = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($file['tmp_name']);

        if ($file['error'] !== UPLOAD_ERR_OK || !isset($allowedTypes[$mimeType]) || $file['size'] > 5 * 1024 * 1024) {
            $error = 'File harus berupa PDF, JPG, atau PNG dengan ukuran maksimal 5 MB.';
        } else {
            $uploadDirectory = __DIR__ . '/../../assets/uploads/sertifikat';
            if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                $error = 'Folder penyimpanan sertifikat tidak dapat dibuat.';
            } else {
                $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
                $uploadedPath = 'assets/uploads/sertifikat/' . $fileName;
                if (move_uploaded_file($file['tmp_name'], __DIR__ . '/../../' . $uploadedPath)) {
                    $filePath = $uploadedPath;
                } else {
                    $uploadedPath = null;
                    $error = 'File sertifikat gagal diunggah.';
                }
            }
        }
    }

    if ($error !== null) {
        setFlash('error', $error);
    } else {
        try {
            $update = $pdo->prepare(
                'UPDATE sertifikat_pegawai
                  SET nama_sertifikat = ?, penyelenggara = ?, tanggal_terbit = ?, file_sertifikat = ?
                 WHERE id_sertifikat = ?'
            );
              $update->execute([$nama, $penyelenggara ?: null, $terbit, $filePath, $idSertifikat]);

            if ($uploadedPath !== null && !empty($sertifikat['file_sertifikat'])) {
                $oldFile = __DIR__ . '/../../' . $sertifikat['file_sertifikat'];
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            setFlash('success', 'Sertifikat berhasil diperbarui.');
            header('Location: index.php?id_pegawai=' . (int) $sertifikat['id_pegawai']);
            exit;
        } catch (Throwable $exception) {
            if ($uploadedPath !== null) {
                $newFile = __DIR__ . '/../../' . $uploadedPath;
                if (is_file($newFile)) {
                    unlink($newFile);
                }
            }
            throw $exception;
        }
    }
}

$pageTitle = 'Edit sertifikat';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" style="max-width: 600px;">
  <input type="hidden" name="id_sertifikat" value="<?= (int) $sertifikat['id_sertifikat'] ?>">
  <div class="mb-3">
    <label class="form-label">Pegawai</label>
    <input type="text" class="form-control" value="<?= htmlspecialchars($sertifikat['nama_lengkap']) ?>" readonly>
  </div>
  <div class="mb-3">
    <label class="form-label" for="nama_sertifikat">Nama sertifikat</label>
    <input type="text" name="nama_sertifikat" id="nama_sertifikat" class="form-control" value="<?= htmlspecialchars($_POST['nama_sertifikat'] ?? $sertifikat['nama_sertifikat']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label" for="penyelenggara">Penyelenggara</label>
    <input type="text" name="penyelenggara" id="penyelenggara" class="form-control" value="<?= htmlspecialchars($_POST['penyelenggara'] ?? $sertifikat['penyelenggara'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="tanggal_terbit">Tanggal terbit</label>
    <input type="date" name="tanggal_terbit" id="tanggal_terbit" class="form-control" value="<?= htmlspecialchars($_POST['tanggal_terbit'] ?? $sertifikat['tanggal_terbit'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="file_sertifikat">File sertifikat</label>
    <input type="file" name="file_sertifikat" id="file_sertifikat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    <small class="text-muted">Kosongkan jika file tidak diganti. Format PDF, JPG, atau PNG; maksimal 5 MB.</small>
    <?php if (!empty($sertifikat['file_sertifikat'])): ?>
      <div><a href="<?= BASE_URL . '/' . htmlspecialchars($sertifikat['file_sertifikat']) ?>" target="_blank" rel="noopener">Lihat file saat ini</a></div>
    <?php endif; ?>
  </div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
  <a href="index.php?id_pegawai=<?= (int) $sertifikat['id_pegawai'] ?>" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
