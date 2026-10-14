<?php
require_once __DIR__ . '/../../config/config.php';
requireKegiatanHakimAccess();

$periode = $_GET['periode'] ?? 'bulan';
if (!is_string($periode) || !in_array($periode, ['bulan', 'triwulan', 'tahun'], true)) {
    $periode = 'bulan';
}
$tahunAkhir = (int) date('Y');
$tahunInput = $_GET['tahun'] ?? '';
$tahun = is_scalar($tahunInput) ? filter_var((string) $tahunInput, FILTER_VALIDATE_INT) : false;
$tahun = $tahun && $tahun >= 2000 && $tahun <= $tahunAkhir + 1 ? $tahun : $tahunAkhir;

$filterType = $_GET['filter'] ?? '';
$filterValue = $_GET['value'] ?? '';
if (!is_string($filterType) || !in_array($filterType, ['', 'hakim', 'jenis'], true)
    || !is_string($filterValue) || strlen($filterValue) > 200
    || ($filterType === '' && $filterValue !== '')
    || ($filterType !== '' && $filterValue === '')) {
    http_response_code(400);
    exit('Filter laporan tidak valid.');
}

$tanggalMulai = sprintf('%04d-01-01', $periode === 'tahun' ? $tahun - 4 : $tahun);
$tanggalBatas = sprintf('%04d-01-01', $tahun + 1);
$sql = "SELECT p.nama_lengkap, kh.jenis_kegiatan, kh.nama_kegiatan, kh.penyelenggara,
               kh.tanggal_mulai, kh.tanggal_selesai, kh.lokasi
        FROM kegiatan_hakim kh
        JOIN pegawai p ON p.id_pegawai = kh.id_pegawai
        JOIN jabatan j ON j.id_jabatan = p.id_jabatan
        WHERE j.nama_jabatan LIKE '%Hakim%'
          AND kh.tanggal_mulai >= ? AND kh.tanggal_mulai < ?";
$params = [$tanggalMulai, $tanggalBatas];
if ($filterType === 'hakim') {
    $sql .= ' AND p.nama_lengkap = ?';
    $params[] = $filterValue;
} elseif ($filterType === 'jenis') {
    $sql .= ' AND kh.jenis_kegiatan = ?';
    $params[] = $filterValue;
}
$sql .= ' ORDER BY kh.tanggal_mulai DESC, p.nama_lengkap';
$statement = Database::getConnection()->prepare($sql);
$statement->execute($params);
$data = $statement->fetchAll();

$filterLabel = $filterType === 'hakim'
    ? 'Nama hakim: ' . $filterValue
    : ($filterType === 'jenis' ? 'Jenis kegiatan: ' . $filterValue : 'Semua data');
$filenameFilter = $filterType === 'hakim' ? 'hakim' : ($filterType === 'jenis' ? 'jenis-kegiatan' : 'semua');
$filename = 'laporan-kegiatan-hakim-' . $filenameFilter . '-' . $tahun . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('X-Content-Type-Options: nosniff');

$output = fopen('php://output', 'wb');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['Laporan kegiatan hakim'], ';', '"', '\\');
fputcsv($output, ['Filter', $filterLabel], ';', '"', '\\');
fputcsv($output, ['Periode', $periode, 'Tahun acuan', $tahun], ';', '"', '\\');
fputcsv($output, [], ';', '"', '\\');
fputcsv($output, ['Nama hakim', 'Jenis kegiatan', 'Nama kegiatan', 'Penyelenggara', 'Tanggal mulai', 'Tanggal selesai', 'Tempat'], ';', '"', '\\');
foreach ($data as $row) {
    fputcsv($output, [
        $row['nama_lengkap'],
        $row['jenis_kegiatan'],
        $row['nama_kegiatan'],
        $row['penyelenggara'] ?: '-',
        $row['tanggal_mulai'],
        $row['tanggal_selesai'] ?: '-',
        $row['lokasi'] ?: '-',
    ], ';', '"', '\\');
}
fclose($output);
