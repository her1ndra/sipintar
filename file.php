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
$wawancaraEvidence = [];
$kuesionerEvidence = [];
$isWawancaraEvidenceFile = false;
$isKuesionerEvidenceFile = false;

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
$buktiSql = "SELECT x.id_wawancara, p.id_jabatan AS id_jabatan_pegawai, k.id_jabatan_dinilai
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
        if (in_array($tabel, ['hasil_wawancara', 'bukti_wawancara'], true)) {
            $isWawancaraEvidenceFile = true;
        } elseif ($tabel === 'hasil_kuesioner') {
            $isKuesionerEvidenceFile = true;
        }
        if ($isAdmin) {
            $diizinkan = true;
        } elseif ($user['role'] === 'Penilai') {
            $wewenang = $wewenang ?? array_map('intval', getJabatanWewenang($pdo, (int) $user['id_jabatan']));
            if (in_array((int) $row['id_jabatan_pegawai'], $wewenang, true)
                || ($row['id_jabatan_dinilai'] !== null && in_array((int) $row['id_jabatan_dinilai'], $wewenang, true))) {
                $diizinkan = true;
                if (in_array($tabel, ['hasil_wawancara', 'bukti_wawancara'], true)) {
                    $wawancaraEvidence[] = (int) $row['id_wawancara'];
                } elseif ($tabel === 'hasil_kuesioner') {
                    $kuesionerEvidence[] = (int) $row['id_wawancara'];
                }
            }
        }
    }
}
$wawancaraDocumentationSql = "SELECT p.id_jabatan AS id_jabatan_pegawai
                              FROM dokumentasi_wawancara d
                              JOIN wawancara w ON w.id_wawancara = d.id_wawancara
                              JOIN pegawai p ON p.id_pegawai = w.id_pegawai
                              WHERE d.file_dokumentasi = ?";
$s = $pdo->prepare($wawancaraDocumentationSql);
$s->execute([$path]);
foreach ($s->fetchAll() as $row) {
    $ditemukan = true;
    if ($isAdmin) {
        $diizinkan = true;
    } elseif ($user['role'] === 'Penilai') {
        $wewenang = $wewenang ?? array_map('intval', getJabatanWewenang($pdo, (int) $user['id_jabatan']));
        if (in_array((int) $row['id_jabatan_pegawai'], $wewenang, true)) {
            $diizinkan = true;
        }
    }
}

$dokumentasiSql = "SELECT p.id_jabatan AS id_jabatan_pegawai,
                          p_target.id_jabatan AS id_jabatan_target,
                          k.id_jabatan_dinilai
                   FROM dokumentasi_kuesioner d
                   JOIN wawancara w ON w.id_wawancara = d.id_wawancara
                   JOIN pegawai p ON p.id_pegawai = w.id_pegawai
                   LEFT JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
                   LEFT JOIN pegawai p_target ON p_target.id_pegawai = k.id_jabatan_dinilai
                   WHERE d.file_dokumentasi = ?";
$s = $pdo->prepare($dokumentasiSql);
$s->execute([$path]);
foreach ($s->fetchAll() as $row) {
    $ditemukan = true;
    if ($isAdmin) {
        $diizinkan = true;
    } elseif ($user['role'] === 'Penilai') {
        $wewenang = $wewenang ?? array_map('intval', getJabatanWewenang($pdo, (int) $user['id_jabatan']));
        if (in_array((int) $row['id_jabatan_pegawai'], $wewenang, true)
            || ($row['id_jabatan_target'] !== null && in_array((int) $row['id_jabatan_target'], $wewenang, true))
            || ($row['id_jabatan_dinilai'] !== null && in_array((int) $row['id_jabatan_dinilai'], $wewenang, true))) {
            $diizinkan = true;
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

$reviewedPdf = false;
if ($user['role'] === 'Penilai' && $wawancaraEvidence) {
    $markReviewed = $pdo->prepare(
        'INSERT INTO pemeriksaan_bukti_wawancara
            (id_wawancara, file_bukti, id_user_penilai)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE diperiksa_at = CURRENT_TIMESTAMP'
    );
    foreach (array_unique($wawancaraEvidence) as $idWawancara) {
        $markReviewed->execute([$idWawancara, $path, (int) $user['id_user']]);
    }
    $reviewedPdf = $ext === 'pdf';
}
if ($user['role'] === 'Penilai' && $kuesionerEvidence) {
    $markReviewed = $pdo->prepare(
        'INSERT INTO pemeriksaan_bukti_kuesioner
            (id_wawancara, file_bukti, id_user_penilai)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE diperiksa_at = CURRENT_TIMESTAMP'
    );
    foreach (array_unique($kuesionerEvidence) as $idWawancara) {
        $markReviewed->execute([$idWawancara, $path, (int) $user['id_user']]);
    }
    $reviewedPdf = $reviewedPdf || $ext === 'pdf';
}
if ($ext === 'pdf' && !$reviewedPdf) {
    foreach ([
        [$isWawancaraEvidenceFile, 'pemeriksaan_bukti_wawancara'],
        [$isKuesionerEvidenceFile, 'pemeriksaan_bukti_kuesioner'],
    ] as [$isEvidenceFile, $reviewTable]) {
        if (!$isEvidenceFile) {
            continue;
        }
        $reviewStmt = $pdo->prepare("SELECT 1 FROM {$reviewTable} WHERE file_bukti = ? LIMIT 1");
        $reviewStmt->execute([$path]);
        if ($reviewStmt->fetchColumn()) {
            $reviewedPdf = true;
            break;
        }
    }
}

[$mime, $inline] = $tipe[$ext];
if (isset($_GET['download'])) {
    $inline = false;
}

$namaUnduh = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($full));
$output = null;
if ($reviewedPdf) {
    require_once __DIR__ . '/includes/reviewed_pdf.php';
    try {
        $output = createReviewedPdf($full);
    } catch (\RuntimeException $error) {
        error_log('SI PINTAR: dependensi PDF untuk file yang sudah diperiksa tidak tersedia: ' . $error->getMessage());
        http_response_code(503);
        header('Content-Type: text/plain; charset=utf-8');
        exit('Salinan PDF bertanda belum dapat dibuat. Silakan hubungi administrator.');
    } catch (\setasign\Fpdi\FpdiException $error) {
        error_log('SI PINTAR: gagal memberi tanda pemeriksaan pada PDF: ' . $error->getMessage());
        http_response_code(422);
        header('Content-Type: text/plain; charset=utf-8');
        exit('PDF tidak dapat diberi tanda pemeriksaan. File asli tetap tersimpan.');
    }
    $namaUnduh = pathinfo($namaUnduh, PATHINFO_FILENAME) . '-sudah-diperiksa.pdf';
}
session_write_close();
header('Content-Type: ' . $mime);
header('Content-Length: ' . ($output === null ? filesize($full) : strlen($output)));
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $namaUnduh . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
if ($output === null) {
    readfile($full);
} else {
    echo $output;
}
exit;
