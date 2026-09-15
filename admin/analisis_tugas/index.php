<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT a.*, j.nama_jabatan
     FROM analisis_tugas a
     JOIN jabatan j ON a.id_jabatan = j.id_jabatan
     ORDER BY j.nama_jabatan, a.created_at DESC"
)->fetchAll();
$pageTitle = 'Analisis tugas';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah analisis tugas</a>
<table class="table table-bordered table-striped bg-white">
<thead>
<tr>
  <th>Nama Jabatan</th>
  <th>Tugas</th>
  <th>Kegiatan</th>
  <th>Kompetensi Sementara Jabatan</th>
  <th>Kompetensi Jabatan</th>
  <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= nl2br(htmlspecialchars($row['tugas'])) ?></td>
  <td><?= nl2br(htmlspecialchars($row['kegiatan'])) ?></td>
  <td><?= $row['kompetensi_sementara_jabatan'] ? nl2br(htmlspecialchars($row['kompetensi_sementara_jabatan'])) : '-' ?></td>
  <td><?= nl2br(htmlspecialchars($row['kompetensi_jabatan'])) ?></td>
  <td>
    <?php $idAnalisis = $row['id_analisis_tugas'] ?? null; ?>
    <?php if ($idAnalisis !== null): ?>
      <a href="edit.php?id=<?= (int) $idAnalisis ?>" class="btn btn-sm btn-warning">Edit</a>
      <a href="hapus.php?id=<?= (int) $idAnalisis ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus analisis tugas ini?')">Hapus</a>
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
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
