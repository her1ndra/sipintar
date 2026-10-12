<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/sertifikat_form.php';
requireRole('Admin');
$pdo = Database::getConnection();
$pegawaiList = $pdo->query('SELECT id_pegawai, nama_lengkap FROM pegawai ORDER BY nama_lengkap')->fetchAll();
$idPegawaiTerpilih = filter_input(INPUT_GET, 'id_pegawai', FILTER_VALIDATE_INT) ?: null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $idPegawaiTerpilih = $idPegawai ?: $idPegawaiTerpilih;
    [$nilai, $error] = sertifikatBacaInput($_POST);

    if ($error === null) {
        $cek = $pdo->prepare('SELECT 1 FROM pegawai WHERE id_pegawai = ?');
        $cek->execute([$idPegawai]);
        if (!$cek->fetchColumn()) {
            $error = 'Pegawai tidak ditemukan.';
        }
    }

    $upload = ['status' => 'none', 'path' => null, 'error' => null];
    if ($error === null) {
        $upload = simpanUploadAman($_FILES['file_sertifikat'] ?? null, 'sertifikat', UPLOAD_PDF_GAMBAR);
        if ($upload['status'] === 'error') {
            $error = 'File harus berupa PDF, JPG, atau PNG dengan ukuran maksimal 5 MB.';
        }
    }

    if ($error !== null) {
        setFlash('error', $error);
    } else {
        try {
            $pdo->prepare(
                'INSERT INTO sertifikat_pegawai
                    (id_pegawai, nama_sertifikat, penyelenggara, tanggal_terbit, nomor_sertifikat,
                     jalur_pelatihan, status_validasi, jenis, file_sertifikat, dibuat_oleh)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            )->execute([
                $idPegawai, $nilai['nama_sertifikat'], $nilai['penyelenggara'], $nilai['tanggal_terbit'],
                $nilai['nomor_sertifikat'], $nilai['jalur_pelatihan'], $nilai['status_validasi'], $nilai['jenis'],
                $upload['path'], currentUser()['id_user'],
            ]);
        } catch (Throwable $e) {
            hapusFileUpload($upload['path']);
            throw $e;
        }
        auditLog('tambah_sertifikat', 'Tambah sertifikat pegawai #' . $idPegawai);
        setFlash('success', 'Sertifikat berhasil ditambahkan.');
        header('Location: index.php?id_pegawai=' . $idPegawai);
        exit;
    }
}
$pageTitle = 'Tambah sertifikat';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;"><?= csrfField() ?>
  <div class="mb-3">
    <label class="form-label">Pegawai</label>
    <select name="id_pegawai" class="form-select" required>
      <option value="">-- pilih pegawai --</option>
      <?php foreach ($pegawaiList as $p): ?>
      <option value="<?= (int) $p['id_pegawai'] ?>" <?= (int) $p['id_pegawai'] === (int) $idPegawaiTerpilih ? 'selected' : '' ?>><?= htmlspecialchars($p['nama_lengkap']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Nama sertifikat</label>
    <input type="text" name="nama_sertifikat" class="form-control" maxlength="200" value="<?= e($_POST['nama_sertifikat'] ?? '') ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Penyelenggara</label>
    <input type="text" name="penyelenggara" class="form-control" maxlength="150" value="<?= e($_POST['penyelenggara'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Tanggal terbit</label>
    <input type="date" name="tanggal_terbit" class="form-control" value="<?= e($_POST['tanggal_terbit'] ?? '') ?>">
  </div>
<?php sertifikatFieldTambahan($_POST); ?>
  <div class="mb-3">
    <label class="form-label">File sertifikat</label>
    <input type="file" name="file_sertifikat" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
    <small class="text-muted">Format PDF, JPG, atau PNG. Maksimal 5 MB.</small>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
