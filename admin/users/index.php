<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT u.id_user, u.username, u.role, u.status_aktif, p.nama_lengkap, j.nama_jabatan
     FROM users u
     LEFT JOIN pegawai p ON u.id_pegawai = p.id_pegawai
     LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     ORDER BY u.username"
)->fetchAll();
$pageTitle = 'Akun user';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah akun</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Username</th><th>Role</th><th>Pegawai</th><th>Jabatan</th><th>Status</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['username']) ?></td>
  <td><?= htmlspecialchars($row['role']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan'] ?? '-') ?></td>
  <td><?= $row['status_aktif'] ? 'Aktif' : 'Nonaktif' ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
