<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$id = (int) ($_GET['id'] ?? $_POST['id_kebutuhan'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM kebutuhan_diklat WHERE id_kebutuhan = ?');
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) {
    setFlash('error', 'Kebutuhan diklat tidak ditemukan.');
    header('Location: index.php');
    exit;
}
$pegawaiList = $pdo->query(
    'SELECT p.id_pegawai, p.nama_lengkap, j.nama_jabatan FROM pegawai p JOIN jabatan j ON j.id_jabatan = p.id_jabatan WHERE p.status_aktif = "Aktif" ORDER BY j.id_jabatan, p.nama_lengkap'
)->fetchAll();
$diklatList = $pdo->query('SELECT id_diklat, nama_diklat FROM diklat ORDER BY nama_diklat')->fetchAll();
$gapList = $pdo->query(
    'SELECT id_kesenjangan, id_pegawai, gap_kompetensi FROM analisis_kesenjangan_kompetensi ORDER BY created_at DESC'
)->fetchAll();
$gapByPegawai = [];
foreach ($gapList as $gap) {
    $pegawaiId = (int) $gap['id_pegawai'];
    if (!isset($gapByPegawai[$pegawaiId])) {
        $gapByPegawai[$pegawaiId] = [
            'id' => (int) $gap['id_kesenjangan'],
            'text' => $gap['gap_kompetensi'],
        ];
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $idKesenjangan = ($_POST['id_kesenjangan'] ?? '') !== '' ? (int) $_POST['id_kesenjangan'] : null;
    $pilihanDiklat = $_POST['id_diklat'] ?? '';
    $idDiklat = $pilihanDiklat !== '' && $pilihanDiklat !== 'lainnya' ? (int) $pilihanDiklat : null;
    $diklatLainnya = $pilihanDiklat === 'lainnya' ? trim($_POST['diklat_lainnya'] ?? '') : '';
    $metode = trim($_POST['metode_pengembangan'] ?? '');
    $prioritas = $_POST['prioritas'] ?? '';
    $tahun = (int) ($_POST['tahun_rencana'] ?? 0);
    $status = $_POST['status'] ?? 'Diusulkan';
    $catatan = trim($_POST['catatan'] ?? '');
    $diklatValid = $pilihanDiklat === '' || ($pilihanDiklat === 'lainnya' ? $diklatLainnya !== '' : false);
    if ($idDiklat !== null) {
        $diklatStmt = $pdo->prepare('SELECT id_diklat FROM diklat WHERE id_diklat = ?');
        $diklatStmt->execute([$idDiklat]);
        $diklatValid = (bool) $diklatStmt->fetch();
    }
    $gapStmt = $pdo->prepare('SELECT id_pegawai FROM analisis_kesenjangan_kompetensi WHERE id_kesenjangan = ?');
    $gapStmt->execute([$idKesenjangan]);
    $gap = $idKesenjangan === null ? null : $gapStmt->fetch();
    if ($idKesenjangan !== null && (!$gap || (int) $gap['id_pegawai'] !== $idPegawai)) {
        setFlash('error', 'Gap kompetensi harus milik pegawai yang dipilih.');
    } elseif ($idPegawai <= 0 || !$diklatValid || $metode === '' || !in_array($prioritas, ['Tinggi', 'Sedang', 'Rendah'], true) || !in_array($status, ['Diusulkan', 'Disetujui', 'Terlaksana', 'Ditolak'], true) || $tahun < 2000) {
        setFlash('error', 'Data kebutuhan diklat belum lengkap atau tidak valid.');
    } else {
        if ($pilihanDiklat === 'lainnya') {
            $masterDiklatStmt = $pdo->prepare('SELECT id_diklat FROM diklat WHERE nama_diklat = ? LIMIT 1');
            $masterDiklatStmt->execute([$diklatLainnya]);
            $idDiklat = $masterDiklatStmt->fetchColumn();
            if (!$idDiklat) {
                $pdo->prepare('INSERT INTO diklat (nama_diklat) VALUES (?)')->execute([$diklatLainnya]);
                $idDiklat = (int) $pdo->lastInsertId();
            }
            $diklatLainnya = '';
        }
        $pdo->prepare('UPDATE kebutuhan_diklat SET id_pegawai = ?, id_kesenjangan = ?, id_diklat = ?, diklat_lainnya = ?, metode_pengembangan = ?, prioritas = ?, status = ?, tahun_rencana = ?, catatan = ? WHERE id_kebutuhan = ?')
            ->execute([$idPegawai, $idKesenjangan, $idDiklat, $diklatLainnya ?: null, $metode, $prioritas, $status, $tahun, $catatan, $id]);
        setFlash('success', 'Kebutuhan diklat berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
    $data = array_merge($data, $_POST);
}
$pageTitle = 'Edit kebutuhan diklat';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:600px;">
<input type="hidden" name="id_kebutuhan" value="<?= $id ?>">
<?php
$selected = static function (string $field, string $value) use ($data): string {
    return (string) ($data[$field] ?? '') === $value ? 'selected' : '';
};
?>
<div class="mb-3"><label class="form-label">Nama</label><select name="id_pegawai" class="form-select" required>
<?php foreach ($pegawaiList as $pegawai): ?><option value="<?= (int) $pegawai['id_pegawai'] ?>" <?= (int) ($data['id_pegawai'] ?? 0) === (int) $pegawai['id_pegawai'] ? 'selected' : '' ?>><?= htmlspecialchars($pegawai['nama_jabatan'] . ' - ' . $pegawai['nama_lengkap']) ?></option><?php endforeach; ?>
</select></div>
<div class="mb-3"><label class="form-label">Gap kompetensi</label>
<input type="hidden" name="id_kesenjangan" id="id_kesenjangan" value="<?= (int) ($data['id_kesenjangan'] ?? 0) ?>">
<textarea id="gap_kompetensi" class="form-control" rows="3" readonly placeholder="Gap akan terisi setelah pegawai dipilih"></textarea>
<div class="form-text">Gap kompetensi diambil otomatis dari data analisis pegawai.</div></div>
<div class="mb-3"><label class="form-label">Diklat yang dibutuhkan</label><select name="id_diklat" id="id_diklat" class="form-select"><option value="">-- belum ditentukan --</option>
<?php foreach ($diklatList as $diklat): ?><option value="<?= (int) $diklat['id_diklat'] ?>" <?= (int) ($data['id_diklat'] ?? 0) === (int) $diklat['id_diklat'] ? 'selected' : '' ?>><?= htmlspecialchars($diklat['nama_diklat']) ?></option><?php endforeach; ?><option value="lainnya" <?= !empty($data['diklat_lainnya']) && empty($data['id_diklat']) ? 'selected' : '' ?>>Lainnya</option></select></div>
<div class="mb-3" id="diklat_lainnya_group" style="display:none;"><label class="form-label">Nama diklat lainnya</label><input type="text" name="diklat_lainnya" class="form-control" value="<?= htmlspecialchars($data['diklat_lainnya'] ?? '') ?>"></div>
<div class="mb-3"><label class="form-label">Metode pengembangan</label><input name="metode_pengembangan" class="form-control" value="<?= htmlspecialchars($data['metode_pengembangan'] ?? '') ?>" required></div>
<div class="mb-3"><label class="form-label">Prioritas</label><select name="prioritas" class="form-select"><?php foreach (['Tinggi', 'Sedang', 'Rendah'] as $value): ?><option value="<?= $value ?>" <?= $selected('prioritas', $value) ?>><?= $value ?></option><?php endforeach; ?></select></div>
<div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><?php foreach (['Diusulkan', 'Disetujui', 'Terlaksana', 'Ditolak'] as $value): ?><option value="<?= $value ?>" <?= $selected('status', $value) ?>><?= $value ?></option><?php endforeach; ?></select></div>
<div class="mb-3"><label class="form-label">Tahun rencana</label><input type="number" name="tahun_rencana" min="2000" class="form-control" value="<?= htmlspecialchars($data['tahun_rencana']) ?>" required></div>
<div class="mb-3"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control"><?= htmlspecialchars($data['catatan'] ?? '') ?></textarea></div>
<button class="btn btn-primary">Simpan</button> <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
const gapByPegawai = <?= json_encode($gapByPegawai, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const pegawaiSelect = document.querySelector('select[name="id_pegawai"]');
const gapIdInput = document.getElementById('id_kesenjangan');
const gapText = document.getElementById('gap_kompetensi');
const diklatSelect = document.getElementById('id_diklat');
const diklatLainnyaGroup = document.getElementById('diklat_lainnya_group');

function updateDiklatLainnya() {
    diklatLainnyaGroup.style.display = diklatSelect.value === 'lainnya' ? '' : 'none';
}

function updateGap() {
  const gap = gapByPegawai[pegawaiSelect.value];
  gapIdInput.value = gap ? gap.id : '';
  gapText.value = gap ? gap.text : '';
}

pegawaiSelect.addEventListener('change', updateGap);
diklatSelect.addEventListener('change', updateDiklatLainnya);
updateDiklatLainnya();
updateGap();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
