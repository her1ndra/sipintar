<?php
require_once __DIR__ . '/../config/config.php';
requireRole('Admin');
$pdo = Database::getConnection();
$totalPegawai   = $pdo->query('SELECT COUNT(*) c FROM pegawai')->fetch()['c'];
$totalUser      = $pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
$totalSertifikat = $pdo->query('SELECT COUNT(*) c FROM sertifikat_pegawai')->fetch()['c'];
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
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
