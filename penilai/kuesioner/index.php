<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$stmt = $pdo->prepare(
    "SELECT k.id_kuesioner, k.judul_kuesioner, k.tahun_periode, k.status,
            j.nama_jabatan, w.id_wawancara, p.nama_lengkap,
            GROUP_CONCAT(CONCAT(q.nomor_urut, '. ', q.teks_pertanyaan)
                ORDER BY q.nomor_urut SEPARATOR '<br>') AS daftar_pertanyaan,
            hk.file_bukti, hk.status_kompetensi
     FROM kuesioner k JOIN jabatan j ON k.id_jabatan_dinilai = j.id_jabatan
     JOIN pertanyaan_kuesioner q ON q.id_kuesioner = k.id_kuesioner
     LEFT JOIN wawancara w ON w.id_kuesioner = k.id_kuesioner
     LEFT JOIN pegawai p ON w.id_pegawai = p.id_pegawai
     LEFT JOIN hasil_kuesioner hk ON hk.id_wawancara = w.id_wawancara
     WHERE k.id_user_pembuat = ?
     GROUP BY k.id_kuesioner, w.id_wawancara, p.nama_lengkap, j.nama_jabatan,
              hk.file_bukti, hk.status_kompetensi
     ORDER BY k.created_at DESC, w.id_wawancara DESC"
);
$stmt->execute([$user['id_user']]);
$data = $stmt->fetchAll();
$pageTitle = 'Kuesioner saya';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Buat kuesioner</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>No</th><th>Nama pegawai</th><th>Kompetensi jabatan</th><th>Daftar pertanyaan</th><th>Bukti kompetensi (jawaban tertulis)</th><th>Kompeten / Tidak</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $index => $row): ?>
<tr>
  <td><?= $index + 1 ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap'] ?: '-') ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
  <td><?= $row['daftar_pertanyaan'] ?: '-' ?></td>
  <td>
    <?php if ($row['file_bukti']): ?><a target="_blank" rel="noopener" href="<?= BASE_URL . '/' . htmlspecialchars($row['file_bukti']) ?>">Lihat file</a><?php else: ?>-<?php endif; ?>
  </td>
  <td><?= htmlspecialchars($row['status_kompetensi'] ?: '-') ?></td>
  <td><?php if ($row['id_wawancara']): ?><a href="jawab.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-primary">Isi data</a><?php endif; ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="7" class="text-center text-muted">Belum ada kuesioner.</td></tr>
<?php endif; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
