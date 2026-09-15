<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT s.*, p.nama_lengkap FROM sertifikat_pegawai s
     JOIN pegawai p ON s.id_pegawai = p.id_pegawai
     ORDER BY s.created_at DESC"
)->fetchAll();
$pageTitle = 'Sertifikat pegawai';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah sertifikat</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Pegawai</th><th>Nama sertifikat</th><th>Penyelenggara</th><th>Terbit</th><th>Kadaluarsa</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_sertifikat']) ?></td>
  <td><?= htmlspecialchars($row['penyelenggara'] ?? '-') ?></td>
  <td><?= htmlspecialchars($row['tanggal_terbit'] ?? '-') ?></td>
  <td><?= htmlspecialchars($row['tanggal_kadaluarsa'] ?? '-') ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="5" class="text-center text-muted">Belum ada data sertifikat.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
