<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Penilai');

$pdo = Database::getConnection();
$user = currentUser();
$idKebutuhan = (int) ($_GET['id'] ?? $_POST['id_kebutuhan'] ?? 0);
$jabatanIds = getJabatanWewenang($pdo, (int) $user['id_jabatan']);

$kebutuhan = null;
if ($idKebutuhan > 0 && $jabatanIds) {
    $placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT kd.*
         FROM kebutuhan_diklat kd
         JOIN pegawai p ON p.id_pegawai = kd.id_pegawai
         WHERE kd.id_kebutuhan = ? AND p.id_jabatan IN ($placeholders)"
    );
    $stmt->execute(array_merge([$idKebutuhan], $jabatanIds));
    $kebutuhan = $stmt->fetch();
}
if (!$kebutuhan) {
    setFlash('error', 'Kebutuhan diklat tidak ditemukan atau di luar wewenang Anda.');
    header('Location: index.php');
    exit;
}

$diklatList = $pdo->query('SELECT id_diklat, nama_diklat FROM diklat ORDER BY nama_diklat')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pilihanDiklat = (string) ($_POST['id_diklat'] ?? '');
    $idDiklat = $pilihanDiklat !== '' && $pilihanDiklat !== 'lainnya' ? (int) $pilihanDiklat : null;
    $diklatLainnya = $pilihanDiklat === 'lainnya' ? trim($_POST['diklat_lainnya'] ?? '') : '';
    $prioritas = (string) ($_POST['prioritas'] ?? '');
    $tahun = (int) ($_POST['tahun_rencana'] ?? 0);
    $catatan = trim($_POST['catatan'] ?? '');

    $diklatValid = $pilihanDiklat === '' || ($pilihanDiklat === 'lainnya' && $diklatLainnya !== '');
    if ($idDiklat !== null) {
        $cekDiklat = $pdo->prepare('SELECT 1 FROM diklat WHERE id_diklat = ?');
        $cekDiklat->execute([$idDiklat]);
        $diklatValid = (bool) $cekDiklat->fetchColumn();
    }

    if (!$diklatValid || !in_array($prioritas, ['Sangat Tinggi', 'Tinggi', 'Sedang', 'Rendah'], true) || $tahun < (int) date('Y') || $tahun > 2100) {
        setFlash('error', 'Data kebutuhan diklat tidak valid. Periksa diklat, prioritas, dan tahun rencana.');
        $kebutuhan = array_merge($kebutuhan, $_POST);
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

        $update = $pdo->prepare(
            "UPDATE kebutuhan_diklat kd
             JOIN pegawai p ON p.id_pegawai = kd.id_pegawai
             JOIN wewenang_penilaian w
               ON w.id_jabatan_dinilai = p.id_jabatan
              AND w.id_jabatan_penilai = ?
             SET kd.id_diklat = ?, kd.diklat_lainnya = ?, kd.prioritas = ?, kd.tahun_rencana = ?, kd.catatan = ?
             WHERE kd.id_kebutuhan = ?"
        );
        $update->execute([
            (int) $user['id_jabatan'],
            $idDiklat,
            $diklatLainnya ?: null,
            $prioritas,
            $tahun,
            $catatan,
            $idKebutuhan,
        ]);

        auditLog('edit_kebutuhan_diklat', 'Edit kebutuhan diklat #' . $idKebutuhan);
        setFlash('success', 'Kebutuhan diklat berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Edit kebutuhan diklat';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:600px;"><?= csrfField() ?>
  <input type="hidden" name="id_kebutuhan" value="<?= $idKebutuhan ?>">
  <div class="mb-3">
    <label class="form-label">Diklat</label>
    <select name="id_diklat" id="id_diklat" class="form-select">
      <option value="">-- belum ditentukan --</option>
      <?php foreach ($diklatList as $diklat): ?>
      <option value="<?= (int) $diklat['id_diklat'] ?>" <?= (string) ($kebutuhan['id_diklat'] ?? '') === (string) $diklat['id_diklat'] ? 'selected' : '' ?>><?= htmlspecialchars($diklat['nama_diklat']) ?></option>
      <?php endforeach; ?>
      <option value="lainnya" <?= !empty($kebutuhan['diklat_lainnya']) && empty($kebutuhan['id_diklat']) ? 'selected' : '' ?>>Lainnya</option>
    </select>
  </div>
  <div class="mb-3" id="diklat_lainnya_group" style="display:none;">
    <label class="form-label">Nama diklat lainnya</label>
    <input type="text" name="diklat_lainnya" class="form-control" value="<?= htmlspecialchars($kebutuhan['diklat_lainnya'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Prioritas</label>
    <select name="prioritas" class="form-select" required>
      <?php foreach (['Sangat Tinggi', 'Tinggi', 'Sedang', 'Rendah'] as $prioritas): ?>
      <option value="<?= $prioritas ?>" <?= ($kebutuhan['prioritas'] ?? '') === $prioritas ? 'selected' : '' ?>><?= $prioritas ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Tahun rencana</label>
    <input type="number" name="tahun_rencana" class="form-control" min="<?= date('Y') ?>" max="2100" value="<?= htmlspecialchars((string) ($kebutuhan['tahun_rencana'] ?? date('Y') + 1)) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="catatan" class="form-control"><?= htmlspecialchars($kebutuhan['catatan'] ?? '') ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
const diklatSelect = document.getElementById('id_diklat');
const diklatLainnyaGroup = document.getElementById('diklat_lainnya_group');
function updateDiklatLainnya() {
  diklatLainnyaGroup.style.display = diklatSelect.value === 'lainnya' ? '' : 'none';
}
diklatSelect.addEventListener('change', updateDiklatLainnya);
updateDiklatLainnya();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
