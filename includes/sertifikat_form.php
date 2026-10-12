<?php
/**
 * Bagian bersama form sertifikat (tambah/edit): opsi pilihan & validasi
 * kolom nomor_sertifikat, jalur_pelatihan, status_validasi, jenis.
 */

const SERTIFIKAT_JENIS  = ['Pelatihan', 'Sertifikasi'];
const SERTIFIKAT_STATUS = ['Tervalidasi', 'Ditolak - Perbaiki Data'];
const SERTIFIKAT_JALUR  = [
    'Pelatihan Struktural Kepemimpinan', 'Pelatihan Manajerial', 'Pelatihan Teknis', 'Pelatihan Fungsional',
    'Pelatihan Sosial Kultural', 'Bimbingan Teknis', 'Seminar/Konferensi/Sarasehan', 'Workshop atau Lokakarya',
    'Penataran', 'Sosialisasi', 'E-learning', 'Kursus', 'Belajar Mandiri', 'Mentoring',
];

/**
 * @return array{0: array, 1: ?string} [nilai terbersih, pesan error]
 */
function sertifikatBacaInput(array $in): array
{
    $tanggal = trim((string) ($in['tanggal_terbit'] ?? ''));
    if ($tanggal !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $tanggal);
        if (!$d || $d->format('Y-m-d') !== $tanggal) {
            return [[], 'Tanggal terbit tidak valid.'];
        }
    }
    $jenis = trim((string) ($in['jenis'] ?? ''));
    if ($jenis !== '' && !in_array($jenis, SERTIFIKAT_JENIS, true)) {
        return [[], 'Jenis harus Pelatihan atau Sertifikasi.'];
    }
    $status = trim((string) ($in['status_validasi'] ?? ''));
    if ($status !== '' && !in_array($status, SERTIFIKAT_STATUS, true)) {
        return [[], 'Status validasi tidak valid.'];
    }
    $nilai = [
        'nama_sertifikat'  => trim((string) ($in['nama_sertifikat'] ?? '')),
        'penyelenggara'    => trim((string) ($in['penyelenggara'] ?? '')),
        'tanggal_terbit'   => $tanggal !== '' ? $tanggal : null,
        'nomor_sertifikat' => trim((string) ($in['nomor_sertifikat'] ?? '')),
        'jalur_pelatihan'  => trim((string) ($in['jalur_pelatihan'] ?? '')),
        'status_validasi'  => $status !== '' ? $status : null,
        'jenis'            => $jenis !== '' ? $jenis : null,
    ];
    if ($nilai['nama_sertifikat'] === '') {
        return [[], 'Nama sertifikat wajib diisi.'];
    }
    if (mb_strlen($nilai['nama_sertifikat']) > 200 || mb_strlen($nilai['penyelenggara']) > 150
        || mb_strlen($nilai['nomor_sertifikat']) > 100 || mb_strlen($nilai['jalur_pelatihan']) > 80) {
        return [[], 'Salah satu isian terlalu panjang.'];
    }
    foreach (['penyelenggara', 'nomor_sertifikat', 'jalur_pelatihan'] as $k) {
        if ($nilai[$k] === '') {
            $nilai[$k] = null;
        }
    }
    return [$nilai, null];
}

/** Field tambahan form (nomor, jalur, jenis, status). $v = nilai saat ini. */
function sertifikatFieldTambahan(array $v): void
{
    ?>
  <div class="mb-3">
    <label class="form-label" for="nomor_sertifikat">Nomor sertifikat</label>
    <input type="text" name="nomor_sertifikat" id="nomor_sertifikat" class="form-control" maxlength="100" value="<?= e($v['nomor_sertifikat'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="jalur_pelatihan">Jalur pelatihan</label>
    <input type="text" name="jalur_pelatihan" id="jalur_pelatihan" class="form-control" maxlength="80" list="daftar-jalur" value="<?= e($v['jalur_pelatihan'] ?? '') ?>">
    <datalist id="daftar-jalur">
      <?php foreach (SERTIFIKAT_JALUR as $j): ?><option value="<?= e($j) ?>"><?php endforeach; ?>
    </datalist>
  </div>
  <div class="mb-3">
    <label class="form-label" for="jenis">Jenis</label>
    <select name="jenis" id="jenis" class="form-select">
      <option value="">-- pilih jenis --</option>
      <?php foreach (SERTIFIKAT_JENIS as $j): ?>
      <option value="<?= e($j) ?>" <?= ($v['jenis'] ?? '') === $j ? 'selected' : '' ?>><?= e($j) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label" for="status_validasi">Status validasi</label>
    <select name="status_validasi" id="status_validasi" class="form-select">
      <option value="">Belum divalidasi</option>
      <?php foreach (SERTIFIKAT_STATUS as $st): ?>
      <option value="<?= e($st) ?>" <?= ($v['status_validasi'] ?? '') === $st ? 'selected' : '' ?>><?= e($st) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
<?php
}
