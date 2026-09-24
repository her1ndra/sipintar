-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Sep 2026 pada 08.46
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `si_pintar`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `analisis_kesenjangan_kompetensi`
--

CREATE TABLE `analisis_kesenjangan_kompetensi` (
  `id_kesenjangan` int(10) UNSIGNED NOT NULL,
  `id_jabatan` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `kompetensi_jabatan` text NOT NULL COMMENT 'Standar/kebutuhan kompetensi untuk jabatan',
  `kompetensi_pegawai_saat_ini` text NOT NULL COMMENT 'Kondisi/skor kompetensi pegawai saat ini',
  `gap_kompetensi` text NOT NULL COMMENT 'Nilai selisih/kategori gap',
  `dampak` text DEFAULT NULL COMMENT 'Dampak dari adanya gap kompetensi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `analisis_kesenjangan_kompetensi`
--

INSERT INTO `analisis_kesenjangan_kompetensi` (`id_kesenjangan`, `id_jabatan`, `id_pegawai`, `kompetensi_jabatan`, `kompetensi_pegawai_saat_ini`, `gap_kompetensi`, `dampak`, `created_at`, `updated_at`) VALUES
(1, 11, 25, 'Administrasi perkara Tipikor', 'Pemahaman SOP belum merata\r\nKetelitian minutasi masih rendah', 'Perlu peningkatan administrasi perkara dan ketelitian kerja', 'Potensi keterlambatan minutasi\r\nKesalahan dokumen', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(2, 8, 23, 'Penguasaan e-Berpadu', 'Penggunaan aplikasi e-berpadu dan SIPP belum optimal', 'Perlu pemahaman aplikasi perkara pidana', 'Risiko kesalahan input', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(3, 7, 24, 'Administrasi Perkara Perdata', 'Pemahaman SOP belum merata', 'Perlu peningkatan administrasi perkara dan ketelitian kerja', 'Risiko kesalahan dokumen penting', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(4, 10, 21, 'Pemahaman administrasi perkara PHI', 'Pemahaman SOP belum merata', 'Perlu peningkatan administrasi perkara PHI dan ketelitian kerja', 'Risiko kesalahan dokumen penting', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(5, 9, 22, 'Pelaporan pengarsipan\r\nPengelola pengaduan', 'Laporan belum seragam\r\nPengarsipan belum optimal', 'Perlu arsiparis perkara', 'Ketidaktepatan laporan berkala\r\nPengarsipan tidak optimal', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(6, 17, 117, 'Administrasi ASN\r\nPenguasaan Aplikasi SIKEP\r\nSI ASN\r\nKOMDANAS\r\nE KINERJA', 'Kesalahan dokumen kepegawaian masih terjadi', 'Perlu penguatan administrasi ASN', 'Hambatan pelayanan internal untuk administrasi kepegawaian', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(7, 48, 64, 'Monitoring Perencanaan\r\nTI dan Pelaporan', 'Monitoring belum optimal\r\nPelaporan belum akurat', 'Perlu pelatihan monev\r\nPengelolaan TI', 'Laporan kinerja kurang akurat', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(8, 18, 69, 'BMN\r\nAdministrasi Umum dan Keuangan\r\nPenggunaan Aplikasi Sakti\r\ne-Bima\r\ne-Monev Bappenas\r\nKomdanas\r\nCoretax\r\nDigit\r\nMyintress\r\nSirup\r\nLPSE\r\nInaproc\r\ne-Sadewa', 'Penggunaan aplikasi belum optimal karena keterbatasan personil', 'Perlu penambahan personil\r\nPelatihan untuk Aplikasi di Keuangan', 'Risiko temuan audit keuangan', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(9, 12, 33, 'Pembuatan Berita Acara\r\nPenginputan BA ke Aplikasi SIPP', 'Masih terjadi salah upload\r\nMasih ada kesalahan dalam membuat BA', 'Perlu ketelitian dalam membuat BA\r\nKetelitian dalam penguploadan data ke Aplikasi SIPP', 'Risiko kesalahan Berita Acara Persidangan\r\nRisiko kesalahan dalam penginputan data', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(10, 23, 70, 'Pelayanan prima', 'Pelayanan sudah konsisten\r\nPenanganan pengaduan belum optimal', 'Perlu service excellent\r\nPengaduan publik', 'Kepuasan publik menurun', '2026-09-22 02:22:13', '2026-09-22 02:22:13'),
(11, 27, 78, 'Teknisi Sarana dan Prasarana Umum', 'Pemahaman prosedur keselamatan (K3)\r\nPerbaikan fasilitas kantor/gedung\r\nMekanikal/inspeksi rutin', 'Kurangnya pemahaman mendalam tentang standar K3 dalam operasional sehari–hari\r\nKemampuan masih sebatas teori belum teknis secara nyata dalam pemeliharaan preventif', 'Risiko kecelakaan kerja\r\nPerbaikan kurang tepat waktu\r\nKurang efisien', '2026-09-22 02:22:13', '2026-09-22 02:22:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `analisis_tugas`
--

CREATE TABLE `analisis_tugas` (
  `id_analisis_tugas` int(10) UNSIGNED NOT NULL,
  `id_jabatan` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED DEFAULT NULL,
  `tugas` text NOT NULL,
  `kegiatan` text NOT NULL,
  `kompetensi_sementara_jabatan` text DEFAULT NULL,
  `kompetensi_jabatan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `analisis_tugas`
--

INSERT INTO `analisis_tugas` (`id_analisis_tugas`, `id_jabatan`, `id_pegawai`, `tugas`, `kegiatan`, `kompetensi_sementara_jabatan`, `kompetensi_jabatan`, `created_at`, `updated_at`) VALUES
(1, 7, 24, 'Melaksanakan Administrasi Perkara Di Bidang Perdata', 'Pelaksanaan Pemeriksaan Dan Penelaahan Kelengkapan Berkas Perkara Perdata.\r\nPelaksanaan Registrasi Perkara Gugatan Dan Permohonan.\r\nPelaksanaan Distribusi Perkara Yang Telah Diregister Untuk Diteruskan Kepada Ketua Majelis Hakim Berdasarkan Penetapan Penunjukkan Majelis Hakim Dari Ketua Pengadilan.\r\nPelaksanaan Penerimaan Kembali Berkas Perkara Yang Sudah Diputus Dan Diminutasi.\r\nPelaksanaan Pemberitahuan Isi Putusan Tingkat Pertama Kepada Para Pihak Yang Tidak Hadir.\r\nPelaksanaan Penyampaian Pemberitahuan Putusan Tingkat Banding, Kasasi Dan Peninjauan Kembali Kepada Para Pihak.\r\nPelaksanaan Penerimaan Dan Pengiriman Berkas Perkara Yang Dimohonkan Banding, Kasasi Dan Peninjauan Kembali.\r\nPelaksanaan Pengawasan Terhadap Pemberitahuan Isi Putusan Upaya Hukum Kepada Para Pihak Dan Menyampaikan Relas Penyerahan Isi Putusan Kepada Pengadilan Tinggi Dan Mahkamah Agung.\r\nPelaksanaan Penerimaan Konsinyasi.\r\nPelaksanaan Penerimaan Permohonan Eksekusi.\r\nPelaksanaan Penyimpanan Berkas Perkara Yang Belum Mempunyai Kekuatan Hukum Tetap.\r\nPelaksanaan Penyerahan Berkas Perkara Yang Sudah Berkekuatan Hukum Tetap Kepada Panitera Muda Hukum.\r\nPelaksanaan Urusan Tata Usaha Kepaniteraan.\r\nPelaksanaan Fungsi Lain Yang Diberikan Oleh Panitera.', 'Ketelitian tinggi, pemahaman hukum acara perdata, penguasaan aplikasi SIPP', 'Administrasi perkara perdata, penguasaan SIPP, ketepatan minutasi, pelayanan prima', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(2, 8, 23, 'Melaksanakan Administrasi Perkara Di Bidang Pidana', 'Pelaksanaan Pemeriksaan Dan Penelaahan Kelengkapan Berkas Perkara Pidana.\r\nPelaksanaan Registrasi Perkara Pidana.\r\nPelaksanaan Penerimaan Permohonan Praperadilan Dan Pemberitahuan Kepada Termohon.\r\nPelaksanaan Distribusi Perkara Yang Telah Diregister Untuk Diteruskan Kepada Ketua Majelis Hakim Berdasarkan Penetapan Penunjukkan Majelis Hakim Dari Ketua Pengadilan.\r\nPelaksanaan Penghitungan, Penyiapan Dan Pengiriman Penetapan Penahanan, Perpanjangan Penahanan Dan Penangguhan Penahanan.\r\nPelaksanaan Penerimaan Permohonan Ijin Penggeledahan Dan Ijin Penyitaan Dari Penyidik.\r\nPelaksanaan Penerimaan Kembali Berkas Perkara Yang Sudah Diputus Dan Diminutasi.\r\nPelaksanaan Pemberitahuan Isi Putusan Tingkat Pertama Kepada Para Pihak Yang Tidak Hadir.\r\nPelaksanaan Penyampaian Pemberitahuan Putusan Tingkat Banding, Kasasi Dan Peninjauan Kembali Kepada Para Pihak.\r\nPelaksanaan Penerimaan Dan Pengiriman Berkas Perkara Yang Dimohonkan Banding, Kasasi Dan Peninjauan Kembali.\r\nPelaksanaan Pengawasan Terhadap Pemberitahuan Isi Putusan Upaya Hukum Kepada Para Pihak Dan Menyampaikan Relas Penyerahan Isi Putusan Kepada Pengadilan Tinggi Dan Mahkamah Agung.\r\nPelaksanaan Pemberitahuan Isi Putusan Upaya Hukum Kepada Jaksa Penuntut Umum Dan Terdakwa.\r\nPelaksanaan Penerimaan Permohonan Eksekusi.\r\nPelaksanaan Penyimpanan Berkas Perkara Yang Belum Mempunyai Kekuatan Hukum Tetap.\r\nPelaksanaan Penyerahan Berkas Perkara Yang Sudah Berkekuatan Hukum Tetap Kepada Panitera Muda Hukum.\r\nPelaksanaan Urusan Tata Usaha Kepaniteraan.\r\nPelaksanaan Fungsi Lain Yang Diberikan Oleh Panitera.', 'Ketelitian, pemahaman hukum acara pidana, disiplin kerja', 'Administrasi perkara pidana, penguasaan e-Court, ketepatan register', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(3, 11, 25, 'Melaksanakan Administrasi Perkara Di Bidang Perkara Khusus Tindak Pidana Korupsi', 'Koordinator pelayanan PTSP.\r\nMonitoring dan evaluasi pelaksanaan administrasi pada Kepaniteraan TIPIKOR.\r\nMonitoring dan evaluasi pelimpahan perkara TIPIKOR melalui E-berpadu.\r\nMonitoring dan evaluasi pemrosesan izin besuk , izin penyitaan, izin penggeledahan dan penahanan melalui E-berpadu.\r\nMonitoring dan evaluasi pemrosesan upaya hukum perkara TIPIKOR secara elektronik.', 'Integritas tinggi, kerahasiaan dokumen, pemahaman hukum tipikor', 'Administrasi perkara tipikor, akurasi dokumen, pengendalian arsip', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(4, 10, 21, 'Melaksanakan Administrasi Perkara Di Bidang Perkara Khusus Penyelesaian Perselisihan Hubungan Industrial', 'Membantu Hakim dalam persidangan dengan mengikuti dan mencatat jalannya persidangan.\r\nMemonitor pendaftaran perkara Gugatan, secara elektronik.\r\nMemonitor, panjar biaya Gugatan yang diajukan.\r\nMemonitor register perkara PHI secara elektronik.\r\nMemeriksa berkas perkara yang diajukan upaya hukum Kasasi.\r\nMenerima dan memeriksa berkas perkara yang telah diminutasi.\r\nMemonitor penyerahan berkas perkara In Aktif yang sudah Berkekuatan Hukum Tetap (BHT) ke kepaniteraan Hukum.\r\nMemeriksa permohonan eksekusi yang diajukan oleh Pemohon.\r\nMembuat resume atas permohonan eksekusi.\r\nMemonitor surat masuk dan surat kembali, serta mendistribusikan.\r\nMembuat surat balasan atas surat masuk.', 'Pemahaman hukum PHI, komunikasi koordinatif, ketelitian', 'Administrasi perkara PHI, ketelitian dokumen, kecepatan pelayanan', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(5, 9, 22, 'Melaksanakan Pengumpulan, Pengolahan Dan Penyajian Data Perkara, Kehumasan, Penataan Arsip Perkara Serta Pelaporan', 'Pelaksanaan Pengumpulan, Pengelolaan Dan Penyajian Data Perkara.\r\nPelaksanaan Penyajian Statistik Perkara.\r\nPelaksanaan Penyusunan Dan Pengiriman Pelaporan Perkara.\r\nPelaksanaan Penataan, Penyimpanan Dan Pemeliharaan Arsip Perkara.\r\nPelaksanaan Kerja Sama Dengan Arsip Daerah Untuk Penitipan Berkas Perkara.\r\nPelaksanaan Penyiapan, Pengelolaan Dan Penyajian Bahan-Bahan Yang Berkaitan Dengan Transparansi Perkara.\r\nPelaksanaan Penghimpunan Pengaduan Dari Masyarakat.\r\nPelaksanaan Fungsi Lain Yang Diberikan Oleh Panitera.', 'Penyusunan laporan, dokumentasi hukum, penguasaan aplikasi', 'Pelaporan perkara, dokumentasi hukum, publikasi informasi', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(6, 17, 117, 'Melaksanakan Penyiapan Bahan Pelaksanaan Urusan Kepegawaian, Penataan Organisasi Dan Tata Laksana', 'Merencanakan dan memimpin pelaksanaan tugas bagian Kepegawaian, organisasi, dan Tata Laksana.\r\nMenetapkan sasaran kegiatan setiap tahun.\r\nMenyusun dan menjadwalkan rencana kegiatan.\r\nMembagi tugas kepada bawahan dan menentukan penanggung jawab kegiatan Sub bagian Kepegawaian.\r\nOrganisasi dan Tata laksana.\r\nMenggerakkan dan mengarahkan pelaksanaan kegiatan Sub Bagian Kepegawaian, Organisasi dan Tata Laksana.\r\nMengadakan konsultasi dengan atasan setiap waktu diperlakukan.\r\nMelakukan tugas khusus dari atasan/pimpinanan.\r\nMengevaluasi pelaksanaan tugas bawahan.\r\nMemantau dan melakukan evaluasi pelaksanaan tugas para bawahan di lingkungan Sub bagian Kepegawaian, Organisasi dan Tata laksana.\r\nMelaksanakan tugas dari atasan sehubungan dengan kedinasan.', 'Pemahaman regulasi ASN, administrasi kepegawaian, tata naskah dinas', 'Administrasi ASN, tata laksana organisasi, ketelitian dokumen', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(7, 48, 64, 'Melaksanakan Penyiapan Bahan Pelaksanaan, Program, Dan Anggaran, Pengelolaan Teknologi Informasi, Dan Statistik, Serta Pelaksanaan Pemantauan, Evaluasi Dan Dokumentasi Serta Pelaporan', 'Melaksanakan Penyiapan Bahan Pelaksanaan Program Dan Anggaran.\r\nMelaksanakan Penyusunan Perencanaan Program Dan Anggaran/ Kegiatan/RKAKL Dan Penyusunan TOR, RAB, Bahan Dan Data Dukung Pengajuan Anggaran.\r\nMelaksanakan Revisi DIPA Dan DIPA Hal III Berupa Perubahan/Pergeseran Rincian Anggaran Yang Telah Ditetapkan Berdasarkan APBN/DIPA.\r\nMelaksanakan Revisi POK/Komponen Dalam Satu Output Yang Disesuaikan Dengan Output Yang Disesuaikan Dengan Rencana Kegiatan Dan Dana Yang Tersedia.\r\nMelaksanakan Perawatan Dan Pemeliharaan Jaringan Sehingga Kualitas Dan Kinerja Perangkat Jaringan Stabil Dan Tidak Terjadi Kerusakan Pada Komponen Yang Ada Didalamnya.\r\nMelaksanakan Perawatan Dan Pengelolaan CCTV, Yaitu Menjaga Sarpras, Sistem, Aplikasi Jaringan, Database CCTV Dalam Kondisi Baik Dan Dapat Berfungsi Dengan Baik.\r\nMonev Terlaksananya Keterbukaan Informasi Publik (KIP) Yaitu Dengan Menjaga Keamanan Website Berikut Aplikasi Yang Terintegrasi, Publikasi Informasi Website Dan Media Sosial.\r\nMelaksanakan Pengelolaan Server Yaitu Dengan Menjaga Sarpras, Sistem, Aplikasi, Jaringan, Database Server Dalam Kondisi Baik Dan Dapat Berfungsi Dengan Baik.\r\nMonev Terlaksananya Koordinasi Dengan Bagian Umum Terkait Pengelolaan Jaringan Listrik Dan Genset Yang Mendukung Perangkat/Sarpras IT Dapat Berfungsi Dengan Baik.\r\nMonev Terlaksananya Pengumpulan Data Statistik Akurat Dan Benar.\r\nMonev Terkoordinirnya Pengelolaan Dokumen Surat Keputusan Dalam Aplikasi JDIH.\r\nMelaksanakan Perawatan Dan Pengelolaan Sistem IT Yaitu Menjaga Sarpras, Sistem Aplikasi, Jaringan, Database dll Dalam Kondisi Dan Fungsi Baik: Sinkronisasi SIPP (Web, PT, MA, Ecourt, Direktori Putusan, SPPT-TI), Media Sosial, Layanan Ptsp, Sidang Online.\r\nMelaksanakan Penyusunan Dan Pengiriman Laporan Badilum Selain Perkara Sesuai, Benar Dan Tepat Waktu.\r\nMonev Terlaksananya Penyusunan Dan Pengiriman Laporan Monev Smart Sesuai Benar Dan Tepat Waktu.\r\nMonev Terlaksananya Penyusunan Laporan Kegiatan Bulanan Sesuai, Benar Dan Tepat Waktu.\r\nMonev Terlaksananya Penyusunan Dan Pengiriman Data Dan Dokumen SAKIP Pada Komdanas Sesuai, Benar Dan Tepat Waktu.\r\nMelaksanakan Penyusunan Dan Pengiriman Laporan Tahunan/ Kegiatan Sesuai, Benar Dan Tepat Waktu.\r\nMelaksanakan Penyusunan Dokumen Evaluasi Kinerja Pada Aplikasi SEMAR Sesuai, Benar Dan Tepat Waktu.\r\nPenyelesaian Tindak Lanjut Atas Hasil Monitoring Dan Evaluasi/ Assesment Surveilance AMPUH Badilum.\r\nPenyelesaian Tindak Lanjut Atas Hasil Pemeriksaan Pengadilan Tinggi.\r\nPenyelesaian Tindak Lanjut Atas Hasil Monitoring Dan Evaluasi/ Assesment Sistem Manajemen Anti Penyuapan.\r\nPenyelesaian Tindak Lanjut Atas Hasil Pemeriksaan Bawas.\r\nMonev Penyelesaian Tindak Lanjut Atas Hasil Pengawasan Bidang.\r\nMonev Terlaksananya Administrasi Dan Dokumentasi Persuratan Tertata Dengan Rapi.\r\nTerlaksananya Tugas-Tugas Lain Yang Ada Di Perencanaan, IT Dan Pelaporan Serta Tugas Lain Yang Diberikan Pimpinan.\r\nTerlaksananya Monitoring & Evaluasi Secara Berjenjang Di Kesekretariatan, Terutama Bagian PTIP.\r\nPenyelesaian Pengembangan Sistem/Aplikasi Nasional (SIPP, E-Court, E-Berpadu, SIAP.', 'Penguasaan TI, analisis data, perencanaan program', 'Monitoring evaluasi, pengelolaan TI, penyusunan laporan kinerja', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(8, 18, 69, 'Pengelolaan BMN dan keuangan', 'Membuat Perencanaan Pencairan Anggaran Seminggu Sebelum Pencairan Dana Dan Menginput Dalam Aplikasi AFS,Kemudian Mengirimkan Ke KPPN.\r\nMengajukan Uang Persediaan Dan Mengantarkan Spmnya Ke KPPN.\r\nMembuat Dan Mengajukan Tambahan Uang Persediaan Yang Bersifat Mendesak Dan Mengantarkannya Spmnya Ke KPPN.\r\nMengajukan Ganti Uang Persediaan Dan Mengatarkan Spmnya Ke KPPN.\r\nMembuat SPM Langsung Dan Mengantarkannya Ke KPPN.\r\nMengisi Setiap Transaksi Ke Dalam Buku: Buku Kas Umum, Buku Kas Umum, Buku Pembantu Kas Tunai, Buku Pembantu Bank, Buku Pembantu Pajak, Buku Pembantu Uang Persediaan, Buku Pembantu Kas LS.\r\nMembuat Laporan Pertanggungjawaban Bendahara Pengeluaran, Membuat Realisasi Anggaran Manual, Membuat Penyerapan Anggaran Manual.\r\nMenerima Dan Menyetorkan Pajak Melalui Kantor Pos Bank.\r\nMembuat Daftar Dan Pertanggungjawaban Remnunisasi Dan Mengirimkannya ke KORWIL.', 'Pemahaman keuangan negara, ketelitian, akuntabilitas', 'Pengelolaan BMN, keuangan, akuntabilitas anggaran', '2026-09-22 01:59:58', '2026-09-22 01:59:58'),
(9, 23, 70, 'Pelayanan publik dan informasi', 'Pelayanan informasi perkara.\r\nPenerimaan tamu.\r\nPengaduan masyarakat.\r\nPelayanan dokumen.', 'Komunikasi efektif, pelayanan publik, etika layanan', 'Service excellent, komunikasi publik, penanganan pengaduan', '2026-09-22 01:59:58', '2026-09-22 01:59:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bukti_dokumentasi`
--

CREATE TABLE `bukti_dokumentasi` (
  `id_bukti` int(10) UNSIGNED NOT NULL,
  `id_penilaian` int(10) UNSIGNED NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `jenis_bukti` enum('Foto','Dokumen','Sertifikat','Surat Tugas','Lainnya') NOT NULL DEFAULT 'Dokumen',
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bukti_wawancara`
--

CREATE TABLE `bukti_wawancara` (
  `id_bukti` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_bukti` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_wawancara`
--

CREATE TABLE `detail_wawancara` (
  `id_detail` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `kompetensi` varchar(200) NOT NULL,
  `isi_penilaian` text NOT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `status_kompetensi` enum('Kompeten','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `diklat`
--

CREATE TABLE `diklat` (
  `id_diklat` int(10) UNSIGNED NOT NULL,
  `id_kompetensi` int(10) UNSIGNED DEFAULT NULL,
  `nama_diklat` varchar(200) NOT NULL,
  `penyelenggara` varchar(150) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_kuesioner`
--

CREATE TABLE `hasil_kuesioner` (
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_bukti` varchar(255) DEFAULT NULL,
  `status_kompetensi` enum('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `daftar_nilai` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_wawancara`
--

CREATE TABLE `hasil_wawancara` (
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `kompetensi` varchar(200) NOT NULL,
  `isi_penilaian` text NOT NULL,
  `nilai` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Nilai 0-100',
  `file_bukti` varchar(255) DEFAULT NULL,
  `status_kompetensi` enum('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_wawancara`
--

INSERT INTO `hasil_wawancara` (`id_hasil`, `id_wawancara`, `kompetensi`, `isi_penilaian`, `nilai`, `file_bukti`, `status_kompetensi`, `created_at`, `updated_at`) VALUES
(0, 4, 'Penguasaan SIPP', 'Bagaimana prosedur minutasi perkara sampai selesai?', NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 04:08:10'),
(1, 1, 'Pelayanan Prima', 'Bagaimana menangani ublicn masyarakat yang tidak puas?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:42'),
(2, 1, 'Pelayanan Prima', 'Apa yang dimaksud dengan standar pelayanan prima?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 03:05:26'),
(3, 1, 'Pelayanan Prima', 'Bagaimana prosedur pelayanan informasi perkara kepada masyarakat?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:37'),
(4, 2, 'Pelayanan Prima', 'Bagaimana menangani masyarakat yang tidak puas?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:55'),
(5, 2, 'Pelayanan Prima', 'Apa yang dimaksud dengan standar pelayanan prima?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 03:05:26'),
(6, 2, 'Pelayanan Prima', 'Bagaimana prosedur pelayanan informasi perkara kepada masyarakat?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:52'),
(7, 3, 'Teknisi Sarana dan Prasarana Umum', 'Apa langkah yang Anda lakukan dalam pemiliharaan preventif system kelistrikan gedung?', NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 04:07:31'),
(8, 3, 'Teknisi Sarana dan Prasarana Umum', 'Bagaimana Anda beradaptasi dengan teknologi atau peralatan baru dalam system pemeliharaan sarana?', NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 03:05:26'),
(9, 3, 'Teknisi Sarana dan Prasarana Umum', 'Bagaimana Anda memastikan standar keamanan (K3) terpenuhi saat Anda melakukan perbaikan prasarana?', NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 04:07:04'),
(10, 4, 'Penguasaan SIPP', 'Bagaimana cara melakukan koreksi data perkara yang salah input?', NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 04:08:36'),
(12, 4, 'Penguasaan SIPP', 'Apakah Saudara memahami seluruh tahapan input perkara pada SIPP?', NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 04:08:30'),
(13, 5, 'Penguasaan SIPP', 'Apakah Saudara memahami seluruh tahapan input perkara pada SIPP?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:08'),
(14, 5, 'Penguasaan SIPP', 'Bagaimana prosedur minutasi perkara sampai selesai?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 03:05:26'),
(15, 5, 'Penguasaan SIPP', 'Bagaimana cara melakukan koreksi data perkara yang salah input?', NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jabatan`
--

CREATE TABLE `jabatan` (
  `id_jabatan` int(10) UNSIGNED NOT NULL,
  `kode_jabatan` varchar(20) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  `is_penilai` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = jabatan ini berpotensi menjadi user Penilai',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jabatan`
--

INSERT INTO `jabatan` (`id_jabatan`, `kode_jabatan`, `nama_jabatan`, `is_penilai`, `created_at`) VALUES
(1, 'KTU', 'Ketua', 1, '2026-09-22 08:00:00'),
(2, 'WKL', 'Wakil Ketua', 1, '2026-09-22 08:00:00'),
(3, 'HKM-UM', 'Hakim Utama Muda', 0, '2026-09-22 08:00:00'),
(4, 'HKM-MU', 'Hakim Madya Utama', 0, '2026-09-22 08:00:00'),
(5, 'HKM-MM', 'Hakim Madya Muda', 0, '2026-09-22 08:00:00'),
(6, 'PAN', 'Panitera', 1, '2026-09-22 08:00:00'),
(7, 'PM-PDT', 'Panitera Muda Perdata', 0, '2026-09-22 08:00:00'),
(8, 'PM-PID', 'Panitera Muda Pidana', 0, '2026-09-22 08:00:00'),
(9, 'PM-HKM', 'Panitera Muda Hukum', 0, '2026-09-22 08:00:00'),
(10, 'PM-PHI', 'Panitera Muda Khusus PHI', 0, '2026-09-22 08:00:00'),
(11, 'PM-TPK', 'Panitera Muda Khusus Tipikor', 0, '2026-09-22 08:00:00'),
(12, 'PP', 'Panitera Pengganti', 0, '2026-09-22 08:00:00'),
(13, 'JS', 'Jurusita', 0, '2026-09-22 08:00:00'),
(14, 'JSP', 'Jurusita Pengganti', 0, '2026-09-22 08:00:00'),
(15, 'SEK', 'Sekretaris', 1, '2026-09-22 08:00:00'),
(16, 'KSB-KOT', 'Kepala Sub Bagian Kepegawaian, Organisasi dan Tata Laksana', 0, '2026-09-22 08:00:00'),
(17, 'KSB-PTIP', 'Kepala Sub Bagian Perencanaan, TI dan Pelaporan', 0, '2026-09-22 08:00:00'),
(18, 'KSB-UK', 'Kepala Sub Bagian Umum dan Keuangan', 0, '2026-09-22 08:00:00'),
(19, 'AAPBN', 'Analis APBN', 0, '2026-09-22 08:00:00'),
(20, 'PKAP', 'Pranata Komputer Ahli Pertama', 0, '2026-09-22 08:00:00'),
(21, 'ART', 'Arsiparis Terampil', 0, '2026-09-22 08:00:00'),
(22, 'PLO', 'Penata Layanan Operasional', 0, '2026-09-22 08:00:00'),
(23, 'APP', 'Analis Perkara Peradilan', 0, '2026-09-22 08:00:00'),
(24, 'PGP', 'Pengelola Perkara', 0, '2026-09-22 08:00:00'),
(25, 'PDI', 'Pengelola Data dan Informasi', 0, '2026-09-22 08:00:00'),
(26, 'TSP', 'Teknisi Sarana dan Prasarana', 0, '2026-09-22 08:00:00'),
(27, 'PAK', 'Pengadministrasi Perkantoran', 0, '2026-09-22 08:00:00'),
(28, 'HAP', 'Hakim Ad Hoc PHI', 0, '2026-09-22 08:00:00'),
(29, 'HAT', 'Hakim Ad Hoc Tipikor', 0, '2026-09-22 08:00:00'),
(30, 'OLO-PDT', 'Operator Layanan Operasional-Perdata', 0, '2026-09-22 08:00:00'),
(31, 'OLO-PID', 'Operator Layanan Operasional-Pidana', 0, '2026-09-22 08:00:00'),
(32, 'OLO-TPK', 'Operator Layanan Operasional-Tipikor', 0, '2026-09-22 08:00:00'),
(33, 'OLO-UK', 'Operator Layanan Operasional-Umum Keu', 0, '2026-09-22 08:00:00'),
(34, 'OLO-HKM', 'Operator Layanan Operasional-Hukum', 0, '2026-09-22 08:00:00'),
(35, 'OLO-HAM', 'Operator Layanan Operasional-HAM', 0, '2026-09-22 08:00:00'),
(36, 'OLO-PTIP', 'Operator Layanan Operasional-PTIP', 0, '2026-09-22 08:00:00'),
(37, 'OLO-PHI', 'Operator Layanan Operasional-PHI', 0, '2026-09-22 08:00:00'),
(38, 'PAK-UK', 'Pengadministrasi Perkantoran-Umum Keu', 0, '2026-09-22 08:00:00'),
(39, 'PAK-PTIP', 'Pengadministrasi Perkantoran-PTIP', 0, '2026-09-22 08:00:00'),
(40, 'PAK-KOT', 'Pengadministrasi Perkantoran-Kepeg Ortala', 0, '2026-09-22 08:00:00'),
(41, 'PUO-UK', 'Pengelola Umum Operasional-Umum Keu', 0, '2026-09-22 08:00:00'),
(42, 'PUO-KOT', 'Pengelola Umum Operasional-Kepeg Ortala', 0, '2026-09-22 08:00:00'),
(43, 'PUO-HKM', 'Pengelola Umum Operasional-Hukum', 0, '2026-09-22 08:00:00'),
(44, 'PLO-UK', 'Penata Layanan Operasional-Umum Keu', 0, '2026-09-22 08:00:00'),
(45, 'PLO-PTIP', 'Penata Layanan Operasional-PTIP', 0, '2026-09-22 08:00:00'),
(46, 'PLO-PID', 'Penata Layanan Operasional-Pidana (Paruh Waktu)', 0, '2026-09-22 08:00:00'),
(47, 'PRB', 'Pramubhakti', 0, '2026-09-22 08:00:00'),
(48, 'KSB', 'Kepala Sub Bagian', 0, '2026-09-22 08:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jawaban_wawancara`
--

CREATE TABLE `jawaban_wawancara` (
  `id_jawaban` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `id_pertanyaan` int(10) UNSIGNED NOT NULL,
  `jawaban_teks` text DEFAULT NULL,
  `file_jawaban` varchar(255) DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `status_kompetensi` enum('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
  `skor` decimal(5,2) DEFAULT NULL COMMENT 'Skor 1-5',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kebutuhan_diklat`
--

CREATE TABLE `kebutuhan_diklat` (
  `id_kebutuhan` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `id_kesenjangan` int(10) UNSIGNED DEFAULT NULL,
  `id_diklat` int(10) UNSIGNED DEFAULT NULL,
  `metode_pengembangan` varchar(100) DEFAULT NULL,
  `prioritas` enum('Tinggi','Sedang','Rendah') NOT NULL DEFAULT 'Sedang',
  `status` enum('Diusulkan','Disetujui','Terlaksana','Ditolak') NOT NULL DEFAULT 'Diusulkan',
  `tahun_rencana` year(4) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kebutuhan_diklat`
--

INSERT INTO `kebutuhan_diklat` (`id_kebutuhan`, `id_pegawai`, `id_kesenjangan`, `id_diklat`, `metode_pengembangan`, `prioritas`, `status`, `tahun_rencana`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 58, NULL, NULL, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', '2026', 'Jabatan: Panitera Muda Perdata | Gap: Perlu peningkatan administrasi perkara dan ketelitian kerja | Diklat: Diklat Administrasi Perkara Perdata', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(2, 58, NULL, NULL, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', '2026', 'Jabatan: Panitera Muda Pidana | Gap: Perlu pemahaman aplikasi pidana | Diklat: Pelatihan SIPP dan e-Berpadu', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(3, 58, NULL, NULL, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', '2026', 'Jabatan: Panitera Muda Tipikor | Gap: Perlu peningkatan administrasi perkara dan ketelitian kerja | Diklat: Diklat Administrasi Perkara Tipikor', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(4, 58, NULL, NULL, 'Diklat teknis kepaniteraan', 'Sedang', 'Diusulkan', '2026', 'Jabatan: Panitera Muda PHI | Gap: Perlu peningkatan administrasi perkara PHI dan ketelitian kerja | Diklat: Pelatihan Teknis Perkara PHI', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(5, 58, NULL, NULL, 'Diklat teknis kepaniteraan', 'Sedang', 'Diusulkan', '2026', 'Jabatan: Panitera Muda Hukum | Gap: Perlu arsiparis perkara | Diklat: Pelatihan Dokumentasi dan Pelaporan', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(6, 67, NULL, NULL, 'Diklat + coaching', 'Tinggi', 'Diusulkan', '2026', 'Jabatan: Kasubbag Kepegawaian | Gap: Administrasi ASN, Penguasaan Aplikasi SIKEP, SI ASN, KOMDANAS, E KINERJA | Diklat: Diklat Administrasi Kepegawaian', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(7, 67, NULL, NULL, 'Bimtek Kepegawaian', 'Sedang', 'Diusulkan', '2026', 'Jabatan: Kasubbag PTIP | Gap: Monitoring Perencanaan, TI dan Pelaporan | Diklat: Pelatihan Monitoring Evaluasi dan TI', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(8, 67, NULL, NULL, 'Bimtek Kepegawaian', 'Tinggi', 'Diusulkan', '2026', 'Jabatan: Kasubbag Umum dan Keuangan | Gap: Perlu penambahan personil dan pelatihan untuk Aplikasi di Keuangan | Diklat: Diklat Pengelolaan BMN dan Keuangan', '2026-09-22 04:41:56', '2026-09-22 04:41:56'),
(9, 70, 10, NULL, 'In House Training', 'Tinggi', 'Diusulkan', '2026', 'Jabatan: Petugas PTSP | Gap: Perlu service excellent dan pengaduan publik | Diklat: Pelatihan Service Excellent, Pelatihan PTSP', '2026-09-22 04:41:56', '2026-09-22 06:22:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan_hakim`
--

CREATE TABLE `kegiatan_hakim` (
  `id_kegiatan` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `jenis_kegiatan` enum('Narasumber','Bimtek/Pelatihan','Pengajar') NOT NULL,
  `nama_kegiatan` varchar(200) NOT NULL,
  `penyelenggara` varchar(150) DEFAULT NULL,
  `peran_topik` varchar(200) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `file_bukti` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kompetensi`
--

CREATE TABLE `kompetensi` (
  `id_kompetensi` int(10) UNSIGNED NOT NULL,
  `kode_kompetensi` varchar(20) NOT NULL,
  `nama_kompetensi` varchar(150) NOT NULL,
  `jenis_kompetensi` enum('Manajerial','Teknis','Sosiokultural','Pemerintahan') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kuesioner`
--

CREATE TABLE `kuesioner` (
  `id_kuesioner` int(10) UNSIGNED NOT NULL,
  `id_user_pembuat` int(10) UNSIGNED NOT NULL,
  `id_jabatan_dinilai` int(10) UNSIGNED NOT NULL,
  `kompetensi` varchar(200) NOT NULL DEFAULT '',
  `judul_kuesioner` varchar(200) NOT NULL,
  `tahun_periode` year(4) NOT NULL,
  `status` enum('Draft','Aktif','Non-Aktif') NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kuesioner`
--

INSERT INTO `kuesioner` (`id_kuesioner`, `id_user_pembuat`, `id_jabatan_dinilai`, `kompetensi`, `judul_kuesioner`, `tahun_periode`, `status`, `created_at`) VALUES
(1, 1, 58, 'Administrasi Perkara/Jurusita Pengganti', 'Kuesioner Evaluasi Jeanne Pamela, S.Kom., M.T', '2026', 'Aktif', '2026-09-22 03:17:52'),
(2, 1, 67, 'Arsiparis Terampil', 'Kuesioner Evaluasi Muhammad Nur Firdaus S, Amd', '2026', 'Aktif', '2026-09-22 03:17:52'),
(3, 1, 70, 'Analis Perkara Peradilan', 'Kuesioner Evaluasi Okta Emilia Larasati, S.H', '2026', 'Aktif', '2026-09-22 03:17:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `id_jabatan` int(10) UNSIGNED NOT NULL,
  `status_aktif` enum('Aktif','Non-Aktif') NOT NULL DEFAULT 'Aktif',
  `dibuat_oleh` int(10) UNSIGNED DEFAULT NULL COMMENT 'users.id_user (role Admin) yang menginput data ini',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nip`, `nama_lengkap`, `id_jabatan`, `status_aktif`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES
(2, '196804141996031002', 'SYAFRIZAL, S.H.', 1, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(3, '197809112001122002', 'MELINDA ARITONANG, S.H.', 2, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(4, '196905311996031001', 'SUNARYANTO, SH.,MH', 3, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(5, '197501272000032003', 'NI LUH SUKMARINI, SH., MH', 4, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(6, '197204271993031003', 'KARSENA, S.H.,M.H.', 4, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(7, '196908101990031006', 'JAMUJI, SH.,MH', 4, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(8, '197510282000122002', 'FITRI RAMADHAN, SH', 4, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(9, '197701312001121002', 'SURYA LAKSEMANA, SH', 4, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(10, '197609172001122003', 'SETYANINGSIH, SH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(11, '197801122002121002', 'GABRIEL SIALLAGAN, SH.,MH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(12, '197505162002122001', 'PATYARINI MEININGSIH R, SH.,M.Hum', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(13, '197610092002121004', 'MUHAMMAD ISMAIL HAMID, SH.,MH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(14, '197709242002122003', 'SRI SULASTUTI, SH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(15, '197912282002122001', 'ERNI KUSUMAWATI, SH., MH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(16, '197703192002122003', 'SRI WIJAYANTI TANJUNG, SH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(17, '197701192002121004', 'REZA TYRAMA, SH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(18, '197807152003121002', 'PURNOMO WIBOWO, SH.,MH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(19, '197901272003121001', 'DJOKO WIRYONO BUDHI S, SH', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(20, '197303151992032001', 'JOHANA CAROLINA LEKBILA, S.IP.,SH', 6, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(21, '197807082006042001', 'DIAN UMAWATI,SH., MH', 10, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(22, '197207092006042002', 'VIRONIKA SRI YULIATI, S.Sos.,SH.,MH', 9, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(23, '198010092008051002', 'ANDANG CATUR PRASETYA, SH., MH', 8, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(24, '198204152011011005', 'DARU BUANA SEJATI, SH', 7, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(25, '196805141990032005', 'HENY SURYANI, SH', 11, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(26, '197801272002122003', 'THESIANA MAYA FITRIA A, SH.,MH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(27, '198310262008012008', 'OCTAVIA MARIANA WIJAYANTI, SH.,MH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(28, '196710201993032005', 'RR.DINAWATI, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(29, '196907141994032005', 'Rr. SRI WINASTUTI,SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(30, '196606021999032003', 'ANNA HENY W,SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(31, '196911151992032004', 'MARIA LUSIATI,SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(32, '196908051992031004', 'KUWAT WAHYU MURDANA,SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(33, '197001191992032002', 'YANI WIDIYANTI, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(34, '197006101992032002', 'SRI SUWANTI, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(35, '197509052001122001', 'NURI MAHAR KESTRI,SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(36, '196604091990032003', 'NUNUNG DIAH RST, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(37, '197111102006041001', 'ANTONIUS ANDI SUSANTO, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(38, '196911161993031002', 'YUDI SUHENDRO, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(39, '196605181988031001', 'SURYONO NUGROHO,SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(40, '197907092009042004', 'RULLIANA YUDAWATI, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(41, '197606152006042002', 'YUDHA AYU TIMORNIYATI, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(42, '197706072000122002', 'RR. WORO HAPSARI D,SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(43, '198508052009122005', 'RIKE SIMBALAGO, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(44, '198309172011011008', 'SEPTIAN ADI SASTRIA, S.H.', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(45, '198207062011012009', 'NAFISATUN ANA FITRIA UTAMI, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(46, '198711122006041001', 'FRANGKY ANTONI P, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(47, '197111061993031002', 'AGUS RIYANTO, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(48, '198212192006041002', 'RIMBANG KRISDIANTO, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(49, '198803252015032001', 'SHEILA POSITA, SH.,MH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(50, '199006132009042001', 'YUNITA NILA KRISNA, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(51, '198409262009041004', 'BARATA MUHARAMIN, SH', 12, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(52, '197306261994031003', 'HERI PRASETYA, SH', 13, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(53, '197508252006042003', 'LUSI RACHMAYANI,SE.SH', 13, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(54, '198007072008051001', 'ARLYO PERDANA PUTRA,SH', 13, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(55, '197305252006041004', 'NANANG SUPRIYADI, SE.,SH.,M.Kn', 13, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(56, '197210041993031005', 'SALASA AGUS EKOYADI, SH', 13, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(57, '198001012008052002', 'NURMAYA REZEKY AR, SH', 13, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(58, '198209222009042008', 'JEANNE PAMELA,S.Kom,MT', 14, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(59, '197001171990032001', 'WARSIYATI', 14, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(60, '197601011995101001', 'DOMINGOS DOUTEL', 14, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(61, '196812211990031002', 'MOHAMAD SAID IDUL FITRI', 14, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(62, '197308161994031001', 'TASIMAN, SH.,MH', 15, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(63, '198404102009042016', 'YENNY VIKKY EFFENDY,ST.SH.M.Eng', 16, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(64, '198103302006041004', 'EVENDI NUGROHO,ST', 48, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(65, '198607242011011005', 'KUNCORO SETYA R,SE.,MM', 19, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(66, '199102032019031005', 'NUGRAHA ABDILLAH, S.Kom', 20, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(67, '199509072020121004', 'MUHAMMAD NUR FIRDAUS S, A.Md', 21, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(68, '198208182010121002', 'HARIS HERMAWAN EFFENDI, SS.,MM', 22, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(69, '198510182015031001', 'ARDI WICAKSONO, ST', 18, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(70, '199510162020122006', 'OKTA EMILIA LARASATI, SH', 23, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(71, '199511142020122005', 'NADYA PRIMAASHA BRAHMANA, SH', 23, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(72, '198911262015032003', 'NOVITA DIASTUTI, S.Kom', 22, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(73, '199711132020121003', 'DWI NOVIANDARU, S. Tr. Kom', 22, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(74, '199603252020122003', 'NADYA MAULANI MELYANA, A.Md. A.P', 24, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(75, '199911232024052001', 'DINA TRI LESTARI, SH', 23, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(76, '200010122024052002', 'PUTRI AZZAHRA, SH', 23, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(77, '200007192024051002', 'RYAN ADE SAPUTRO, SH', 23, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(78, '197212161993031001', 'MOH. RUSDIANTO', 27, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(79, '197806192014081004', 'NINDYA YOSDALU PUTRA', 25, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(80, '199904292022032021', 'INDAH MELINDA, A.Md.A.B.', 25, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(81, '199412122022032011', 'TESA MONICA BR GULTOM, A.Md', 26, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(82, '199112192022032006', 'SUSI SUSANTI SINAGA, A.Md', 25, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(83, '200108022025062018', 'JESSICA IRENE NADEAK, SH', 24, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(84, '199310022025062003', 'FRANCISCA WESTRI INDASARI, SH', 23, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(85, '199703042025062014', 'ANIS HANIFAH, A.Md.Kom', 25, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(86, '1471110904700001', 'HERI PURNOMO, S.Si.,SH.,MH', 28, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(87, '3471085612690001', 'SITI UMI AKHIROKH, SH.,MH', 28, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(88, '3216072404760003', 'AJI, S.H.', 28, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(89, '7106096210740001', 'MAYA RIESKE J RUMAMBI, SH.,MH', 28, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(90, '3578080611690005', 'SOEBEKTI, S.H.', 29, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(91, '1271071003660004', 'ELIAS HAMONANGAN, SE.,SH.,MH', 29, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(92, '3401070601700001', 'WARSONO, SH.,MH', 29, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(93, '3322195308730001', 'ATUN BUDI ASTUTI, SH', 29, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(94, '3217022704690001', 'KUSMAT TIRTA SASMITA, SH', 29, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(95, '3313092802720002', 'YULIUS EKA SETIAWAN, SH.,MH', 29, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(96, '198107302025212017', 'DIAH SUKORINI,SH', 44, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(97, '198708282025211042', 'FAHMI HIDAYAT, SH', 45, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(98, '198412272025211029', 'DONNY SURIPTO', 30, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(99, '197312172025211016', 'TUNJUNG SULAKSANA P', 32, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(100, '198701202025211030', 'KEMAS INDARTO', 31, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(101, '197308302025211014', 'WIRID WINOTO', 38, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(102, '198508312025211035', 'ARIF PRIHENDARTO', 39, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(103, '196906272025212006', 'BARIYAH', 33, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(104, '197305292025211010', 'BUDI PRASETYO', 34, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(105, '197511092025211023', 'ANDIK SULISTYO', 40, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(106, '197608072025211028', 'DENY DWI SUSILO', 35, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(107, '198902142025211034', 'PEBRIANTO', 36, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(108, '198107172025211034', 'BAMBANG NUGROHO A MARTANTYO', 37, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(109, '198307232025211038', 'DWI RIYANTO', 33, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(110, '198109292025211037', 'SUDARMADI', 41, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(111, '197801252025211018', 'NGADIYO', 42, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(112, '198412312025211083', 'EDI SISWANTO', 43, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(113, '199204082025211067', 'ANGGA PERDANA PUTRA, SH', 46, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(114, '197304212005021003', 'HERI KUSMANTO, S.H.', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(115, '197109011991031002', 'NURHADI, S.H.', 9, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(116, '197603112001121001', 'MULYADI ARIBOWO, S.H., M.H.', 5, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(117, '198702182009042004', 'LUTHFININGRUM NUR AFIYAH, S.E., M.B.A.', 17, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-15 01:53:35'),
(118, '198602192009121003', 'DANU ARMAN, SH.,MH', 12, 'Aktif', 1, '2026-09-22 08:00:00', '2026-09-22 08:00:00'),
(119, 'PPNPN-119', 'NOVIA IKE DEVITA, S.Kom.', 47, 'Aktif', 1, '2026-09-22 08:00:00', '2026-09-22 08:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian_kompetensi`
--

CREATE TABLE `penilaian_kompetensi` (
  `id_penilaian` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `id_kompetensi` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED DEFAULT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `periode_penilaian` year(4) NOT NULL,
  `skor_akhir` decimal(5,2) DEFAULT NULL,
  `status_kompetensi` enum('Kompeten','Cukup','Tidak Kompeten') NOT NULL,
  `tanggal_penilaian` date NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pertanyaan_kuesioner`
--

CREATE TABLE `pertanyaan_kuesioner` (
  `id_pertanyaan` int(10) UNSIGNED NOT NULL,
  `id_kuesioner` int(10) UNSIGNED NOT NULL,
  `id_kompetensi` int(10) UNSIGNED DEFAULT NULL,
  `nomor_urut` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `teks_pertanyaan` text NOT NULL,
  `bobot` decimal(5,2) NOT NULL DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pertanyaan_kuesioner`
--

INSERT INTO `pertanyaan_kuesioner` (`id_pertanyaan`, `id_kuesioner`, `id_kompetensi`, `nomor_urut`, `teks_pertanyaan`, `bobot`, `created_at`) VALUES
(1, 3, NULL, 3, 'Bagaimana standar pelayanan informasi perkara?', 1.00, '2026-09-22 03:17:52'),
(2, 3, NULL, 2, 'Bagaimana menghadapi masyarakat yang menyampaikan keluhan?', 1.00, '2026-09-22 03:17:52'),
(3, 3, NULL, 1, 'Apa yang dimaksud pelayanan prima?', 1.00, '2026-09-22 03:17:52'),
(4, 2, NULL, 3, 'Bagaimana ublic penyimpanan arsip dinas?', 1.00, '2026-09-22 03:17:52'),
(5, 2, NULL, 2, 'Bagaimana prosedur disposisi surat masuk?', 1.00, '2026-09-22 03:17:52'),
(6, 2, NULL, 1, 'Bagaimana format surat dinas resmi?', 1.00, '2026-09-22 03:17:52'),
(7, 1, NULL, 3, 'Bagaimana tata cara pengarsipan berkas perkara?', 1.00, '2026-09-22 03:17:52'),
(8, 1, NULL, 2, 'Bagaimana prosedur registrasi perkara baru?', 1.00, '2026-09-22 03:17:52'),
(9, 1, NULL, 1, 'Apakah Saudara memahami SOP administrasi perkara?', 1.00, '2026-09-22 03:17:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sertifikat_pegawai`
--

CREATE TABLE `sertifikat_pegawai` (
  `id_sertifikat` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `nama_sertifikat` varchar(200) NOT NULL,
  `penyelenggara` varchar(150) DEFAULT NULL,
  `tanggal_terbit` date DEFAULT NULL,
  `tanggal_kadaluarsa` date DEFAULT NULL,
  `file_sertifikat` varchar(255) DEFAULT NULL COMMENT 'Path file upload',
  `dibuat_oleh` int(10) UNSIGNED DEFAULT NULL COMMENT 'users.id_user (role Admin)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `standar_kompetensi_jabatan`
--

CREATE TABLE `standar_kompetensi_jabatan` (
  `id_standar` int(10) UNSIGNED NOT NULL,
  `id_jabatan` int(10) UNSIGNED NOT NULL,
  `id_kompetensi` int(10) UNSIGNED NOT NULL,
  `level_minimal` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Skala 1-5',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED DEFAULT NULL COMMENT 'Wajib diisi jika role = Penilai; boleh NULL untuk Admin murni',
  `NIP` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Penilai') NOT NULL DEFAULT 'Penilai',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `id_pegawai`, `NIP`, `password`, `role`, `status_aktif`, `last_login`, `created_at`) VALUES
(1, NULL, '12323', '$2y$10$9NtGq3urEkM56R8LK/6Uq.JfEwCHCSWYwJwVc5IAMlyagwxXJ7nPa', 'Admin', 1, '2026-09-22 11:11:05', '2026-09-13 13:20:29'),
(3, 20, '197303151992032001', '$2y$10$DhDzfuWv46/2oBH3WvCfZeQQT0RAX.q.IcGKBwg.GL4scD5ZzWzz2', 'Penilai', 1, '2026-09-22 10:18:51', '2026-09-15 03:39:28'),
(4, 2, '196804141996031002', '$2y$10$Sqlz49AhHXPXvGi4fnj0X.wpyEIa9YDhWHcn0NAmkJXD2Dnb6hkF6', 'Penilai', 1, '2026-09-17 10:30:59', '2026-09-17 01:20:29'),
(5, 62, '197308161994031001', '$2y$10$HBPIMXcf3dMF2YWpWWn22.AP2H1iB/g6h/uNl6oNqBO95zF4/.Eai', 'Penilai', 1, '2026-09-22 11:10:24', '2026-09-22 03:53:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wawancara`
--

CREATE TABLE `wawancara` (
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `id_kuesioner` int(10) UNSIGNED DEFAULT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `tanggal_wawancara` date NOT NULL,
  `status` enum('Terjadwal','Berlangsung','Selesai','Batal') NOT NULL DEFAULT 'Terjadwal',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wawancara`
--

INSERT INTO `wawancara` (`id_wawancara`, `id_pegawai`, `id_kuesioner`, `id_user_penilai`, `tanggal_wawancara`, `status`, `catatan`, `created_at`) VALUES
(1, 79, NULL, 1, '2026-04-29', 'Terjadwal', 'Wawancara Kompetensi Petugas PTSP', '2026-09-22 03:05:26'),
(2, 80, NULL, 1, '2026-04-29', 'Terjadwal', 'Wawancara Kompetensi Petugas PTSP', '2026-09-22 03:05:26'),
(3, 78, NULL, 1, '2026-04-29', 'Terjadwal', 'Wawancara Kompetensi Staf Umum dan Keuangan', '2026-09-22 03:05:26'),
(4, 44, NULL, 1, '2026-04-29', 'Terjadwal', 'Wawancara Kompetensi Panitera Pengganti', '2026-09-22 03:05:26'),
(5, 33, NULL, 1, '2026-04-29', 'Terjadwal', 'Wawancara Kompetensi Panitera Pengganti', '2026-09-22 03:05:26'),
(6, 58, 1, 3, '2026-09-22', 'Terjadwal', NULL, '2026-09-22 03:41:48'),
(7, 70, 3, 3, '2026-09-22', 'Terjadwal', NULL, '2026-09-22 03:41:48'),
(9, 67, 2, 5, '2026-09-22', 'Terjadwal', NULL, '2026-09-22 03:57:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wewenang_penilaian`
--

CREATE TABLE `wewenang_penilaian` (
  `id_wewenang` int(10) UNSIGNED NOT NULL,
  `id_jabatan_penilai` int(10) UNSIGNED NOT NULL,
  `id_jabatan_dinilai` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wewenang_penilaian`
--

INSERT INTO `wewenang_penilaian` (`id_wewenang`, `id_jabatan_penilai`, `id_jabatan_dinilai`, `created_at`) VALUES
(1, 1, 3, '2026-09-22 08:00:00'),
(2, 1, 4, '2026-09-22 08:00:00'),
(3, 1, 5, '2026-09-22 08:00:00'),
(4, 1, 28, '2026-09-22 08:00:00'),
(5, 1, 29, '2026-09-22 08:00:00'),
(6, 2, 3, '2026-09-22 08:00:00'),
(7, 2, 4, '2026-09-22 08:00:00'),
(8, 2, 5, '2026-09-22 08:00:00'),
(9, 2, 28, '2026-09-22 08:00:00'),
(10, 2, 29, '2026-09-22 08:00:00'),
(11, 6, 7, '2026-09-22 08:00:00'),
(12, 6, 8, '2026-09-22 08:00:00'),
(13, 6, 9, '2026-09-22 08:00:00'),
(14, 6, 10, '2026-09-22 08:00:00'),
(15, 6, 11, '2026-09-22 08:00:00'),
(16, 6, 12, '2026-09-22 08:00:00'),
(17, 6, 13, '2026-09-22 08:00:00'),
(18, 6, 14, '2026-09-22 08:00:00'),
(19, 6, 23, '2026-09-22 08:00:00'),
(20, 6, 24, '2026-09-22 08:00:00'),
(21, 15, 16, '2026-09-22 08:00:00'),
(22, 15, 17, '2026-09-22 08:00:00'),
(23, 15, 18, '2026-09-22 08:00:00'),
(24, 15, 48, '2026-09-22 08:00:00'),
(25, 15, 19, '2026-09-22 08:00:00'),
(26, 15, 20, '2026-09-22 08:00:00'),
(27, 15, 21, '2026-09-22 08:00:00'),
(28, 15, 22, '2026-09-22 08:00:00'),
(29, 15, 25, '2026-09-22 08:00:00'),
(30, 15, 26, '2026-09-22 08:00:00'),
(31, 15, 27, '2026-09-22 08:00:00'),
(32, 15, 30, '2026-09-22 08:00:00'),
(33, 15, 31, '2026-09-22 08:00:00'),
(34, 15, 32, '2026-09-22 08:00:00'),
(35, 15, 33, '2026-09-22 08:00:00'),
(36, 15, 34, '2026-09-22 08:00:00'),
(37, 15, 35, '2026-09-22 08:00:00'),
(38, 15, 36, '2026-09-22 08:00:00'),
(39, 15, 37, '2026-09-22 08:00:00'),
(40, 15, 38, '2026-09-22 08:00:00'),
(41, 15, 39, '2026-09-22 08:00:00'),
(42, 15, 40, '2026-09-22 08:00:00'),
(43, 15, 41, '2026-09-22 08:00:00'),
(44, 15, 42, '2026-09-22 08:00:00'),
(45, 15, 43, '2026-09-22 08:00:00'),
(46, 15, 44, '2026-09-22 08:00:00'),
(47, 15, 45, '2026-09-22 08:00:00'),
(48, 15, 46, '2026-09-22 08:00:00'),
(49, 15, 47, '2026-09-22 08:00:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `analisis_kesenjangan_kompetensi`
--
ALTER TABLE `analisis_kesenjangan_kompetensi`
  ADD PRIMARY KEY (`id_kesenjangan`),
  ADD KEY `fk_kesenjangan_jabatan` (`id_jabatan`),
  ADD KEY `fk_kesenjangan_pegawai` (`id_pegawai`);

--
-- Indeks untuk tabel `analisis_tugas`
--
ALTER TABLE `analisis_tugas`
  ADD PRIMARY KEY (`id_analisis_tugas`),
  ADD KEY `fk_analisis_jabatan` (`id_jabatan`),
  ADD KEY `fk_analisis_pegawai` (`id_pegawai`);

--
-- Indeks untuk tabel `bukti_dokumentasi`
--
ALTER TABLE `bukti_dokumentasi`
  ADD PRIMARY KEY (`id_bukti`),
  ADD KEY `fk_bukti_penilaian` (`id_penilaian`),
  ADD KEY `fk_bukti_uploader` (`uploaded_by`);

--
-- Indeks untuk tabel `bukti_wawancara`
--
ALTER TABLE `bukti_wawancara`
  ADD PRIMARY KEY (`id_bukti`),
  ADD KEY `idx_bukti_wawancara` (`id_wawancara`);

--
-- Indeks untuk tabel `detail_wawancara`
--
ALTER TABLE `detail_wawancara`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_wawancara` (`id_wawancara`);

--
-- Indeks untuk tabel `diklat`
--
ALTER TABLE `diklat`
  ADD PRIMARY KEY (`id_diklat`),
  ADD KEY `fk_diklat_kompetensi` (`id_kompetensi`);

--
-- Indeks untuk tabel `hasil_kuesioner`
--
ALTER TABLE `hasil_kuesioner`
  ADD PRIMARY KEY (`id_hasil`),
  ADD UNIQUE KEY `id_wawancara` (`id_wawancara`);

--
-- Indeks untuk tabel `hasil_wawancara`
--
ALTER TABLE `hasil_wawancara`
  ADD PRIMARY KEY (`id_hasil`),
  ADD KEY `idx_hasil_wawancara` (`id_wawancara`);

--
-- Indeks untuk tabel `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id_jabatan`),
  ADD UNIQUE KEY `kode_jabatan` (`kode_jabatan`);

--
-- Indeks untuk tabel `jawaban_wawancara`
--
ALTER TABLE `jawaban_wawancara`
  ADD PRIMARY KEY (`id_jawaban`),
  ADD UNIQUE KEY `uq_wawancara_pertanyaan` (`id_wawancara`,`id_pertanyaan`),
  ADD KEY `fk_jawaban_pertanyaan` (`id_pertanyaan`);

--
-- Indeks untuk tabel `kebutuhan_diklat`
--
ALTER TABLE `kebutuhan_diklat`
  ADD PRIMARY KEY (`id_kebutuhan`),
  ADD KEY `fk_kebutuhan_pegawai` (`id_pegawai`),
  ADD KEY `fk_kebutuhan_gap` (`id_kesenjangan`),
  ADD KEY `fk_kebutuhan_diklat` (`id_diklat`);

--
-- Indeks untuk tabel `kegiatan_hakim`
--
ALTER TABLE `kegiatan_hakim`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `fk_kegiatan_pegawai` (`id_pegawai`);

--
-- Indeks untuk tabel `kompetensi`
--
ALTER TABLE `kompetensi`
  ADD PRIMARY KEY (`id_kompetensi`),
  ADD UNIQUE KEY `kode_kompetensi` (`kode_kompetensi`);

--
-- Indeks untuk tabel `kuesioner`
--
ALTER TABLE `kuesioner`
  ADD PRIMARY KEY (`id_kuesioner`),
  ADD KEY `fk_kuesioner_user` (`id_user_pembuat`),
  ADD KEY `fk_kuesioner_jabatan_dinilai` (`id_jabatan_dinilai`);

--
-- Indeks untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `fk_pegawai_jabatan` (`id_jabatan`),
  ADD KEY `fk_pegawai_dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `penilaian_kompetensi`
--
ALTER TABLE `penilaian_kompetensi`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD UNIQUE KEY `uq_pegawai_kompetensi_periode` (`id_pegawai`,`id_kompetensi`,`periode_penilaian`),
  ADD KEY `fk_penilaian_kompetensi` (`id_kompetensi`),
  ADD KEY `fk_penilaian_wawancara` (`id_wawancara`),
  ADD KEY `fk_penilaian_user_penilai` (`id_user_penilai`);

--
-- Indeks untuk tabel `pertanyaan_kuesioner`
--
ALTER TABLE `pertanyaan_kuesioner`
  ADD PRIMARY KEY (`id_pertanyaan`),
  ADD KEY `fk_pertanyaan_kuesioner` (`id_kuesioner`),
  ADD KEY `fk_pertanyaan_kompetensi` (`id_kompetensi`);

--
-- Indeks untuk tabel `sertifikat_pegawai`
--
ALTER TABLE `sertifikat_pegawai`
  ADD PRIMARY KEY (`id_sertifikat`),
  ADD KEY `fk_sertifikat_pegawai` (`id_pegawai`),
  ADD KEY `fk_sertifikat_dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `standar_kompetensi_jabatan`
--
ALTER TABLE `standar_kompetensi_jabatan`
  ADD PRIMARY KEY (`id_standar`),
  ADD UNIQUE KEY `uq_jabatan_kompetensi` (`id_jabatan`,`id_kompetensi`),
  ADD KEY `fk_standar_kompetensi` (`id_kompetensi`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`NIP`),
  ADD UNIQUE KEY `id_pegawai` (`id_pegawai`);

--
-- Indeks untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  ADD PRIMARY KEY (`id_wawancara`),
  ADD KEY `fk_wawancara_pegawai` (`id_pegawai`),
  ADD KEY `fk_wawancara_kuesioner` (`id_kuesioner`),
  ADD KEY `fk_wawancara_penilai` (`id_user_penilai`);

--
-- Indeks untuk tabel `wewenang_penilaian`
--
ALTER TABLE `wewenang_penilaian`
  ADD PRIMARY KEY (`id_wewenang`),
  ADD UNIQUE KEY `uq_wewenang` (`id_jabatan_penilai`,`id_jabatan_dinilai`),
  ADD KEY `fk_wewenang_dinilai` (`id_jabatan_dinilai`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `analisis_kesenjangan_kompetensi`
--
ALTER TABLE `analisis_kesenjangan_kompetensi`
  MODIFY `id_kesenjangan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `analisis_tugas`
--
ALTER TABLE `analisis_tugas`
  MODIFY `id_analisis_tugas` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `bukti_dokumentasi`
--
ALTER TABLE `bukti_dokumentasi`
  MODIFY `id_bukti` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bukti_wawancara`
--
ALTER TABLE `bukti_wawancara`
  MODIFY `id_bukti` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_wawancara`
--
ALTER TABLE `detail_wawancara`
  MODIFY `id_detail` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `diklat`
--
ALTER TABLE `diklat`
  MODIFY `id_diklat` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hasil_kuesioner`
--
ALTER TABLE `hasil_kuesioner`
  MODIFY `id_hasil` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hasil_wawancara`
--
ALTER TABLE `hasil_wawancara`
  MODIFY `id_hasil` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id_jabatan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `jawaban_wawancara`
--
ALTER TABLE `jawaban_wawancara`
  MODIFY `id_jawaban` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kebutuhan_diklat`
--
ALTER TABLE `kebutuhan_diklat`
  MODIFY `id_kebutuhan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `kegiatan_hakim`
--
ALTER TABLE `kegiatan_hakim`
  MODIFY `id_kegiatan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kompetensi`
--
ALTER TABLE `kompetensi`
  MODIFY `id_kompetensi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kuesioner`
--
ALTER TABLE `kuesioner`
  MODIFY `id_kuesioner` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT untuk tabel `penilaian_kompetensi`
--
ALTER TABLE `penilaian_kompetensi`
  MODIFY `id_penilaian` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pertanyaan_kuesioner`
--
ALTER TABLE `pertanyaan_kuesioner`
  MODIFY `id_pertanyaan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `sertifikat_pegawai`
--
ALTER TABLE `sertifikat_pegawai`
  MODIFY `id_sertifikat` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `standar_kompetensi_jabatan`
--
ALTER TABLE `standar_kompetensi_jabatan`
  MODIFY `id_standar` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  MODIFY `id_wawancara` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `wewenang_penilaian`
--
ALTER TABLE `wewenang_penilaian`
  MODIFY `id_wewenang` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `analisis_kesenjangan_kompetensi`
--
ALTER TABLE `analisis_kesenjangan_kompetensi`
  ADD CONSTRAINT `fk_kesenjangan_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kesenjangan_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `analisis_tugas`
--
ALTER TABLE `analisis_tugas`
  ADD CONSTRAINT `fk_analisis_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_analisis_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bukti_dokumentasi`
--
ALTER TABLE `bukti_dokumentasi`
  ADD CONSTRAINT `fk_bukti_penilaian` FOREIGN KEY (`id_penilaian`) REFERENCES `penilaian_kompetensi` (`id_penilaian`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bukti_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bukti_wawancara`
--
ALTER TABLE `bukti_wawancara`
  ADD CONSTRAINT `fk_bukti_wawancara` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_wawancara`
--
ALTER TABLE `detail_wawancara`
  ADD CONSTRAINT `fk_detail_wawancara` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `diklat`
--
ALTER TABLE `diklat`
  ADD CONSTRAINT `fk_diklat_kompetensi` FOREIGN KEY (`id_kompetensi`) REFERENCES `kompetensi` (`id_kompetensi`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hasil_wawancara`
--
ALTER TABLE `hasil_wawancara`
  ADD CONSTRAINT `fk_hasil_wawancara_wawancara` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jawaban_wawancara`
--
ALTER TABLE `jawaban_wawancara`
  ADD CONSTRAINT `fk_jawaban_pertanyaan` FOREIGN KEY (`id_pertanyaan`) REFERENCES `pertanyaan_kuesioner` (`id_pertanyaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_jawaban_wawancara` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kebutuhan_diklat`
--
ALTER TABLE `kebutuhan_diklat`
  ADD CONSTRAINT `fk_kebutuhan_diklat` FOREIGN KEY (`id_diklat`) REFERENCES `diklat` (`id_diklat`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kebutuhan_kesenjangan` FOREIGN KEY (`id_kesenjangan`) REFERENCES `analisis_kesenjangan_kompetensi` (`id_kesenjangan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kebutuhan_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kegiatan_hakim`
--
ALTER TABLE `kegiatan_hakim`
  ADD CONSTRAINT `fk_kegiatan_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kuesioner`
--
ALTER TABLE `kuesioner`
  ADD CONSTRAINT `fk_kuesioner_user` FOREIGN KEY (`id_user_pembuat`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pegawai`
--
ALTER TABLE `pegawai`
  ADD CONSTRAINT `fk_pegawai_dibuat_oleh` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pegawai_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penilaian_kompetensi`
--
ALTER TABLE `penilaian_kompetensi`
  ADD CONSTRAINT `fk_penilaian_kompetensi` FOREIGN KEY (`id_kompetensi`) REFERENCES `kompetensi` (`id_kompetensi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_penilaian_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_penilaian_user_penilai` FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_penilaian_wawancara` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pertanyaan_kuesioner`
--
ALTER TABLE `pertanyaan_kuesioner`
  ADD CONSTRAINT `fk_pertanyaan_kompetensi` FOREIGN KEY (`id_kompetensi`) REFERENCES `kompetensi` (`id_kompetensi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pertanyaan_kuesioner` FOREIGN KEY (`id_kuesioner`) REFERENCES `kuesioner` (`id_kuesioner`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sertifikat_pegawai`
--
ALTER TABLE `sertifikat_pegawai`
  ADD CONSTRAINT `fk_sertifikat_dibuat_oleh` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sertifikat_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `standar_kompetensi_jabatan`
--
ALTER TABLE `standar_kompetensi_jabatan`
  ADD CONSTRAINT `fk_standar_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_standar_kompetensi` FOREIGN KEY (`id_kompetensi`) REFERENCES `kompetensi` (`id_kompetensi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `wawancara`
--
ALTER TABLE `wawancara`
  ADD CONSTRAINT `fk_wawancara_kuesioner` FOREIGN KEY (`id_kuesioner`) REFERENCES `kuesioner` (`id_kuesioner`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wawancara_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wawancara_penilai` FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `wewenang_penilaian`
--
ALTER TABLE `wewenang_penilaian`
  ADD CONSTRAINT `fk_wewenang_dinilai` FOREIGN KEY (`id_jabatan_dinilai`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wewenang_penilai` FOREIGN KEY (`id_jabatan_penilai`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
