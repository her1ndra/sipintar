<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query('SELECT * FROM jabatan ORDER BY id_jabatan')->fetchAll();
$pageTitle = 'Master jabatan';
require_once __DIR__ . '/../../includes/header.php';
?>
<p class="text-muted">Master jabatan bersifat tetap sesuai struktur organisasi. Aturan siapa menilai siapa
diatur di menu <a href="../wewenang/index.php">Wewenang penilaian</a>.</p>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Kode</th><th>Nama jabatan</th><th>Berperan sbg penilai?</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['kode_jabatan']) ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= $row['is_penilai'] ? 'Ya' : 'Tidak' ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
