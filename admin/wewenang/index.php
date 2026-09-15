<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT w.id_wewenang, jp.nama_jabatan AS penilai, jd.nama_jabatan AS dinilai
     FROM wewenang_penilaian w
     JOIN jabatan jp ON w.id_jabatan_penilai = jp.id_jabatan
     JOIN jabatan jd ON w.id_jabatan_dinilai = jd.id_jabatan
     ORDER BY jp.nama_jabatan, jd.nama_jabatan"
)->fetchAll();
$pageTitle = 'Wewenang penilaian';
require_once __DIR__ . '/../../includes/header.php';
?>
<p class="text-muted">Menentukan jabatan mana yang berwenang menilai jabatan lain
(mis. Ketua/Wakil Ketua menilai Hakim). Data sudah diisi otomatis lewat seed SQL,
tambahkan baris baru langsung ke tabel <code>wewenang_penilaian</code> jika struktur berubah.</p>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Jabatan penilai</th><th>Berwenang menilai jabatan</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr><td><?= htmlspecialchars($row['penilai']) ?></td><td><?= htmlspecialchars($row['dinilai']) ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
