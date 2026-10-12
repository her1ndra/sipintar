<?php
/**
 * Smoke test end-to-end (CLI). Jalankan server dulu:
 *     php -S 127.0.0.1:8000 router.php
 * lalu:
 *     SMOKE_ADMIN_NIP=... SMOKE_ADMIN_PASS=... SMOKE_KETUA_NIP=... SMOKE_KETUA_PASS=... \
 *     SMOKE_PANITERA_NIP=... SMOKE_PANITERA_PASS=... php tests/smoke.php
 * PERINGATAN: tes ini menulis & menghapus data. Jalankan HANYA pada database uji.
 */
if (PHP_SAPI !== 'cli') { exit; }
require_once __DIR__ . '/../config/config.php';

clearstatcache();
$base = rtrim(getenv('SMOKE_BASE') ?: 'http://127.0.0.1:8000', '/');
$pdo = Database::getConnection();
$pass = 0; $fail = 0;

function ok(bool $cond, string $name): void {
    global $pass, $fail;
    echo ($cond ? "  [PASS] " : "  [FAIL] ") . $name . PHP_EOL;
    $cond ? $pass++ : $fail++;
}

class Client {
    public string $jar;
    public function __construct(private string $base) { $this->jar = tempnam(sys_get_temp_dir(), 'sp'); }
    public function req(string $method, string $path, array $post = [], array $files = []): array {
        $ch = curl_init($this->base . $path);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_COOKIEJAR => $this->jar, CURLOPT_COOKIEFILE => $this->jar, CURLOPT_TIMEOUT => 20]);
        if ($method === 'POST') {
            foreach ($files as $k => $f) { $post[$k] = new CURLFile($f[0], $f[1], $f[2]); }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $raw = curl_exec($ch);
        $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $code, 'headers' => substr($raw, 0, $hs), 'body' => substr($raw, $hs)];
    }
    public function get(string $p): array { return $this->req('GET', $p); }
    public function post(string $p, array $d = [], array $f = []): array { return $this->req('POST', $p, $d, $f); }
    public function token(string $path): string {
        $r = $this->get($path);
        // token ada di input hidden form, atau di JS logout pada footer
        if (!preg_match('/name="_csrf" value="([a-f0-9]+)"/', $r['body'], $m)) {
            preg_match('/t\.value = "([a-f0-9]+)"/', $r['body'], $m);
        }
        return $m[1] ?? '';
    }
    public function login(string $nip, string $pw): array {
        $t = $this->token('/auth/login.php');
        return $this->post('/auth/proses_login.php', ['_csrf' => $t, 'nip' => $nip, 'password' => $pw]);
    }
}

echo "== Perlindungan folder & file sensitif\n";
$c = new Client($base);
foreach (['/database/si_pintar.sql', '/config/local.php', '/includes/auth.php', '/composer.json', '/assets/uploads/sertifikat/x.pdf', '/uploads/sertifikat/1/x.pdf', '/README.md'] as $p) {
    ok($c->get($p)['code'] === 403, "403 untuk $p");
}

echo "== CSRF & login\n";
$r = $c->get('/auth/login.php');
ok(strpos($r['body'], 'name="_csrf"') !== false, 'form login memuat token CSRF');
ok(preg_match('/Set-Cookie: SIPINTARSESSID=[^;]+;.*HttpOnly/is', $r['headers']) === 1, 'cookie sesi HttpOnly');
ok(stripos($r['headers'], 'SameSite=Lax') !== false, 'cookie sesi SameSite=Lax');
ok($c->post('/auth/proses_login.php', ['nip' => '1', 'password' => 'x'])['code'] === 419, 'POST login tanpa token ditolak (419)');

echo "== Admin\n";
$a = new Client($base);
$before = $a->get('/auth/login.php'); preg_match('/SIPINTARSESSID\s+(\S+)/', file_get_contents($a->jar), $m1);
$r = $a->login(getenv('SMOKE_ADMIN_NIP'), getenv('SMOKE_ADMIN_PASS'));
preg_match('/SIPINTARSESSID\s+(\S+)/', file_get_contents($a->jar), $m2);
ok($r['code'] === 302 && strpos($r['headers'], '/admin/dashboard.php') !== false, 'login admin berhasil');
ok(($m1[1] ?? 'a') !== ($m2[1] ?? 'a'), 'ID sesi diganti setelah login (anti session fixation)');
ok($a->get('/admin/dashboard.php')['code'] === 200, 'dashboard admin terbuka');
ok($a->get('/penilai/dashboard.php')['code'] !== 200, 'admin tidak bisa membuka area penilai');

