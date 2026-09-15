<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$stmt = $pdo->prepare(
    "SELECT k.id_kuesioner, k.judul_kuesioner, k.tahun_periode, k.status, j.nama_jabatan
     FROM kuesioner k JOIN jabatan j ON k.id_jabatan_dinilai = j.id_jabatan
     WHERE k.id_user_pembuat = ?
     ORDER BY k.created_at DESC"
);
$stmt->execute([$user['id_user']]);
$data = $stmt->fetchAll();
$pageTitle = 'Kuesioner saya';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Buat kuesioner</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Judul</th><th>Jabatan target</th><th>Tahun</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['judul_kuesioner']) ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= htmlspecialchars($row['tahun_periode']) ?></td>
  <td><?= htmlspecialchars($row['status']) ?></td>
  <td><a href="pertanyaan.php?id=<?= $row['id_kuesioner'] ?>" class="btn btn-sm btn-secondary">Kelola pertanyaan</a></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="5" class="text-center text-muted">Belum ada kuesioner.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
