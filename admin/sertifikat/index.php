<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$idPegawai = filter_input(INPUT_GET, 'id_pegawai', FILTER_VALIDATE_INT);
$pegawaiTerpilih = null;

if ($idPegawai) {
    $pegawaiStmt = $pdo->prepare('SELECT id_pegawai, nama_lengkap, nip FROM pegawai WHERE id_pegawai = ?');
    $pegawaiStmt->execute([$idPegawai]);
    $pegawaiTerpilih = $pegawaiStmt->fetch();
    if (!$pegawaiTerpilih) {
        setFlash('error', 'Data pegawai tidak ditemukan.');
        header('Location: index.php');
        exit;
    }

    $sertifikatStmt = $pdo->prepare(
        'SELECT * FROM sertifikat_pegawai WHERE id_pegawai = ? ORDER BY created_at DESC'
    );
    $sertifikatStmt->execute([$idPegawai]);
    $data = $sertifikatStmt->fetchAll();
    $pageTitle = 'Sertifikat: ' . $pegawaiTerpilih['nama_lengkap'];
} else {
    $data = $pdo->query(
        'SELECT p.id_pegawai, p.nama_lengkap, p.nip, COUNT(s.id_sertifikat) AS jumlah_sertifikat
         FROM pegawai p
         JOIN jabatan j ON j.id_jabatan = p.id_jabatan
         LEFT JOIN sertifikat_pegawai s ON s.id_pegawai = p.id_pegawai
         GROUP BY p.id_pegawai, p.nama_lengkap, p.nip, j.id_jabatan
         ORDER BY j.id_jabatan, p.nama_lengkap'
    )->fetchAll();
    $pageTitle = 'Sertifikat pegawai';
}

require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($pegawaiTerpilih): ?>
<a href="index.php" class="btn btn-light mb-3"><i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> Daftar pegawai</a>
<a href="tambah.php?id_pegawai=<?= (int) $pegawaiTerpilih['id_pegawai'] ?>" class="btn btn-primary mb-3"><i class="fas fa-plus mr-1" aria-hidden="true"></i> Tambah sertifikat</a>
<div class="mb-3">
  <div class="font-weight-bold"><?= htmlspecialchars($pegawaiTerpilih['nama_lengkap']) ?></div>
  <div class="text-muted">NIP <?= htmlspecialchars($pegawaiTerpilih['nip']) ?></div>
</div>
<div class="table-responsive">
<table class="table table-bordered table-striped bg-white" style="table-layout: fixed; min-width: 800px;">
<colgroup>
  <col style="width: 33%"><col style="width: 27%"><col style="width: 12%">
  <col style="width: 18%"><col style="width: 10%">
</colgroup>
<thead><tr><th>Nama sertifikat</th><th>Penyelenggara</th><th>Terbit</th><th>File</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_sertifikat']) ?></td>
  <td><?= htmlspecialchars($row['penyelenggara'] ?? '-') ?></td>
  <td><?= htmlspecialchars($row['tanggal_terbit'] ?? '-') ?></td>
  <td class="text-center align-middle">
    <?php if (!empty($row['file_sertifikat'])): ?>
      <a href="<?= BASE_URL . '/' . htmlspecialchars($row['file_sertifikat']) ?>" target="_blank" rel="noopener">Lihat file</a>
    <?php else: ?>
      -
    <?php endif; ?>
  </td>
  <td class="text-center align-middle">
    <a href="edit.php?id=<?= (int) $row['id_sertifikat'] ?>" class="btn btn-sm btn-warning table-action-btn" title="Edit" aria-label="Edit sertifikat"><i class="fas fa-edit" aria-hidden="true"></i></a>
    <a href="hapus.php?id=<?= (int) $row['id_sertifikat'] ?>" class="btn btn-sm btn-danger table-action-btn" title="Hapus" aria-label="Hapus sertifikat" onclick="return confirm('Hapus sertifikat ini?')"><i class="fas fa-trash-alt" aria-hidden="true"></i></a>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="5" class="text-center text-muted">Belum ada sertifikat untuk pegawai ini.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php else: ?>
<a href="tambah.php" class="btn btn-primary mb-3"><i class="fas fa-plus mr-1" aria-hidden="true"></i> Tambah sertifikat</a>
<div class="table-responsive">
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Nama pegawai</th><th>NIP</th><th class="text-center">Jumlah sertifikat</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><a class="font-weight-bold text-primary" href="index.php?id_pegawai=<?= (int) $row['id_pegawai'] ?>"><?= htmlspecialchars($row['nama_lengkap']) ?></a></td>
  <td><?= htmlspecialchars($row['nip']) ?></td>
  <td class="text-center"><?= (int) $row['jumlah_sertifikat'] ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="3" class="text-center text-muted">Belum ada data pegawai.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>