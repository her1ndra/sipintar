<?php
require_once __DIR__ . '/../../config/config.php';
requireKegiatanHakimAccess();
$user = currentUser();

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

// Kunci periode sebuah tanggal, sama dengan kunci pada $labels.
$kunciPeriode = static function (string $tanggal) use ($periode): string {
    $ts = strtotime($tanggal);
    if ($periode === 'bulan') {
        return date('Y-m', $ts);
    }
    if ($periode === 'triwulan') {
        return date('Y', $ts) . '-Q' . (int) ceil((int) date('n', $ts) / 3);
    }
    return date('Y', $ts);
};

$statement = $pdo->prepare(
    "SELECT kh.id_kegiatan, kh.jenis_kegiatan, kh.nama_kegiatan, kh.penyelenggara, kh.peran_topik,
            kh.tanggal_mulai, kh.tanggal_selesai, kh.lokasi,
            p.id_pegawai, p.nama_lengkap
     FROM kegiatan_hakim kh
     JOIN pegawai p ON p.id_pegawai = kh.id_pegawai
     JOIN jabatan j ON j.id_jabatan = p.id_jabatan
     WHERE j.nama_jabatan LIKE '%Hakim%'
       AND kh.tanggal_mulai >= ? AND kh.tanggal_mulai < ?
     ORDER BY kh.tanggal_mulai, p.nama_lengkap"
);
$statement->execute([$tanggalMulai, $tanggalBatas]);
$semuaKegiatan = $statement->fetchAll();

// Urutan jenis: master dulu, lalu jenis lain yang muncul pada data.
$daftarJenis = $pdo->query('SELECT nama_jenis FROM jenis_kegiatan_hakim ORDER BY nama_jenis')->fetchAll(PDO::FETCH_COLUMN);
foreach ($semuaKegiatan as $row) {
    if (!in_array($row['jenis_kegiatan'], $daftarJenis, true)) {
        $daftarJenis[] = $row['jenis_kegiatan'];
    }
}

$hakimPerPeriode = [];
$matriksJenis = [];          // [jenis][periode] = jumlah kegiatan
$kegiatanPerPeriode = [];    // [periode] => baris kegiatan
foreach ($daftarJenis as $jenis) {
    $matriksJenis[$jenis] = array_fill_keys(array_keys($labels), 0);
}
foreach ($semuaKegiatan as $row) {
    $key = $kunciPeriode($row['tanggal_mulai']);
    if (!array_key_exists($key, $labels)) {
        continue;
    }
    $jumlahKegiatan[$key]++;
    $hakimPerPeriode[$key][(int) $row['id_pegawai']] = true;
    $matriksJenis[$row['jenis_kegiatan']][$key]++;
    $kegiatanPerPeriode[$key][] = $row;
}
foreach ($hakimPerPeriode as $key => $hakimSet) {
    $jumlahHakim[$key] = count($hakimSet);
}

// Periode yang dirinci: dari pilihan pengguna, atau periode berjalan, atau periode terakhir yang berisi data.
$rincian = $_GET['rincian'] ?? '';
if (!is_string($rincian) || !array_key_exists($rincian, $labels)) {
    $rincian = $kunciPeriode(date('Y-m-d'));
    if (!array_key_exists($rincian, $labels)) {
        $berisi = array_keys(array_filter($jumlahKegiatan));
        $rincian = $berisi ? end($berisi) : (string) array_key_last($labels);
    }
}
$labelRincian = $labels[$rincian];
if ($periode === 'bulan') {
    $labelRincian .= ' ' . $tahun;
} elseif ($periode === 'triwulan') {
    $labelRincian .= ' ' . $tahun;
}

// Rincian per jenis untuk periode terpilih.
$rincianJenis = [];
foreach ($daftarJenis as $jenis) {
    $rincianJenis[$jenis] = ['kegiatan' => [], 'hakim' => []];
}
foreach ($kegiatanPerPeriode[$rincian] ?? [] as $row) {
    $rincianJenis[$row['jenis_kegiatan']]['kegiatan'][] = $row;
    $nama = $row['nama_lengkap'];
    $rincianJenis[$row['jenis_kegiatan']]['hakim'][$nama] = ($rincianJenis[$row['jenis_kegiatan']]['hakim'][$nama] ?? 0) + 1;
}

