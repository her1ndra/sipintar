<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
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
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah analisis kesenjangan</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Jabatan</th><th>Pegawai</th><th>Kompetensi Jabatan</th><th>Kompetensi Pegawai Saat Ini</th><th>Gap Kompetensi</th><th>Dampak</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= nl2br(htmlspecialchars($row['kompetensi_jabatan'])) ?></td>
  <td><?= nl2br(htmlspecialchars($row['kompetensi_pegawai_saat_ini'])) ?></td>
  <td><?= nl2br(htmlspecialchars($row['gap_kompetensi'])) ?></td>
  <td><?= nl2br(htmlspecialchars($row['dampak'])) ?></td>
  <td>
    <a href="edit.php?id=<?= (int) $row['id_kesenjangan'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="hapus.php?id=<?= (int) $row['id_kesenjangan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus analisis kesenjangan ini?')">Hapus</a>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="7" class="text-center text-muted">Belum ada data analisis kesenjangan.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
