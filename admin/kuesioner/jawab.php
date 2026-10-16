<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
require_once __DIR__ . '/../../includes/penilaian_delete.php';
require_once __DIR__ . '/../../includes/kuesioner_dokumentasi.php';
$pdo = Database::getConnection();
$idWawancara = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    'SELECT w.id_wawancara, p.nama_lengkap, k.id_kuesioner, k.judul_kuesioner, k.kompetensi
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
$questionStmt = $pdo->prepare(
    'SELECT id_pertanyaan, nomor_urut, teks_pertanyaan FROM pertanyaan_kuesioner
     WHERE id_kuesioner = (SELECT id_kuesioner FROM wawancara WHERE id_wawancara = ?)
     ORDER BY nomor_urut'
);
$questionStmt->execute([$idWawancara]);
$pertanyaan = $questionStmt->fetchAll();
$pertanyaanById = [];
foreach ($pertanyaan as $item) {
    $pertanyaanById[(int) $item['id_pertanyaan']] = $item;
}
$resultStmt = $pdo->prepare(
    'SELECT file_bukti, daftar_nilai, daftar_jawaban, status_kompetensi
     FROM hasil_kuesioner WHERE id_wawancara = ?'
);
$resultStmt->execute([$idWawancara]);
$hasil = $resultStmt->fetch() ?: [
    'file_bukti' => null, 'daftar_nilai' => null,
    'daftar_jawaban' => null, 'status_kompetensi' => 'Tidak Kompeten',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scores = $_POST['nilai'] ?? [];
    $questions = $_POST['pertanyaan'] ?? [];
    $questionIds = $_POST['id_pertanyaan'] ?? [];
    $answers = $_POST['jawaban'] ?? [];
    $submittedIds = [];
    $status = 'Tidak Kompeten';
    $file = $_FILES['file_bukti'] ?? null;
    $hapusDokumentasi = ($_POST['hapus_dokumentasi'] ?? '') === '1';
    $hapusFotoDokumentasi = $_POST['hapus_foto_dokumentasi'] ?? [];
    $hapusFileBukti = ($_POST['hapus_file_bukti'] ?? '') === '1';
    $dokumentasiPaths = [];
    $path = null;
    $oldEvidencePath = null;
    $canSave = true;
    $existingDocumentationStmt = $pdo->prepare(
        'SELECT id_dokumentasi FROM dokumentasi_kuesioner WHERE id_wawancara = ?'
    );
    $existingDocumentationStmt->execute([$idWawancara]);
    $existingDocumentationIds = array_map('strval', $existingDocumentationStmt->fetchAll(PDO::FETCH_COLUMN));
    if (!is_array($scores) || !is_array($questions) || !is_array($questionIds)
        || !$questions || count($scores) !== count($questions) || count($questionIds) !== count($questions)
        || array_filter($scores, static function ($score) {
        return $score !== '' && ($score === null || !is_scalar($score)
            || filter_var($score, FILTER_VALIDATE_INT) === false || (int) $score < 0 || (int) $score > 100);
    })) {
        setFlash('error', 'Pertanyaan wajib diisi. Nilai boleh dikosongkan atau diisi 0 sampai 100.');
        $canSave = false;
    } else {
        foreach ($questionIds as $questionId) {
            if ($questionId === '') {
                continue;
            }
            if (!is_scalar($questionId) || !ctype_digit((string) $questionId)
                || !isset($pertanyaanById[(int) $questionId])) {
                $canSave = false;
                break;
            }
            $submittedIds[] = (int) $questionId;
        }
        if (count(array_unique($submittedIds)) !== count($submittedIds)) {
            $canSave = false;
        }
        if (!$canSave) {
            setFlash('error', 'Daftar pertanyaan tidak valid. Muat ulang halaman dan coba lagi.');
        }
    }
    if ($canSave && (!is_array($answers) || count($answers) !== count($questions)
        || array_filter($answers, static function ($answer) { return !is_string($answer); }))) {
        setFlash('error', 'Data jawaban tidak valid.');
        $canSave = false;
    }
    if ($canSave && array_filter($questions, static function ($question) {
        return !is_string($question) || trim($question) === '';
    })) {
        setFlash('error', 'Semua pertanyaan wajib diisi.');
        $canSave = false;
    } elseif ($canSave && $file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = simpanUploadAman($file, 'kuesioner', UPLOAD_PDF_GAMBAR);
        if ($upload['status'] === 'ok') {
            $path = $upload['path'];
        } else {
            setFlash('error', 'Jawaban/bukti kompetensi harus PDF/JPG/PNG dan maksimal 5 MB.');
            $canSave = false;
        }
    }
    if ($canSave && isset($_POST['hapus_file_bukti'])
        && !in_array($_POST['hapus_file_bukti'], ['', '1'], true)) {
        setFlash('error', 'Pilihan penghapusan file jawaban/bukti kompetensi tidak valid.');
        $canSave = false;
    }
    if ($canSave && isset($_POST['hapus_dokumentasi'])
        && !in_array($_POST['hapus_dokumentasi'], ['', '1'], true)) {
        setFlash('error', 'Pilihan penghapusan dokumentasi tidak valid.');
        $canSave = false;
    }
    if ($canSave && (!is_array($hapusFotoDokumentasi)
        || array_filter($hapusFotoDokumentasi, static function ($id) use ($existingDocumentationIds) {
            return !is_scalar($id) || !ctype_digit((string) $id)
                || !in_array((string) $id, $existingDocumentationIds, true);
        }))) {
        setFlash('error', 'Pilihan penghapusan foto dokumentasi tidak valid.');
        $canSave = false;
    }
    if ($canSave) {
        $uploadDokumentasi = unggahFotoDokumentasi($_FILES['foto_dokumentasi'] ?? null, 'kuesioner');
        if ($uploadDokumentasi['error'] !== null) {
            setFlash('error', $uploadDokumentasi['error']);
            $canSave = false;
        } else {
            $dokumentasiPaths = $uploadDokumentasi['paths'];
        }
    }
    if (!$canSave) {
        hapusFileBuktiPenilaian($dokumentasiPaths);
        hapusFileBuktiPenilaian(array_filter([$path]));
    }
    $hapusFileBukti = $hapusFileBukti && $path === null;
    if ($canSave && ($hapusFileBukti || $path !== null) && $hasil['file_bukti']) {
        $oldEvidencePath = $hasil['file_bukti'];
    }
    if ($canSave && (!$file || $file['error'] === UPLOAD_ERR_NO_FILE || $path !== null)) {
        $scoredValues = array_values(array_filter($scores, static function ($score) {
            return $score !== '' && $score !== null;
        }));
        $average = $scoredValues ? array_sum(array_map('intval', $scoredValues)) / count($scoredValues) : null;
        if ($average !== null) {
            $status = $average <= 30 ? 'Tidak Kompeten' : ($average <= 70 ? 'Cukup' : 'Kompeten');
        }
        $idKuesioner = (int) $sesi['id_kuesioner'];
        $removedQuestions = array_values(array_filter($pertanyaan, static function ($item) use ($submittedIds) {
            return !in_array((int) $item['id_pertanyaan'], $submittedIds, true);
        }));
        usort($removedQuestions, static function ($left, $right) {
            return (int) $right['nomor_urut'] <=> (int) $left['nomor_urut'];
        });

        $dokumentasiDihapus = [];
        try {
        $pdo->beginTransaction();
        if ($hapusDokumentasi) {
            $documentationStmt = $pdo->prepare('SELECT file_dokumentasi FROM dokumentasi_kuesioner WHERE id_wawancara = ?');
            $documentationStmt->execute([$idWawancara]);
            $dokumentasiDihapus = $documentationStmt->fetchAll(PDO::FETCH_COLUMN);
            $pdo->prepare('DELETE FROM dokumentasi_kuesioner WHERE id_wawancara = ?')->execute([$idWawancara]);
        } else {
            $getDocumentation = $pdo->prepare(
                'SELECT file_dokumentasi FROM dokumentasi_kuesioner
                 WHERE id_dokumentasi = ? AND id_wawancara = ?'
            );
            $deleteDocumentation = $pdo->prepare(
                'DELETE FROM dokumentasi_kuesioner WHERE id_dokumentasi = ? AND id_wawancara = ?'
            );
            foreach (array_unique(array_map('intval', $hapusFotoDokumentasi)) as $documentationId) {
                $getDocumentation->execute([$documentationId, $idWawancara]);
                $documentationPath = $getDocumentation->fetchColumn();
                if ($documentationPath !== false) {
                    $dokumentasiDihapus[] = $documentationPath;
                    $deleteDocumentation->execute([$documentationId, $idWawancara]);
                }
            }
        }
        if ($removedQuestions) {
            $resultsStmt = $pdo->prepare(
                'SELECT h.id_hasil, h.daftar_nilai, h.daftar_jawaban
                 FROM hasil_kuesioner h
                 JOIN wawancara w ON w.id_wawancara = h.id_wawancara
                 WHERE w.id_kuesioner = ?'
            );
            $resultsStmt->execute([$idKuesioner]);
            $scoreRows = $resultsStmt->fetchAll();
            foreach ($scoreRows as $scoreRow) {
                $savedScores = json_decode($scoreRow['daftar_nilai'] ?? '', true);
                if (is_array($savedScores)) {
                    foreach ($removedQuestions as $removedQuestion) {
                        $scoreIndex = (int) $removedQuestion['nomor_urut'] - 1;
                        if (array_key_exists($scoreIndex, $savedScores)) {
                            array_splice($savedScores, $scoreIndex, 1);
                        }
                    }
                }
                $savedAnswers = json_decode($scoreRow['daftar_jawaban'] ?? '', true);
                if (is_array($savedAnswers)) {
                    foreach ($removedQuestions as $removedQuestion) {
                        unset($savedAnswers[(string) $removedQuestion['id_pertanyaan']]);
                    }
                }
                if (is_array($savedScores)) {
                    $savedScoredValues = array_values(array_filter($savedScores, static function ($score) {
                        return $score !== '' && $score !== null;
                    }));
                    if ($savedScoredValues) {
                        $savedAverage = array_sum(array_map('intval', $savedScoredValues)) / count($savedScoredValues);
                        $savedStatus = $savedAverage <= 30 ? 'Tidak Kompeten' : ($savedAverage <= 70 ? 'Cukup' : 'Kompeten');
                        $pdo->prepare('UPDATE hasil_kuesioner SET daftar_nilai = ?, status_kompetensi = ? WHERE id_hasil = ?')
                            ->execute([json_encode(array_values($savedScores)), $savedStatus, $scoreRow['id_hasil']]);
                    } else {
                        $pdo->prepare('UPDATE hasil_kuesioner SET daftar_nilai = ? WHERE id_hasil = ?')
                            ->execute([json_encode(array_values($savedScores)), $scoreRow['id_hasil']]);
                    }
                }
                if (is_array($savedAnswers)) {
                    $pdo->prepare('UPDATE hasil_kuesioner SET daftar_jawaban = ? WHERE id_hasil = ?')
                        ->execute([json_encode($savedAnswers, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE), $scoreRow['id_hasil']]);
                }
            }
            $deleteQuestion = $pdo->prepare('DELETE FROM pertanyaan_kuesioner WHERE id_pertanyaan = ? AND id_kuesioner = ?');
            foreach ($removedQuestions as $removedQuestion) {
                $deleteQuestion->execute([$removedQuestion['id_pertanyaan'], $idKuesioner]);
            }
        }

        $updateQuestion = $pdo->prepare('UPDATE pertanyaan_kuesioner SET nomor_urut = ?, teks_pertanyaan = ? WHERE id_pertanyaan = ? AND id_kuesioner = ?');
        $insertQuestion = $pdo->prepare('INSERT INTO pertanyaan_kuesioner (id_kuesioner, nomor_urut, teks_pertanyaan) VALUES (?, ?, ?)');
        $savedAnswers = [];
        foreach (array_values($questions) as $index => $question) {
            $questionId = $questionIds[$index];
            if ($questionId === '') {
                $insertQuestion->execute([$idKuesioner, $index + 1, trim($question)]);
                $questionId = (int) $pdo->lastInsertId();
            } else {
                $updateQuestion->execute([$index + 1, trim($question), (int) $questionId, $idKuesioner]);
                $questionId = (int) $questionId;
            }
            $savedAnswers[(string) $questionId] = trim($answers[$index]);
        }
        $pdo->prepare(
            'INSERT INTO hasil_kuesioner (id_wawancara, file_bukti, daftar_nilai, daftar_jawaban, status_kompetensi)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
             file_bukti = CASE WHEN ? = 1 THEN NULL ELSE COALESCE(VALUES(file_bukti), file_bukti) END,
             daftar_nilai = VALUES(daftar_nilai),
             daftar_jawaban = VALUES(daftar_jawaban),
             status_kompetensi = VALUES(status_kompetensi)'
        )->execute([
            $idWawancara, $path, json_encode(array_map(static function ($score) {
                return $score === '' || $score === null ? null : (int) $score;
            }, array_values($scores))),
            json_encode($savedAnswers, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
            $status, $hapusFileBukti ? 1 : 0,
        ]);
        $insertDocumentation = $pdo->prepare(
            'INSERT INTO dokumentasi_kuesioner (id_wawancara, file_dokumentasi)
             VALUES (?, ?)'
        );
        foreach ($dokumentasiPaths as $documentationPath) {
            $insertDocumentation->execute([$idWawancara, $documentationPath]);
        }
        $pdo->commit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            hapusFileBuktiPenilaian(array_merge($dokumentasiPaths, array_filter([$path])));
            throw $exception;
        }
        hapusFileBuktiPenilaian(array_merge($dokumentasiDihapus, $oldEvidencePath && $oldEvidencePath !== $path ? [$oldEvidencePath] : []));
        setFlash('success', 'Data kuesioner berhasil disimpan.');
        header('Location: index.php');
        exit;
    }
}

$documentationStmt = $pdo->prepare(
    'SELECT id_dokumentasi, file_dokumentasi FROM dokumentasi_kuesioner
     WHERE id_wawancara = ? ORDER BY id_dokumentasi'
);
$documentationStmt->execute([$idWawancara]);
$dokumentasi = $documentationStmt->fetchAll();
$nilaiTersimpan = $hasil['daftar_nilai'] ? json_decode($hasil['daftar_nilai'], true) : [];
$jawabanTersimpan = $hasil['daftar_jawaban'] ? json_decode($hasil['daftar_jawaban'], true) : [];
if (!is_array($jawabanTersimpan)) {
    $jawabanTersimpan = [];
}
$pageTitle = 'Isi kuesioner';
require_once __DIR__ . '/../../includes/header.php';
?>
<style>
.kuesioner-form .question-row { border: 1px solid #dee2e6; border-radius: .25rem; padding: .5rem; margin-bottom: .5rem; }
.kuesioner-form .question-text { font-size: 1rem; line-height: 1.6; }
</style>
<h2 class="h4 mb-3">Kuesioner: <?= htmlspecialchars($sesi['nama_lengkap']) ?></h2>
<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm kuesioner-form"><?= csrfField() ?>
    <div class="mb-3"><label class="form-label">Kompetensi / Jabatan</label><div class="form-control bg-light"><?= htmlspecialchars($sesi['kompetensi']) ?></div></div>
    <div class="mb-3">
        <label class="form-label">Daftar Pertanyaan, Jawaban, dan Nilai</label>
        <small class="form-text text-muted mb-2">Nilai boleh dikosongkan dan diisi nanti. Jawaban teks, file jawaban/bukti kompetensi, dan dokumentasi dapat disimpan terpisah.</small>
        <div id="daftar-pertanyaan">
        <?php foreach ($pertanyaan as $item): ?>
            <div class="question-row"><input type="hidden" name="id_pertanyaan[]" value="<?= (int) $item['id_pertanyaan'] ?>"><input name="pertanyaan[]" class="form-control mb-2 question-text" value="<?= htmlspecialchars($item['teks_pertanyaan']) ?>" required><textarea name="jawaban[]" class="form-control mb-2" rows="3" placeholder="Ketik jawaban di sini"><?= htmlspecialchars((string) ($jawabanTersimpan[(string) $item['id_pertanyaan']] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea><input type="number" name="nilai[]" class="form-control mb-2" min="0" max="100" step="1" value="<?= isset($nilaiTersimpan[$item['nomor_urut'] - 1]) ? (int) $nilaiTersimpan[$item['nomor_urut'] - 1] : '' ?>" placeholder="Nilai 0-100"><small class="text-muted">Opsional: 0-30 Tidak Kompeten, 31-70 Cukup, 71-100 Kompeten</small><button type="button" class="btn btn-sm btn-outline-danger hapus-pertanyaan table-action-btn" title="Hapus pertanyaan" aria-label="Hapus pertanyaan"><i class="fas fa-trash-alt" aria-hidden="true"></i></button></div>
        <?php endforeach; ?>
        </div>
        <button type="button" id="tambah-pertanyaan" class="btn btn-outline-secondary">+ Tambah pertanyaan</button>
    </div>
    <div class="mb-3">
        <label class="form-label">Dokumentasi (foto)</label>
        <input type="file" name="foto_dokumentasi[]" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" multiple>
        <small class="text-muted">Foto JPG/PNG, maksimal 5 MB per foto dan 10 foto per unggahan.</small>
        <?php foreach ($dokumentasi as $foto): ?>
            <div>
                <a target="_blank" rel="noopener" href="<?= htmlspecialchars(fileUrl($foto['file_dokumentasi'])) ?>">Lihat foto dokumentasi</a>
                <label class="form-check-label ml-2"><input type="checkbox" name="hapus_foto_dokumentasi[]" value="<?= (int) $foto['id_dokumentasi'] ?>"> Hapus foto ini saat disimpan</label>
            </div>
        <?php endforeach; ?>
        <?php if ($dokumentasi): ?>
            <div class="form-check mt-2"><input type="checkbox" name="hapus_dokumentasi" value="1" class="form-check-input" id="hapus-dokumentasi"><label class="form-check-label" for="hapus-dokumentasi">Hapus semua foto dokumentasi saat disimpan</label></div>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Jawaban / Bukti Kompetensi</label>
        <input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        <small class="text-muted">Satu file PDF/JPG/PNG, maksimal 5 MB.</small>
        <?php if ($hasil['file_bukti']): ?>
            <div><a target="_blank" rel="noopener" href="<?= htmlspecialchars(fileUrl($hasil['file_bukti'])) ?>">Lihat file saat ini</a></div>
            <div class="form-check mt-2"><input type="checkbox" name="hapus_file_bukti" value="1" class="form-check-input" id="hapus-file-bukti"><label class="form-check-label" for="hapus-file-bukti">Hapus file jawaban/bukti kompetensi saat disimpan</label></div>
        <?php endif; ?>
    </div>
    <button class="btn btn-primary">Simpan data</button>
    <a href="index.php" class="btn btn-secondary">Kembali</a>
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
    var row = document.querySelector('.question-row').cloneNode(true);
    row.querySelectorAll('input, textarea').forEach(function (input) { input.value = ''; });
    daftarPertanyaan.appendChild(row);
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
