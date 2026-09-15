<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$pegawaiList = $pdo->query('SELECT id_pegawai, nama_lengkap FROM pegawai ORDER BY nama_lengkap')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $tanggal = $_POST['tanggal_wawancara'] ?? '';
    $kompetensi = trim($_POST['kompetensi'] ?? '');
    $pertanyaan = $_POST['pertanyaan'] ?? [];
    $rows = [];
    foreach ($pertanyaan as $value) {
        $isiPertanyaan = trim($value);
        if ($isiPertanyaan !== '') {
            $rows[] = $isiPertanyaan;
        }
    }
    if ($idPegawai <= 0 || $tanggal === '' || $kompetensi === '' || !$rows) {
        setFlash('error', 'Nama pegawai, tanggal, kompetensi, dan pertanyaan wajib diisi.');
    } else {
        $pdo->beginTransaction();
        $pdo->prepare('INSERT INTO wawancara (id_pegawai, id_kuesioner, id_user_penilai, tanggal_wawancara) VALUES (?, NULL, ?, ?)')
            ->execute([$idPegawai, currentUser()['id_user'], $tanggal]);
        $idWawancara = $pdo->lastInsertId();
        $detail = $pdo->prepare(
            'INSERT INTO hasil_wawancara (id_wawancara, kompetensi, isi_penilaian)
             VALUES (?, ?, ?)'
        );
        foreach ($rows as $row) {
            $detail->execute([$idWawancara, $kompetensi, $row]);
        }
        $pdo->commit();
        setFlash('success', 'Jadwal wawancara berhasil dibuat.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Buat wawancara';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3"><label class="form-label">Nama pegawai yang dinilai</label>
        <select name="id_pegawai" class="form-select" required><option value="">-- pilih pegawai --</option>
        <?php foreach ($pegawaiList as $pegawai): ?><option value="<?= (int) $pegawai['id_pegawai'] ?>"><?= htmlspecialchars($pegawai['nama_lengkap']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3"><label class="form-label">Tanggal wawancara</label><input type="date" name="tanggal_wawancara" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Kompetensi / Jabatan</label><input name="kompetensi" class="form-control" placeholder="Kompetensi / jabatan" required></div>
    <div class="form-label">Daftar Pertanyaan</div>
    <div id="detail-wawancara">
        <div class="row g-2 mb-2 detail-row">
            <div class="col-md-11"><input name="pertanyaan[]" class="form-control" placeholder="Daftar pertanyaan" required></div>
            <div class="col-md-1"><button type="button" class="btn btn-outline-danger hapus-baris">X</button></div>
        </div>
    </div>
    <button type="button" id="tambah-baris" class="btn btn-outline-secondary mb-3">+ Tambah pertanyaan</button><br>
    <button type="submit" class="btn btn-primary">Jadwalkan wawancara</button> <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
document.getElementById('tambah-baris').addEventListener('click', function () {
    var row = document.querySelector('.detail-row').cloneNode(true);
    row.querySelector('input').value = '';
    document.getElementById('detail-wawancara').appendChild(row);
});
document.getElementById('detail-wawancara').addEventListener('click', function (event) {
    if (event.target.classList.contains('hapus-baris') &&
        document.querySelectorAll('.detail-row').length > 1) {
        event.target.closest('.detail-row').remove();
    }
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
