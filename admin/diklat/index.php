<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT kd.*, p.nama_lengkap, j.nama_jabatan, d.nama_diklat,
            g.gap_kompetensi
     FROM kebutuhan_diklat kd
     JOIN pegawai p ON p.id_pegawai = kd.id_pegawai
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
     LEFT JOIN diklat d ON d.id_diklat = kd.id_diklat
     LEFT JOIN analisis_kesenjangan_kompetensi g ON g.id_kesenjangan = kd.id_kesenjangan
     ORDER BY j.id_jabatan, p.nama_lengkap, kd.created_at DESC"
)->fetchAll();
$pageTitle = 'Kebutuhan diklat';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Tentukan kebutuhan diklat</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Nama</th><th>Jabatan</th><th>Gap kompetensi</th><th>Diklat yang dibutuhkan</th><th>Metode pengembangan</th><th>Prioritas</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= nl2br(htmlspecialchars($row['gap_kompetensi'] ?? '-')) ?></td>
  <td><?= htmlspecialchars($row['diklat_lainnya'] ?: ($row['nama_diklat'] ?? '-')) ?></td>
  <td><?= htmlspecialchars($row['metode_pengembangan'] ?? '-') ?></td>
  <td class="text-center align-middle"><?= htmlspecialchars($row['prioritas']) ?></td>
  <td class="text-center align-middle">
    <a href="edit.php?id=<?= (int) $row['id_kebutuhan'] ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="hapus.php?id=<?= (int) $row['id_kebutuhan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kebutuhan diklat ini?')">Hapus</a>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="7" class="text-center text-muted">Belum ada penentuan diklat.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
