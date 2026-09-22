<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();
$pegawaiList = $pdo->query('SELECT id_pegawai, id_jabatan, nama_lengkap FROM pegawai ORDER BY nama_lengkap')->fetchAll();

function formatGapItems(array $items): string
{
    $clean = [];
    foreach ($items as $item) {
        $item = trim((string) $item);
        if ($item !== '') {
            $item = preg_replace('/^(?:[a-zA-Z]|[0-9]+)[\.)]\s*/', '', $item);
            $clean[] = $item;
        }
    }
    if (count($clean) === 1) {
        return $clean[0];
    }
    return implode(PHP_EOL, array_map(static function (string $item, int $index): string {
        return chr(97 + $index) . '. ' . $item;
    }, $clean, array_keys($clean)));
}

function gapPostValue(string $name): string
{
    $value = $_POST[$name] ?? [];
    if (!is_array($value)) {
        $value = [$value];
    }
    return formatGapItems($value);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJabatan = (int) ($_POST['id_jabatan'] ?? 0);
    $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
    $kompetensiJabatan = gapPostValue('kompetensi_jabatan');
    $kompetensiPegawai = gapPostValue('kompetensi_pegawai_saat_ini');
    $gapKompetensi = gapPostValue('gap_kompetensi');
    $dampak = gapPostValue('dampak');

    if ($idJabatan <= 0 || $idPegawai <= 0 || $kompetensiJabatan === '' || $kompetensiPegawai === '' || $gapKompetensi === '' || $dampak === '') {
        setFlash('error', 'Semua data analisis kesenjangan wajib diisi.');
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO analisis_kesenjangan_kompetensi
               (id_jabatan, id_pegawai, kompetensi_jabatan, kompetensi_pegawai_saat_ini, gap_kompetensi, dampak)
               VALUES (?, ?, ?, ?, ?, ?)'
        );
             $stmt->execute([$idJabatan, $idPegawai, $kompetensiJabatan, $kompetensiPegawai, $gapKompetensi, $dampak]);
        setFlash('success', 'Analisis kesenjangan berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Tambah analisis kesenjangan';
require_once __DIR__ . '/../../includes/header.php';
?>
<style>
.gap-form textarea, .gap-form input[name$="[]"] { text-align: justify; }
</style>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" class="bg-white p-4 rounded shadow-sm gap-form" style="max-width:800px;">
  <div class="mb-3">
    <label class="form-label" for="id_jabatan">Nama jabatan</label>
    <select name="id_jabatan" id="id_jabatan" class="form-select" required>
      <option value="">-- pilih jabatan --</option>
      <?php foreach ($jabatanList as $jabatan): ?>
      <option value="<?= $jabatan['id_jabatan'] ?>" <?= ((int) ($_POST['id_jabatan'] ?? 0) === (int) $jabatan['id_jabatan']) ? 'selected' : '' ?>><?= htmlspecialchars($jabatan['nama_jabatan']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label" for="id_pegawai">Pegawai</label>
    <select name="id_pegawai" id="id_pegawai" class="form-select" required>
      <option value="">-- pilih jabatan terlebih dahulu --</option>
      <?php foreach ($pegawaiList as $pegawai): ?>
      <option value="<?= $pegawai['id_pegawai'] ?>" data-jabatan="<?= $pegawai['id_jabatan'] ?>" <?= ((int) ($_POST['id_pegawai'] ?? 0) === (int) $pegawai['id_pegawai']) ? 'selected' : '' ?>><?= htmlspecialchars($pegawai['nama_lengkap']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php foreach (['kompetensi_jabatan' => 'Kompetensi jabatan', 'kompetensi_pegawai_saat_ini' => 'Kompetensi pegawai saat ini', 'gap_kompetensi' => 'Gap kompetensi', 'dampak' => 'Dampak'] as $field => $label): ?>
  <div class="mb-3"><label class="form-label"><?= $label ?></label><div class="gap-items" data-field="<?= $field ?>">
    <?php $values = $_POST[$field] ?? ['']; if (!is_array($values)) $values = [$values]; ?>
    <?php foreach ($values as $value): ?><div class="gap-item mb-2 d-flex align-items-center gap-2"><input type="text" name="<?= $field ?>[]" class="form-control" value="<?= htmlspecialchars($value) ?>" required><button type="button" class="btn btn-outline-danger btn-sm remove-gap-item">Hapus</button></div><?php endforeach; ?>
  </div><button type="button" class="btn btn-sm btn-outline-primary add-gap-item">+ Tambah poin</button></div>
  <?php endforeach; ?>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
document.querySelectorAll('.add-gap-item').forEach(function (button) {
  button.addEventListener('click', function () {
    var wrapper = button.previousElementSibling;
    var field = wrapper.dataset.field;
    var item = document.createElement('div');
    item.className = 'gap-item mb-2 d-flex align-items-center gap-2';
    item.innerHTML = '<input type="text" name="' + field + '[]" class="form-control" required><button type="button" class="btn btn-outline-danger btn-sm remove-gap-item">Hapus</button>';
    wrapper.appendChild(item);
  });
});
document.addEventListener('click', function (event) {
  if (event.target.classList.contains('remove-gap-item')) {
    var wrapper = event.target.closest('.gap-items');
    if (wrapper.querySelectorAll('.gap-item').length > 1) event.target.closest('.gap-item').remove();
  }
});
</script>
<script>
    var jabatanInput = document.getElementById('id_jabatan');
    var pegawaiInput = document.getElementById('id_pegawai');
    var selectedPegawai = <?= json_encode((string) ($_POST['id_pegawai'] ?? '')) ?>;

    function filterPegawai() {
        var jabatanId = jabatanInput.value;
        pegawaiInput.value = '';
        Array.from(pegawaiInput.options).forEach(function (option) {
            if (!option.dataset.jabatan) {
                option.hidden = false;
                return;
            }
            option.hidden = option.dataset.jabatan !== jabatanId;
        });
        if (selectedPegawai && !pegawaiInput.options[pegawaiInput.selectedIndex].hidden) {
            pegawaiInput.value = selectedPegawai;
        }
        selectedPegawai = '';
    }

    jabatanInput.addEventListener('change', filterPegawai);
    filterPegawai();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
