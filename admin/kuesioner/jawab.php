<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$idWawancara = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    'SELECT w.id_wawancara, p.nama_lengkap, k.judul_kuesioner
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
     WHERE w.id_wawancara = ?'
);
$stmt->execute([$idWawancara]);
$sesi = $stmt->fetch();
if (!$sesi) {
    setFlash('error', 'Kuesioner tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status_kompetensi'] ?? '';
    $file = $_FILES['file_bukti'] ?? null;
    $path = null;
    if (!in_array($status, ['Kompeten', 'Cukup', 'Tidak Kompeten'], true)) {
        setFlash('error', 'Status kompetensi tidak valid.');
    } elseif ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extensions = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
        if ($file['error'] !== UPLOAD_ERR_OK || !isset($extensions[$mime]) || $file['size'] > 5 * 1024 * 1024) {
            setFlash('error', 'Bukti harus PDF/JPG/PNG dan maksimal 5 MB.');
        } else {
            $directory = __DIR__ . '/../../assets/uploads/kuesioner';
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $path = 'assets/uploads/kuesioner/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
            if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../' . $path)) {
                $path = null;
                setFlash('error', 'Bukti gagal diunggah.');
            }
        }
    }
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE || $path !== null) {
        $pdo->prepare(
            'INSERT INTO hasil_kuesioner (id_wawancara, file_bukti, status_kompetensi)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE file_bukti = COALESCE(VALUES(file_bukti), file_bukti),
             status_kompetensi = VALUES(status_kompetensi)'
        )->execute([$idWawancara, $path, $status]);
        setFlash('success', 'Data kuesioner berhasil disimpan.');
        header('Location: jawab.php?id=' . $idWawancara);
        exit;
    }
}

$stmt = $pdo->prepare(
    'SELECT h.file_bukti, h.status_kompetensi
     FROM hasil_kuesioner h WHERE h.id_wawancara = ?'
);
$stmt->execute([$idWawancara]);
$hasil = $stmt->fetch() ?: ['file_bukti' => null, 'status_kompetensi' => 'Tidak Kompeten'];
$stmt = $pdo->prepare(
    'SELECT nomor_urut, teks_pertanyaan FROM pertanyaan_kuesioner
     WHERE id_kuesioner = (SELECT id_kuesioner FROM wawancara WHERE id_wawancara = ?)
     ORDER BY nomor_urut'
);
$stmt->execute([$idWawancara]);
$pertanyaan = $stmt->fetchAll();
$pageTitle = 'Isi kuesioner';
require_once __DIR__ . '/../../includes/header.php';
?>
<h2 class="h4 mb-3"><?= htmlspecialchars($sesi['nama_lengkap']) ?> - <?= htmlspecialchars($sesi['judul_kuesioner']) ?></h2>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3">
        <label class="form-label">Daftar Pertanyaan</label>
        <?php foreach ($pertanyaan as $item): ?>
            <div class="form-control bg-light mb-2"><?= (int) $item['nomor_urut'] ?>. <?= htmlspecialchars($item['teks_pertanyaan']) ?></div>
        <?php endforeach; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Bukti Kompetensi</label>
        <input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        <small class="text-muted">Satu file PDF/JPG/PNG, maksimal 5 MB.</small>
        <?php if ($hasil['file_bukti']): ?><div><a target="_blank" rel="noopener" href="<?= BASE_URL . '/' . htmlspecialchars($hasil['file_bukti']) ?>">Lihat file saat ini</a></div><?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Kompeten / Cukup / Tidak Kompeten</label>
        <select name="status_kompetensi" class="form-select">
            <?php foreach (['Kompeten', 'Cukup', 'Tidak Kompeten'] as $status): ?>
                <option value="<?= $status ?>" <?= $hasil['status_kompetensi'] === $status ? 'selected' : '' ?>><?= $status ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-primary">Simpan data</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
