<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$idKuesioner = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM kuesioner WHERE id_kuesioner = ?');
$stmt->execute([$idKuesioner]);
$kuesioner = $stmt->fetch();
if (!$kuesioner) {
    setFlash('error', 'Kuesioner tidak ditemukan.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teks = trim($_POST['teks_pertanyaan'] ?? '');
    $bobot = (float) ($_POST['bobot'] ?? 1);
    if ($teks === '' || $bobot <= 0) {
        setFlash('error', 'Pertanyaan dan bobot wajib diisi dengan benar.');
    } else {
        $stmt = $pdo->prepare(
            'SELECT COALESCE(MAX(nomor_urut), 0) + 1 AS nomor FROM pertanyaan_kuesioner WHERE id_kuesioner = ?'
        );
        $stmt->execute([$idKuesioner]);
        $nomor = $stmt->fetch()['nomor'];
        $pdo->prepare(
            'INSERT INTO pertanyaan_kuesioner (id_kuesioner, nomor_urut, teks_pertanyaan, bobot)
             VALUES (?, ?, ?, ?)'
        )->execute([$idKuesioner, $nomor, $teks, $bobot]);
        setFlash('success', 'Pertanyaan berhasil ditambahkan.');
        header('Location: pertanyaan.php?id=' . $idKuesioner);
        exit;
    }
}

$stmt = $pdo->prepare(
    'SELECT * FROM pertanyaan_kuesioner WHERE id_kuesioner = ? ORDER BY nomor_urut'
);
$stmt->execute([$idKuesioner]);
$pertanyaan = $stmt->fetchAll();

$pageTitle = 'Kelola pertanyaan: ' . $kuesioner['judul_kuesioner'];
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-3 rounded shadow-sm mb-4 d-flex gap-2 align-items-start">
    <div class="flex-grow-1">
        <textarea name="teks_pertanyaan" class="form-control" placeholder="Tulis pertanyaan baru" required></textarea>
    </div>
    <input type="number" step="0.1" min="0.1" name="bobot" class="form-control" style="width:100px" value="1" required>
    <button type="submit" class="btn btn-primary">Tambah</button>
</form>
<ol class="list-group list-group-numbered bg-white">
<?php foreach ($pertanyaan as $item): ?>
    <li class="list-group-item d-flex justify-content-between">
        <span><?= htmlspecialchars($item['teks_pertanyaan']) ?></span>
        <span class="badge bg-secondary">bobot <?= htmlspecialchars($item['bobot']) ?></span>
    </li>
<?php endforeach; ?>
<?php if (!$pertanyaan): ?>
    <li class="list-group-item text-muted">Belum ada pertanyaan.</li>
<?php endif; ?>
</ol>
<a href="index.php" class="btn btn-secondary mt-3">Kembali</a>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
