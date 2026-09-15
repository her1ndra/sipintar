<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);

$data = [];
if ($jabatanIds) {
    $placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT g.*, j.nama_jabatan, p.nama_lengkap
         FROM analisis_kesenjangan_kompetensi g
         JOIN jabatan j ON g.id_jabatan = j.id_jabatan
         JOIN pegawai p ON g.id_pegawai = p.id_pegawai
         WHERE p.id_jabatan IN ($placeholders)
         ORDER BY g.periode_analisis DESC"
    );
    $stmt->execute($jabatanIds);
    $data = $stmt->fetchAll();
}
$pageTitle = 'Analisis gap kompetensi';
require_once __DIR__ . '/../../includes/header.php';
?>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Jabatan</th><th>Pegawai</th><th>Kompetensi Jabatan</th><th>Kompetensi Saat Ini</th><th>Gap</th><th>Dampak</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= nl2br(htmlspecialchars($row['kompetensi_jabatan'])) ?></td>
  <td><?= nl2br(htmlspecialchars($row['kompetensi_pegawai_saat_ini'])) ?></td>
  <td><?= nl2br(htmlspecialchars($row['gap_kompetensi'])) ?></td>
  <td><?= nl2br(htmlspecialchars($row['dampak'])) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="6" class="text-center text-muted">Belum ada data analisis gap.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
