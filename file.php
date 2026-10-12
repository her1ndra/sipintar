<?php
/**
 * Pengiriman file upload yang dilindungi.
 * File di assets/uploads/ dan uploads/ TIDAK dapat diakses langsung dari browser
 * (diblokir .htaccess / router.php). Semua akses lewat skrip ini:
 *   file.php?f=<path relatif seperti tersimpan di database>
 * Skrip memastikan: pengguna sudah login, path tercatat di database, dan
 * pengguna berhak atas data pemilik file tersebut.
 */
require_once __DIR__ . '/config/config.php';
requireLogin();

function fileNotFound(): void
{
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'File tidak ditemukan.';
    exit;
}

$raw = $_GET['f'] ?? '';
if (!is_string($raw) || $raw === '' || strlen($raw) > 300) {
    fileNotFound();
}
$path = ltrim(str_replace('\\', '/', $raw), '/');
$aman = '#^(?:assets/uploads/(?:sertifikat|kegiatan_hakim|kuesioner|wawancara)/[A-Za-z0-9._-]+'
      . '|uploads/sertifikat/[0-9]+/[A-Za-z0-9._-]+)$#D';
if (!preg_match($aman, $path) || strpos($path, '..') !== false) {
    fileNotFound();
}

$pdo  = Database::getConnection();
$user = currentUser();
$isAdmin = $user['role'] === 'Admin';
$ditemukan = false;
$diizinkan = false;

// 1. Sertifikat pegawai: hanya Admin.
$s = $pdo->prepare('SELECT 1 FROM sertifikat_pegawai WHERE file_sertifikat = ? LIMIT 1');
$s->execute([$path]);
if ($s->fetchColumn()) {
    $ditemukan = true;
    $diizinkan = $diizinkan || $isAdmin;
}

// 2. Lampiran kegiatan hakim: Admin, Ketua, Wakil Ketua.
$s = $pdo->prepare('SELECT 1 FROM kegiatan_hakim WHERE file_bukti = ? LIMIT 1');
$s->execute([$path]);
if ($s->fetchColumn()) {
    $ditemukan = true;
    $diizinkan = $diizinkan || canAccessKegiatanHakim($user);
}

// 3. Bukti kuesioner/wawancara: Admin, atau Penilai yang berwenang atas pegawai tsb.
$buktiSql = "SELECT p.id_jabatan AS id_jabatan_pegawai, k.id_jabatan_dinilai
             FROM %s x
             JOIN wawancara w ON w.id_wawancara = x.id_wawancara
             JOIN pegawai p ON p.id_pegawai = w.id_pegawai
             LEFT JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
             WHERE x.file_bukti = ?";
$wewenang = null;
foreach (['hasil_kuesioner', 'hasil_wawancara', 'bukti_wawancara'] as $tabel) {
    $s = $pdo->prepare(sprintf($buktiSql, $tabel));
    $s->execute([$path]);
    foreach ($s->fetchAll() as $row) {
        $ditemukan = true;
        if ($isAdmin) {
            $diizinkan = true;
        } elseif ($user['role'] === 'Penilai') {
            $wewenang = $wewenang ?? array_map('intval', getJabatanWewenang($pdo, (int) $user['id_jabatan']));
            if (in_array((int) $row['id_jabatan_pegawai'], $wewenang, true)
                || ($row['id_jabatan_dinilai'] !== null && in_array((int) $row['id_jabatan_dinilai'], $wewenang, true))) {
                $diizinkan = true;
            }
        }
    }
}

// Tidak dibedakan antara "tidak ada" dan "tidak berhak" agar keberadaan file tidak bocor.
if (!$ditemukan || !$diizinkan) {
    fileNotFound();
}

$full = realpath(__DIR__ . '/' . $path);
$akar = [realpath(__DIR__ . '/assets/uploads'), realpath(__DIR__ . '/uploads')];
$dalamAkar = false;
foreach ($akar as $a) {
    if ($a !== false && $full !== false && strpos($full, $a . DIRECTORY_SEPARATOR) === 0) {
        $dalamAkar = true;
    }
}
if (!$dalamAkar || !is_file($full)) {
    fileNotFound();
}

$tipe = [
    'pdf'  => ['application/pdf', true],
    'jpg'  => ['image/jpeg', true],
    'jpeg' => ['image/jpeg', true],
    'png'  => ['image/png', true],
    'doc'  => ['application/msword', false],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', false],
];
$ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
if (!isset($tipe[$ext])) {
    fileNotFound();
}
[$mime, $inline] = $tipe[$ext];
if (isset($_GET['download'])) {
    $inline = false;
}

$namaUnduh = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($full));
session_write_close();
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($full));
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $namaUnduh . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
readfile($full);
exit;
