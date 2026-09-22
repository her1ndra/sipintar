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

function parseKegiatanItems(?string $value): array
{
    if ($value === null || trim($value) === '') {
        return [];
    }

    $items = [];
    foreach (preg_split('/\r\n|\r|\n/', $value) as $line) {
        $line = trim((string) $line);
        if ($line === '') {
            continue;
        }

        $line = preg_replace('/^(?:[a-zA-Z]|[0-9]+)[\.)]\s*/', '', $line);
        $items[] = $line;
    }

    return $items;
}

$pdo = Database::getConnection();
$id = (int) ($_GET['id'] ?? $_POST['id_analisis_tugas'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$jabatanList = $pdo->query('SELECT id_jabatan, nama_jabatan FROM jabatan ORDER BY nama_jabatan')->fetchAll();
$stmt = $pdo->prepare('SELECT * FROM analisis_tugas WHERE id_analisis_tugas = ?');
$stmt->execute([$id]);
$analisis = $stmt->fetch();

if (!$analisis) {
    setFlash('error', 'Data analisis tugas tidak ditemukan.');
    header('Location: index.php');
    exit;
}

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
        $analisis = array_merge($analisis, $_POST);
    } else {
        $update = $pdo->prepare(
            'UPDATE analisis_tugas
             SET id_jabatan = ?, tugas = ?, kegiatan = ?, kompetensi_sementara_jabatan = ?, kompetensi_jabatan = ?
             WHERE id_analisis_tugas = ?'
        );
        $update->execute([
            $idJabatan,
            $tugas,
            $kegiatan,
            $kompetensiSementara ?: null,
            $kompetensiJabatan,
            $id,
        ]);
        setFlash('success', 'Analisis tugas berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Edit analisis tugas';
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
  <input type="hidden" name="id_analisis_tugas" value="<?= (int) $id ?>">
  <div class="mb-3">
    <label class="form-label" for="id_jabatan">Nama jabatan</label>
    <select name="id_jabatan" id="id_jabatan" class="form-select" required>
      <option value="">-- pilih jabatan --</option>
      <?php foreach ($jabatanList as $jabatan): ?>
      <option value="<?= $jabatan['id_jabatan'] ?>" <?= ((int) ($analisis['id_jabatan'] ?? 0) === (int) $jabatan['id_jabatan']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($jabatan['nama_jabatan']) ?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label" for="tugas">Tugas</label>
    <textarea name="tugas" id="tugas" class="form-control" rows="4" required><?= htmlspecialchars($analisis['tugas'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Kegiatan</label>
    <div id="kegiatan-wrapper">
      <?php $kegiatanItems = parseKegiatanItems($analisis['kegiatan'] ?? ''); ?>
      <?php if ($kegiatanItems): ?>
        <?php foreach ($kegiatanItems as $index => $item): ?>
          <div class="kegiatan-item mb-2 d-flex align-items-center gap-2">
            <input type="text" name="kegiatan[]" class="form-control" value="<?= htmlspecialchars($item) ?>">
            <?php if ($index > 0): ?>
              <button type="button" class="btn btn-outline-danger btn-sm remove-kegiatan" aria-label="Hapus poin">Hapus</button>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="kegiatan-item mb-2 d-flex align-items-center gap-2">
          <input type="text" name="kegiatan[]" class="form-control" placeholder="Contoh: Melaksanakan koordinasi internal">
        </div>
      <?php endif; ?>
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary" id="tambah-kegiatan">+ Tambah poin kegiatan</button>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_sementara_jabatan">Kompetensi sementara jabatan</label>
    <textarea name="kompetensi_sementara_jabatan" id="kompetensi_sementara_jabatan" class="form-control" rows="4"><?= htmlspecialchars($analisis['kompetensi_sementara_jabatan'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="kompetensi_jabatan">Kompetensi jabatan</label>
    <textarea name="kompetensi_jabatan" id="kompetensi_jabatan" class="form-control" rows="4" required><?= htmlspecialchars($analisis['kompetensi_jabatan'] ?? '') ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Simpan perubahan</button>
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
