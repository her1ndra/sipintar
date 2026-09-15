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
        "SELECT g.*, p.nama_lengkap, k.nama_kompetensi
         FROM analisis_gap g
         JOIN pegawai p ON g.id_pegawai = p.id_pegawai
         JOIN kompetensi k ON g.id_kompetensi = k.id_kompetensi
         WHERE p.id_jabatan IN ($placeholders)
         ORDER BY g.periode_analisis DESC"
    );
    $stmt->execute($jabatanIds);
    $data = $stmt->fetchAll();
}
$pageTitle = 'Analisis gap kompetensi';
require_once __DIR__ . '/../../includes/header.php';
?>
<p class="text-muted">Gap dihitung dari selisih <code>level_standar</code>
(tabel <code>standar_kompetensi_jabatan</code>) dengan <code>level_aktual</code> hasil
<code>penilaian_kompetensi</code>. TODO: buat proses yang otomatis mengisi tabel
<code>analisis_gap</code> setiap ada penilaian baru tersimpan.</p>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Pegawai</th><th>Kompetensi</th><th>Standar</th><th>Aktual</th><th>Gap</th><th>Kategori</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_kompetensi']) ?></td>
  <td><?= $row['level_standar'] ?></td>
  <td><?= $row['level_aktual'] ?></td>
  <td><?= $row['nilai_gap'] ?></td>
  <td><?= htmlspecialchars($row['kategori_gap']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="6" class="text-center text-muted">Belum ada data analisis gap.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
