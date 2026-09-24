# SI PINTAR - Pengembangan Kompetensi Aparatur

Kerangka aplikasi web PHP native (tanpa framework) + MySQL (PDO) untuk penilaian
kompetensi pegawai, sesuai ERD/DFD yang sudah dirancang sebelumnya.

## Setup

1. Buat database MySQL dan import `database/si_pintar_schema.sql`.
2. Sesuaikan kredensial database di `config/database.php` (host, nama db, user, password).
3. Karena hanya Admin yang boleh membuat akun lain lewat aplikasi, buat akun Admin
   pertama secara manual lewat SQL:
   ```sql
   INSERT INTO users (username, password, role) VALUES ('admin', '<hash>', 'Admin');
   ```
   Buat hash password lewat terminal:
   ```
   php -r "echo password_hash('rahasia', PASSWORD_DEFAULT);"
   ```
4. Jalankan dengan PHP built-in server dari folder proyek:
   ```
   php -S localhost:8000
   ```
5. Untuk mengaktifkan ekspor PDF dengan format yang sama seperti Word, instal dependency:
  ```
  composer install
  ```
6. Buka `http://localhost:8000/auth/login.php`

## Struktur folder

- `config/` - koneksi database (`database.php`) & konfigurasi aplikasi (`config.php`)
- `includes/` - middleware otorisasi (`auth.php`: requireLogin, requireRole,
  getJabatanWewenang), layout `header.php`/`footer.php`
- `auth/` - login, proses login, logout
- `admin/` - modul khusus role Admin: pegawai (CRUD lengkap), jabatan (master,
  read-only), sertifikat, wewenang penilaian, akun user
  wewenang), kuesioner (buat kuesioner + pertanyaan sendiri), wawancara, analisis
  kesenjangan, kebutuhan diklat, kegiatan hakim (khusus Ketua/Wakil Ketua)
- `admin/laporan/` - laporan TNA 2026 untuk Admin, dapat difilter bulanan/tahunan dan diunduh dalam format Word atau PDF dari data aplikasi.
  `assets/vendor/`, logo & foto gedung PN Yogyakarta di `assets/img/`, `assets/css/style.css`
  berisi override tema hijau-emas PN Yogyakarta agar selaras dengan SI-APIC)
- `database/` - file skema SQL

## Alur otorisasi

Setiap Penilai hanya bisa melihat dan menilai pegawai yang jabatannya termasuk dalam
wewenangnya, dicek lewat fungsi `getJabatanWewenang()` yang membaca tabel
`wewenang_penilaian`. Contoh: user dengan jabatan Panitera hanya akan melihat pegawai
berjabatan Panitera Muda, Panitera Pengganti, Jurusita, Jurusita Pengganti, dan Staf
Kepaniteraan di dashboard serta form-form penilaiannya.

## Yang masih perlu dikembangkan (kerangka dasar)

- Modul wawancara: baru sebatas penjadwalan; pengisian `jawaban_wawancara`
  per pertanyaan belum ada form-nya.
- Modul penilaian: upload bukti dokumentasi (`bukti_dokumentasi`) belum ada form upload
  file - tinggal tambahkan `<input type="file">` + proses `move_uploaded_file()`.
- Modul gap: `analisis_gap` masih diisi manual/lewat SQL - idealnya dibuat proses
  otomatis yang membandingkan `standar_kompetensi_jabatan` dengan
  `penilaian_kompetensi` setiap kali penilaian baru disimpan.
- Validasi input sisi server masih minim (baru cek field kosong) - tambahkan validasi
  lebih ketat sesuai kebutuhan.
