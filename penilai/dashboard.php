<?php
require_once __DIR__ . '/../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanDinilai = getJabatanWewenang($pdo, (int) $user['id_jabatan']);

$pegawaiBinaan = [];
if ($jabatanDinilai) {
    $placeholders = implode(',', array_fill(0, count($jabatanDinilai), '?'));
    $stmt = $pdo->prepare(
        "SELECT p.id_pegawai, p.nip, p.nama_lengkap, j.nama_jabatan
         FROM pegawai p JOIN jabatan j ON p.id_jabatan = j.id_jabatan
         WHERE p.id_jabatan IN ($placeholders)
         ORDER BY p.nama_lengkap"
    );
    $stmt->execute($jabatanDinilai);
    $pegawaiBinaan = $stmt->fetchAll();
}
$pageTitle = 'Dashboard penilai';
require_once __DIR__ . '/../includes/header.php';
?>
<p class="text-muted">Sebagai <strong><?= htmlspecialchars($user['nama_jabatan']) ?></strong>,
anda berwenang menilai <?= count($pegawaiBinaan) ?> pegawai berikut.</p>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>NIP</th><th>Nama</th><th>Jabatan</th></tr></thead>
<tbody>
<?php foreach ($pegawaiBinaan as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nip']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$pegawaiBinaan): ?>
<tr><td colspan="3" class="text-center text-muted">Belum ada wewenang penilaian untuk jabatan anda.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
