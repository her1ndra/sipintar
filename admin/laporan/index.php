<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
require_once __DIR__ . '/data.php';

$pdo = Database::getConnection();
[$mode, $year, $month, $start, $end, $periodLabel] = laporanPeriode($_GET);
$data = laporanAmbilData($pdo, $start, $end);
$monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
$periodLabel = $mode === 'bulanan' ? $monthNames[$month] . ' ' . $year : 'Tahun ' . $year;
$downloadQuery = http_build_query(['periode' => $mode, 'tahun' => $year, 'bulan' => $month]);
$pageTitle = 'Laporan TNA 2026';
require_once __DIR__ . '/../../includes/header.php';
?>
<style>
.report-hero { background: linear-gradient(135deg, #006837, #0b7d54); color: #fff; border-radius: .5rem; padding: 1.5rem; }
.report-card { border-left: 4px solid #f9a825; }
.report-table td { vertical-align: top; }
</style>

<div class="report-hero mb-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center">
    <div>
      <div class="small text-uppercase font-weight-bold" style="letter-spacing: .08em;">Dokumen Training Need Analysis 2026</div>
      <h2 class="h4 mb-1">Laporan <?= htmlspecialchars($periodLabel) ?></h2>
      <div class="small">Sumber data: seluruh data aplikasi pada periode <?= htmlspecialchars($start->format('d/m/Y')) ?> sampai <?= htmlspecialchars($end->modify('-1 day')->format('d/m/Y')) ?>.</div>
    </div>
    <div class="mt-3 mt-md-0">
      <a class="btn btn-light mr-1" href="download.php?<?= $downloadQuery ?>&format=word"><i class="fas fa-file-word text-primary mr-1"></i> Word</a>
      <a class="btn btn-warning" href="download.php?<?= $downloadQuery ?>&format=pdf"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    </div>
  </div>
</div>

<form method="get" class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="form-row align-items-end">
      <div class="form-group col-md-3 mb-md-0">
        <label for="periode">Jenis laporan</label>
        <select class="form-control" name="periode" id="periode">
          <option value="bulanan" <?= $mode === 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
          <option value="tahunan" <?= $mode === 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
        </select>
      </div>
      <div class="form-group col-md-3 mb-md-0">
        <label for="tahun">Tahun</label>
        <input class="form-control" type="number" name="tahun" id="tahun" min="2000" max="2100" value="<?= $year ?>" required>
      </div>
      <div class="form-group col-md-3 mb-md-0" id="bulan-wrapper">
        <label for="bulan">Bulan</label>
        <select class="form-control" name="bulan" id="bulan">
          <?php foreach ($monthNames as $number => $name): ?>
          <option value="<?= $number ?>" <?= $month === $number ? 'selected' : '' ?>><?= $name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group col-md-3 mb-0">
        <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-filter mr-1"></i> Tampilkan laporan</button>
      </div>
    </div>
  </div>
</form>

<div class="row mb-4">
<?php foreach ($data as $key => $rows): ?>
  <div class="col-sm-6 col-xl-3 mb-3">
    <div class="card report-card shadow-sm h-100">
      <div class="card-body py-3">
        <div class="text-xs font-weight-bold text-success text-uppercase mb-1"><?= htmlspecialchars(laporanLabel($key)) ?></div>
        <div class="h4 mb-0 font-weight-bold text-gray-800"><?= count($rows) ?></div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-white font-weight-bold">Ringkasan kebutuhan diklat</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-striped mb-0 report-table">
        <thead><tr><th>Nama</th><th>Jabatan</th><th>Diklat</th><th>Prioritas</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach (array_slice($data['diklat'], 0, 10) as $row): ?>
        <tr>
          <td><?= htmlspecialchars(laporanNilai($row['nama_lengkap'])) ?></td>
          <td><?= htmlspecialchars(laporanNilai($row['nama_jabatan'])) ?></td>
          <td><?= htmlspecialchars(laporanNilai($row['nama_diklat'])) ?></td>
          <td><?= htmlspecialchars(laporanNilai($row['prioritas'])) ?></td>
          <td><?= htmlspecialchars(laporanNilai($row['status'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$data['diklat']): ?><tr><td colspan="5" class="text-center text-muted">Belum ada data pada periode ini.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.getElementById('periode').addEventListener('change', function () {
  document.getElementById('bulan-wrapper').style.display = this.value === 'bulanan' ? '' : 'none';
});
document.getElementById('periode').dispatchEvent(new Event('change'));
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>