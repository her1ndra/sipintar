<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$stmt = $pdo->prepare(
    "SELECT pk.id_penilaian, p.nama_lengkap, k.nama_kompetensi, pk.status_kompetensi, pk.periode_penilaian
     FROM penilaian_kompetensi pk
     JOIN pegawai p ON pk.id_pegawai = p.id_pegawai
     JOIN kompetensi k ON pk.id_kompetensi = k.id_kompetensi
     WHERE pk.id_user_penilai = ?
     ORDER BY pk.created_at DESC"
);
$stmt->execute([$user['id_user']]);
$data = $stmt->fetchAll();
$pageTitle = 'Penilaian kompetensi';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Input penilaian</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Pegawai</th><th>Kompetensi</th><th>Status</th><th>Periode</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_kompetensi']) ?></td>
  <td>
    <?php $badge = $row['status_kompetensi'] === 'Kompeten' ? 'success' : ($row['status_kompetensi'] === 'Cukup' ? 'warning' : 'danger'); ?>
    <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($row['status_kompetensi']) ?></span>
  </td>
  <td><?= htmlspecialchars($row['periode_penilaian']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="4" class="text-center text-muted">Belum ada data penilaian.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
