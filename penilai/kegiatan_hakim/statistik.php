<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'Admin' && ($user['role'] !== 'Penilai' || $user['nama_jabatan'] !== 'Ketua')) {
    http_response_code(403);
    die('Akses statistik kegiatan hakim hanya untuk Admin atau Ketua.');
}

$pdo = Database::getConnection();
$periode = $_GET['periode'] ?? 'bulan';
if (!in_array($periode, ['bulan', 'triwulan', 'tahun'], true)) {
    $periode = 'bulan';
}
$tahunAkhir = (int) date('Y');
$tahun = filter_input(INPUT_GET, 'tahun', FILTER_VALIDATE_INT);
$tahun = $tahun && $tahun >= 2000 && $tahun <= $tahunAkhir + 1 ? $tahun : $tahunAkhir;

$namaBulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
];
$labels = [];
$jumlahKegiatan = [];
$jumlahHakim = [];

if ($periode === 'bulan') {
    $judulPeriode = 'Bulanan';
    $tanggalMulai = sprintf('%04d-01-01', $tahun);
    $tanggalBatas = sprintf('%04d-01-01', $tahun + 1);
    $groupExpression = "DATE_FORMAT(kh.tanggal_mulai, '%Y-%m')";
    for ($bulan = 1; $bulan <= 12; $bulan++) {
        $key = sprintf('%04d-%02d', $tahun, $bulan);
        $labels[$key] = $namaBulan[sprintf('%02d', $bulan)];
    }
} elseif ($periode === 'triwulan') {
    $judulPeriode = 'Triwulanan';
    $tanggalMulai = sprintf('%04d-01-01', $tahun);
    $tanggalBatas = sprintf('%04d-01-01', $tahun + 1);
    $groupExpression = "CONCAT(YEAR(kh.tanggal_mulai), '-Q', QUARTER(kh.tanggal_mulai))";
    for ($triwulan = 1; $triwulan <= 4; $triwulan++) {
        $labels[sprintf('%04d-Q%d', $tahun, $triwulan)] = 'Triwulan ' . ['I', 'II', 'III', 'IV'][$triwulan - 1];
    }
} else {
    $judulPeriode = 'Tahunan';
    $tahunMulai = $tahun - 4;
    $tanggalMulai = sprintf('%04d-01-01', $tahunMulai);
    $tanggalBatas = sprintf('%04d-01-01', $tahun + 1);
    $groupExpression = 'YEAR(kh.tanggal_mulai)';
    for ($tahunGrafik = $tahunMulai; $tahunGrafik <= $tahun; $tahunGrafik++) {
        $labels[(string) $tahunGrafik] = (string) $tahunGrafik;
    }
}

foreach ($labels as $key => $label) {
    $jumlahKegiatan[$key] = 0;
    $jumlahHakim[$key] = 0;
}

$statement = $pdo->prepare(
    "SELECT $groupExpression AS periode,
            COUNT(*) AS jumlah_kegiatan,
            COUNT(DISTINCT kh.id_pegawai) AS jumlah_hakim
     FROM kegiatan_hakim kh
     JOIN pegawai p ON p.id_pegawai = kh.id_pegawai
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
     WHERE j.nama_jabatan LIKE '%Hakim%'
       AND kh.tanggal_mulai >= ? AND kh.tanggal_mulai < ?
     GROUP BY periode
     ORDER BY periode"
);
$statement->execute([$tanggalMulai, $tanggalBatas]);
foreach ($statement->fetchAll() as $row) {
    $key = (string) $row['periode'];
    if (array_key_exists($key, $labels)) {
        $jumlahKegiatan[$key] = (int) $row['jumlah_kegiatan'];
        $jumlahHakim[$key] = (int) $row['jumlah_hakim'];
    }
}

$pageTitle = 'Statistik kegiatan hakim';
$boldPageTitle = true;
require_once __DIR__ . '/../../includes/header.php';
?>
<form method="get" class="form-row align-items-end mb-4">
  <div class="form-group col-sm-5 col-md-3">
    <label for="periode">Periode</label>
    <select name="periode" id="periode" class="form-control">
      <option value="bulan" <?= $periode === 'bulan' ? 'selected' : '' ?>>Per bulan</option>
      <option value="triwulan" <?= $periode === 'triwulan' ? 'selected' : '' ?>>Per triwulan</option>
      <option value="tahun" <?= $periode === 'tahun' ? 'selected' : '' ?>>Per tahun</option>
    </select>
  </div>
  <div class="form-group col-sm-4 col-md-2">
    <label for="tahun">Tahun acuan</label>
    <select name="tahun" id="tahun" class="form-control">
      <?php for ($opsiTahun = $tahunAkhir + 1; $opsiTahun >= max(2000, $tahunAkhir - 10); $opsiTahun--): ?>
      <option value="<?= $opsiTahun ?>" <?= $tahun === $opsiTahun ? 'selected' : '' ?>><?= $opsiTahun ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="form-group col-sm-3 col-md-2">
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1" aria-hidden="true"></i>Tampilkan</button>
  </div>
</form>
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white font-weight-bold">Jumlah hakim aktif dan kegiatan <?= htmlspecialchars(strtolower($judulPeriode)) ?></div>
  <div class="card-body">
    <div style="position:relative;height:360px;">
      <canvas id="statistikKegiatanChart" role="img" aria-label="Grafik jumlah hakim aktif dan kegiatan hakim"></canvas>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const chartElement = document.getElementById('statistikKegiatanChart');
const chartLabels = <?= json_encode(array_values($labels), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const kegiatanValues = <?= json_encode(array_values($jumlahKegiatan)) ?>;
const hakimValues = <?= json_encode(array_values($jumlahHakim)) ?>;

new Chart(chartElement, {
  type: 'bar',
  data: {
    labels: chartLabels,
    datasets: [
      {
        label: 'Jumlah kegiatan',
        data: kegiatanValues,
        backgroundColor: 'rgba(0, 107, 63, 0.78)',
        borderColor: '#006b3f',
        borderWidth: 1,
        borderRadius: 4
      },
      {
        label: 'Jumlah hakim aktif',
        data: hakimValues,
        backgroundColor: 'rgba(249, 168, 37, 0.82)',
        borderColor: '#d58a00',
        borderWidth: 1,
        borderRadius: 4
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { precision: 0 },
        title: { display: true, text: 'Jumlah' }
      }
    },
    plugins: {
      legend: { position: 'bottom' },
      tooltip: { enabled: true }
    }
  }
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>