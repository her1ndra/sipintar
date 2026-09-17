<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$pegawaiList = $pdo->query('SELECT id_pegawai, nama_lengkap, id_jabatan FROM pegawai ORDER BY nama_lengkap')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $tanggal = date('Y-m-d');
    $kompetensi = trim($_POST['kompetensi'] ?? '');
    $pertanyaan = $_POST['pertanyaan'] ?? [];
    $rows = [];
    foreach ($pertanyaan as $value) {
        $value = trim($value);
        if ($value !== '') $rows[] = $value;
    }

    if ($idPegawai <= 0 || $tanggal === '' || $kompetensi === '' || !$rows) {
        setFlash('error', 'Nama pegawai, tanggal, kompetensi, dan pertanyaan wajib diisi.');
    } else {
        $pdo->beginTransaction();
        $user = currentUser();
        $pegawai = array_values(array_filter($pegawaiList, static function ($item) use ($idPegawai) {
            return (int) $item['id_pegawai'] === $idPegawai;
        }))[0] ?? null;
        if (!$pegawai) {
            $pdo->rollBack();
            setFlash('error', 'Pegawai tidak ditemukan.');
        } else {
            $judul = 'Kuesioner ' . date('Y-m-d H:i');
            $stmt = $pdo->prepare(
                'INSERT INTO kuesioner (id_user_pembuat, id_jabatan_dinilai, judul_kuesioner, tahun_periode, status)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$user['id_user'], $pegawai['id_jabatan'], $judul, (int) date('Y', strtotime($tanggal)), 'Aktif']);
            $idKuesioner = $pdo->lastInsertId();
            $pdo->prepare(
                'INSERT INTO wawancara (id_pegawai, id_kuesioner, id_user_penilai, tanggal_wawancara)
                 VALUES (?, ?, ?, ?)'
            )->execute([$idPegawai, $idKuesioner, $user['id_user'], $tanggal]);
            $idWawancara = $pdo->lastInsertId();
            $detail = $pdo->prepare(
                'INSERT INTO pertanyaan_kuesioner (id_kuesioner, nomor_urut, teks_pertanyaan)
                 VALUES (?, ?, ?)'
            );
            foreach ($rows as $index => $row) {
                $detail->execute([$idKuesioner, $index + 1, $row]);
            }
            $pdo->commit();
            setFlash('success', 'Kuesioner berhasil dibuat.');
            header('Location: index.php');
            exit;
        }
    }
}

$pageTitle = 'Buat kuesioner';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm">
    <div class="mb-3"><label class="form-label">Nama pegawai yang dinilai</label>
        <select name="id_pegawai" class="form-select" required><option value="">-- pilih pegawai --</option>
        <?php foreach ($pegawaiList as $pegawai): ?><option value="<?= (int) $pegawai['id_pegawai'] ?>"><?= htmlspecialchars($pegawai['nama_lengkap']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3"><label class="form-label">Kompetensi / Jabatan</label><input name="kompetensi" class="form-control" placeholder="Kompetensi / jabatan" required></div>
    <div class="form-label">Daftar Pertanyaan</div>
    <div id="detail-kuesioner">
        <div class="row g-2 mb-2 detail-row">
            <div class="col-md-11"><input name="pertanyaan[]" class="form-control" placeholder="Daftar pertanyaan" required></div>
            <div class="col-md-1"><button type="button" class="btn btn-outline-danger hapus-baris">X</button></div>
        </div>
    </div>
    <button type="button" id="tambah-baris" class="btn btn-outline-secondary mb-3">+ Tambah pertanyaan</button><br>
    <button type="submit" class="btn btn-primary">Buat kuesioner</button> <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
document.getElementById('tambah-baris').addEventListener('click', function () {
    var row = document.querySelector('.detail-row').cloneNode(true);
    row.querySelector('input').value = '';
    document.getElementById('detail-kuesioner').appendChild(row);
});
document.getElementById('detail-kuesioner').addEventListener('click', function (event) {
    if (event.target.classList.contains('hapus-baris') &&
        document.querySelectorAll('.detail-row').length > 1) {
        event.target.closest('.detail-row').remove();
    }
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
