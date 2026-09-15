<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);

$data = [];
if ($jabatanIds) {
    $placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT kd.*, p.nama_lengkap, d.nama_diklat
         FROM kebutuhan_diklat kd
         JOIN pegawai p ON kd.id_pegawai = p.id_pegawai
         LEFT JOIN diklat d ON kd.id_diklat = d.id_diklat
         WHERE p.id_jabatan IN ($placeholders)
         ORDER BY kd.created_at DESC"
    );
    $stmt->execute($jabatanIds);
    $data = $stmt->fetchAll();
}
$pageTitle = 'Kebutuhan diklat';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Ajukan kebutuhan diklat</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Pegawai</th><th>Diklat</th><th>Prioritas</th><th>Status</th><th>Tahun rencana</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_diklat'] ?? '-') ?></td>
  <td><?= htmlspecialchars($row['prioritas']) ?></td>
  <td><?= htmlspecialchars($row['status']) ?></td>
  <td><?= htmlspecialchars($row['tahun_rencana']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="5" class="text-center text-muted">Belum ada usulan diklat.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
