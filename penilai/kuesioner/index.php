<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
$jabatanPlaceholders = $jabatanIds ? implode(',', array_fill(0, count($jabatanIds), '?')) : 'NULL';
$stmt = $pdo->prepare(
    "SELECT k.id_kuesioner, k.judul_kuesioner, k.tahun_periode, k.status,
            k.kompetensi, w.id_wawancara, w.tanggal_wawancara,
            COALESCE(p.nama_lengkap, p_target.nama_lengkap) AS nama_lengkap,
            COALESCE(j.nama_jabatan, j_target.nama_jabatan) AS nama_jabatan,
            COALESCE((
                SELECT GROUP_CONCAT(
                    DISTINCT CASE
                        WHEN atasan.nama_lengkap IS NULL THEN jabatan_atasan.nama_jabatan
                        ELSE CONCAT(atasan.nama_lengkap, ' (', jabatan_atasan.nama_jabatan, ')')
                    END
                    ORDER BY jabatan_atasan.id_jabatan
                    SEPARATOR ', '
                )
                FROM wewenang_penilaian wp
                JOIN jabatan jabatan_atasan ON jabatan_atasan.id_jabatan = wp.id_jabatan_penilai
                LEFT JOIN pegawai atasan
                    ON atasan.id_jabatan = jabatan_atasan.id_jabatan
                   AND atasan.status_aktif = 'Aktif'
                WHERE wp.id_jabatan_dinilai = p.id_jabatan
            ), '-') AS nama_penilai,
            GROUP_CONCAT(CONCAT(q.nomor_urut, '. ', q.teks_pertanyaan)
                ORDER BY q.nomor_urut SEPARATOR '<br>') AS daftar_pertanyaan,
            GROUP_CONCAT(q.id_pertanyaan ORDER BY q.nomor_urut SEPARATOR ',') AS daftar_id_pertanyaan,
            hk.file_bukti, hk.daftar_nilai, hk.daftar_jawaban, hk.status_kompetensi,
            dk.dokumentasi_banyak
     FROM kuesioner k
     JOIN pertanyaan_kuesioner q ON q.id_kuesioner = k.id_kuesioner
     LEFT JOIN wawancara w ON w.id_kuesioner = k.id_kuesioner
     LEFT JOIN pegawai p ON w.id_pegawai = p.id_pegawai
     LEFT JOIN pegawai p_target ON p_target.id_pegawai = k.id_jabatan_dinilai
     LEFT JOIN jabatan j ON j.id_jabatan = p.id_jabatan
     LEFT JOIN jabatan j_target ON j_target.id_jabatan = p_target.id_jabatan
     LEFT JOIN hasil_kuesioner hk ON hk.id_wawancara = w.id_wawancara
     LEFT JOIN (
         SELECT id_wawancara,
                GROUP_CONCAT(file_dokumentasi ORDER BY id_dokumentasi SEPARATOR '||') AS dokumentasi_banyak
         FROM dokumentasi_kuesioner
         GROUP BY id_wawancara
     ) dk ON dk.id_wawancara = w.id_wawancara
     WHERE p.id_jabatan IN ($jabatanPlaceholders)
        OR p_target.id_jabatan IN ($jabatanPlaceholders)
        OR k.id_jabatan_dinilai IN ($jabatanPlaceholders)
     GROUP BY k.id_kuesioner, w.id_wawancara, w.tanggal_wawancara, p.id_jabatan, p.nama_lengkap, p_target.nama_lengkap,
              j.nama_jabatan, j_target.nama_jabatan, k.kompetensi,
              hk.file_bukti, hk.daftar_nilai, hk.daftar_jawaban, hk.status_kompetensi,
              dk.dokumentasi_banyak
     ORDER BY k.id_kuesioner ASC, w.id_wawancara ASC"
);
$stmt->execute(array_merge($jabatanIds, $jabatanIds, $jabatanIds));
$data = $stmt->fetchAll();
$interviewIds = array_values(array_unique(array_filter(array_map(
    static function ($row) { return (int) $row['id_wawancara']; },
    $data
))));
$proofReviews = [];
$answerReviews = [];
if ($interviewIds) {
    $placeholders = implode(',', array_fill(0, count($interviewIds), '?'));
    $reviewStmt = $pdo->prepare(
        "SELECT r.id_wawancara, r.file_bukti, COALESCE(p.nama_lengkap, u.NIP) AS nama_penilai, r.diperiksa_at
         FROM pemeriksaan_bukti_kuesioner r
         JOIN users u ON u.id_user = r.id_user_penilai
         LEFT JOIN pegawai p ON p.id_pegawai = u.id_pegawai
         WHERE r.id_wawancara IN ($placeholders)
         ORDER BY r.diperiksa_at DESC"
    );
    $reviewStmt->execute($interviewIds);
    foreach ($reviewStmt->fetchAll() as $review) {
        $proofReviews[(int) $review['id_wawancara']][(string) $review['file_bukti']][] = $review;
    }
    $answerReviewStmt = $pdo->prepare(
        "SELECT h.id_wawancara, COALESCE(p.nama_lengkap, u.NIP) AS nama_penilai, r.diperiksa_at
         FROM pemeriksaan_jawaban_kuesioner r
         JOIN hasil_kuesioner h ON h.id_hasil = r.id_hasil
         JOIN users u ON u.id_user = r.id_user_penilai
         LEFT JOIN pegawai p ON p.id_pegawai = u.id_pegawai
         WHERE h.id_wawancara IN ($placeholders)
           AND r.jawaban_hash = SHA2(CONCAT(COALESCE(h.daftar_nilai, ''), '|', COALESCE(h.daftar_jawaban, ''), '|', COALESCE(h.file_bukti, '')), 256)
         ORDER BY r.diperiksa_at DESC"
    );
    $answerReviewStmt->execute($interviewIds);
    foreach ($answerReviewStmt->fetchAll() as $review) {
        $answerReviews[(int) $review['id_wawancara']][] = $review;
    }
}
$renderReviewIcon = static function (array $reviews): string {
    if (!$reviews) {
        return '';
    }
    $details = array_map(static function ($review) {
        return $review['nama_penilai'] . ' (' . date('d-m-Y H:i', strtotime($review['diperiksa_at'])) . ')';
    }, $reviews);
    $info = htmlspecialchars('Sudah diperiksa oleh: ' . implode(', ', $details), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return '<button type="button" class="btn btn-link text-success p-0 ml-1 review-check" data-toggle="popover" data-trigger="focus" data-placement="top" title="Status pemeriksaan" data-content="' . $info . '" aria-label="Lihat informasi pemeriksaan"><i class="fas fa-check-circle" aria-hidden="true"></i><span class="sr-only">Sudah diperiksa</span></button>';
};
$renderQuestions = static function (?string $value, string $modalId): string {
    $items = $value ? explode('<br>', $value) : [];
    if (!$items) return '-';
    $previewItems = array_slice($items, 0, 3);
    $html = '<div class="question-preview">' . implode('<br>', array_map('htmlspecialchars', $previewItems)) . '</div>';
    if (count($items) <= 3) return $html;
    return $html . '<button type="button" class="btn btn-link btn-sm p-0 mt-2 question-more" data-toggle="modal" data-target="#' . $modalId . '">Lihat selengkapnya</button><div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Daftar pertanyaan lengkap</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">' . implode('<br>', array_map('htmlspecialchars', $items)) . '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$renderAnswerModal = static function (array $row, string $modalId) use ($proofReviews, $answerReviews, $renderReviewIcon): string {
    $questions = $row['daftar_pertanyaan'] ? explode('<br>', $row['daftar_pertanyaan']) : [];
    $savedAnswers = $row['daftar_jawaban'] ? json_decode($row['daftar_jawaban'], true) : [];
    if (!is_array($savedAnswers)) {
        $savedAnswers = [];
    }
    $questionIds = $row['daftar_id_pertanyaan'] ? explode(',', $row['daftar_id_pertanyaan']) : [];
    $answers = [];
    foreach ($questions as $index => $question) {
        $questionId = $questionIds[$index] ?? '';
        $typedAnswer = $savedAnswers[(string) $questionId] ?? '';
        $hasAnswer = is_string($typedAnswer) && trim($typedAnswer) !== '';
        if (!$hasAnswer) {
            continue;
        }
        $questionText = preg_replace('/^\d+\.\s*/', '', $question);
        $answers[] = '<li value="' . ($index + 1) . '" class="mb-3"><strong>'
            . htmlspecialchars($questionText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '</strong><br>Jawaban: '
            . nl2br(htmlspecialchars($typedAnswer, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) . '</li>';
    }
    $evidence = '';
    if ($row['file_bukti']) {
        $reviews = $proofReviews[(int) $row['id_wawancara']][(string) $row['file_bukti']] ?? [];
        $fileUrl = htmlspecialchars(fileUrl($row['file_bukti']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $extension = strtolower(pathinfo($row['file_bukti'], PATHINFO_EXTENSION));
        $evidence = '<div class="mt-1"><a target="_blank" rel="noopener" href="' . $fileUrl . '">Lihat file jawaban</a>'
            . '<br> ' . $renderReviewIcon($reviews) . '</div>';
    }
    $answerModal = '';
    if ($answers) {
        $statusId = 'status-jawaban-' . $modalId;
        $answerStatus = $renderReviewIcon($answerReviews[(int) $row['id_wawancara']] ?? []);
        $answerModal = '<button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat jawaban</button>'
            . '<div id="' . $statusId . '" class="mt-1 d-block">' . $answerStatus . '</div>'
            . '<div class="modal fade answer-review-modal" id="' . $modalId . '" data-wawancara-id="' . (int) $row['id_wawancara'] . '" data-status-id="' . $statusId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document"><div class="modal-content">'
            . '<div class="modal-header"><h5 class="modal-title">Jawaban kuesioner</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>'
            . '<div class="modal-body"><ol class="mb-0 pl-3" style="text-align: justify;">' . implode('', $answers) . '</ol></div>'
            . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
    }
    return $answerModal . $evidence;
};
$renderDocumentationModal = static function (?string $files, string $modalId): string {
    $items = array_values(array_filter(explode('||', (string) $files)));
    if (!$items) {
        return '';
    }
    $links = [];
    foreach ($items as $index => $file) {
        $links[] = '<div class="mb-2"><a target="_blank" rel="noopener" href="' . htmlspecialchars(fileUrl($file))
            . '">Lihat foto ' . ($index + 1) . '</a></div>';
    }
    return '<button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat dokumentasi</button>'
        . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">'
        . '<div class="modal-header"><h5 class="modal-title">Dokumentasi kuesioner</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>'
        . '<div class="modal-body">' . implode('', $links) . '</div>'
        . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$renderInfoModal = static function (array $row, string $modalId): string {
    $tanggal = $row['tanggal_wawancara'] ? date('d-m-Y', strtotime($row['tanggal_wawancara'])) : '-';
    $scores = $row['daftar_nilai'] ? json_decode($row['daftar_nilai'], true) : [];
    $nilai = is_array($scores) && $scores
        ? implode('<br>', array_map(static function ($index, $score) {
            return ($index + 1) . '. ' . ($score === null || $score === '' ? '-' : (int) $score);
        }, array_keys(array_values($scores)), array_values($scores)))
        : '-';
    return '<button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat informasi</button>'
        . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">'
        . '<div class="modal-header"><h5 class="modal-title">Informasi kuesioner</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>'
        . '<div class="modal-body"><dl class="mb-0"><dt>Yang menilai</dt><dd>' . htmlspecialchars($row['nama_penilai']) . '</dd>'
        . '<dt>Yang dinilai</dt><dd>' . htmlspecialchars($row['nama_lengkap'] . ' (' . $row['nama_jabatan'] . ')') . '</dd>'
        . '<dt>Tanggal kuesioner</dt><dd>' . htmlspecialchars($tanggal) . '</dd>'
        . '<dt>Nilai per pertanyaan</dt><dd>' . $nilai . '</dd></dl></div>'
        . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$tableStyle = '<style>
.kuesioner-table { table-layout: fixed; width: 100%; min-width: 1000px; }
.kuesioner-table thead th { height: 64px; text-align: center; vertical-align: middle; }
.kuesioner-table tbody td { vertical-align: top; word-wrap: break-word; }
.kuesioner-table .col-no { width: 4.1%; } .kuesioner-table .col-nama { width: 14.8%; } .kuesioner-table .col-kompetensi { width: 11.6%; } .kuesioner-table .col-pertanyaan { width: 35.5%; } .kuesioner-table .col-bukti { width: 12.5%; } .kuesioner-table .col-status { width: 10.6%; } .kuesioner-table .col-aksi { width: 10.9%; }
.kuesioner-table .question-cell { text-align: justify; font-size: 1.1rem; line-height: 1.6; }
.kuesioner-table .question-preview { max-height: 8rem; overflow: hidden; font-size: .95rem; }
.kuesioner-table .modal-body { font-size: .95rem; line-height: 1.6; }
.kuesioner-table .score-modal-list { font-size: 1rem; line-height: 1.6; }
.kuesioner-table .evidence-cell, .kuesioner-table .status-cell, .kuesioner-table .action-cell { text-align: center; }
.kuesioner-table .review-check { font-size: 1.35rem; line-height: 1; vertical-align: middle; }
</style>';
$pageTitle = 'Kuesioner';
require_once __DIR__ . '/../../includes/header.php';
?>
<?= $tableStyle ?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Buat kuesioner</a>
<div class="table-responsive"><table class="table table-bordered table-hover bg-white align-middle kuesioner-table">
<thead><tr><th class="text-center col-no">No</th><th class="col-nama">Nama pegawai/Jabatan</th><th class="col-kompetensi">Kompetensi jabatan</th><th class="col-pertanyaan">Daftar pertanyaan</th><th class="col-bukti">Bukti kompetensi</th><th class="col-status">Status</th><th class="col-aksi">Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $index => $row): ?>
<tr>
  <?php
  $answerContent = $row['id_wawancara']
      ? $renderAnswerModal($row, 'modal-jawaban-' . (int) $row['id_wawancara'])
      : '';
  $documentationContent = $row['id_wawancara']
      ? $renderDocumentationModal($row['dokumentasi_banyak'], 'modal-dokumentasi-' . (int) $row['id_wawancara'])
      : '';
  ?>
  <td class="text-center"><?= $index + 1 ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap'] ? $row['nama_lengkap'] . ($row['nama_jabatan'] ? ' (' . $row['nama_jabatan'] . ')' : '') : 'Sesi belum dibuat') ?></td>
  <td><?= htmlspecialchars($row['kompetensi']) ?></td>
  <td class="question-cell"><?= $renderQuestions($row['daftar_pertanyaan'], 'modal-kuesioner-' . (int) $row['id_kuesioner'] . '-' . (int) $row['id_wawancara']) ?></td>
  <td class="evidence-cell"><?= $answerContent || $documentationContent
      ? $answerContent . ($answerContent && $documentationContent ? '<br>' : '') . $documentationContent
      : '-' ?></td>
  <?php
  $savedScores = $row['daftar_nilai'] ? json_decode($row['daftar_nilai'], true) : [];
  $hasScore = is_array($savedScores) && count(array_filter($savedScores, static function ($score) {
      return $score !== null && $score !== '';
  })) > 0;
  ?>
  <td class="status-cell"><?= $hasScore ? htmlspecialchars($row['status_kompetensi'] ?: '-') : '-' ?><?php if ($row['id_wawancara']): ?><br><?= $renderInfoModal($row, 'modal-informasi-' . (int) $row['id_wawancara']) ?><?php endif; ?></td>
        <td class="action-cell">
            <div class="table-actions">
                <div class="dropdown mr-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle table-action-btn" data-toggle="dropdown" title="Download kuesioner" aria-label="Download kuesioner"><i class="fas fa-download" aria-hidden="true"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="download.php?id=<?= (int) $row['id_kuesioner'] ?>&amp;format=pdf"><i class="fas fa-file-pdf text-danger mr-2" aria-hidden="true"></i>PDF</a>
                        <a class="dropdown-item" href="download.php?id=<?= (int) $row['id_kuesioner'] ?>&amp;format=word"><i class="fas fa-file-word text-primary mr-2" aria-hidden="true"></i>Word</a>
                    </div>
                </div>
                <?php if ($row['id_wawancara']): ?>
                <a href="jawab.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-primary table-action-btn" title="Isi data" aria-label="Isi data"><i class="fas fa-clipboard-check" aria-hidden="true"></i></a>
                <form method="post" action="hapus.php" class="d-inline ml-1" data-confirm-message="Hapus sesi kuesioner dan hasil penilaian ini?"><?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $row['id_wawancara'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger table-action-btn" title="Hapus sesi" aria-label="Hapus sesi"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                </form>
                <?php endif; ?>
            </div>
        </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="7" class="text-center text-muted">Belum ada kuesioner.</td></tr>
<?php endif; ?>
</tbody>
</table></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('[data-toggle="popover"]').popover({ trigger: 'focus' });
    $(document).on('show.bs.modal', '.answer-review-modal', function () {
        var modal = this;
        var status = document.getElementById(modal.dataset.statusId);
        var body = new URLSearchParams();
        body.set('_csrf', <?= json_encode(csrfToken()) ?>);
        body.set('id_wawancara', modal.dataset.wawancaraId);
        fetch('periksa_jawaban.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        }).then(function (response) {
            return response.json().then(function (result) {
                if (!response.ok) {
                    throw new Error(result.error || 'Pemeriksaan jawaban gagal dicatat.');
                }
                return result;
            });
        }).then(function (result) {
            var info = 'Sudah diperiksa oleh: ' + result.nama_penilai + ' (' + result.diperiksa_at + ')';
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-link text-success p-0 review-check';
            button.setAttribute('aria-label', 'Lihat informasi pemeriksaan');
            button.innerHTML = '<i class="fas fa-check-circle" aria-hidden="true"></i><span class="sr-only">Sudah diperiksa</span>';
            status.innerHTML = '';
            status.appendChild(button);
            $(button).popover({ trigger: 'focus', placement: 'top', title: 'Status pemeriksaan', content: info });
        }).catch(function (error) {
            status.className = 'small text-danger mt-1 d-block';
            status.textContent = error.message;
        });
    });
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