$formatTanggal = static function (?string $tanggal): string {
    return $tanggal ? date('d-m-Y', strtotime($tanggal)) : '';
};
$paletJenis = ['#006b3f', '#f9a825', '#1e88e5', '#8e24aa', '#e53935', '#00897b', '#6d4c41', '#546e7a'];

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
  <div class="form-group col-sm-6 col-md-3">
    <label for="rincian">Rincian periode</label>
    <select name="rincian" id="rincian" class="form-control">
      <?php foreach ($labels as $key => $label): ?>
      <option value="<?= e($key) ?>" <?= $rincian === (string) $key ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
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
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white font-weight-bold">Kegiatan per jenis <?= e(strtolower($judulPeriode)) ?></div>
  <div class="card-body">
    <div style="position:relative;height:320px;">
      <canvas id="statistikJenisChart" role="img" aria-label="Grafik kegiatan hakim per jenis"></canvas>
    </div>
    <div class="table-responsive mt-4">
      <table class="table table-bordered table-sm bg-white mb-0">
        <thead>
          <tr>
            <th>Jenis kegiatan</th>
            <?php foreach ($labels as $label): ?><th class="text-center"><?= e($label) ?></th><?php endforeach; ?>
            <th class="text-center">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($matriksJenis as $jenis => $perPeriode): ?>
          <tr>
            <td><?= e($jenis) ?></td>
            <?php foreach ($perPeriode as $nilai): ?><td class="text-center"><?= $nilai ?: '-' ?></td><?php endforeach; ?>
            <td class="text-center font-weight-bold"><?= array_sum($perPeriode) ?></td>
          </tr>
          <?php endforeach; ?>
          <tr class="font-weight-bold bg-light">
            <td>Semua jenis</td>
            <?php foreach ($jumlahKegiatan as $nilai): ?><td class="text-center"><?= $nilai ?></td><?php endforeach; ?>
            <td class="text-center"><?= array_sum($jumlahKegiatan) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-white font-weight-bold">Rincian kegiatan: <?= e($labelRincian) ?></div>
  <div class="card-body">
    <?php if (!($kegiatanPerPeriode[$rincian] ?? [])): ?>
      <p class="text-muted mb-0">Tidak ada kegiatan hakim pada periode ini.</p>
    <?php else: ?>
      <div class="row mb-3">
        <?php foreach ($rincianJenis as $jenis => $data): ?>
        <div class="col-6 col-md-3 mb-2">
          <div class="border rounded p-2 h-100">
            <div class="small text-muted"><?= e($jenis) ?></div>
            <div class="h4 mb-0"><?= count($data['kegiatan']) ?> <small class="text-muted">kegiatan</small></div>
            <div class="small"><?= count($data['hakim']) ?> hakim</div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php foreach ($rincianJenis as $jenis => $data): ?>
        <?php if (!$data['kegiatan']) { continue; } ?>
        <h6 class="mt-4 font-weight-bold"><?= e($jenis) ?>
          <span class="text-muted font-weight-normal">- <?= count($data['kegiatan']) ?> kegiatan, <?= count($data['hakim']) ?> hakim</span>
        </h6>
        <p class="mb-2">
          <?php foreach ($data['hakim'] as $nama => $jumlah): ?>
            <span class="badge badge-light border mr-1"><?= e($nama) ?><?= $jumlah > 1 ? ' (' . $jumlah . 'x)' : '' ?></span>
          <?php endforeach; ?>
        </p>
        <div class="table-responsive">
          <table class="table table-bordered table-sm table-striped bg-white">
            <thead>
              <tr><th style="width:4%">No</th><th>Hakim</th><th>Kegiatan</th><th>Peran/topik</th><th>Tanggal</th><th>Penyelenggara</th><th>Lokasi</th></tr>
            </thead>
            <tbody>
              <?php foreach ($data['kegiatan'] as $i => $row): ?>
              <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= e($row['nama_lengkap']) ?></td>
                <td><?= e($row['nama_kegiatan']) ?></td>
                <td><?= e($row['peran_topik'] ?: '-') ?></td>
                <td class="text-nowrap"><?= e($formatTanggal($row['tanggal_mulai'])) ?><?= $row['tanggal_selesai'] && $row['tanggal_selesai'] !== $row['tanggal_mulai'] ? ' s/d ' . e($formatTanggal($row['tanggal_selesai'])) : '' ?></td>
                <td><?= e($row['penyelenggara'] ?: '-') ?></td>
                <td><?= e($row['lokasi'] ?: '-') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const chartElement = document.getElementById('statistikKegiatanChart');
const chartLabels = <?= json_encode(array_values($labels), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const kegiatanValues = <?= json_encode(array_values($jumlahKegiatan)) ?>;
const hakimValues = <?= json_encode(array_values($jumlahHakim)) ?>;
const jenisPalet = <?= json_encode($paletJenis) ?>;
const jenisDatasets = <?= json_encode(array_map(static function ($jenis, $perPeriode) {
    return ['label' => $jenis, 'data' => array_values($perPeriode)];
}, array_keys($matriksJenis), array_values($matriksJenis)), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
jenisDatasets.forEach(function (d, i) {
  d.backgroundColor = jenisPalet[i % jenisPalet.length];
  d.borderRadius = 2;
  d.stack = 'jenis';
});
new Chart(document.getElementById('statistikJenisChart'), {
  type: 'bar',
  data: { labels: chartLabels, datasets: jenisDatasets },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    scales: {
      x: { stacked: true },
      y: { stacked: true, beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Jumlah kegiatan' } }
    },
    plugins: { legend: { position: 'bottom' } }
  }
});

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