<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
// Hanya pegawai berjabatan Ketua/Wakil Ketua/Panitera/Sekretaris yang relevan jadi Penilai
$pegawaiList = $pdo->query(
    "SELECT p.id_pegawai, p.nip, p.nama_lengkap, j.nama_jabatan
     FROM pegawai p JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE j.is_penilai = 1
     ORDER BY p.nama_lengkap"
)->fetchAll();
$akunPegawai = $pdo->query('SELECT id_pegawai FROM users WHERE id_pegawai IS NOT NULL')->fetchAll(PDO::FETCH_COLUMN);
$akunPegawai = array_map('intval', $akunPegawai);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nip = trim($_POST['nip'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? '';
  $idPegawai = !empty($_POST['id_pegawai']) ? (int) $_POST['id_pegawai'] : null;

    if ($role === 'Penilai' && $idPegawai) {
      $nipStmt = $pdo->prepare('SELECT nip FROM pegawai WHERE id_pegawai = ?');
      $nipStmt->execute([$idPegawai]);
      $nip = (string) $nipStmt->fetchColumn();
    }

  if ($nip === '' || $password === '' || !in_array($role, ['Admin', 'Penilai'], true)) {
    setFlash('error', 'NIP, kata sandi, dan role wajib diisi dengan benar.');
  } else  if ($role === 'Penilai' && !$idPegawai) {
      setFlash('error', 'Role Penilai wajib dikaitkan dengan data pegawai.');
  } else {
  $existingStmt = $pdo->prepare(
    'SELECT id_user, id_pegawai, nip FROM users WHERE nip = ? OR (? IS NOT NULL AND id_pegawai = ?) LIMIT 1'
  );
    $existingStmt->execute([$nip, $idPegawai, $idPegawai]);
    $existingAccount = $existingStmt->fetch();

    if ($existingAccount) {
      $message = $existingAccount['id_pegawai'] !== null && $idPegawai !== null
        && (int) $existingAccount['id_pegawai'] === $idPegawai
        ? 'Pegawai tersebut sudah memiliki akun user.'
        : 'NIP tersebut sudah memiliki akun user.';
      setFlash('error', $message);
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare(
        'INSERT INTO users (id_pegawai, nip, password, role) VALUES (?, ?, ?, ?)'
      );

      try {
          $stmt->execute([$idPegawai, $nip, $hash, $role]);
        setFlash('success', 'Akun user berhasil dibuat.');
        header('Location: index.php');
        exit;
      } catch (PDOException $exception) {
        if ($exception->getCode() === '23000') {
          setFlash('error', 'NIP tersebut sudah memiliki akun user atau pegawai sudah terhubung ke akun lain.');
        } else {
          throw $exception;
        }
      }
    }
    }
}
$pageTitle = 'Tambah akun user';
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="post" class="bg-white p-4 rounded shadow-sm" style="max-width:500px;">
  <div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" id="role" class="form-select" required
      onchange="document.getElementById('pegawaiWrap').style.display = this.value === 'Penilai' ? 'block' : 'none'">
      <option value="Admin">Admin</option>
      <option value="Penilai">Penilai</option>
    </select>
  </div>
  <div class="mb-3" id="pegawaiWrap">
    <label class="form-label">Pegawai (khusus role Penilai)</label>
    <select name="id_pegawai" class="form-select">
      <option value="">-- tidak terkait pegawai --</option>
      <?php foreach ($pegawaiList as $p): ?>
      <?php if (in_array((int) $p['id_pegawai'], $akunPegawai, true)) continue; ?>
      <option value="<?= $p['id_pegawai'] ?>" data-nip="<?= htmlspecialchars($p['nip']) ?>">
        <?= htmlspecialchars($p['nama_lengkap']) ?> (<?= htmlspecialchars($p['nama_jabatan']) ?>)
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">NIP</label>
    <input type="text" name="nip" id="nip" class="form-control" inputmode="numeric" pattern="[0-9]+" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Kata sandi</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Simpan</button>
  <a href="index.php" class="btn btn-secondary">Batal</a>
</form>
<script>
  var roleInput = document.getElementById('role');
  var pegawaiInput = document.querySelector('select[name="id_pegawai"]');
  var nipInput = document.getElementById('nip');
  var pegawaiWrap = document.getElementById('pegawaiWrap');

  function updateNipFromPegawai() {
    var selectedOption = pegawaiInput.options[pegawaiInput.selectedIndex];
    var isPenilai = roleInput.value === 'Penilai';

    pegawaiWrap.style.display = isPenilai ? 'block' : 'none';
    nipInput.readOnly = isPenilai;
    if (isPenilai) {
      nipInput.value = selectedOption ? selectedOption.dataset.nip || '' : '';
    }
  }

  roleInput.addEventListener('change', updateNipFromPegawai);
  pegawaiInput.addEventListener('change', updateNipFromPegawai);
  updateNipFromPegawai();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>