<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT w.id_wawancara, p.nama_lengkap, j.nama_jabatan,
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
            w.tanggal_wawancara, d.kompetensi,
            GROUP_CONCAT(CONCAT(d.nomor_pertanyaan, '. ', d.isi_penilaian)
                ORDER BY d.nomor_pertanyaan SEPARATOR '<br>') AS daftar_pertanyaan,
            GROUP_CONCAT(CONCAT(d.nomor_pertanyaan, '. ', COALESCE(d.nilai, '-'))
                ORDER BY d.nomor_pertanyaan SEPARATOR '<br>') AS daftar_nilai,
            COUNT(d.id_hasil) AS jumlah_pertanyaan,
            COUNT(d.nilai) AS jumlah_dinilai,
            bw.bukti_banyak,
            wd.foto_banyak,
            CASE
                WHEN AVG(d.nilai) <= 30 THEN 'Tidak Kompeten'
                WHEN AVG(d.nilai) <= 70 THEN 'Cukup'
                WHEN AVG(d.nilai) > 70 THEN 'Kompeten'
                ELSE '-'
            END AS status_kompetensi
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
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
     LEFT JOIN (
         SELECT id_wawancara,
                GROUP_CONCAT(file_dokumentasi ORDER BY id_dokumentasi SEPARATOR '||') AS foto_banyak
         FROM dokumentasi_wawancara
         GROUP BY id_wawancara
     ) wd ON wd.id_wawancara = w.id_wawancara
     WHERE w.id_kuesioner IS NULL
     GROUP BY w.id_wawancara, p.nama_lengkap, p.id_jabatan, j.nama_jabatan,
              w.tanggal_wawancara, d.kompetensi
     ORDER BY w.id_wawancara DESC"
)->fetchAll();
$evidenceByInterview = [];
$interviewIds = array_values(array_unique(array_map(
    static function ($row) { return (int) $row['id_wawancara']; },
    $data
)));
if ($interviewIds) {
    $placeholders = implode(',', array_fill(0, count($interviewIds), '?'));
    $evidenceStmt = $pdo->prepare(
        "SELECT id_wawancara, kompetensi, isi_penilaian, jawaban, file_bukti,
                ROW_NUMBER() OVER (
                    PARTITION BY id_wawancara
                    ORDER BY id_hasil
                ) AS nomor_pertanyaan
         FROM hasil_wawancara
         WHERE id_wawancara IN ($placeholders)
         ORDER BY id_hasil"
    );
    $evidenceStmt->execute($interviewIds);
    foreach ($evidenceStmt->fetchAll() as $evidence) {
        $evidenceByInterview[(int) $evidence['id_wawancara']][] = $evidence;
    }
}
$renderEvidence = static function (int $idWawancara, ?string $kompetensi, ?string $legacyFiles, ?string $photoFiles, string $modalId) use ($evidenceByInterview): string {
    $items = [];
    $textAnswers = [];
    $index = 0;
    foreach ($evidenceByInterview[$idWawancara] ?? [] as $evidence) {
        if ($evidence['kompetensi'] !== $kompetensi) {
            continue;
        }
        $index++;
        if (trim((string) $evidence['jawaban']) !== '') {
            $textAnswers[] = '<li class="mb-2" value="' . (int) $evidence['nomor_pertanyaan'] . '">'
                . nl2br(htmlspecialchars($evidence['jawaban'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'))
                . '</li>';
        }
        if ($evidence['file_bukti']) {
            $items[] = '<div class="mb-2"><strong>File bukti ' . $index . ':</strong> '
                . '<a class="btn btn-link btn-sm p-0" target="_blank" rel="noopener" href="' . htmlspecialchars(fileUrl($evidence['file_bukti'])) . '">Lihat file</a></div>';
        }
    }
    foreach (array_filter(explode('||', (string) $legacyFiles)) as $file) {
        $items[] = '<div class="mb-2"><a class="btn btn-link btn-sm p-0" target="_blank" rel="noopener" href="'
            . htmlspecialchars(fileUrl($file)) . '">Lihat file jawaban</a></div>';
    }
    foreach (array_filter(explode('||', (string) $photoFiles)) as $photo) {
        $items[] = '<div class="mb-2"><a class="btn btn-link btn-sm p-0" target="_blank" rel="noopener" href="'
            . htmlspecialchars(fileUrl($photo)) . '">Lihat foto dokumentasi</a></div>';
    }
    if ($textAnswers) {
        $items[] = '<button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat jawaban</button>'
            . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document"><div class="modal-content">'
            . '<div class="modal-header"><h5 class="modal-title">Jawaban wawancara</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>'
            . '<div class="modal-body"><ol class="mb-0 pl-3" style="text-align: justify;">' . implode('', $textAnswers) . '</ol></div>'
            . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
    }
    return $items ? implode('', $items) : '-';
};
$renderList = static function (?string $value, string $modalId): string {
    $items = $value ? explode('<br>', $value) : [];
    if (!$items) return '-';
    $previewItems = array_slice($items, 0, 3);
    $html = '<div class="small lh-lg question-preview">' . implode('<br>', array_map('htmlspecialchars', $previewItems)) . '</div>';
    if (count($items) > 3) {
        return $html
        . '<button type="button" class="btn btn-link btn-sm p-0 mt-2 question-more" data-toggle="modal" data-target="#' . $modalId . '">Lihat selengkapnya</button>'
        . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Daftar pertanyaan lengkap</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><div class="small lh-lg">'
        . implode('<br>', array_map('htmlspecialchars', $items)) . '</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
    }
    return $html;
};
$renderAll = static function (?string $value): string {
    $items = $value ? explode('<br>', $value) : [];
    return $items ? '<div class="small lh-lg">' . implode('<br>', array_map('htmlspecialchars', $items)) . '</div>' : '-';
};
$renderInfoModal = static function (array $row, string $modalId): string {
    $tanggal = date('d-m-Y', strtotime($row['tanggal_wawancara']));
    $nilaiItems = $row['daftar_nilai'] ? explode('<br>', $row['daftar_nilai']) : [];
    $nilai = $nilaiItems
        ? implode('<br>', array_map('htmlspecialchars', $nilaiItems))
        : '-';
    return '<button type="button" class="btn btn-link btn-sm p-0" data-toggle="modal" data-target="#' . $modalId . '">Lihat informasi</button>'
        . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">'
        . '<div class="modal-header"><h5 class="modal-title">Informasi wawancara</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button></div>'
        . '<div class="modal-body"><dl class="mb-0"><dt>Yang menilai</dt><dd>' . htmlspecialchars($row['nama_penilai']) . '</dd>'
        . '<dt>Yang dinilai</dt><dd>' . htmlspecialchars($row['nama_lengkap'] . ' (' . $row['nama_jabatan'] . ')') . '</dd>'
        . '<dt>Tanggal wawancara</dt><dd>' . htmlspecialchars($tanggal) . '</dd>'
        . '<dt>Nilai per pertanyaan</dt><dd>' . $nilai . '</dd></dl></div>'
        . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div></div></div></div>';
};
$tableStyle = '<style>
.wawancara-table { table-layout: fixed; min-width: 1250px; }
.wawancara-table thead th { height: 64px; text-align: center; vertical-align: middle; }
.wawancara-table tbody td { vertical-align: top; word-wrap: break-word; }
.wawancara-table .col-no { width: 50px; }
.wawancara-table .col-nama { width: 180px; }
.wawancara-table .col-kompetensi { width: 140px; }
.wawancara-table .col-pertanyaan { width: 430px; }
.wawancara-table .col-nilai { width: 100px; }
.wawancara-table .col-bukti { width: 150px; }
.wawancara-table .col-status { width: 130px; }
.wawancara-table .col-aksi { width: 160px; }
.wawancara-table .action-buttons { display: flex; flex-wrap: nowrap; align-items: center; justify-content: center; gap: .25rem; white-space: nowrap; }
.wawancara-table .action-buttons form { display: flex; margin: 0; }
.wawancara-table .action-status { display: block; margin-bottom: .4rem; }
.wawancara-table .cell-scroll { padding-right: 4px; }
.wawancara-table .question-cell { text-align: justify; font-size: 1.2rem; line-height: 1.6; }
.wawancara-table .question-preview { max-height: 8rem; overflow: hidden; }
.wawancara-table .score-cell { font-size: 1.2rem; line-height: 1.6; }
.wawancara-table .evidence-cell, .wawancara-table .status-cell { text-align: center; }
</style>';
$pageTitle = 'Wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<?= $tableStyle ?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Jadwalkan wawancara</a>
<div class="table-responsive">
<table class="table table-bordered table-hover bg-white align-middle wawancara-table"><thead class="table-light"><tr><th class="text-center col-no">No</th><th class="col-nama">Nama Pegawai/Jabatan</th><th class="col-kompetensi">Kompetensi Jabatan</th><th class="col-pertanyaan">Daftar pertanyaan</th><th class="col-bukti">Bukti kompetensi</th><th class="col-status">Status</th><th class="text-center col-aksi">Aksi</th></tr></thead><tbody>
<?php foreach ($data as $index => $row): $sudahDinilai = (int) $row['jumlah_pertanyaan'] > 0 && (int) $row['jumlah_dinilai'] === (int) $row['jumlah_pertanyaan']; ?><tr><td class="text-center"><?= $index + 1 ?></td><td><?= htmlspecialchars($row['nama_lengkap']) ?><br>(<?= htmlspecialchars($row['nama_jabatan']) ?>)</td><td><?= htmlspecialchars($row['kompetensi'] ?: '-') ?></td><td class="question-cell"><?= $renderList($row['daftar_pertanyaan'], 'modal-pertanyaan-' . (int) $row['id_wawancara']) ?></td><td class="evidence-cell"><?= $renderEvidence((int) $row['id_wawancara'], $row['kompetensi'], $row['bukti_banyak'], $row['foto_banyak'], 'modal-jawaban-' . (int) $row['id_wawancara'] . '-' . substr(sha1((string) $row['kompetensi']), 0, 10)) ?></td><td class="status-cell"><?= htmlspecialchars($row['status_kompetensi'] ?: '-') ?><br><?= $renderInfoModal($row, 'modal-informasi-' . (int) $row['id_wawancara']) ?></td><td class="text-center"><span class="badge badge-<?= $sudahDinilai ? 'success' : 'secondary' ?> action-status"><?= $sudahDinilai ? 'Sudah dinilai' : 'Belum dinilai' ?></span><div class="action-buttons"><a href="download.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-outline-secondary table-action-btn" title="Download data wawancara Word" aria-label="Download data wawancara Word"><i class="fas fa-download" aria-hidden="true"></i></a><a href="jawab.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-primary table-action-btn" title="Isi data" aria-label="Isi data"><i class="fas fa-clipboard-check" aria-hidden="true"></i></a><form method="post" action="hapus.php" class="d-inline" onsubmit="return confirm('Hapus wawancara beserta hasil dan bukti terkait?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int) $row['id_wawancara'] ?>"><button type="submit" class="btn btn-sm btn-danger table-action-btn" title="Hapus wawancara" aria-label="Hapus wawancara"><i class="fas fa-trash-alt" aria-hidden="true"></i></button></form></div></td></tr><?php endforeach; ?>
<?php if (!$data): ?><tr><td colspan="7" class="text-center text-muted">Belum ada data wawancara.</td></tr><?php endif; ?>
</tbody></table>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
