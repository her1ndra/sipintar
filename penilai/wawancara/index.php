<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
$data = [];
if ($jabatanIds) {
    $jabatanPlaceholders = implode(',', array_fill(0, count($jabatanIds), '?'));
    $stmt = $pdo->prepare(
    "SELECT w.id_wawancara, p.nama_lengkap, d.kompetensi,
            GROUP_CONCAT(CONCAT(d.nomor_pertanyaan, '. ', d.isi_penilaian)
                ORDER BY d.nomor_pertanyaan SEPARATOR '<br>') AS daftar_pertanyaan,
            GROUP_CONCAT(CONCAT(d.nomor_pertanyaan, '. ', COALESCE(d.nilai, '-'))
                ORDER BY d.nomor_pertanyaan SEPARATOR '<br>') AS daftar_nilai,
            MAX(d.file_bukti) AS file_bukti,
            bw.bukti_banyak,
            CASE
                WHEN AVG(d.nilai) <= 30 THEN 'Tidak Kompeten'
                WHEN AVG(d.nilai) <= 70 THEN 'Cukup'
                WHEN AVG(d.nilai) > 70 THEN 'Kompeten'
                ELSE '-'
            END AS status_kompetensi
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     LEFT JOIN (
         SELECT h.*,
                ROW_NUMBER() OVER (
                    PARTITION BY h.id_wawancara
                    ORDER BY h.id_hasil
                ) AS nomor_pertanyaan
         FROM hasil_wawancara h
     ) d ON d.id_wawancara = w.id_wawancara
     LEFT JOIN (
         SELECT id_wawancara,
                GROUP_CONCAT(file_bukti ORDER BY id_bukti SEPARATOR '||') AS bukti_banyak
         FROM bukti_wawancara
         GROUP BY id_wawancara
     ) bw ON bw.id_wawancara = w.id_wawancara
     WHERE w.id_kuesioner IS NULL
       AND p.id_jabatan IN ($jabatanPlaceholders)
     GROUP BY w.id_wawancara, p.nama_lengkap, d.kompetensi
     ORDER BY w.id_wawancara DESC"
    );
    $stmt->execute($jabatanIds);
    $data = $stmt->fetchAll();
}
$renderList = static function (?string $value, string $modalId): string {
    $items = $value ? explode('<br>', $value) : [];
    if (!$items) return '-';
    $html = '<div class="small lh-lg question-preview">' . implode('<br>', array_map('htmlspecialchars', $items)) . '</div>';
    if ($value !== '') {
        $html .= '<button type="button" class="btn btn-link btn-sm p-0 mt-2 question-more" data-toggle="modal" data-target="#' . $modalId . '">Lihat selengkapnya</button>'
            . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Daftar pertanyaan lengkap</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><div class="small lh-lg">'
            . implode('<br>', array_map('htmlspecialchars', $items)) . '</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
    }
    return $html;
};
$renderAll = static function (?string $value): string {
    $items = $value ? explode('<br>', $value) : [];
    return $items ? '<div class="small lh-lg">' . implode('<br>', array_map('htmlspecialchars', $items)) . '</div>' : '-';
};
$renderScoreModal = static function (?string $value, string $modalId): string {
    $items = $value ? explode('<br>', $value) : [];
    $items = array_values(array_filter($items, static function ($item) {
        return preg_match('/^\d+\.\s*\d+$/', trim($item)) === 1;
    }));
    if (!$items) return '';
    return '<button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat nilai</button><div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Nilai pertanyaan</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div><div class="modal-body score-modal-list">' . implode('<br>', array_map('htmlspecialchars', $items)) . '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$tableStyle = '<style>
.wawancara-table { table-layout: fixed; min-width: 1250px; }
.wawancara-table tbody tr:hover { background-color: inherit !important; }
.wawancara-table thead tr, .wawancara-table thead th,
.wawancara-table thead tr:hover, .wawancara-table thead th:hover { background-color: #fff !important; }
.wawancara-table thead th { height: 64px; text-align: center; vertical-align: middle; border: 1px solid #d9dee7 !important; }
.wawancara-table td { vertical-align: top; word-wrap: break-word; border: 1px solid #d9dee7 !important; }
.wawancara-table .col-no { width: 50px; }
.wawancara-table .col-nama { width: 140px; }
.wawancara-table .col-kompetensi { width: 140px; }
.wawancara-table .col-pertanyaan { width: 430px; }
.wawancara-table .col-nilai { width: 100px; }
.wawancara-table .col-bukti { width: 150px; }
.wawancara-table .col-status { width: 130px; }
.wawancara-table .col-aksi { width: 110px; }
.wawancara-table .cell-scroll { padding-right: 4px; }
.wawancara-table .question-cell { text-align: justify; font-size: 1.2rem; line-height: 1.6; }
.wawancara-table .question-preview { max-height: 8rem; overflow: hidden; }
.wawancara-table .score-cell { font-size: 1.2rem; line-height: 1.6; }
.wawancara-table .evidence-cell, .wawancara-table .status-cell { text-align: center; }
.wawancara-table .score-modal-list { font-size: 1.2rem; line-height: 1.6; }
</style>';
$pageTitle = 'Wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<?= $tableStyle ?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Jadwalkan wawancara</a>
<div class="table-responsive">
<table class="table table-bordered table-hover bg-white align-middle wawancara-table"><thead class="table-light"><tr><th class="text-center col-no">No</th><th class="col-nama">Nama pegawai</th><th class="col-kompetensi">Kompetensi Jabatan</th><th class="col-pertanyaan">Daftar pertanyaan</th><th class="col-bukti">Bukti kompetensi</th><th class="col-status">Status</th><th class="text-center col-aksi">Aksi</th></tr></thead><tbody>
<?php foreach ($data as $index => $row): ?><tr><td class="text-center"><?= $index + 1 ?></td><td><?= htmlspecialchars($row['nama_lengkap']) ?></td><td><?= htmlspecialchars($row['kompetensi'] ?: '-') ?></td><td class="question-cell"><?= $renderList($row['daftar_pertanyaan'], 'modal-pertanyaan-' . (int) $row['id_wawancara']) ?></td><td class="evidence-cell"><?php $bukti = array_filter(array_merge($row['file_bukti'] ? [$row['file_bukti']] : [], $row['bukti_banyak'] ? explode('||', $row['bukti_banyak']) : [])); ?><?= $bukti ? implode('<br>', array_map(static function ($file) { return '<a target="_blank" rel="noopener" href="' . BASE_URL . '/' . htmlspecialchars($file) . '">Lihat bukti</a>'; }, $bukti)) : '-' ?></td><td class="status-cell"><?= htmlspecialchars($row['status_kompetensi'] ?: '-') ?><br><?= $renderScoreModal($row['daftar_nilai'], 'modal-nilai-' . (int) $row['id_wawancara']) ?></td><td class="text-center"><a href="jawab.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-primary">Isi data</a></td></tr><?php endforeach; ?>
<?php if (!$data): ?><tr><td colspan="7" class="text-center text-muted">Belum ada data wawancara.</td></tr><?php endif; ?>
</tbody></table>
</div>
<script>
document.querySelectorAll('.question-preview').forEach(function (preview) {
    var moreButton = preview.nextElementSibling;
    if (moreButton && preview.scrollHeight <= preview.clientHeight) {
        moreButton.style.display = 'none';
    }
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
