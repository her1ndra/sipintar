<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
require_once __DIR__ . '/../../includes/penilaian_delete.php';
require_once __DIR__ . '/../../includes/kuesioner_dokumentasi.php';
$pdo = Database::getConnection();
$user = currentUser();
$idWawancara = (int) ($_GET['id'] ?? 0);
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
$jabatanPlaceholders = $jabatanIds ? implode(',', array_fill(0, count($jabatanIds), '?')) : 'NULL';
$stmt = $pdo->prepare("SELECT w.id_wawancara, p.nama_lengkap, j.nama_jabatan
    FROM wawancara w
    JOIN pegawai p ON p.id_pegawai = w.id_pegawai
    JOIN jabatan j ON j.id_jabatan = p.id_jabatan
    WHERE w.id_wawancara = ? AND p.id_jabatan IN ($jabatanPlaceholders)");
$stmt->execute(array_merge([$idWawancara], $jabatanIds));
$sesi = $stmt->fetch();
if (!$sesi) { setFlash('error', 'Wawancara tidak ditemukan.'); header('Location: index.php'); exit; }
$competencyStmt = $pdo->prepare('SELECT kompetensi FROM hasil_wawancara WHERE id_wawancara = ? ORDER BY id_hasil LIMIT 1');
$competencyStmt->execute([$idWawancara]);
$competency = $competencyStmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scores = is_array($_POST['nilai'] ?? null) ? $_POST['nilai'] : [];
    $questions = is_array($_POST['pertanyaan'] ?? null) ? $_POST['pertanyaan'] : [];
    $files = $_FILES['file_bukti'] ?? null;
    $uploadedPaths = [];
    $documentationPaths = [];
    $canSave = true;
    $stmt = $pdo->prepare('SELECT id_hasil, jawaban, file_bukti FROM hasil_wawancara WHERE id_wawancara = ?');
    $stmt->execute([$idWawancara]);
    $existingDetails = $stmt->fetchAll();
    $existingById = [];
    foreach ($existingDetails as $detail) {
        $existingById[(string) $detail['id_hasil']] = $detail;
    }
    $stmt = $pdo->prepare('SELECT file_bukti FROM bukti_wawancara WHERE id_wawancara = ?');
    $stmt->execute([$idWawancara]);
    $existingSessionFiles = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $validIds = array_keys($existingById);
    $questionIds = array_map('strval', array_filter(array_keys($questions), 'is_numeric'));
    $answers = is_array($_POST['jawaban'] ?? null) ? $_POST['jawaban'] : [];
    $deleteSessionFile = ($_POST['hapus_file_bukti'] ?? '') === '1';
    $deleteDocumentation = ($_POST['hapus_dokumentasi'] ?? '') === '1';
    $newQuestions = array_filter($questions, static function ($question, $id) {
        return !is_numeric($id) && trim($question) !== '';
    }, ARRAY_FILTER_USE_BOTH);
    $newScores = array_intersect_key($scores, $newQuestions);
    $submittedIds = array_map('strval', array_merge($questionIds, array_keys($newQuestions)));
    $scoreIds = array_map('strval', array_keys($scores));
    $removedIds = array_values(array_diff($validIds, $questionIds));
    if (!$submittedIds || array_diff($questionIds, $validIds) || array_filter($questions, static function ($question) {
        return trim($question) === '';
    })) {
        setFlash('error', 'Semua pertanyaan wajib diisi dengan benar.');
        $canSave = false;
    } elseif (array_diff($scoreIds, $submittedIds) || array_diff($submittedIds, $scoreIds)) {
        setFlash('error', 'Data pertanyaan tidak valid.');
        $canSave = false;
    } elseif (count($scores) !== count($submittedIds) || count($newScores) !== count($newQuestions)) {
        setFlash('error', 'Semua pertanyaan wajib diberi nilai.');
        $canSave = false;
    } elseif (array_filter($scores, static function ($score) {
        return filter_var($score, FILTER_VALIDATE_INT) === false || (int) $score < 0 || (int) $score > 100;
    })) {
        setFlash('error', 'Nilai harus berupa angka 0 sampai 100.');
        $canSave = false;
    } elseif (array_diff(array_map('strval', array_keys($answers)), $submittedIds)
        || array_diff($submittedIds, array_map('strval', array_keys($answers)))
        || array_filter($answers, static function ($answer) { return !is_string($answer); })
        || (isset($_POST['hapus_file_bukti']) && !in_array($_POST['hapus_file_bukti'], ['1'], true))
        || (isset($_POST['hapus_dokumentasi']) && !in_array($_POST['hapus_dokumentasi'], ['1'], true))) {
        setFlash('error', 'Data jawaban tidak valid.');
        $canSave = false;
    }

    if ($canSave && $files) {
        $upload = simpanUploadAman($files, 'wawancara', UPLOAD_PDF_GAMBAR);
        if ($upload['status'] === 'error') {
            setFlash('error', 'File jawaban harus PDF/JPG/PNG dan maksimal 5 MB.');
            $canSave = false;
        } elseif ($upload['status'] === 'ok') {
            $uploadedPaths[] = $upload['path'];
        }
    }
    if ($canSave) {
        $uploadDocumentation = unggahFotoDokumentasi($_FILES['foto_dokumentasi'] ?? null, 'wawancara');
        if ($uploadDocumentation['error'] !== null) {
            setFlash('error', $uploadDocumentation['error']);
            $canSave = false;
        } else {
            $documentationPaths = $uploadDocumentation['paths'];
        }
    }
    if ($canSave) {
        $hasTextAnswer = (bool) array_filter($answers, static function ($answer) {
            return trim($answer) !== '';
        });
        $hasLegacyDetailFile = (bool) array_filter($existingById, static function ($detail) use ($questionIds) {
            return in_array((string) $detail['id_hasil'], $questionIds, true) && !empty($detail['file_bukti']);
        });
        $hasExistingSessionFile = $existingSessionFiles && !$deleteSessionFile;
        if (!$hasTextAnswer && !$uploadedPaths && !$hasExistingSessionFile && !$hasLegacyDetailFile) {
            setFlash('error', 'Isi jawaban tertulis atau unggah satu file jawaban.');
            $canSave = false;
        }
    }
    if (!$canSave) {
        hapusFileBuktiPenilaian(array_merge($uploadedPaths, $documentationPaths));
    }
    if ($canSave) {
        $average = array_sum(array_map('intval', $scores)) / count($scores);
        $overallStatus = $average <= 30 ? 'Tidak Kompeten' : ($average <= 70 ? 'Cukup' : 'Kompeten');
        $deletedEvidence = [];
        $deletedDocumentation = [];
        try {
        $pdo->beginTransaction();
        if ($deleteDocumentation) {
            $documentationStmt = $pdo->prepare('SELECT file_dokumentasi FROM dokumentasi_wawancara WHERE id_wawancara = ?');
            $documentationStmt->execute([$idWawancara]);
            $deletedDocumentation = $documentationStmt->fetchAll(PDO::FETCH_COLUMN);
            $pdo->prepare('DELETE FROM dokumentasi_wawancara WHERE id_wawancara = ?')->execute([$idWawancara]);
        }
        $getEvidence = $pdo->prepare('SELECT file_bukti FROM hasil_wawancara WHERE id_hasil = ? AND id_wawancara = ?');
        $deleteQuestion = $pdo->prepare('DELETE FROM hasil_wawancara WHERE id_hasil = ? AND id_wawancara = ?');
        foreach ($removedIds as $idHasil) {
            $getEvidence->execute([$idHasil, $idWawancara]);
            $filePath = $getEvidence->fetchColumn();
            if ($filePath) {
                $deletedEvidence[] = $filePath;
            }
            $deleteQuestion->execute([$idHasil, $idWawancara]);
        }
        $update = $pdo->prepare(
            'UPDATE hasil_wawancara
             SET isi_penilaian = ?, jawaban = ?, nilai = ?, status_kompetensi = ?
             WHERE id_hasil = ? AND id_wawancara = ?'
        );
        foreach ($questions as $idHasil => $question) {
            if (is_numeric($idHasil)) {
                $jawaban = trim($answers[(string) $idHasil]);
                $update->execute([
                    trim($question), $jawaban !== '' ? $jawaban : null,
                    (int) $scores[$idHasil], $overallStatus, $idHasil, $idWawancara,
                ]);
            }
        }
        if ($newQuestions) {
            $addQuestion = $pdo->prepare(
                'INSERT INTO hasil_wawancara
                 (id_wawancara, kompetensi, isi_penilaian, jawaban, nilai, status_kompetensi)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            foreach ($newQuestions as $questionId => $question) {
                $score = (int) $newScores[$questionId];
                $jawaban = trim($answers[$questionId]);
                $addQuestion->execute([
                    $idWawancara, $competency, trim($question),
                    $jawaban !== '' ? $jawaban : null, $score, $overallStatus,
                ]);
            }
        }
        if ($uploadedPaths || ($deleteSessionFile && $existingSessionFiles)) {
            $deletedEvidence = array_merge($deletedEvidence, $existingSessionFiles);
            $pdo->prepare('DELETE FROM bukti_wawancara WHERE id_wawancara = ?')->execute([$idWawancara]);
            if ($uploadedPaths) {
                $pdo->prepare('INSERT INTO bukti_wawancara (id_wawancara, file_bukti) VALUES (?, ?)')
                    ->execute([$idWawancara, $uploadedPaths[0]]);
            }
        }
        $insertDocumentation = $pdo->prepare(
            'INSERT INTO dokumentasi_wawancara (id_wawancara, file_dokumentasi) VALUES (?, ?)'
        );
        foreach ($documentationPaths as $documentationPath) {
            $insertDocumentation->execute([$idWawancara, $documentationPath]);
        }
        $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            hapusFileBuktiPenilaian(array_merge($uploadedPaths, $documentationPaths));
            throw $exception;
        }
        hapusFileBuktiPenilaian(array_merge($deletedEvidence, $deletedDocumentation));
        setFlash('success', 'Data wawancara berhasil disimpan.');
        header('Location: index.php'); exit;
    }
}
$stmt = $pdo->prepare('SELECT file_bukti FROM bukti_wawancara WHERE id_wawancara = ? ORDER BY id_bukti');
$stmt->execute([$idWawancara]);
$buktiList = $stmt->fetchAll(PDO::FETCH_COLUMN);
$documentationStmt = $pdo->prepare(
    'SELECT id_dokumentasi, file_dokumentasi FROM dokumentasi_wawancara
     WHERE id_wawancara = ? ORDER BY id_dokumentasi'
);
$documentationStmt->execute([$idWawancara]);
$dokumentasi = $documentationStmt->fetchAll();
$stmt = $pdo->prepare('SELECT id_hasil, kompetensi, isi_penilaian, jawaban, file_bukti, nilai, status_kompetensi FROM hasil_wawancara WHERE id_wawancara = ? ORDER BY id_hasil');
$stmt->execute([$idWawancara]);
$details = $stmt->fetchAll();
$pageTitle = 'Isi wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<h2 class="h4 mb-3">Wawancara: <?= htmlspecialchars($sesi['nama_lengkap']) ?> (<?= htmlspecialchars($sesi['nama_jabatan']) ?>)</h2>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm"><?= csrfField() ?>
    <div class="mb-3"><label class="form-label">Kompetensi / Jabatan</label><div class="form-control bg-light"><?= htmlspecialchars($details[0]['kompetensi'] ?? '-') ?></div></div>
    <div class="mb-3"><label class="form-label">Pertanyaan, Jawaban, dan Nilai</label><small class="form-text text-muted mb-2">Minimal satu pertanyaan harus tersisa. Isi jawaban tertulis atau unggah satu file PDF/JPG/PNG (maksimal 5 MB).</small><div id="daftar-pertanyaan"><?php foreach ($details as $i => $item): ?><div class="border rounded p-2 mb-2 question-row"><input name="pertanyaan[<?= (int) $item['id_hasil'] ?>]" class="form-control mb-2" value="<?= htmlspecialchars($item['isi_penilaian']) ?>" required><textarea name="jawaban[<?= (int) $item['id_hasil'] ?>]" class="form-control mb-2" rows="3" placeholder="Ketik jawaban"><?= htmlspecialchars($item['jawaban'] ?? '') ?></textarea>    <?php if ($item['file_bukti']): ?><div class="small mb-2"><a target="_blank" rel="noopener" href="<?= htmlspecialchars(fileUrl($item['file_bukti'])) ?>">Lihat bukti lama</a></div><?php endif; ?><input type="number" name="nilai[<?= (int) $item['id_hasil'] ?>]" class="form-control mb-2" min="0" max="100" step="1" value="<?= $item['nilai'] !== null ? (int) $item['nilai'] : '' ?>" placeholder="Nilai 0-100" required><small class="text-muted">0-30 Tidak Kompeten, 31-70 Cukup, 71-100 Kompeten</small><div class="mt-2"><button type="button" class="btn btn-sm btn-outline-danger hapus-pertanyaan table-action-btn" title="Hapus pertanyaan" aria-label="Hapus pertanyaan"><i class="fas fa-trash-alt" aria-hidden="true"></i></button></div></div><?php endforeach; ?></div><button type="button" id="tambah-pertanyaan" class="btn btn-outline-secondary">+ Tambah pertanyaan</button></div>
    <div class="mb-3"><label class="form-label">File jawaban (opsional, satu file untuk wawancara ini)</label><input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png"><small class="text-muted">Pilih satu PDF/JPG/PNG maksimal 5 MB. File baru menggantikan file jawaban sebelumnya.</small><?php foreach ($buktiList as $bukti): ?><div><a target="_blank" rel="noopener" href="<?= htmlspecialchars(fileUrl($bukti)) ?>">Lihat file jawaban</a></div><?php endforeach; ?><?php if ($buktiList): ?><div class="form-check mt-2"><input type="checkbox" name="hapus_file_bukti" value="1" class="form-check-input" id="hapus-file-bukti"><label class="form-check-label" for="hapus-file-bukti">Hapus file jawaban saat disimpan</label></div><?php endif; ?></div>
    <div class="mb-3">
        <label class="form-label">Dokumentasi (foto)</label>
        <input type="file" name="foto_dokumentasi[]" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" multiple>
        <small class="text-muted">Foto JPG/PNG, maksimal 5 MB per foto dan 10 foto per unggahan.</small>
        <?php foreach ($dokumentasi as $foto): ?>
            <div><a target="_blank" rel="noopener" href="<?= htmlspecialchars(fileUrl($foto['file_dokumentasi'])) ?>">Lihat foto dokumentasi</a></div>
        <?php endforeach; ?>
        <?php if ($dokumentasi): ?>
            <div class="form-check mt-2"><input type="checkbox" name="hapus_dokumentasi" value="1" class="form-check-input" id="hapus-dokumentasi"><label class="form-check-label" for="hapus-dokumentasi">Hapus semua foto dokumentasi saat disimpan</label></div>
        <?php endif; ?>
    </div>
    <button class="btn btn-primary">Simpan data</button> <a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
<script>
var daftarPertanyaan = document.getElementById('daftar-pertanyaan');

function perbaruiTombolHapus() {
    var rows = daftarPertanyaan.querySelectorAll('.question-row');
    rows.forEach(function (row) {
        row.querySelector('.hapus-pertanyaan').disabled = rows.length <= 1;
    });
}

document.getElementById('tambah-pertanyaan').addEventListener('click', function () {
    var id = 'baru_' + Date.now();
    var wrapper = document.createElement('div');
    wrapper.className = 'border rounded p-2 mb-2 question-row';
    wrapper.innerHTML = '<input name="pertanyaan[' + id + ']" class="form-control mb-2" placeholder="Pertanyaan baru" required>' +
        '<textarea name="jawaban[' + id + ']" class="form-control mb-2" rows="3" placeholder="Ketik jawaban"></textarea>' +
        '<input type="number" name="nilai[' + id + ']" class="form-control mb-2" min="0" max="100" step="1" placeholder="Nilai 0-100" required>' +
        '<small class="text-muted">0-30 Tidak Kompeten, 31-70 Cukup, 71-100 Kompeten</small>' +
        '<div><button type="button" class="btn btn-sm btn-outline-danger hapus-pertanyaan table-action-btn" title="Hapus pertanyaan" aria-label="Hapus pertanyaan"><i class="fas fa-trash-alt" aria-hidden="true"></i></button></div>';
    daftarPertanyaan.appendChild(wrapper);
    perbaruiTombolHapus();
});

daftarPertanyaan.addEventListener('click', function (event) {
    if (event.target.closest('.hapus-pertanyaan') && daftarPertanyaan.querySelectorAll('.question-row').length > 1) {
        event.target.closest('.question-row').remove();
        perbaruiTombolHapus();
    }
});

perbaruiTombolHapus();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
