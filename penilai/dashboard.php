<?php
require_once __DIR__ . '/../config/config.php';
requireRole('Penilai');
$pdo = Database::getConnection();
$user = currentUser();
$jabatanDinilai = getJabatanWewenang($pdo, (int) $user['id_jabatan']);
require_once __DIR__ . '/../includes/dashboard_penilaian.php';
$ringkasan = dashboardPenilaianRingkasan($pdo, $jabatanDinilai);
$persentaseSelesai = $ringkasan['total'] > 0
    ? (int) round($ringkasan['selesai'] / $ringkasan['total'] * 100)
    : 0;
$trenJson = json_encode(
    $ringkasan['tren'],
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR
);

$pegawaiBinaan = [];
if ($jabatanDinilai) {
    $placeholders = implode(',', array_fill(0, count($jabatanDinilai), '?'));
    $stmt = $pdo->prepare(
        "SELECT p.id_pegawai, p.nip, p.nama_lengkap, j.nama_jabatan
         FROM pegawai p JOIN jabatan j ON p.id_jabatan = j.id_jabatan
         WHERE p.id_jabatan IN ($placeholders)
         ORDER BY p.nama_lengkap"
    );
    $stmt->execute($jabatanDinilai);
    $pegawaiBinaan = $stmt->fetchAll();
}
$pageTitle = 'Dashboard penilai';
require_once __DIR__ . '/../includes/header.php';
?>
<p class="text-muted">Sebagai <strong><?= htmlspecialchars($user['nama_jabatan']) ?></strong>,
anda berwenang menilai <?= count($pegawaiBinaan) ?> pegawai berikut.</p>
<div class="row">
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card stat-card border-left-danger shadow-sm h-100" style="border-left: 5px solid #e74a3b !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Perlu ditindaklanjuti</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $ringkasan['perlu_ditindaklanjuti'] ?></div>
        <span class="small text-muted">Sesi belum terisi lengkap</span>
        <div class="small mt-1">
          <a href="<?= BASE_URL ?>/penilai/wawancara/index.php">Wawancara</a>
          &middot;
          <a href="<?= BASE_URL ?>/penilai/kuesioner/index.php">Kuesioner</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card stat-card border-left-success shadow-sm h-100" style="border-left: 5px solid #1cc88a !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-success text-uppercase mb-1">Penilaian selesai</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $ringkasan['selesai'] ?></div>
        <span class="small text-muted">Wawancara dan kuesioner terisi lengkap</span>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card stat-card border-left-primary shadow-sm h-100" style="border-left: 5px solid #4e73df !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total sesi penilaian</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $ringkasan['total'] ?></div>
        <span class="small text-muted">Dalam lingkup wewenang Anda</span>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-5 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">Progres penilaian</h6></div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
          <span><?= $ringkasan['selesai'] ?> dari <?= $ringkasan['total'] ?> sesi selesai</span>
          <strong><?= $persentaseSelesai ?>%</strong>
        </div>
        <div class="progress mb-4" role="progressbar" aria-label="Progres penilaian" aria-valuenow="<?= $persentaseSelesai ?>" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar bg-success" style="width: <?= $persentaseSelesai ?>%"></div>
        </div>
        <div class="small text-muted">Wawancara: <?= $ringkasan['wawancara_selesai'] ?> / <?= $ringkasan['wawancara_total'] ?> selesai</div>
        <div class="small text-muted">Kuesioner: <?= $ringkasan['kuesioner_selesai'] ?> / <?= $ringkasan['kuesioner_total'] ?> selesai</div>
        <div class="mt-3"><a href="<?= BASE_URL ?>/penilai/wawancara/index.php">Lihat daftar penilaian</a></div>
      </div>
    </div>
  </div>
  <div class="col-lg-7 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success">Penilaian per bulan</h6></div>
      <div class="card-body">
        <p class="small text-muted">Jumlah sesi wawancara dan kuesioner yang dibuat dalam 6 bulan terakhir.</p>
        <canvas id="dashboard-tren-penilaian" aria-label="Grafik tren penilaian per bulan" role="img"></canvas>
        <div id="dashboard-chart-fallback" class="small text-muted" hidden>Grafik tidak tersedia saat ini.</div>
      </div>
    </div>
  </div>
</div>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>NIP</th><th>Nama</th><th>Jabatan</th></tr></thead>
<tbody>
<?php foreach ($pegawaiBinaan as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nip']) ?></td>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['nama_jabatan']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (!$pegawaiBinaan): ?>
<tr><td colspan="3" class="text-center text-muted">Belum ada wewenang penilaian untuk jabatan anda.</td></tr>
<?php endif; ?>
</tbody>
</table>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
  var canvas = document.getElementById('dashboard-tren-penilaian');
  if (!window.Chart) {
    canvas.hidden = true;
    document.getElementById('dashboard-chart-fallback').hidden = false;
    return;
  }
  var trend = <?= $trenJson ?>;
  new Chart(canvas, {
    type: 'bar',
    data: {
      labels: trend.labels,
      datasets: [
        { label: 'Wawancara', data: trend.wawancara, backgroundColor: '#1cc88a' },
        { label: 'Kuesioner', data: trend.kuesioner, backgroundColor: '#4e73df' }
      ]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });
}());
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
