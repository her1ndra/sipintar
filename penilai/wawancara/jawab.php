<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$idWawancara = (int) ($_GET['id'] ?? 0);
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
$jabatanPlaceholders = $jabatanIds ? implode(',', array_fill(0, count($jabatanIds), '?')) : 'NULL';
$stmt = $pdo->prepare("SELECT w.id_wawancara, p.nama_lengkap
    FROM wawancara w
    JOIN pegawai p ON p.id_pegawai = w.id_pegawai
    WHERE w.id_wawancara = ? AND p.id_jabatan IN ($jabatanPlaceholders)");
$stmt->execute(array_merge([$idWawancara], $jabatanIds));
$sesi = $stmt->fetch();
if (!$sesi) { setFlash('error', 'Wawancara tidak ditemukan.'); header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scores = $_POST['nilai'] ?? [];
    $questions = $_POST['pertanyaan'] ?? [];
    $files = $_FILES['file_bukti'] ?? null;
    $uploadedPaths = [];
    $path = null;
    $canSave = true;
    $stmt = $pdo->prepare('SELECT id_hasil FROM hasil_wawancara WHERE id_wawancara = ?');
    $stmt->execute([$idWawancara]);
    $validIds = array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    $questionIds = array_filter(array_keys($questions), 'is_numeric');
    $newQuestions = array_filter($questions, static function ($question, $id) {
        return !is_numeric($id) && trim($question) !== '';
    }, ARRAY_FILTER_USE_BOTH);
    $newScores = array_intersect_key($scores, $newQuestions);
    if (!$validIds || array_diff($questionIds, $validIds) || array_filter($questions, static function ($question) {
        return trim($question) === '';
    })) {
        setFlash('error', 'Semua pertanyaan wajib diisi dengan benar.');
        $canSave = false;
    } elseif (array_diff(array_keys($scores), array_merge($validIds, array_keys($newQuestions)))) {
        setFlash('error', 'Data pertanyaan tidak valid.');
        $canSave = false;
    } elseif (count($scores) !== count($validIds) + count($newQuestions) || array_diff($validIds, array_keys($scores)) || count($newScores) !== count($newQuestions)) {
        setFlash('error', 'Semua pertanyaan wajib diberi nilai.');
        $canSave = false;
    } elseif (array_filter($scores, static function ($score) {
        return filter_var($score, FILTER_VALIDATE_INT) === false || (int) $score < 0 || (int) $score > 100;
    })) {
        setFlash('error', 'Nilai harus berupa angka 0 sampai 100.');
        $canSave = false;
    } elseif ($files && is_array($files['name'])) {
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        foreach ($files['name'] as $index => $name) {
            if ($files['error'][$index] === UPLOAD_ERR_NO_FILE) continue;
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($files['tmp_name'][$index]);
            if ($files['error'][$index] !== UPLOAD_ERR_OK || !isset($extensions[$mime]) || $files['size'][$index] > 5 * 1024 * 1024) {
                setFlash('error', 'Setiap bukti harus JPG/PNG dan maksimal 5 MB.');
                $canSave = false;
                break;
            }
            $directory = __DIR__ . '/../../assets/uploads/wawancara';
            if (!is_dir($directory)) mkdir($directory, 0755, true);
            $path = 'assets/uploads/wawancara/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
            if (!move_uploaded_file($files['tmp_name'][$index], __DIR__ . '/../../' . $path)) {
                setFlash('error', 'Bukti gagal diunggah.');
                $canSave = false;
                break;
            }
            $uploadedPaths[] = $path;
        }
    }
    if ($canSave) {
        $average = array_sum(array_map('intval', $scores)) / count($scores);
        $overallStatus = $average <= 30 ? 'Tidak Kompeten' : ($average <= 70 ? 'Cukup' : 'Kompeten');
        $update = $pdo->prepare(
            'UPDATE hasil_wawancara SET nilai = ?, status_kompetensi = ?
             WHERE id_hasil = ? AND id_wawancara = ?'
        );
        foreach ($scores as $idHasil => $score) {
            $score = (int) $score;
            $update->execute([$score, $overallStatus, $idHasil, $idWawancara]);
        }
        $editQuestion = $pdo->prepare('UPDATE hasil_wawancara SET isi_penilaian = ? WHERE id_hasil = ? AND id_wawancara = ?');
        foreach ($questions as $idHasil => $question) {
            if (is_numeric($idHasil)) {
                $editQuestion->execute([trim($question), $idHasil, $idWawancara]);
            }
        }
        if ($newQuestions) {
            $competencyStmt = $pdo->prepare('SELECT kompetensi FROM hasil_wawancara WHERE id_wawancara = ? ORDER BY id_hasil LIMIT 1');
            $competencyStmt->execute([$idWawancara]);
            $competency = $competencyStmt->fetchColumn();
            $addQuestion = $pdo->prepare('INSERT INTO hasil_wawancara (id_wawancara, kompetensi, isi_penilaian, nilai, status_kompetensi) VALUES (?, ?, ?, ?, ?)');
            foreach ($newQuestions as $questionId => $question) {
                $score = (int) $newScores[$questionId];
                $addQuestion->execute([$idWawancara, $competency, trim($question), $score, $overallStatus]);
            }
        }
        if ($uploadedPaths) {
            $addEvidence = $pdo->prepare('INSERT INTO bukti_wawancara (id_wawancara, file_bukti) VALUES (?, ?)');
            foreach ($uploadedPaths as $uploadedPath) {
                $addEvidence->execute([$idWawancara, $uploadedPath]);
            }
        }
        setFlash('success', 'Data wawancara berhasil disimpan.');
        header('Location: index.php'); exit;
    }
}
$stmt = $pdo->prepare('SELECT file_bukti FROM hasil_wawancara WHERE id_wawancara = ? ORDER BY id_hasil LIMIT 1');
$stmt->execute([$idWawancara]);
$hasil = $stmt->fetch() ?: ['file_bukti' => null];
$stmt = $pdo->prepare('SELECT file_bukti FROM bukti_wawancara WHERE id_wawancara = ? ORDER BY id_bukti');
$stmt->execute([$idWawancara]);
$buktiList = $stmt->fetchAll(PDO::FETCH_COLUMN);
$stmt = $pdo->prepare('SELECT id_hasil, kompetensi, isi_penilaian, nilai, status_kompetensi FROM hasil_wawancara WHERE id_wawancara = ? ORDER BY id_hasil');
$stmt->execute([$idWawancara]);
$details = $stmt->fetchAll();
$pageTitle = 'Isi wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<h2 class="h4 mb-3">Wawancara: <?= htmlspecialchars($sesi['nama_lengkap']) ?></h2>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3"><label class="form-label">Kompetensi / Jabatan</label><div class="form-control bg-light"><?= htmlspecialchars($details[0]['kompetensi'] ?? '-') ?></div></div>
    <div class="mb-3"><label class="form-label">Daftar Pertanyaan dan Nilai</label><div id="daftar-pertanyaan"><?php foreach ($details as $i => $item): ?><div class="border rounded p-2 mb-2"><input name="pertanyaan[<?= (int) $item['id_hasil'] ?>]" class="form-control mb-2" value="<?= htmlspecialchars($item['isi_penilaian']) ?>" required><input type="number" name="nilai[<?= (int) $item['id_hasil'] ?>]" class="form-control" min="0" max="100" step="1" value="<?= $item['nilai'] !== null ? (int) $item['nilai'] : '' ?>" placeholder="Nilai 0-100" required><small class="text-muted">0-30 Tidak Kompeten, 31-70 Cukup, 71-100 Kompeten</small></div><?php endforeach; ?></div><button type="button" id="tambah-pertanyaan" class="btn btn-outline-secondary">+ Tambah pertanyaan</button></div>
    <div class="mb-3"><label class="form-label">Bukti Kompetensi</label><input type="file" name="file_bukti[]" class="form-control" accept=".jpg,.jpeg,.png" multiple><small class="text-muted">Bisa memilih beberapa file JPG/PNG, masing-masing maksimal 5 MB.</small><?php if ($hasil['file_bukti']): ?><div><a target="_blank" rel="noopener" href="<?= BASE_URL . '/' . htmlspecialchars($hasil['file_bukti']) ?>">Lihat file saat ini</a></div><?php endif; ?><?php foreach ($buktiList as $bukti): ?><div><a target="_blank" rel="noopener" href="<?= BASE_URL . '/' . htmlspecialchars($bukti) ?>">Lihat bukti</a></div><?php endforeach; ?></div>
    <button class="btn btn-primary">Simpan data</button> <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
<script>
document.getElementById('tambah-pertanyaan').addEventListener('click', function () {
    var id = 'baru_' + Date.now();
    var wrapper = document.createElement('div');
    wrapper.className = 'border rounded p-2 mb-2';
    wrapper.innerHTML = '<input name="pertanyaan[' + id + ']" class="form-control mb-2" placeholder="Pertanyaan baru" required>' +
        '<input type="number" name="nilai[' + id + ']" class="form-control" min="0" max="100" step="1" placeholder="Nilai 0-100" required>' +
        '<small class="text-muted">0-30 Tidak Kompeten, 31-70 Cukup, 71-100 Kompeten</small>';
    document.getElementById('daftar-pertanyaan').appendChild(wrapper);
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
