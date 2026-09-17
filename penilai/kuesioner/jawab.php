<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$idWawancara = (int) ($_GET['id'] ?? 0);
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
$jabatanPlaceholders = $jabatanIds ? implode(',', array_fill(0, count($jabatanIds), '?')) : 'NULL';
$stmt = $pdo->prepare(
    "SELECT w.id_wawancara, p.nama_lengkap, k.judul_kuesioner, j.nama_jabatan
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
     JOIN jabatan j ON j.id_jabatan = k.id_jabatan_dinilai
     WHERE w.id_wawancara = ? AND k.id_jabatan_dinilai IN ($jabatanPlaceholders)"
);
$stmt->execute(array_merge([$idWawancara], $jabatanIds));
$sesi = $stmt->fetch();
if (!$sesi) {
    setFlash('error', 'Kuesioner tidak ditemukan.');
    header('Location: index.php');
    exit;
}
$questionCountStmt = $pdo->prepare('SELECT COUNT(*) FROM pertanyaan_kuesioner WHERE id_kuesioner = (SELECT id_kuesioner FROM wawancara WHERE id_wawancara = ?)');
$questionCountStmt->execute([$idWawancara]);
$questionCount = (int) $questionCountStmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scores = $_POST['nilai'] ?? [];
    $questions = $_POST['pertanyaan'] ?? [];
    $status = '';
    $file = $_FILES['file_bukti'] ?? null;
    $path = null;
    $canSave = true;
    if (!$questions || count($scores) !== count($questions) || array_filter($scores, static function ($score) {
        return filter_var($score, FILTER_VALIDATE_INT) === false || (int) $score < 0 || (int) $score > 100;
    })) {
        setFlash('error', 'Semua pertanyaan wajib diisi dan diberi nilai 0 sampai 100.');
        $canSave = false;
    } elseif (array_filter($questions, static function ($question) {
        return trim($question) === '';
    })) {
        setFlash('error', 'Semua pertanyaan wajib diisi.');
        $canSave = false;
    } elseif ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extensions = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
        if ($file['error'] !== UPLOAD_ERR_OK || !isset($extensions[$mime]) || $file['size'] > 5 * 1024 * 1024) {
            setFlash('error', 'Bukti harus PDF/JPG/PNG dan maksimal 5 MB.');
            $canSave = false;
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
    if ($canSave && (!$file || $file['error'] === UPLOAD_ERR_NO_FILE || $path !== null)) {
        $average = array_sum(array_map('intval', $scores)) / count($scores);
        $status = $average <= 30 ? 'Tidak Kompeten' : ($average <= 70 ? 'Cukup' : 'Kompeten');
        $pdo->prepare(
            'INSERT INTO hasil_kuesioner (id_wawancara, file_bukti, daftar_nilai, status_kompetensi)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE file_bukti = COALESCE(VALUES(file_bukti), file_bukti),
             daftar_nilai = VALUES(daftar_nilai),
             status_kompetensi = VALUES(status_kompetensi)'
        )->execute([$idWawancara, $path, json_encode(array_values($scores)), $status]);
        $updateQuestions = $pdo->prepare('UPDATE pertanyaan_kuesioner SET teks_pertanyaan = ? WHERE id_kuesioner = (SELECT id_kuesioner FROM wawancara WHERE id_wawancara = ?) AND nomor_urut = ?');
        foreach (array_values($questions) as $index => $question) {
            if ($index < $questionCount) {
                $updateQuestions->execute([trim($question), $idWawancara, $index + 1]);
            } else {
                $addQuestion = $pdo->prepare('INSERT INTO pertanyaan_kuesioner (id_kuesioner, nomor_urut, teks_pertanyaan) VALUES ((SELECT id_kuesioner FROM wawancara WHERE id_wawancara = ?), ?, ?)');
                $addQuestion->execute([$idWawancara, $index + 1, trim($question)]);
            }
        }
        setFlash('success', 'Data kuesioner berhasil disimpan.');
        header('Location: index.php');
        exit;
    }
}

$stmt = $pdo->prepare('SELECT file_bukti, daftar_nilai, status_kompetensi FROM hasil_kuesioner WHERE id_wawancara = ?');
$stmt->execute([$idWawancara]);
$hasil = $stmt->fetch() ?: ['file_bukti' => null, 'daftar_nilai' => null, 'status_kompetensi' => 'Tidak Kompeten'];
$nilaiTersimpan = $hasil['daftar_nilai'] ? json_decode($hasil['daftar_nilai'], true) : [];
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
<style>
.kuesioner-form .question-row { border: 1px solid #dee2e6; border-radius: .25rem; padding: .5rem; margin-bottom: .5rem; }
.kuesioner-form .question-text { font-size: 1rem; line-height: 1.6; }
</style>
<h2 class="h4 mb-3">Kuesioner: <?= htmlspecialchars($sesi['nama_lengkap']) ?></h2>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm kuesioner-form">
    <div class="mb-3"><label class="form-label">Kompetensi / Jabatan</label><div class="form-control bg-light"><?= htmlspecialchars($sesi['nama_jabatan']) ?></div></div>
    <div class="mb-3">
        <label class="form-label">Daftar Pertanyaan dan Nilai</label>
        <div id="daftar-pertanyaan">
        <?php foreach ($pertanyaan as $item): ?>
            <div class="question-row"><input name="pertanyaan[]" class="form-control mb-2 question-text" value="<?= htmlspecialchars($item['teks_pertanyaan']) ?>" required><input type="number" name="nilai[]" class="form-control" min="0" max="100" step="1" value="<?= isset($nilaiTersimpan[$item['nomor_urut'] - 1]) ? (int) $nilaiTersimpan[$item['nomor_urut'] - 1] : '' ?>" placeholder="Nilai 0-100" required><small class="text-muted">0-30 Tidak Kompeten, 31-70 Cukup, 71-100 Kompeten</small></div>
        <?php endforeach; ?>
        </div>
        <button type="button" id="tambah-pertanyaan" class="btn btn-outline-secondary">+ Tambah pertanyaan</button>
    </div>
    <div class="mb-3">
        <label class="form-label">Bukti Kompetensi</label>
        <input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        <small class="text-muted">Satu file PDF/JPG/PNG, maksimal 5 MB.</small>
        <?php if ($hasil['file_bukti']): ?><div><a target="_blank" rel="noopener" href="<?= BASE_URL . '/' . htmlspecialchars($hasil['file_bukti']) ?>">Lihat file saat ini</a></div><?php endif; ?>
    </div>
    <button class="btn btn-primary">Simpan data</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
<script>
document.getElementById('tambah-pertanyaan').addEventListener('click', function () {
    var row = document.querySelector('.question-row').cloneNode(true);
    row.querySelectorAll('input').forEach(function (input) { input.value = ''; });
    document.getElementById('daftar-pertanyaan').appendChild(row);
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
