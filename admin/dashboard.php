<?php
require_once __DIR__ . '/../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$totalPegawai   = $pdo->query('SELECT COUNT(*) c FROM pegawai')->fetch()['c'];
$totalUser      = $pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
$totalSertifikat = $pdo->query('SELECT COUNT(*) c FROM sertifikat_pegawai')->fetch()['c'];
require_once __DIR__ . '/../includes/dashboard_penilaian.php';
$ringkasan = dashboardPenilaianRingkasan($pdo, null, (int) $_SESSION['id_user']);
$persentaseSelesai = $ringkasan['total'] > 0
    ? (int) round($ringkasan['selesai'] / $ringkasan['total'] * 100)
    : 0;
$trenJson = json_encode(
    $ringkasan['tren'],
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR
);
$pageTitle = 'Dashboard admin';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="row">
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card stat-card border-left-primary shadow-sm h-100" style="border-left: 5px solid #4e73df !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Pegawai</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $totalPegawai ?></div>
        <i class="fas fa-users stat-icon-bg text-primary"></i>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card stat-card border-left-success shadow-sm h-100" style="border-left: 5px solid #1cc88a !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Akun User</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $totalUser ?></div>
        <i class="fas fa-user-cog stat-icon-bg text-success"></i>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card stat-card shadow-sm h-100" style="border-left: 5px solid #F9A825 !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Total Sertifikat</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $totalSertifikat ?></div>
        <i class="fas fa-certificate stat-icon-bg text-warning"></i>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xl-3 col-md-6 mb-4">
    <a class="text-decoration-none" href="<?= BASE_URL ?>/admin/wawancara/index.php">
      <div class="card stat-card border-left-warning shadow-sm h-100" style="border-left: 5px solid #f6c23e !important;">
        <div class="card-body">
          <div class="text-xs fw-bold text-warning text-uppercase mb-1">Wawancara belum dibaca</div>
          <div class="h2 mb-0 fw-bold text-gray-800"><?= $ringkasan['belum_dibaca_wawancara'] ?></div>
          <span class="small text-muted">Buka daftar wawancara</span>
        </div>
      </div>
    </a>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <a class="text-decoration-none" href="<?= BASE_URL ?>/admin/kuesioner/index.php">
      <div class="card stat-card border-left-warning shadow-sm h-100" style="border-left: 5px solid #f6c23e !important;">
        <div class="card-body">
          <div class="text-xs fw-bold text-warning text-uppercase mb-1">Kuesioner belum dibaca</div>
          <div class="h2 mb-0 fw-bold text-gray-800"><?= $ringkasan['belum_dibaca_kuesioner'] ?></div>
          <span class="small text-muted">Buka daftar kuesioner</span>
        </div>
      </div>
    </a>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-left-success shadow-sm h-100" style="border-left: 5px solid #1cc88a !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-success text-uppercase mb-1">Penilaian selesai</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $ringkasan['selesai'] ?></div>
        <span class="small text-muted">Wawancara dan kuesioner terisi lengkap</span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-left-danger shadow-sm h-100" style="border-left: 5px solid #e74a3b !important;">
      <div class="card-body">
        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Perlu ditindaklanjuti</div>
        <div class="h2 mb-0 fw-bold text-gray-800"><?= $ringkasan['perlu_ditindaklanjuti'] ?></div>
        <span class="small text-muted">Sesi belum terisi lengkap</span>
        <div class="small mt-1">
          <a href="<?= BASE_URL ?>/admin/wawancara/index.php">Wawancara</a>
          &middot;
          <a href="<?= BASE_URL ?>/admin/kuesioner/index.php">Kuesioner</a>
        </div>
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
