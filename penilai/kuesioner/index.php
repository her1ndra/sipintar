<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
$jabatanPlaceholders = $jabatanIds ? implode(',', array_fill(0, count($jabatanIds), '?')) : 'NULL';
$stmt = $pdo->prepare(
    "SELECT k.id_kuesioner, k.judul_kuesioner, k.tahun_periode, k.status,
            k.kompetensi, w.id_wawancara,
            COALESCE(p.nama_lengkap, p_target.nama_lengkap) AS nama_lengkap,
            GROUP_CONCAT(CONCAT(q.nomor_urut, '. ', q.teks_pertanyaan)
                ORDER BY q.nomor_urut SEPARATOR '<br>') AS daftar_pertanyaan,
            hk.file_bukti, hk.daftar_nilai, hk.status_kompetensi
     FROM kuesioner k
     JOIN pertanyaan_kuesioner q ON q.id_kuesioner = k.id_kuesioner
     LEFT JOIN wawancara w ON w.id_kuesioner = k.id_kuesioner
     LEFT JOIN pegawai p ON w.id_pegawai = p.id_pegawai
     LEFT JOIN pegawai p_target ON p_target.id_pegawai = k.id_jabatan_dinilai
     LEFT JOIN hasil_kuesioner hk ON hk.id_wawancara = w.id_wawancara
     WHERE p.id_jabatan IN ($jabatanPlaceholders)
        OR p_target.id_jabatan IN ($jabatanPlaceholders)
        OR k.id_jabatan_dinilai IN ($jabatanPlaceholders)
     GROUP BY k.id_kuesioner, w.id_wawancara, p.nama_lengkap, p_target.nama_lengkap, k.kompetensi,
              hk.file_bukti, hk.daftar_nilai, hk.status_kompetensi
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
$renderScoreModal = static function (?string $value, string $modalId): string {
    $scores = $value ? json_decode($value, true) : [];
    if (!is_array($scores) || !$scores) return '';
    $items = [];
    foreach (array_values($scores) as $index => $score) {
        $items[] = ($index + 1) . '. ' . (int) $score;
    }
    return '<br><button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat nilai</button><div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Nilai pertanyaan</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div><div class="modal-body">' . implode('<br>', $items) . '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$tableStyle = '<style>
.kuesioner-table { table-layout: fixed; min-width: 1250px; }
.kuesioner-table thead tr, .kuesioner-table thead th, .kuesioner-table thead tr:hover, .kuesioner-table thead th:hover { background-color: #fff !important; }
.kuesioner-table tbody tr:hover { background-color: inherit !important; }
.kuesioner-table thead th { height: 64px; text-align: center; vertical-align: middle; border: 1px solid #d9dee7 !important; }
.kuesioner-table td { vertical-align: top; word-wrap: break-word; border: 1px solid #d9dee7 !important; }
.kuesioner-table .col-no { width: 50px; } .kuesioner-table .col-nama { width: 140px; } .kuesioner-table .col-kompetensi { width: 140px; } .kuesioner-table .col-pertanyaan { width: 430px; } .kuesioner-table .col-bukti { width: 150px; } .kuesioner-table .col-status { width: 130px; } .kuesioner-table .col-aksi { width: 110px; }
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
<thead><tr><th class="text-center col-no">No</th><th class="col-nama">Nama pegawai</th><th class="col-kompetensi">Kompetensi jabatan</th><th class="col-pertanyaan">Daftar pertanyaan</th><th class="col-bukti">Bukti kompetensi</th><th class="col-status">Status</th><th class="col-aksi">Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td class="text-center"><?= (int) $row['id_kuesioner'] ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap'] ?: 'Sesi belum dibuat') ?></td>
  <td><?= htmlspecialchars($row['kompetensi']) ?></td>
  <td class="question-cell"><?= $renderQuestions($row['daftar_pertanyaan'], 'modal-kuesioner-' . (int) $row['id_kuesioner'] . '-' . (int) $row['id_wawancara']) ?></td>
  <td class="evidence-cell">
    <?php if ($row['file_bukti']): ?><a target="_blank" rel="noopener" href="<?= BASE_URL . '/' . htmlspecialchars($row['file_bukti']) ?>">Lihat file</a><?php else: ?>-<?php endif; ?>
  </td>
  <td class="status-cell"><?= htmlspecialchars($row['status_kompetensi'] ?: '-') ?><?= $renderScoreModal($row['daftar_nilai'], 'modal-nilai-' . (int) $row['id_kuesioner'] . '-' . (int) $row['id_wawancara']) ?></td>
  <td class="action-cell"><div class="dropdown mb-1"><button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown" title="Download pertanyaan" aria-label="Download pertanyaan"><i class="fas fa-download"></i></button><div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="download.php?id=<?= (int) $row['id_kuesioner'] ?>&format=pdf"><i class="fas fa-file-pdf text-danger mr-2"></i>PDF</a><a class="dropdown-item" href="download.php?id=<?= (int) $row['id_kuesioner'] ?>&format=csv"><i class="fas fa-file-csv text-success mr-2"></i>CSV</a></div></div><?php if ($row['id_wawancara']): ?><a href="jawab.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-primary">Isi data</a><?php endif; ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="7" class="text-center text-muted">Belum ada kuesioner.</td></tr>
<?php endif; ?>
</tbody>
</table></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
