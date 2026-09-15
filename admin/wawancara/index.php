<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT w.id_wawancara, p.nama_lengkap, d.kompetensi,
            GROUP_CONCAT(CONCAT(d.id_hasil, '. ', d.isi_penilaian)
                ORDER BY d.id_hasil SEPARATOR '<br>') AS daftar_pertanyaan,
            d.file_bukti, d.status_kompetensi
     FROM wawancara w
     JOIN pegawai p ON p.id_pegawai = w.id_pegawai
     LEFT JOIN hasil_wawancara d ON d.id_wawancara = w.id_wawancara
     GROUP BY w.id_wawancara, p.nama_lengkap, d.kompetensi,
              d.file_bukti, d.status_kompetensi
     ORDER BY w.id_wawancara DESC"
)->fetchAll();
$pageTitle = 'Wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Jadwalkan wawancara</a>
<table class="table table-bordered table-striped bg-white"><thead><tr><th>No</th><th>Nama pegawai</th><th>Kompetensi / Jabatan</th><th>Daftar pertanyaan</th><th>Bukti kompetensi</th><th>Kompeten / Cukup / Tidak</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($data as $index => $row): ?><tr><td><?= $index + 1 ?></td><td><?= htmlspecialchars($row['nama_lengkap']) ?></td><td><?= htmlspecialchars($row['kompetensi'] ?: '-') ?></td><td><?= $row['daftar_pertanyaan'] ?: '-' ?></td><td><?= $row['file_bukti'] ? '<a target="_blank" rel="noopener" href="' . BASE_URL . '/' . htmlspecialchars($row['file_bukti']) . '">Lihat file</a>' : '-' ?></td><td><?= htmlspecialchars($row['status_kompetensi'] ?: '-') ?></td><td><a href="jawab.php?id=<?= (int) $row['id_wawancara'] ?>" class="btn btn-sm btn-primary">Isi data</a></td></tr><?php endforeach; ?>
<?php if (!$data): ?><tr><td colspan="7" class="text-center text-muted">Belum ada data wawancara.</td></tr><?php endif; ?>
</tbody></table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
