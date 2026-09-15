<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$idWawancara = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT w.id_wawancara, p.nama_lengkap FROM wawancara w JOIN pegawai p ON p.id_pegawai = w.id_pegawai WHERE w.id_wawancara = ? AND w.id_user_penilai = ?');
$stmt->execute([$idWawancara, $user['id_user']]);
$sesi = $stmt->fetch();
if (!$sesi) { setFlash('error', 'Wawancara tidak ditemukan.'); header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status_kompetensi'] ?? '';
    $file = $_FILES['file_bukti'] ?? null;
    $path = null;
    if (!in_array($status, ['Kompeten', 'Cukup', 'Tidak Kompeten'], true)) {
        setFlash('error', 'Status kompetensi tidak valid.');
    } elseif ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        if ($file['error'] !== UPLOAD_ERR_OK || !isset($extensions[$mime]) || $file['size'] > 5 * 1024 * 1024) {
            setFlash('error', 'Bukti harus JPG/PNG dan maksimal 5 MB.');
        } else {
            $directory = __DIR__ . '/../../assets/uploads/wawancara';
            if (!is_dir($directory)) mkdir($directory, 0755, true);
            $path = 'assets/uploads/wawancara/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
            if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../' . $path)) $path = null;
        }
    }
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE || $path !== null) {
        if ($path) {
            $pdo->prepare('UPDATE hasil_wawancara SET file_bukti = ?, status_kompetensi = ? WHERE id_wawancara = ?')
                ->execute([$path, $status, $idWawancara]);
        } else {
            $pdo->prepare('UPDATE hasil_wawancara SET status_kompetensi = ? WHERE id_wawancara = ?')
                ->execute([$status, $idWawancara]);
        }
        setFlash('success', 'Data wawancara berhasil disimpan.');
        header('Location: jawab.php?id=' . $idWawancara); exit;
    }
}
$stmt = $pdo->prepare('SELECT file_bukti, status_kompetensi FROM hasil_wawancara WHERE id_wawancara = ? ORDER BY id_hasil LIMIT 1');
$stmt->execute([$idWawancara]);
$hasil = $stmt->fetch() ?: ['file_bukti' => null, 'status_kompetensi' => 'Tidak Kompeten'];
$stmt = $pdo->prepare('SELECT kompetensi, isi_penilaian FROM hasil_wawancara WHERE id_wawancara = ? ORDER BY id_hasil');
$stmt->execute([$idWawancara]);
$details = $stmt->fetchAll();
$pageTitle = 'Isi wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<h2 class="h4 mb-3">Wawancara: <?= htmlspecialchars($sesi['nama_lengkap']) ?></h2>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3"><label class="form-label">Kompetensi / Jabatan</label><?php foreach ($details as $item): ?><div class="form-control bg-light mb-2"><?= htmlspecialchars($item['kompetensi']) ?></div><?php endforeach; ?></div>
    <div class="mb-3"><label class="form-label">Daftar Pertanyaan</label><?php foreach ($details as $i => $item): ?><div class="form-control bg-light mb-2"><?= $i + 1 ?>. <?= htmlspecialchars($item['isi_penilaian']) ?></div><?php endforeach; ?></div>
    <div class="mb-3"><label class="form-label">Bukti Kompetensi</label><input type="file" name="file_bukti" class="form-control" accept=".jpg,.jpeg,.png"><small class="text-muted">Satu file JPG/PNG, maksimal 5 MB.</small><?php if ($hasil['file_bukti']): ?><div><a target="_blank" rel="noopener" href="<?= BASE_URL . '/' . htmlspecialchars($hasil['file_bukti']) ?>">Lihat file saat ini</a></div><?php endif; ?></div>
    <div class="mb-3"><label class="form-label">Kompeten / Cukup / Tidak Kompeten</label><select name="status_kompetensi" class="form-select"><?php foreach (['Kompeten', 'Cukup', 'Tidak Kompeten'] as $status): ?><option value="<?= $status ?>" <?= $hasil['status_kompetensi'] === $status ? 'selected' : '' ?>><?= $status ?></option><?php endforeach; ?></select></div>
    <button class="btn btn-primary">Simpan data</button> <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
