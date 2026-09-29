<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'Admin' && ($user['role'] !== 'Penilai' || $user['nama_jabatan'] !== 'Ketua')) {
    http_response_code(403);
    die('Akses kegiatan hakim hanya untuk Admin atau Ketua.');
}

$pdo = Database::getConnection();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT kh.* FROM kegiatan_hakim kh
     JOIN pegawai p ON kh.id_pegawai = p.id_pegawai
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE kh.id_kegiatan = ? AND j.nama_jabatan LIKE '%Hakim%'"
);
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) {
    setFlash('error', 'Data kegiatan hakim tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$hakimList = $pdo->query(
    "SELECT p.id_pegawai, p.nama_lengkap FROM pegawai p
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE j.nama_jabatan LIKE '%Hakim%' ORDER BY p.nama_lengkap"
)->fetchAll();
$jenisKegiatan = $pdo->query('SELECT nama_jenis FROM jenis_kegiatan_hakim ORDER BY nama_jenis')->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $jenisPilihan = $_POST['jenis_kegiatan'] ?? '';
    $jenis = $jenisPilihan === '__lainnya__'
        ? trim($_POST['jenis_kegiatan_baru'] ?? '')
        : $jenisPilihan;
    $nama = trim($_POST['nama_kegiatan'] ?? '');
    $penyelenggara = trim($_POST['penyelenggara'] ?? '');
    $mulai = $_POST['tanggal_mulai'] ?? '';
    $selesai = $_POST['tanggal_selesai'] ?? '';
    $tempat = trim($_POST['tempat'] ?? '');
    $filePath = $data['file_bukti'];
    $newFilePath = null;
    $canSave = true;

    $hakimStmt = $pdo->prepare(
        "SELECT p.id_pegawai FROM pegawai p
         JOIN jabatan j ON p.id_jabatan = j.id_jabatan
         WHERE p.id_pegawai = ? AND j.nama_jabatan LIKE '%Hakim%'"
    );
    $hakimStmt->execute([$idPegawai]);
    $tanggalMulai = DateTimeImmutable::createFromFormat('!Y-m-d', $mulai);
    $tanggalSelesai = $selesai === '' ? null : DateTimeImmutable::createFromFormat('!Y-m-d', $selesai);

    if (!$hakimStmt->fetchColumn()) {
        setFlash('error', 'Pegawai yang dipilih harus memiliki jabatan Hakim.');
        $canSave = false;
    } elseif (($jenisPilihan === '__lainnya__' && ($jenis === '' || strlen($jenis) > 100))
        || ($jenisPilihan !== '__lainnya__' && !in_array($jenis, $jenisKegiatan, true))
        || $nama === '' || strlen($nama) > 200 || strlen($penyelenggara) > 150 || strlen($tempat) > 150) {
        setFlash('error', 'Data kegiatan belum lengkap atau melebihi batas karakter.');
        $canSave = false;
    } elseif (!$tanggalMulai || $tanggalMulai->format('Y-m-d') !== $mulai || ($selesai !== '' && (!$tanggalSelesai || $tanggalSelesai->format('Y-m-d') !== $selesai || $tanggalSelesai < $tanggalMulai))) {
        setFlash('error', 'Tanggal kegiatan tidak valid.');
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
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
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
                $newFilePath = 'assets/uploads/kegiatan_hakim/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
                if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../' . $newFilePath)) {
                    $newFilePath = null;
                    setFlash('error', 'Surat tugas gagal diunggah.');
                    $canSave = false;
                } else {
                    $filePath = $newFilePath;
                }
            }
        }
    }

    if ($canSave) {
        if (!$newFilePath && !empty($_POST['hapus_lampiran'])) {
            $filePath = null;
        }
        $pdo->prepare('INSERT IGNORE INTO jenis_kegiatan_hakim (nama_jenis) VALUES (?)')->execute([$jenis]);
        $pdo->prepare(
            'UPDATE kegiatan_hakim
             SET id_pegawai = ?, jenis_kegiatan = ?, nama_kegiatan = ?, penyelenggara = ?, tanggal_mulai = ?, tanggal_selesai = ?, lokasi = ?, file_bukti = ?
             WHERE id_kegiatan = ?'
        )->execute([$idPegawai, $jenis, $nama, $penyelenggara ?: null, $mulai, $selesai ?: null, $tempat ?: null, $filePath, $id]);

        if ($data['file_bukti'] && $data['file_bukti'] !== $filePath) {
            $oldFile = __DIR__ . '/../../' . ltrim($data['file_bukti'], '/');
            if (is_file($oldFile)) {
                unlink($oldFile);
            }
        }
        setFlash('success', 'Kegiatan hakim berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }

    if ($newFilePath && is_file(__DIR__ . '/../../' . $newFilePath)) {
        unlink(__DIR__ . '/../../' . $newFilePath);
    }
    $data = array_merge($data, $_POST);
    $data['file_bukti'] = $data['file_bukti'] ?? null;
}

$pageTitle = 'Edit kegiatan hakim';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm" style="max-width:600px;">
  <input type="hidden" name="id" value="<?= $id ?>">
  <div class="mb-3">
    <label class="form-label">Hakim</label>
    <select name="id_pegawai" class="form-select" required>
      <?php foreach ($hakimList as $hakim): ?>
      <option value="<?= (int) $hakim['id_pegawai'] ?>" <?= (int) ($data['id_pegawai'] ?? 0) === (int) $hakim['id_pegawai'] ? 'selected' : '' ?>><?= htmlspecialchars($hakim['nama_lengkap']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Jenis kegiatan</label>
        <select name="jenis_kegiatan" id="jenis_kegiatan" class="form-select" required>
            <option value="">-- pilih jenis kegiatan --</option>
      <?php foreach ($jenisKegiatan as $jenis): ?>
            <option value="<?= htmlspecialchars($jenis) ?>" <?= ($_POST['jenis_kegiatan'] ?? $data['jenis_kegiatan'] ?? '') === $jenis ? 'selected' : '' ?>><?= htmlspecialchars($jenis) ?></option>
      <?php endforeach; ?>
            <option value="__lainnya__" <?= ($_POST['jenis_kegiatan'] ?? '') === '__lainnya__' ? 'selected' : '' ?>>Lainnya</option>
    </select>
  </div>
    <div class="mb-3 <?= ($_POST['jenis_kegiatan'] ?? '') === '__lainnya__' ? '' : 'd-none' ?>" id="jenis-kegiatan-baru-wrapper">
        <label class="form-label" for="jenis_kegiatan_baru">Jenis kegiatan lainnya</label>
        <input type="text" name="jenis_kegiatan_baru" id="jenis_kegiatan_baru" class="form-control" maxlength="100" value="<?= htmlspecialchars($_POST['jenis_kegiatan_baru'] ?? '') ?>">
    </div>
  <div class="mb-3"><label class="form-label">Nama kegiatan</label><input type="text" name="nama_kegiatan" class="form-control" maxlength="200" value="<?= htmlspecialchars($data['nama_kegiatan'] ?? '') ?>" required></div>
  <div class="mb-3"><label class="form-label">Penyelenggara</label><input type="text" name="penyelenggara" class="form-control" maxlength="150" value="<?= htmlspecialchars($data['penyelenggara'] ?? '') ?>"></div>
  <div class="mb-3"><label class="form-label">Tanggal mulai</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($data['tanggal_mulai'] ?? '') ?>" required></div>
  <div class="mb-3"><label class="form-label">Tanggal selesai</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= htmlspecialchars($data['tanggal_selesai'] ?? '') ?>"></div>
  <div class="mb-3"><label class="form-label">Tempat</label><input type="text" name="tempat" class="form-control" maxlength="150" value="<?= htmlspecialchars($data['lokasi'] ?? '') ?>"></div>
  <div class="mb-3">
    <label class="form-label">Surat tugas</label>
    <?php if (!empty($data['file_bukti'])): ?>
    <div class="mb-2"><a href="<?= htmlspecialchars(BASE_URL . '/' . ltrim($data['file_bukti'], '/')) ?>" target="_blank" rel="noopener">Lihat lampiran saat ini</a></div>
    <div class="form-check mb-2"><input type="checkbox" name="hapus_lampiran" value="1" class="form-check-input" id="hapus_lampiran"><label class="form-check-label" for="hapus_lampiran">Hapus lampiran saat ini</label></div>
    <?php endif; ?>
    <input type="file" name="surat_tugas" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg">
    <small class="text-muted">Format PDF, Word, atau JPG. Maksimal 5 MB. Kosongkan jika tidak mengganti lampiran.</small>
  </div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
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