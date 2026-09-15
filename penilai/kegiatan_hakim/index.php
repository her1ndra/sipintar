<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$user = currentUser();
if (!in_array($user['nama_jabatan'], ['Ketua', 'Wakil Ketua'], true)) {
    http_response_code(403);
    die('Menu ini khusus untuk Ketua/Wakil Ketua sebagai penilai Hakim.');
}
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT kh.*, p.nama_lengkap
     FROM kegiatan_hakim kh
     JOIN pegawai p ON kh.id_pegawai = p.id_pegawai
     ORDER BY kh.tanggal_mulai DESC"
)->fetchAll();
$pageTitle = 'Tracing kegiatan hakim';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Catat kegiatan</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Hakim</th><th>Jenis kegiatan</th><th>Nama kegiatan</th><th>Tanggal mulai</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['jenis_kegiatan']) ?></span></td>
  <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
  <td><?= htmlspecialchars($row['tanggal_mulai']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="4" class="text-center text-muted">Belum ada data kegiatan hakim.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
