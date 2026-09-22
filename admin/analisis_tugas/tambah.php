<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');

function normalizeKegiatanItems(array $items): array
{
    $clean = [];
    foreach ($items as $item) {
        $item = trim((string) $item);
        if ($item === '') {
            continue;
        }

        $item = preg_replace('/^(?:[a-zA-Z]|[0-9]+)[\.)]\s*/', '', $item);
        $clean[] = $item;
    }

    return $clean;
}

function formatKegiatanForStorage(array $items): string
{
    $clean = normalizeKegiatanItems($items);
    $formatted = [];
    foreach ($clean as $index => $item) {
        $formatted[] = chr(97 + $index) . '. ' . $item;
    }

    return implode(PHP_EOL, $formatted);
}

$pdo = Database::getConnection();
$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idJabatan = (int) ($_POST['id_jabatan'] ?? 0);
    $tugas = trim($_POST['tugas'] ?? '');
    $kegiatanInput = $_POST['kegiatan'] ?? [];
    if (!is_array($kegiatanInput)) {
        $kegiatanInput = [$kegiatanInput];
    }
    $kegiatan = formatKegiatanForStorage($kegiatanInput);
    $kompetensiSementara = trim($_POST['kompetensi_sementara_jabatan'] ?? '');
    $kompetensiJabatan = trim($_POST['kompetensi_jabatan'] ?? '');

    if ($idJabatan <= 0 || $tugas === '' || $kegiatan === '' || $kompetensiJabatan === '') {
        setFlash('error', 'Jabatan, tugas, kegiatan, dan kompetensi jabatan wajib diisi.');
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO analisis_tugas
            (id_jabatan, tugas, kegiatan, kompetensi_sementara_jabatan, kompetensi_jabatan)
           VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $idJabatan,
            $tugas,
            $kegiatan,
            $kompetensiSementara ?: null,
            $kompetensiJabatan,
        ]);
        setFlash('success', 'Analisis tugas berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}
$pageTitle = 'Tambah analisis tugas';
require_once __DIR__ . '/../../includes/header.php';
?>
<?php if ($flash = getFlash()): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<style>
.analisis-tugas-form textarea,
.analisis-tugas-form input[name="kegiatan[]"] {
  text-align: justify;
}
</style>
<form method="post" class="bg-white p-4 rounded shadow-sm analisis-tugas-form" style="max-width:800px;">
  <div class="mb-3">
    <label class="form-label" for="id_jabatan">Nama jabatan</label>
    <select name="id_jabatan" id="id_jabatan" class="form-select" required>
      <option value="">-- pilih jabatan --</option>
      <?php foreach ($jabatanList as $jabatan): ?>
      <option value="<?= $jabatan['id_jabatan'] ?>" <?= ((int) ($_POST['id_jabatan'] ?? 0) === (int) $jabatan['id_jabatan']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($jabatan['nama_jabatan']) ?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label" for="tugas">Tugas</label>
    <textarea name="tugas" id="tugas" class="form-control" rows="4" required><?= htmlspecialchars($_POST['tugas'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Kegiatan</label>
    <div id="kegiatan-wrapper">
      <div class="kegiatan-item mb-2 d-flex align-items-center gap-2">
        <input type="text" name="kegiatan[]" class="form-control" placeholder="Contoh: Melaksanakan koordinasi internal" value="<?= htmlspecialchars($_POST['kegiatan'][0] ?? '') ?>">
      </div>
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary" id="tambah-kegiatan">+ Tambah poin kegiatan</button>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_sementara_jabatan">Kompetensi sementara jabatan</label>
    <textarea name="kompetensi_sementara_jabatan" id="kompetensi_sementara_jabatan" class="form-control" rows="4"><?= htmlspecialchars($_POST['kompetensi_sementara_jabatan'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_jabatan">Kompetensi jabatan</label>
    <textarea name="kompetensi_jabatan" id="kompetensi_jabatan" class="form-control" rows="4" required><?= htmlspecialchars($_POST['kompetensi_jabatan'] ?? '') ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
const kegiatanWrapper = document.getElementById('kegiatan-wrapper');
const tambahKegiatanBtn = document.getElementById('tambah-kegiatan');

function buildKegiatanItem(value = '') {
  const item = document.createElement('div');
  item.className = 'kegiatan-item mb-2 d-flex align-items-center gap-2';

  const input = document.createElement('input');
  input.type = 'text';
  input.name = 'kegiatan[]';
  input.className = 'form-control';
  input.placeholder = 'Contoh: Melaksanakan koordinasi internal';
  input.value = value;

  const removeButton = document.createElement('button');
  removeButton.type = 'button';
  removeButton.className = 'btn btn-outline-danger btn-sm remove-kegiatan';
  removeButton.setAttribute('aria-label', 'Hapus poin');
  removeButton.textContent = 'Hapus';

  item.appendChild(input);
  item.appendChild(removeButton);
  return item;
}

const existingKegiatan = <?php echo json_encode($_POST['kegiatan'] ?? []); ?>;
if (Array.isArray(existingKegiatan) && existingKegiatan.length > 1) {
  existingKegiatan.forEach((item) => {
    kegiatanWrapper.appendChild(buildKegiatanItem(item));
  });
}

tambahKegiatanBtn.addEventListener('click', () => {
  kegiatanWrapper.appendChild(buildKegiatanItem());
});

kegiatanWrapper.addEventListener('click', (event) => {
  if (event.target.classList.contains('remove-kegiatan')) {
    const item = event.target.closest('.kegiatan-item');
    if (item && kegiatanWrapper.querySelectorAll('.kegiatan-item').length > 1) {
      item.remove();
    }
  }
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
