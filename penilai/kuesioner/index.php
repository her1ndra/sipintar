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
            hk.file_bukti, hk.daftar_nilai, hk.status_kompetensi,
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
              hk.file_bukti, hk.daftar_nilai, hk.status_kompetensi, dk.dokumentasi_banyak
     ORDER BY k.id_kuesioner ASC, w.id_wawancara DESC"
);
$stmt->execute(array_merge($jabatanIds, $jabatanIds, $jabatanIds));
$data = $stmt->fetchAll();
$renderQuestions = static function (?string $value, string $modalId): string {
    $items = $value ? explode('<br>', $value) : [];
    if (!$items) return '-';
    $previewItems = array_slice($items, 0, 3);
    $html = '<div class="question-preview">' . implode('<br>', array_map('htmlspecialchars', $previewItems)) . '</div>';
    if (count($items) <= 3) return $html;
    return $html . '<button type="button" class="btn btn-link btn-sm p-0 mt-2 question-more" data-toggle="modal" data-target="#' . $modalId . '">Lihat selengkapnya</button><div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Daftar pertanyaan lengkap</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">' . implode('<br>', array_map('htmlspecialchars', $items)) . '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$renderAnswerModal = static function (array $row, string $modalId): string {
    $questions = $row['daftar_pertanyaan'] ? explode('<br>', $row['daftar_pertanyaan']) : [];
    $scores = $row['daftar_nilai'] ? json_decode($row['daftar_nilai'], true) : [];
    if (!is_array($scores)) {
        $scores = [];
    }
    $answers = [];
    foreach ($questions as $index => $question) {
        $score = isset($scores[$index]) ? (int) $scores[$index] : '-';
        $answers[] = '<div class="mb-3"><strong>' . htmlspecialchars($question, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '</strong><br>Nilai: ' . htmlspecialchars((string) $score, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</div>';
    }
    if ($row['file_bukti']) {
        $answers[] = '<div><a target="_blank" rel="noopener" href="' . htmlspecialchars(fileUrl($row['file_bukti'])) . '">Lihat file bukti kompetensi</a></div>';
    }
    return '<button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat jawaban</button>'
        . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document"><div class="modal-content">'
        . '<div class="modal-header"><h5 class="modal-title">Jawaban kuesioner</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>'
        . '<div class="modal-body">' . ($answers ? implode('', $answers) : '-') . '</div>'
        . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$renderDocumentationModal = static function (?string $files, string $modalId): string {
    $items = array_values(array_filter(explode('||', (string) $files)));
    if (!$items) {
        return '-';
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
            return ($index + 1) . '. ' . (int) $score;
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
.kuesioner-table { table-layout: fixed; min-width: 1250px; }
.kuesioner-table thead th { height: 64px; text-align: center; vertical-align: middle; }
.kuesioner-table tbody td { vertical-align: top; word-wrap: break-word; }
.kuesioner-table .col-no { width: 50px; } .kuesioner-table .col-nama { width: 140px; } .kuesioner-table .col-kompetensi { width: 140px; } .kuesioner-table .col-pertanyaan { width: 430px; } .kuesioner-table .col-bukti { width: 180px; } .kuesioner-table .col-status { width: 130px; } .kuesioner-table .col-aksi { width: 110px; }
.kuesioner-table .question-cell { text-align: justify; font-size: 1.1rem; line-height: 1.6; }
.kuesioner-table .question-preview { max-height: 8rem; overflow: hidden; font-size: .95rem; }
.kuesioner-table .modal-body { font-size: .95rem; line-height: 1.6; }
.kuesioner-table .score-modal-list { font-size: 1rem; line-height: 1.6; }
.kuesioner-table .evidence-cell, .kuesioner-table .status-cell, .kuesioner-table .action-cell { text-align: center; }
</style>';
$pageTitle = 'Kuesioner';
require_once __DIR__ . '/../../includes/header.php';
?>
<?= $tableStyle ?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Buat kuesioner</a>
<div class="table-responsive"><table class="table table-bordered table-hover bg-white align-middle kuesioner-table">
<thead><tr><th class="text-center col-no">No</th><th class="col-nama">Nama pegawai/Jabatan</th><th class="col-kompetensi">Kompetensi jabatan</th><th class="col-pertanyaan">Daftar pertanyaan</th><th class="col-bukti">Bukti kompetensi</th><th class="col-status">Status</th><th class="col-aksi">Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td class="text-center"><?= (int) $row['id_kuesioner'] ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap'] ? $row['nama_lengkap'] . ($row['nama_jabatan'] ? ' (' . $row['nama_jabatan'] . ')' : '') : 'Sesi belum dibuat') ?></td>
  <td><?= htmlspecialchars($row['kompetensi']) ?></td>
  <td class="question-cell"><?= $renderQuestions($row['daftar_pertanyaan'], 'modal-kuesioner-' . (int) $row['id_kuesioner'] . '-' . (int) $row['id_wawancara']) ?></td>
  <td class="evidence-cell">
    <?= $row['id_wawancara'] ? $renderAnswerModal($row, 'modal-jawaban-' . (int) $row['id_wawancara']) : '-' ?>
    <?php if ($row['id_wawancara']): ?><br><?= $renderDocumentationModal($row['dokumentasi_banyak'], 'modal-dokumentasi-' . (int) $row['id_wawancara']) ?><?php endif; ?>
  </td>
  <td class="status-cell"><?= htmlspecialchars($row['status_kompetensi'] ?: '-') ?><?php if ($row['id_wawancara']): ?><br><?= $renderInfoModal($row, 'modal-informasi-' . (int) $row['id_wawancara']) ?><?php endif; ?></td>
        <td class="action-cell">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="dropdown mr-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle table-action-btn" data-toggle="dropdown" title="Download pertanyaan" aria-label="Download pertanyaan"><i class="fas fa-download"></i></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="download.php?id=<?= (int) $row['id_kuesioner'] ?>&format=pdf"><i class="fas fa-file-pdf text-danger mr-2"></i>PDF</a>
                        <a class="dropdown-item" href="download.php?id=<?= (int) $row['id_kuesioner'] ?>&format=csv"><i class="fas fa-file-csv text-success mr-2"></i>CSV</a>
                    </div>
                </div>
                <?php if ($row['id_wawancara']): ?>
                <a href="jawab.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-primary table-action-btn" title="Isi data" aria-label="Isi data"><i class="fas fa-clipboard-check" aria-hidden="true"></i></a>
                <form method="post" action="hapus.php" class="d-inline ml-1" onsubmit="return confirm('Hapus sesi kuesioner dan hasil penilaian ini?')"><?= csrfField() ?>
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
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
