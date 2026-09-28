<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT u.id_user, u.nip, u.role, u.status_aktif, p.nama_lengkap, j.nama_jabatan
     FROM users u
     LEFT JOIN pegawai p ON u.id_pegawai = p.id_pegawai
     LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
    ORDER BY u.nip"
)->fetchAll();
$pageTitle = 'Akun user';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah akun</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>NIP</th><th>Role</th><th>Pegawai</th><th>Jabatan</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nip']) ?></td>
  <td><?= htmlspecialchars($row['role']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan'] ?? '-') ?></td>
  <td><?= $row['status_aktif'] ? 'Aktif' : 'Nonaktif' ?></td>
  <td class="text-center align-middle">
    <a href="ganti_password.php?id=<?= (int) $row['id_user'] ?>" class="btn btn-sm btn-warning table-action-btn" title="Ganti password" aria-label="Ganti password"><i class="fas fa-key" aria-hidden="true"></i></a>
    <a href="hapus.php?id=<?= (int) $row['id_user'] ?>" class="btn btn-sm btn-danger table-action-btn" title="Hapus akun" aria-label="Hapus akun" onclick="return confirm('Hapus akun <?= htmlspecialchars($row['nip'], ENT_QUOTES) ?>?')"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
