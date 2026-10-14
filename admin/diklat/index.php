<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
        "SELECT kd.*, p.nama_lengkap, j.nama_jabatan, d.nama_diklat,
          COALESCE(
        NULLIF(g.gap_kompetensi, ''),
        (SELECT g_latest.gap_kompetensi
         FROM analisis_kesenjangan_kompetensi g_latest
         WHERE g_latest.id_pegawai = kd.id_pegawai
         ORDER BY g_latest.created_at DESC, g_latest.id_kesenjangan DESC
         LIMIT 1)
          ) AS gap_kompetensi
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
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<div class="table-responsive">
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Nama</th><th>Jabatan</th><th>Gap kompetensi</th><th>Diklat yang dibutuhkan</th><th>Metode pengembangan</th><th>Prioritas</th><th>Status</th><th>Tahun rencana</th><th class="text-center">Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td class="text-justify"><?= nl2br(htmlspecialchars($row['gap_kompetensi'] ?? '-')) ?></td>
  <td><?= htmlspecialchars($row['diklat_lainnya'] ?: ($row['nama_diklat'] ?? '-')) ?></td>
  <td><?= htmlspecialchars($row['metode_pengembangan'] ?? '-') ?></td>
  <td class="text-center align-middle"><?= htmlspecialchars($row['prioritas']) ?></td>
  <td><?= htmlspecialchars($row['status']) ?></td>
  <td><?= htmlspecialchars($row['tahun_rencana']) ?></td>
  <td class="align-middle diklat-action-cell">
    <div class="diklat-actions">
    <?php if (in_array($row['status'], ['Diusulkan', 'Ditolak'], true)): ?>
    <form method="post" action="<?= BASE_URL ?>/diklat/acc.php" onsubmit="return confirm('Setujui usulan ini?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int) $row['id_kebutuhan'] ?>"><input type="hidden" name="status" value="Disetujui"><button type="submit" class="btn diklat-action-approve" title="Setujui" aria-label="Setujui"><i class="fas fa-clipboard-check" aria-hidden="true"></i></button></form>
    <?php endif; ?>
    <?php if (in_array($row['status'], ['Diusulkan', 'Disetujui'], true)): ?>
    <form method="post" action="<?= BASE_URL ?>/diklat/acc.php" onsubmit="return confirm('Tolak usulan ini?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int) $row['id_kebutuhan'] ?>"><input type="hidden" name="status" value="Ditolak"><button type="submit" class="btn diklat-action-reject" title="Tolak" aria-label="Tolak"><i class="fas fa-times" aria-hidden="true"></i></button></form>
    <?php endif; ?>
    <?php if ($row['status'] === 'Disetujui'): ?>
    <form method="post" action="<?= BASE_URL ?>/diklat/acc.php" onsubmit="return confirm('Tandai diklat ini telah terlaksana?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int) $row['id_kebutuhan'] ?>"><input type="hidden" name="status" value="Terlaksana"><button type="submit" class="btn diklat-action-complete" title="Tandai terlaksana" aria-label="Tandai terlaksana"><i class="fas fa-check-double" aria-hidden="true"></i></button></form>
    <?php endif; ?>
    <?php if ($row['status'] === 'Ditolak'): ?>
    <form method="post" action="<?= BASE_URL ?>/diklat/acc.php" onsubmit="return confirm('Ajukan kembali kebutuhan diklat ini?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int) $row['id_kebutuhan'] ?>"><input type="hidden" name="status" value="Diusulkan"><button type="submit" class="btn diklat-action-resubmit" title="Ajukan kembali" aria-label="Ajukan kembali"><i class="fas fa-undo" aria-hidden="true"></i></button></form>
    <?php endif; ?>
    <a href="edit.php?id=<?= (int) $row['id_kebutuhan'] ?>" class="btn diklat-action-edit" title="Edit" aria-label="Edit"><i class="fas fa-edit" aria-hidden="true"></i></a>
    <form method="post" action="hapus.php" onsubmit="return confirm('Hapus kebutuhan diklat ini?')"><?= csrfField() ?><input type="hidden" name="id" value="<?= (int) $row['id_kebutuhan'] ?>"><button type="submit" class="btn diklat-action-delete" title="Hapus" aria-label="Hapus"><i class="fas fa-trash-alt" aria-hidden="true"></i></button></form>
    </div>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="9" class="text-center text-muted">Belum ada penentuan diklat.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
