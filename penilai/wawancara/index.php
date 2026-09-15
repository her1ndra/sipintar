<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();

$stmt = $pdo->prepare(
    "SELECT w.id_wawancara, w.tanggal_wawancara, w.status, p.nama_lengkap, k.judul_kuesioner
     FROM wawancara w
     JOIN pegawai p ON w.id_pegawai = p.id_pegawai
     JOIN kuesioner k ON w.id_kuesioner = k.id_kuesioner
     WHERE w.id_user_penilai = ?
     ORDER BY w.tanggal_wawancara DESC"
);
$stmt->execute([$user['id_user']]);
$data = $stmt->fetchAll();
$pageTitle = 'Wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Jadwalkan wawancara</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Pegawai</th><th>Kuesioner</th><th>Tanggal</th><th>Status</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['judul_kuesioner']) ?></td>
  <td><?= htmlspecialchars($row['tanggal_wawancara']) ?></td>
  <td><?= htmlspecialchars($row['status']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="4" class="text-center text-muted">Belum ada data wawancara.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