$jmlPegawai = (int) $pdo->query('SELECT COUNT(*) FROM pegawai')->fetchColumn();
$idPeg = (int) $pdo->query('SELECT id_pegawai FROM pegawai ORDER BY id_pegawai DESC LIMIT 1')->fetchColumn();
$a->get("/admin/pegawai/hapus.php?id=$idPeg");
ok((int) $pdo->query('SELECT COUNT(*) FROM pegawai')->fetchColumn() === $jmlPegawai, 'hapus via GET tidak menghapus apa pun');
ok($a->post('/admin/pegawai/hapus.php', ['id' => $idPeg])['code'] === 419, 'hapus via POST tanpa token ditolak');
ok((int) $pdo->query('SELECT COUNT(*) FROM pegawai')->fetchColumn() === $jmlPegawai, 'data tetap utuh setelah POST tanpa token');
$idx = $a->get('/admin/pegawai/index.php');
ok(strpos($idx['body'], 'hapus.php?id=') === false && strpos($idx['body'], 'action="hapus.php"') !== false, 'tombol hapus berupa form POST');

echo "== Sertifikat (kolom baru + upload aman)\n";
$t = $a->token('/admin/sertifikat/tambah.php');
$tmpPdf = tempnam(sys_get_temp_dir(), 'pdf'); file_put_contents($tmpPdf, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");
$r = $a->post('/admin/sertifikat/tambah.php', [
    '_csrf' => $t, 'id_pegawai' => $idPeg, 'nama_sertifikat' => 'Uji Smoke', 'penyelenggara' => 'Tester',
    'tanggal_terbit' => '2026-01-02', 'nomor_sertifikat' => 'NO-123', 'jalur_pelatihan' => 'Pelatihan Teknis',
    'jenis' => 'Sertifikasi', 'status_validasi' => 'Tervalidasi',
], ['file_sertifikat' => [$tmpPdf, 'application/pdf', 'x.pdf']]);
$row = $pdo->query("SELECT * FROM sertifikat_pegawai WHERE nama_sertifikat='Uji Smoke' ORDER BY id_sertifikat DESC LIMIT 1")->fetch();
ok($r['code'] === 302 && $row && $row['nomor_sertifikat'] === 'NO-123' && $row['jenis'] === 'Sertifikasi' && $row['status_validasi'] === 'Tervalidasi' && $row['jalur_pelatihan'] === 'Pelatihan Teknis', 'sertifikat tersimpan dengan kolom baru');
ok($row && preg_match('#^assets/uploads/sertifikat/[a-f0-9]{32}\.pdf$#', (string) $row['file_sertifikat']) === 1, 'nama file diacak, ekstensi dari MIME');

$tmpPhp = tempnam(sys_get_temp_dir(), 'php'); file_put_contents($tmpPhp, '<?php echo "pwn"; ?>');
$t = $a->token('/admin/sertifikat/tambah.php');
$a->post('/admin/sertifikat/tambah.php', ['_csrf' => $t, 'id_pegawai' => $idPeg, 'nama_sertifikat' => 'Uji Jahat'], ['file_sertifikat' => [$tmpPhp, 'application/pdf', 'shell.pdf']]);
ok((int) $pdo->query("SELECT COUNT(*) FROM sertifikat_pegawai WHERE nama_sertifikat='Uji Jahat'")->fetchColumn() === 0, 'file PHP berkedok PDF ditolak');

$idx = $a->get('/admin/sertifikat/index.php?id_pegawai=' . $idPeg);
ok(strpos($idx['body'], 'NO-123') !== false, 'daftar sertifikat menampilkan nomor sertifikat');
ok(strpos($idx['body'], '/file.php?f=') !== false && strpos($idx['body'], 'assets/uploads') === false || strpos($idx['body'], 'href="/assets/uploads') === false, 'tautan file memakai file.php');

echo "== file.php\n";
$path = $row['file_sertifikat'];
$anon = new Client($base);
ok(in_array($anon->get('/file.php?f=' . rawurlencode($path))['code'], [302, 401, 403], true), 'tanpa login tidak bisa mengambil file');
$r = $a->get('/file.php?f=' . rawurlencode($path));
ok($r['code'] === 200 && stripos($r['headers'], 'application/pdf') !== false && stripos($r['headers'], 'nosniff') !== false, 'admin bisa mengambil file (PDF, nosniff)');
ok($a->get('/file.php?f=' . rawurlencode('assets/uploads/sertifikat/../../config/local.php'))['code'] === 404, 'path traversal ditolak');
ok($a->get('/file.php?f=' . rawurlencode('assets/uploads/sertifikat/' . str_repeat('a', 32) . '.pdf'))['code'] === 404, 'file yang tidak tercatat di DB ditolak');

echo "== Penilai (Panitera) & Ketua\n";
$p = new Client($base);
$r = $p->login(getenv('SMOKE_PANITERA_NIP'), getenv('SMOKE_PANITERA_PASS'));
ok($r['code'] === 302 && strpos($r['headers'], '/penilai/dashboard.php') !== false, 'login penilai berhasil');
ok($p->get('/file.php?f=' . rawurlencode($path))['code'] === 404, 'penilai tidak bisa mengambil sertifikat');
ok($p->get('/penilai/kegiatan_hakim/index.php')['code'] === 403, 'Panitera ditolak di modul kegiatan hakim');

$gapIdx = $p->get('/penilai/gap/index.php');
$luar = $pdo->query("SELECT p.nama_lengkap FROM analisis_kesenjangan_kompetensi g JOIN pegawai p ON p.id_pegawai=g.id_pegawai WHERE p.id_jabatan NOT IN (SELECT id_jabatan_dinilai FROM wewenang_penilaian WHERE id_jabatan_penilai=6) LIMIT 1")->fetchColumn();
$dalam = $pdo->query("SELECT p.nama_lengkap FROM analisis_kesenjangan_kompetensi g JOIN pegawai p ON p.id_pegawai=g.id_pegawai WHERE p.id_jabatan IN (SELECT id_jabatan_dinilai FROM wewenang_penilaian WHERE id_jabatan_penilai=6) LIMIT 1")->fetchColumn();
ok($luar !== false && strpos($gapIdx['body'], htmlspecialchars($luar)) === false, 'gap pegawai di luar wewenang tidak tampil');
ok($dalam !== false && strpos($gapIdx['body'], htmlspecialchars($dalam)) !== false, 'gap pegawai dalam wewenang tampil');

$pegLuar = (int) $pdo->query("SELECT id_pegawai, id_jabatan FROM pegawai WHERE id_jabatan NOT IN (SELECT id_jabatan_dinilai FROM wewenang_penilaian WHERE id_jabatan_penilai=6) LIMIT 1")->fetchColumn();
$jabLuar = (int) $pdo->query("SELECT id_jabatan FROM pegawai WHERE id_pegawai=$pegLuar")->fetchColumn();
$jml = (int) $pdo->query('SELECT COUNT(*) FROM analisis_kesenjangan_kompetensi')->fetchColumn();
$t = $p->token('/penilai/gap/tambah.php');
$p->post('/penilai/gap/tambah.php', ['_csrf' => $t, 'id_jabatan' => $jabLuar, 'id_pegawai' => $pegLuar, 'kompetensi_jabatan' => 'x', 'kompetensi_pegawai_saat_ini' => 'x', 'gap_kompetensi' => 'x', 'dampak' => 'x']);
ok((int) $pdo->query('SELECT COUNT(*) FROM analisis_kesenjangan_kompetensi')->fetchColumn() === $jml, 'POST gap untuk pegawai di luar wewenang ditolak');
$t = $p->token('/penilai/diklat/tambah.php');
$jmlD = (int) $pdo->query('SELECT COUNT(*) FROM kebutuhan_diklat')->fetchColumn();
$p->post('/penilai/diklat/tambah.php', ['_csrf' => $t, 'id_pegawai' => $pegLuar, 'id_diklat' => '', 'prioritas' => 'Tinggi', 'tahun_rencana' => 2026, 'catatan' => 'x']);
ok((int) $pdo->query('SELECT COUNT(*) FROM kebutuhan_diklat')->fetchColumn() === $jmlD, 'usulan diklat untuk pegawai di luar wewenang ditolak');

$k = new Client($base);
$k->login(getenv('SMOKE_KETUA_NIP'), getenv('SMOKE_KETUA_PASS'));
ok($k->get('/penilai/kegiatan_hakim/index.php')['code'] === 200, 'Ketua bisa membuka kegiatan hakim');
ok($k->get('/admin/kegiatan_hakim/index.php')['code'] !== 200, 'Ketua tidak bisa membuka alamat admin kegiatan hakim');
$admKh = $a->get('/admin/kegiatan_hakim/index.php');
ok($admKh['code'] === 200 && strpos($admKh['body'], '/admin/kegiatan_hakim/statistik.php') !== false, 'Admin membuka kegiatan hakim lewat alamat admin/ (menu menuju admin/)');
ok($a->get('/admin/kegiatan_hakim/statistik.php')['code'] === 200, 'Admin membuka statistik lewat alamat admin/');

echo "== Logout & sesi\n";
$k->get('/auth/logout.php');
ok($k->get('/penilai/dashboard.php')['code'] === 200, 'logout via GET tidak mengakhiri sesi');
$t = $k->token('/penilai/dashboard.php');
$r = $k->post('/auth/logout.php', ['_csrf' => $t]);
ok($r['code'] === 302 && $k->get('/penilai/dashboard.php')['code'] === 302, 'logout via POST+token mengakhiri sesi');
$pdo->prepare('UPDATE users SET status_aktif = 0 WHERE nip = ?')->execute([getenv('SMOKE_PANITERA_NIP')]);
ok($p->get('/penilai/dashboard.php')['code'] === 302, 'akun yang dinonaktifkan langsung kehilangan akses');
$pdo->prepare('UPDATE users SET status_aktif = 1 WHERE nip = ?')->execute([getenv('SMOKE_PANITERA_NIP')]);

// bersih-bersih
$pdo->prepare('DELETE FROM sertifikat_pegawai WHERE nama_sertifikat = ?')->execute(['Uji Smoke']);
hapusFileUpload($path);

echo PHP_EOL . "Hasil: $pass lulus, $fail gagal" . PHP_EOL;
exit($fail > 0 ? 1 : 0);
