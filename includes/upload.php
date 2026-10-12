<?php
/**
 * Penyimpanan file upload yang aman (dipakai semua modul).
 *  - Memeriksa kode error, is_uploaded_file, ukuran, dan MIME asli (finfo, bukan ekstensi dari klien).
 *  - Untuk gambar, memastikan benar-benar gambar (getimagesize).
 *  - Nama file diacak; ekstensi ditentukan dari MIME, bukan dari nama asli.
 *  - Disimpan di assets/uploads/<subfolder>/ yang tidak bisa dibuka langsung (lihat file.php).
 */

const UPLOAD_MAKS_BYTE = 5 * 1024 * 1024;

const UPLOAD_PDF_GAMBAR = [
    'application/pdf' => 'pdf',
    'image/jpeg'      => 'jpg',
    'image/png'       => 'png',
];

/**
 * @param array|null $file          Satu entri dari $_FILES (bukan array multi-file).
 * @param string     $subfolder     sertifikat | kegiatan_hakim | kuesioner | wawancara
 * @param array      $tipeDiizinkan mime => ekstensi
 * @return array{status:string,path:?string,error:?string}  status: none | ok | error
 */
function simpanUploadAman(?array $file, string $subfolder, array $tipeDiizinkan, int $maksByte = UPLOAD_MAKS_BYTE): array
{
    $none = ['status' => 'none', 'path' => null, 'error' => null];
    $gagal = static function (string $pesan): array {
        return ['status' => 'error', 'path' => null, 'error' => $pesan];
    };

    if (!$file || !isset($file['error']) || is_array($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $none;
    }
    if (!in_array($subfolder, ['sertifikat', 'kegiatan_hakim', 'kuesioner', 'wawancara'], true)) {
        return $gagal('Folder tujuan tidak valid.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return $gagal('File gagal diunggah (kode ' . (int) $file['error'] . ').');
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return $gagal('File upload tidak valid.');
    }
    if ($file['size'] > $maksByte) {
        return $gagal('Ukuran file melebihi batas.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!is_string($mime) || !isset($tipeDiizinkan[$mime])) {
        return $gagal('Tipe file tidak diizinkan.');
    }
    if (strpos($mime, 'image/') === 0 && @getimagesize($file['tmp_name']) === false) {
        return $gagal('File gambar tidak valid.');
    }

    $dir = __DIR__ . '/../assets/uploads/' . $subfolder;
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return $gagal('Folder penyimpanan tidak dapat dibuat.');
    }

    $relatif = 'assets/uploads/' . $subfolder . '/' . bin2hex(random_bytes(16)) . '.' . $tipeDiizinkan[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $relatif)) {
        return $gagal('File gagal disimpan.');
    }
    @chmod(__DIR__ . '/../' . $relatif, 0644);

    return ['status' => 'ok', 'path' => $relatif, 'error' => null];
}
