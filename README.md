# SI PINTAR - Pengembangan Kompetensi Aparatur

Aplikasi web PHP native (tanpa framework) + MySQL/MariaDB (PDO) untuk penilaian kompetensi
pegawai: analisis tugas, kuesioner, wawancara, analisis kesenjangan, kebutuhan diklat,
sertifikat, dan kegiatan hakim.

Kebutuhan: PHP >= 8.1 dengan ekstensi `pdo_mysql`, `fileinfo`, `mbstring`, `zip`, `dom`
serta Composer (lihat `composer.json`).

## Setup

1. Pasang dependensi PHP:
   ```
   composer install
   ```
2. Buat database dan impor dump terbaru:
   ```
   mysql -u root -e "CREATE DATABASE si_pintar CHARACTER SET utf8mb4"
   mysql -u root si_pintar < database/si_pintar.sql
   ```
3. Terapkan migrasi yang belum ada di dump (termasuk tabel `audit_log`, kolom jawaban wawancara, serta dokumentasi foto kuesioner dan wawancara):
   ```
   php database/migrate.php            # tambahkan --status untuk melihat status
   ```
4. Konfigurasi koneksi. Salin `config/local.example.php` menjadi `config/local.php` lalu isi.
   Semua nilai juga dapat diberikan lewat environment variable `SIPINTAR_<NAMA>`
   (mis. `SIPINTAR_DB_PASS`). `config/local.php` tidak di-commit.
   - Pengembangan (XAMPP): isi `'env' => 'development'` (error ditampilkan).
   - Produksi: `'env' => 'production'` (default; error hanya dicatat di log), gunakan
     user database khusus dengan hak terbatas, **bukan `root`**, dan beri password kuat.
5. Akun Admin pertama (jika database kosong) dibuat lewat SQL:
   ```sql
   INSERT INTO users (nip, password, role) VALUES ('<nip angka>', '<hash>', 'Admin');
   ```
   Hash: `php -r "echo password_hash('rahasia-kuat', PASSWORD_DEFAULT);"`
6. Jalankan:
   - Apache/XAMPP: letakkan folder di `htdocs`; pastikan `AllowOverride All` agar `.htaccess` aktif.
   - Pengembangan cepat: `php -S localhost:8000 router.php`  
     (**wajib memakai `router.php`**; built-in server mengabaikan `.htaccess`).
7. Buka `/auth/login.php`.

### Nginx (produksi)
```nginx
location ~ ^/(database|includes|config|tests|vendor)/ { deny all; }
location ~ ^/(assets/uploads|uploads)/              { deny all; }
location ~* \.(sql|log|bak|lock|md)$                { deny all; }
```
Untuk HTTPS di belakang reverse proxy, set `'trust_proxy' => true` di `config/local.php`.

## Keamanan (ringkas)

- **CSRF**: semua request POST wajib membawa token (`csrfField()` di form, divalidasi global di
  `config/config.php`). Aksi hapus dan logout hanya lewat POST.
- **Sesi**: cookie `HttpOnly` + `SameSite=Lax` (+ `Secure` bila HTTPS), ID sesi diganti saat login,
  logout menghapus cookie, sesi berakhir setelah tidak aktif (`session_idle`, default 2 jam),
  akun yang dinonaktifkan langsung kehilangan akses.
- **File upload**: tidak bisa dibuka langsung. Semua lewat `file.php?f=...` yang mengecek login,
  keberadaan file di database, dan hak akses (Admin; Penilai hanya untuk pegawai dalam wewenangnya;
  Ketua/Wakil Ketua untuk kegiatan hakim). Validasi upload terpusat di `includes/upload.php`
  (MIME asli via `finfo`, ukuran, nama acak). PDF bukti wawancara/kuesioner yang sudah diperiksa
  ditampilkan dan diunduh sebagai salinan bertanda kotak centang “Sudah diperiksa”; unggahan asli
  tidak diubah.
- **Otorisasi penilai**: modul gap dan usulan diklat dibatasi sesuai `wewenang_penilaian`.
  Modul Kegiatan Hakim untuk Admin, Ketua, dan Wakil Ketua (`canAccessKegiatanHakim()`).
- **Audit log**: login/logout, hapus data, perubahan sertifikat tercatat di `audit_log`.
- **Error**: tidak ditampilkan di produksi; kredensial tidak ada di kode.

## Struktur folder

- `config/` - bootstrap (`config.php`), koneksi (`database.php`), konfigurasi lokal (`local.php`).
- `includes/` - `auth.php` (requireLogin/requireRole/wewenang), `security.php` (CSRF,
  audit, hapus aman), `upload.php`, `sertifikat_form.php`, layout `header.php`/`footer.php`.
- `auth/` - login, proses login, logout, profil. `file.php` - pengiriman file upload terproteksi.
- `admin/`, `penilai/` - modul per role (Kegiatan Hakim: Admin lewat `admin/kegiatan_hakim/`, Ketua/Wakil Ketua lewat `penilai/kegiatan_hakim/`, isi halaman dipakai bersama). `admin/laporan/` - laporan TNA (Word).
- `database/` - `si_pintar.sql` (dump terbaru), `migrations/` + `migrate.php`, `legacy/` (arsip migrasi
  lama yang sudah tergabung di dump).
- `tests/smoke.php` - tes end-to-end (lihat di bawah).

## Tes

Jalankan **hanya pada database uji** (tes menulis dan menghapus data):
```
php -S 127.0.0.1:8000 router.php &
SMOKE_ADMIN_NIP=.. SMOKE_ADMIN_PASS=.. SMOKE_KETUA_NIP=.. SMOKE_KETUA_PASS=.. \
SMOKE_PANITERA_NIP=.. SMOKE_PANITERA_PASS=.. php tests/smoke.php
```
Butuh ekstensi `curl`, dan data uji berupa akun Admin, Ketua, dan Panitera (jabatan id 6) seperti pada dump.

## Alur otorisasi

Penilai hanya melihat dan menilai pegawai yang jabatannya ada di `wewenang_penilaian`
(`getJabatanWewenang()`, `penilaiBerwenangAtasPegawai()`).

## Yang masih perlu dikembangkan

- Analisis kesenjangan masih diisi manual (belum otomatis membandingkan standar vs hasil penilaian).
- `admin/jawab.php` dan `penilai/jawab.php` (kuesioner, wawancara) masih dua file terpisah dengan tampilan
  mirip; logika upload dan otorisasi sudah dipusatkan, tetapi templatenya belum digabung.
- Hapus pegawai menghapus berantai (FK `ON DELETE CASCADE`) data sertifikat, penilaian, dll. File fisik
  terkait tidak ikut dibersihkan.
