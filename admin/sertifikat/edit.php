<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/sertifikat_form.php';
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
    [$nilai, $error] = sertifikatBacaInput($_POST);
    $filePath = $sertifikat['file_sertifikat'];
    $upload = ['status' => 'none', 'path' => null, 'error' => null];

    if ($error === null) {
        $upload = simpanUploadAman($_FILES['file_sertifikat'] ?? null, 'sertifikat', UPLOAD_PDF_GAMBAR);
        if ($upload['status'] === 'error') {
            $error = 'File harus berupa PDF, JPG, atau PNG dengan ukuran maksimal 5 MB.';
        } elseif ($upload['status'] === 'ok') {
            $filePath = $upload['path'];
        }
    }

    if ($error !== null) {
        setFlash('error', $error);
    } else {
        try {
            $pdo->prepare(
                'UPDATE sertifikat_pegawai
                    SET nama_sertifikat = ?, penyelenggara = ?, tanggal_terbit = ?, nomor_sertifikat = ?,
                        jalur_pelatihan = ?, status_validasi = ?, jenis = ?, file_sertifikat = ?
                  WHERE id_sertifikat = ?'
            )->execute([
                $nilai['nama_sertifikat'], $nilai['penyelenggara'], $nilai['tanggal_terbit'], $nilai['nomor_sertifikat'],
                $nilai['jalur_pelatihan'], $nilai['status_validasi'], $nilai['jenis'], $filePath, $idSertifikat,
            ]);
        } catch (Throwable $exception) {
            hapusFileUpload($upload['path']);
            throw $exception;
        }
        if ($upload['status'] === 'ok') {
            hapusFileUpload($sertifikat['file_sertifikat']);
        }
        auditLog('ubah_sertifikat', 'Ubah sertifikat #' . $idSertifikat);
        setFlash('success', 'Sertifikat berhasil diperbarui.');
        header('Location: index.php?id_pegawai=' . (int) $sertifikat['id_pegawai']);
        exit;
    }
}

$pageTitle = 'Edit sertifikat';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" style="max-width: 600px;"><?= csrfField() ?>
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
<?php sertifikatFieldTambahan($_POST ?: $sertifikat); ?>
  <div class="mb-3">
    <label class="form-label" for="file_sertifikat">File sertifikat</label>
    <input type="file" name="file_sertifikat" id="file_sertifikat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    <small class="text-muted">Kosongkan jika file tidak diganti. Format PDF, JPG, atau PNG; maksimal 5 MB.</small>
    <?php if (!empty($sertifikat['file_sertifikat'])): ?>
      <div><a href="<?= htmlspecialchars(fileUrl($sertifikat['file_sertifikat'])) ?>" target="_blank" rel="noopener">Lihat file saat ini</a></div>
    <?php endif; ?>
  </div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
  <a href="index.php?id_pegawai=<?= (int) $sertifikat['id_pegawai'] ?>" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
