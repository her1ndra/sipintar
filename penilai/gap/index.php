<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');

function parseGapItems(?string $value): array
{
    $items = [];
    foreach (preg_split('/\r\n|\r|\n/', (string) $value) as $line) {
        $line = trim($line);
        if ($line !== '') {
            $items[] = preg_replace('/^(?:[a-zA-Z]|[0-9]+)[\.)]\s*/', '', $line);
        }
    }
    return $items;
}

$renderGapField = static function (?string $value, string $modalId, string $title): string {
    $items = parseGapItems($value);
    if (!$items) return '-';
    if (count($items) === 1) return htmlspecialchars($items[0]);

    $renderItems = static function (array $list): string {
        return '<ol class="mb-0 pl-3">' . implode('', array_map(static function (string $item): string {
            return '<li>' . htmlspecialchars($item) . '</li>';
        }, $list)) . '</ol>';
    };
    return $renderItems($items);
};

$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT g.*, j.nama_jabatan, p.nama_lengkap
     FROM analisis_kesenjangan_kompetensi g
     JOIN jabatan j ON g.id_jabatan = j.id_jabatan
     JOIN pegawai p ON g.id_pegawai = p.id_pegawai
     ORDER BY g.created_at DESC"
)->fetchAll();
$pageTitle = 'Analisis kesenjangan kompetensi';
require_once __DIR__ . '/../../includes/header.php';
?>
<style>
.gap-table { table-layout: fixed; }
.gap-table thead th { text-align: center; vertical-align: middle; }
.gap-table td, .gap-table .gap-preview, .gap-table .gap-preview li, .gap-table .modal-body { text-align: justify; vertical-align: top; word-wrap: break-word; }
.gap-table .dampak-cell { text-align: justify; }
</style>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah analisis kesenjangan</a>
<div class="table-responsive"><table class="table table-bordered table-striped bg-white gap-table">
<thead><tr><th>Jabatan</th><th>Pegawai</th><th>Kompetensi Jabatan</th><th>Kompetensi Pegawai Saat Ini</th><th>Gap Kompetensi</th><th>Dampak</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= $renderGapField($row['kompetensi_jabatan'], 'modal-penilai-gap-jabatan-' . (int) $row['id_kesenjangan'], 'Kompetensi Jabatan') ?></td>
  <td><?= $renderGapField($row['kompetensi_pegawai_saat_ini'], 'modal-penilai-gap-pegawai-' . (int) $row['id_kesenjangan'], 'Kompetensi Pegawai Saat Ini') ?></td>
  <td><?= $renderGapField($row['gap_kompetensi'], 'modal-penilai-gap-kompetensi-' . (int) $row['id_kesenjangan'], 'Gap Kompetensi') ?></td>
  <td class="dampak-cell"><?= $renderGapField($row['dampak'], 'modal-penilai-gap-dampak-' . (int) $row['id_kesenjangan'], 'Dampak') ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="6" class="text-center text-muted">Belum ada data analisis kesenjangan.</td></tr>
<?php endif; ?>
</tbody>
</table></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
