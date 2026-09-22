<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
        "SELECT p.id_pegawai, p.nip, p.nama_lengkap, j.nama_jabatan,
          (SELECT COUNT(*) FROM sertifikat_pegawai s WHERE s.id_pegawai = p.id_pegawai) AS jumlah_sertifikat
     FROM pegawai p JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     ORDER BY j.id_jabatan, p.nama_lengkap"
)->fetchAll();
$pageTitle = 'Data pegawai';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tambah pegawai</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Sertifikat</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nip']) ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><a href="../sertifikat/index.php" class="text-primary"><?= (int) $row['jumlah_sertifikat'] ?> sertifikat</a></td>
  <td>
    <a href="edit.php?id=<?= $row['id_pegawai'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="hapus.php?id=<?= $row['id_pegawai'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pegawai ini?')">Hapus</a>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="5" class="text-center text-muted">Belum ada data pegawai.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
