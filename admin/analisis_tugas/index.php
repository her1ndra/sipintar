<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

function parseKegiatanItems(?string $value): array
{
    if ($value === null || trim($value) === '') {
        return [];
    }

    $items = [];
    foreach (preg_split('/\r\n|\r|\n/', $value) as $line) {
        $line = trim((string) $line);
        if ($line === '') {
            continue;
        }

        $line = preg_replace('/^(?:[a-zA-Z]|[0-9]+)[\.)]\s*/', '', $line);
        $items[] = $line;
    }

    return $items;
}

$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT a.*, j.nama_jabatan
     FROM analisis_tugas a
     JOIN jabatan j ON a.id_jabatan = j.id_jabatan
     ORDER BY j.nama_jabatan, a.created_at DESC"
)->fetchAll();
$renderKegiatan = static function (?string $value, string $modalId): string {
    $items = parseKegiatanItems($value);
    if (!$items) {
        return '-';
    }

    $renderItems = static function (array $list): string {
        return '<ol class="mb-0 pl-3">' . implode('', array_map(static function (string $item): string {
            return '<li>' . htmlspecialchars($item) . '</li>';
        }, $list)) . '</ol>';
    };

    $previewItems = array_slice($items, 0, 2);
    $html = '<div class="kegiatan-preview">' . $renderItems($previewItems) . '</div>';
    if (count($items) <= 2) {
        return $html;
    }

    return $html
        . '<button type="button" class="btn btn-link btn-sm p-0 mt-2 kegiatan-more" data-toggle="modal" data-target="#' . $modalId . '">Lihat selengkapnya</button>'
        . '<div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-hidden="true">'
        . '<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">'
        . '<div class="modal-content"><div class="modal-header">'
        . '<h5 class="modal-title">Daftar kegiatan lengkap</h5>'
        . '<button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>'
        . '</div><div class="modal-body">' . $renderItems($items)
        . '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>'
        . '</div></div></div>';
};
$tableStyle = '<style>
.analisis-tugas-table { table-layout: fixed; }
.analisis-tugas-table td { vertical-align: top; word-wrap: break-word; }
.analisis-tugas-table thead th { text-align: center; vertical-align: middle; }
.analisis-tugas-table .text-justify { text-align: justify; }
.analisis-tugas-table .col-kegiatan { min-width: 280px; }
.analisis-tugas-table .kegiatan-preview,
.analisis-tugas-table .kegiatan-preview li { text-align: justify; }
.analisis-tugas-table .modal-body { text-align: justify; }
.analisis-tugas-table .action-cell { text-align: center; vertical-align: middle; }
.analisis-tugas-table .action-buttons { display: flex; justify-content: center; gap: .35rem; flex-wrap: wrap; }
</style>';
$pageTitle = 'Analisis tugas';
require_once __DIR__ . '/../../includes/header.php';
?>
<?= $tableStyle ?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah analisis tugas</a>
<div class="table-responsive">
<table class="table table-bordered table-striped bg-white analisis-tugas-table">
<thead>
<tr>
  <th>Nama Jabatan</th>
  <th>Tugas</th>
  <th class="col-kegiatan">Kegiatan</th>
  <th>Kompetensi Sementara Jabatan</th>
  <th>Kompetensi Jabatan</th>
  <th class="text-center">Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td class="text-justify"><?= nl2br(htmlspecialchars($row['tugas'])) ?></td>
  <td>
    <?= $renderKegiatan($row['kegiatan'] ?? '', 'modal-kegiatan-' . (int) $row['id_analisis_tugas']) ?>
  </td>
  <td class="text-justify"><?= $row['kompetensi_sementara_jabatan'] ? nl2br(htmlspecialchars($row['kompetensi_sementara_jabatan'])) : '-' ?></td>
  <td class="text-justify"><?= nl2br(htmlspecialchars($row['kompetensi_jabatan'])) ?></td>
  <td class="action-cell">
    <?php $idAnalisis = $row['id_analisis_tugas'] ?? null; ?>
    <?php if ($idAnalisis !== null): ?>
      <div class="action-buttons">
        <a href="edit.php?id=<?= (int) $idAnalisis ?>" class="btn btn-sm btn-warning">Edit</a>
        <a href="hapus.php?id=<?= (int) $idAnalisis ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus analisis tugas ini?')">Hapus</a>
      </div>
    <?php else: ?>
      <span class="text-muted">-</span>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="6" class="text-center text-muted">Belum ada data analisis tugas.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
