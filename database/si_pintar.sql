-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Okt 2026 pada 09.20
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
(1, 1, NULL, 'Bertanggung jawab sepenuhnya terhadap jalannya peradilan, baik ke dalam maupun ke luar, sesuai ketentuan undang-undang, serta memimpin pelaksanaan tugas pokok dan fungsi Pengadilan Negeri Yogyakarta.', 'Bertanggung jawab sepenuhnya terhadap jalannya peradilan, baik ke dalam maupun ke luar, sesuai dengan ketentuan undang-undang.\r\nMemimpin dan bertanggung jawab atas pelaksanaan tugas pokok dan fungsi Pengadilan Negeri Yogyakarta berdasarkan undang-undang, kebijakan Mahkamah Agung dan pemerintah.\r\nMembuat kebijakan umum di bidang kepaniteraan dan kesekretariatan melalui surat keputusan/penetapan atau surat dinas/tugas, serta membagi tugas seluruh aparatur berdasarkan jabatan struktural atau fungsional.\r\nMembuat program kerja berkala bersama para hakim, panitera, sekretaris, dan pejabat kesekretariatan/kepaniteraan terkait anggaran DIPA dan kebutuhan kantor sesuai kebijakan Mahkamah Agung dan pemerintah.\r\nBertanggung jawab atas pembuatan dan realisasi rencana fisik penjabaran DIPA, serta mengadakan pengawasan dan evaluasi setiap triwulan.\r\nMelaksanakan pembinaan dan pengawasan serta menjadi penanggung jawab umum pelaksanaan anggaran dalam DIPA-005.03.2.099128/2024 dan DIPA-005.01.2.098057/2024.\r\nMelakukan kebijakan dan pengawasan terhadap keuangan negara dan semua barang kekayaan (inventaris) milik negara.\r\nMembina dan mengawasi hakim, panitera, sekretaris, dan seluruh pejabat kepaniteraan/kesekretariatan agar peradilan berjalan tertib, adil, dan berwibawa.\r\nMelakukan pengawasan melekat terhadap perilaku seluruh pegawai di dalam maupun di luar kantor, secara langsung atau berjenjang, sebagai catatan prestasi kerja dalam penilaian SKP atau kebijakan lainnya.\r\nMembuat laporan berkala tahunan atas pelaksanaan tugas kepada Ketua Pengadilan Tinggi Yogyakarta dan Ketua Mahkamah Agung c.q. Ketua Muda Urusan Lingkungan Peradilan Umum.\r\nMembuat penetapan majelis hakim yang menangani perkara, menunjuk hakim sebagai pencatat dalam perkara yang diajukan secara lisan, serta menunjuk hakim pengawas bidang di bawah koordinasi Ketua.\r\nMerencanakan dan melakukan rapat koordinasi berkala bersama Panitera dan Sekretaris, serta pertemuan insidentil secara umum maupun terbatas.\r\nMenerima dan menindaklanjuti hasil pengawasan hakim pengawas bidang.\r\nMenilai SKP hakim, Panitera, dan Sekretaris setiap akhir tahun.\r\nMengusulkan pengangkatan atau penambahan pegawai/jabatan tertentu ke Mahkamah Agung melalui Pengadilan Tinggi Yogyakarta.\r\nMelakukan penyumpahan dan pelantikan hakim, Panitera, Sekretaris, pejabat, atau pegawai negeri sipil di lingkungan Pengadilan Negeri Yogyakarta.\r\nMelakukan pembinaan dan pengawasan terhadap anggota IKAHI, PTWP, dan Dharma Yukti Karini Pengadilan Negeri Yogyakarta.\r\nMenetapkan panjar biaya perkara, biaya panggilan, biaya eksekusi, dan biaya proses peradilan lainnya.\r\nMenerima berkas perkara dari Panitera, meneliti, lalu menunjuk majelis hakim yang akan menyidangkan perkara dengan suatu penetapan.\r\nDalam keadaan mendesak/darurat, mengganti ketua majelis dengan hakim lain melalui suatu penetapan.\r\nMemimpin jalannya eksekusi.\r\nMemerintahkan jurusita untuk melakukan pemanggilan terhadap termohon, putusan berkekuatan hukum tetap, putusan provisi, putusan serta merta, dan pelaksanaan eksekusi lainnya.\r\nMemerintahkan jurusita untuk melakukan penangguhan eksekusi untuk jangka waktu tertentu apabila ada gugatan perlawanan oleh pihak ketiga.\r\nMenetapkan pelaksanaan lelang, tempat pelaksanaan lelang, dan/atau kantor lelang negara sebagai pelaksana lelang.\r\nMemeriksa pemegang kas keuangan perkara dan keuangan rutin (DIPA) secara berkala atau insidentil.\r\nMenandatangani izin kuasa khusus/insidentil, penutupan buku-buku register akhir tahun, serta laporan bulanan dan laporan tahunan bersama Panitera dan Sekretaris.\r\nMemberi izin untuk membawa keluar dari ruang kepaniteraan daftar, catatan, risalah, berita acara, atau berkas perkara.\r\nMemberikan penilaian atas prestasi dan SKP bagi hakim, Panitera, dan Sekretaris.\r\nMelakukan koordinasi dengan sesama instansi penegak hukum dan instansi lainnya di lingkungan Pemerintah Kota Yogyakarta, serta dapat menunjuk seorang hakim sebagai pejabat yang melaksanakan tugas kehumasan.\r\nMenunjuk hakim untuk membantu membuat gugatan/permohonan lisan bagi pihak yang buta huruf.\r\nMembuat uraian tugas bagi seluruh pejabat dan pelaksana.\r\nMelaksanakan tugas lainnya sebagai pimpinan Pengadilan Negeri Yogyakarta.\r\nMelaksanakan tugas lainnya yang diatur berdasarkan peraturan perundang-undangan atau perintah atasan.', NULL, 'Kepemimpinan dan manajemen satuan kerja, perencanaan program dan anggaran DIPA, pembinaan dan pengawasan, pengelolaan keuangan dan BMN, penetapan perkara dan eksekusi, penilaian kinerja (SKP), koordinasi antarinstansi, pemahaman peraturan perundang-undangan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(2, 2, NULL, 'Membantu Ketua dalam memimpin Pengadilan Negeri, perencanaan program kerja, pengawasan, dan koordinasi.', 'Membantu Ketua membuat perencanaan program kerja, baik jangka pendek maupun jangka panjang.\r\nMewakili Ketua apabila berhalangan.\r\nMelaksanakan delegasi dari Ketua.\r\nMelakukan pengawasan intern untuk mengamati apakah pelaksanaan tugas telah dikerjakan sesuai dengan rencana kerja dan ketentuan yang berlaku serta melaporkan hasil pengawasan kepada Ketua.\r\nMelakukan evaluasi atas hasil pengawasan dan memberikan penilaian.\r\nMelakukan koordinasi dan kerja sama antarinstansi pemerintah dan swasta.', NULL, 'Kepemimpinan, perencanaan kerja, pengawasan intern, evaluasi, koordinasi, pemahaman peraturan peradilan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(3, 3, NULL, 'Melakukan tugas kekuasaan kehakiman untuk memeriksa, memutus, dan menyelesaikan perkara pidana dan perdata di tingkat pertama.', 'Menerima, memeriksa, mengadili, dan menyelesaikan semua perkara yang diajukan kepadanya.\r\nMelakukan pengawasan yang ditugaskan Ketua untuk mengamati apakah pelaksanaan tugas dilaksanakan sesuai dengan ketentuan yang berlaku dan melaporkannya kepada pimpinan pengadilan.\r\nMelakukan pengawasan dan pengamatan (Kimwasmat) terhadap pelaksanaan putusan pidana di Lembaga Pemasyarakatan/Rumah Tahanan dan melaporkannya kepada Mahkamah Agung RI.\r\nMelaksanakan tugas kedinasan lain yang diperintahkan oleh atasan.', NULL, 'Hukum acara perdata dan pidana, pemeriksaan dan penilaian alat bukti, penyusunan putusan, pengawasan dan pengamatan (Kimwasmat), etika hakim', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(4, 4, NULL, 'Melakukan tugas kekuasaan kehakiman untuk memeriksa, memutus, dan menyelesaikan perkara pidana dan perdata di tingkat pertama.', 'Menerima, memeriksa, mengadili, dan menyelesaikan semua perkara yang diajukan kepadanya.\r\nMelakukan pengawasan yang ditugaskan Ketua untuk mengamati apakah pelaksanaan tugas dilaksanakan sesuai dengan ketentuan yang berlaku dan melaporkannya kepada pimpinan pengadilan.\r\nMelakukan pengawasan dan pengamatan (Kimwasmat) terhadap pelaksanaan putusan pidana di Lembaga Pemasyarakatan/Rumah Tahanan dan melaporkannya kepada Mahkamah Agung RI.\r\nMelaksanakan tugas kedinasan lain yang diperintahkan oleh atasan.', NULL, 'Hukum acara perdata dan pidana, pemeriksaan dan penilaian alat bukti, penyusunan putusan, pengawasan dan pengamatan (Kimwasmat), etika hakim', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(5, 5, NULL, 'Melakukan tugas kekuasaan kehakiman untuk memeriksa, memutus, dan menyelesaikan perkara pidana dan perdata di tingkat pertama.', 'Menerima, memeriksa, mengadili, dan menyelesaikan semua perkara yang diajukan kepadanya.\r\nMelakukan pengawasan yang ditugaskan Ketua untuk mengamati apakah pelaksanaan tugas dilaksanakan sesuai dengan ketentuan yang berlaku dan melaporkannya kepada pimpinan pengadilan.\r\nMelakukan pengawasan dan pengamatan (Kimwasmat) terhadap pelaksanaan putusan pidana di Lembaga Pemasyarakatan/Rumah Tahanan dan melaporkannya kepada Mahkamah Agung RI.\r\nMelaksanakan tugas kedinasan lain yang diperintahkan oleh atasan.', NULL, 'Hukum acara perdata dan pidana, pemeriksaan dan penilaian alat bukti, penyusunan putusan, pengawasan dan pengamatan (Kimwasmat), etika hakim', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(6, 6, NULL, 'Melaksanakan pemberian dukungan di bidang teknis dan administrasi perkara serta menyelesaikan surat-surat.', 'Melaksanakan koordinasi, pembinaan, dan pengawasan pelaksanaan tugas dalam pemberian dukungan di bidang teknis.\r\nMelaksanakan pengelolaan administrasi perkara perdata.\r\nMelaksanakan pengelolaan administrasi perkara pidana.\r\nMelaksanakan pengelolaan administrasi perkara khusus.\r\nMelaksanakan pengelolaan administrasi perkara, penyajian data perkara, dan transparansi perkara.\r\nMelaksanakan administrasi keuangan dalam program teknis dan keuangan perkara yang ditetapkan berdasarkan peraturan perundang-undangan, minutasi, evaluasi, dan administrasi kepaniteraan.\r\nMelaksanakan mediasi.\r\nMelaksanakan pembinaan teknis kepaniteraan dan kejurusitaan.\r\nMelaksanakan fungsi lain yang diberikan oleh Ketua Pengadilan Negeri.', NULL, 'Kepemimpinan teknis kepaniteraan, pengelolaan administrasi perkara perdata/pidana/khusus, keuangan perkara dan minutasi, mediasi, pembinaan teknis kepaniteraan dan kejurusitaan, koordinasi dan pengawasan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(7, 7, NULL, 'Melaksanakan administrasi perkara di bidang perdata.', 'Melaksanakan pemeriksaan dan penelaahan kelengkapan berkas perkara perdata.\r\nMelaksanakan registrasi perkara gugatan dan permohonan.\r\nMelaksanakan distribusi perkara yang telah diregister untuk diteruskan kepada ketua majelis hakim berdasarkan penetapan penunjukan majelis hakim dari Ketua Pengadilan.\r\nMelaksanakan penerimaan kembali berkas perkara yang sudah diputus dan diminutasi.\r\nMelaksanakan pemberitahuan isi putusan tingkat pertama kepada para pihak yang tidak hadir.\r\nMelaksanakan penyampaian pemberitahuan putusan tingkat banding, kasasi, dan peninjauan kembali kepada para pihak.\r\nMelaksanakan penerimaan dan pengiriman berkas perkara yang dimohonkan banding, kasasi, dan peninjauan kembali.\r\nMelaksanakan pengawasan terhadap pemberitahuan isi putusan upaya hukum kepada para pihak dan penyampaian relaas penyerahan isi putusan kepada Pengadilan Tinggi dan Mahkamah Agung.\r\nMelaksanakan penerimaan konsinyasi.\r\nMelaksanakan penerimaan permohonan eksekusi.\r\nMelaksanakan penyimpanan berkas perkara yang belum mempunyai kekuatan hukum tetap.\r\nMelaksanakan penyerahan berkas perkara yang sudah berkekuatan hukum tetap kepada Panitera Muda Hukum.\r\nMelaksanakan urusan tata usaha kepaniteraan.\r\nMelaksanakan fungsi lain yang diberikan oleh Panitera.', NULL, 'Administrasi perkara perdata, registrasi dan distribusi perkara, penanganan upaya hukum (banding, kasasi, PK), konsinyasi dan permohonan eksekusi, pengelolaan berkas perkara, tata usaha kepaniteraan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(8, 49, NULL, 'Menerima, membukukan, dan mengeluarkan uang panjar biaya perkara serta membuat laporan keuangan perkara.', 'Menerima dan membukukan panjar biaya perkara sebagaimana tercantum dalam SKUM pada buku jurnal keuangan perkara yang bersangkutan.\r\nMengeluarkan dan membukukan uang yang diperlukan bagi segala jenis penyelenggaraan peradilan sesuai dengan SK Biaya Panjar yang berlaku.\r\nMenyerahkan uang hak-hak kepaniteraan kepada Bendahara Penerima untuk disetorkan ke Kas Negara.\r\nMelakukan penginputan semua jenis transaksi keuangan terkait biaya panjar ke dalam SIPP.\r\nMembuat laporan bulanan keuangan perkara.\r\nMelaksanakan tugas sebagai Bendahara Proses.\r\nMelaksanakan tugas-tugas yang diberikan pimpinan yang berkaitan dengan kedinasan.', NULL, 'Pembukuan keuangan perkara, pengelolaan panjar biaya perkara, penguasaan SIPP, penyusunan laporan keuangan perkara, ketelitian dan akuntabilitas', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(9, 50, NULL, 'Melaksanakan tugas jurusita pengganti, input perkara pada SIPP, penerimaan berkas perkara, serta layanan Pojok E-Court dan PTSP.', 'Melaksanakan tugas sebagai Jurusita Pengganti.\r\nMenginput perkara permohonan, gugatan, gugatan sederhana, dan eksekusi pada SIPP.\r\nMemproses penerimaan berkas permohonan, gugatan, dan gugatan sederhana beserta kelengkapannya untuk diserahkan kepada Panitera Pengganti.\r\nMeregister buku induk keuangan dan keuangan eksekusi.\r\nMelaksanakan tugas Pojok E-Court.\r\nMelaksanakan tugas PTSP.\r\nMembantu tugas lainnya yang diberikan oleh Panitera Muda Perdata.\r\nMelaksanakan tugas-tugas yang diberikan pimpinan yang berkaitan dengan kedinasan.', NULL, 'Kejurusitaan dan pemanggilan, penginputan SIPP, penerimaan dan pemeriksaan kelengkapan berkas, e-Court, pelayanan PTSP, register keuangan perkara dan eksekusi', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(10, 51, NULL, 'Memproses administrasi upaya hukum perkara perdata (banding, kasasi, dan peninjauan kembali) serta menyusun laporan keperdataan.', 'Membuat akta-akta terkait upaya hukum, termasuk akta banding, akta kasasi, dan akta peninjauan kembali, serta menerima dan memeriksa memori banding, kontra memori, memori kasasi, kontra memori kasasi, dan berkas PK.\r\nMenginput seluruh data perkara upaya hukum (banding, kasasi, dan PK) ke dalam SIPP, E-Court, dan E-Court Banding.\r\nMemproses dan mengirim berkas perkara upaya hukum ke Pengadilan Tinggi untuk banding dan ke Mahkamah Agung untuk kasasi dan PK secara E-Court/E-Filing.\r\nMelaksanakan pemberitahuan isi putusan banding, putusan kasasi, dan putusan peninjauan kembali.\r\nMenyusun laporan-laporan keperdataan, termasuk laporan Hawasbid Kepaniteraan Perdata dan laporan rutin terkait perkara upaya hukum.\r\nMelaksanakan penginputan pada aplikasi-aplikasi perdata lainnya sesuai ketentuan pengadilan.\r\nMengirim berkas inaktif yang telah berkekuatan hukum tetap (BHT) ke Bagian Hukum sesuai prosedur arsip perkara.\r\nMelaksanakan layanan PTSP pada lingkup Kepaniteraan Perdata sesuai penugasan.\r\nMelaksanakan tugas kedinasan lain yang diberikan oleh pimpinan sesuai kebutuhan satuan kerja.', NULL, 'Pembuatan akta upaya hukum, penginputan SIPP dan e-Court, pengiriman berkas banding/kasasi/PK secara elektronik, pemberitahuan putusan, penyusunan laporan keperdataan, pengarsipan berkas perkara, pelayanan PTSP', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(11, 8, NULL, 'Melaksanakan administrasi perkara di bidang pidana.', 'Melaksanakan pemeriksaan dan penelaahan kelengkapan berkas perkara pidana.\r\nMelaksanakan registrasi perkara pidana.\r\nMelaksanakan penerimaan permohonan praperadilan dan pemberitahuan kepada termohon.\r\nMelaksanakan distribusi perkara yang telah diregister untuk diteruskan kepada ketua majelis hakim berdasarkan penetapan penunjukan majelis hakim dari Ketua Pengadilan.\r\nMelaksanakan penghitungan, penyiapan, dan pengiriman penetapan penahanan, perpanjangan penahanan, dan penangguhan penahanan.\r\nMelaksanakan penerimaan permohonan izin penggeledahan dan izin penyitaan dari penyidik.\r\nMelaksanakan penerimaan kembali berkas perkara yang sudah diputus dan diminutasi.\r\nMelaksanakan pemberitahuan isi putusan tingkat pertama kepada para pihak yang tidak hadir.\r\nMelaksanakan penyampaian pemberitahuan putusan tingkat banding, kasasi, dan peninjauan kembali kepada para pihak.\r\nMelaksanakan penerimaan dan pengiriman berkas perkara yang dimohonkan banding, kasasi, dan peninjauan kembali.\r\nMelaksanakan pengawasan terhadap pemberitahuan isi putusan upaya hukum kepada para pihak dan penyampaian relaas penyerahan isi putusan kepada Pengadilan Tinggi dan Mahkamah Agung.\r\nMelaksanakan pemberitahuan isi putusan upaya hukum kepada Jaksa Penuntut Umum dan terdakwa.\r\nMelaksanakan penerimaan permohonan eksekusi.\r\nMelaksanakan penyimpanan berkas perkara yang belum mempunyai kekuatan hukum tetap.\r\nMelaksanakan penyerahan berkas perkara yang sudah berkekuatan hukum tetap kepada Panitera Muda Hukum.\r\nMelaksanakan urusan tata usaha kepaniteraan.\r\nMelaksanakan fungsi lain yang diberikan oleh Panitera.', NULL, 'Administrasi perkara pidana, registrasi dan distribusi perkara, penetapan penahanan, izin penggeledahan dan penyitaan, praperadilan, penanganan upaya hukum, tata usaha kepaniteraan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(12, 52, NULL, 'Melaksanakan tugas jurusita pengganti, memproses permohonan melalui E-Berpadu, dan mengirimkan salinan serta petikan putusan.', 'Menjalankan tugas-tugas Jurusita Pengganti.\r\nMemproses permohonan sita, geledah, diversi, dan tahanan melalui E-Berpadu.\r\nMengirimkan salinan putusan dan petikan putusan kepada terdakwa/penasihat hukum dan penuntut umum.\r\nMembantu Panitera Muda Pidana dalam melaksanakan tugas-tugas lainnya.\r\nMelaksanakan tugas-tugas yang diberikan pimpinan berkaitan dengan dinas.', NULL, 'Kejurusitaan, penguasaan aplikasi E-Berpadu, penanganan permohonan sita/geledah/diversi/tahanan, pengiriman salinan dan petikan putusan, pelayanan prima', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(13, 53, NULL, 'Melayani PTSP serta menerima, mendaftarkan, dan menginput perkara pidana dari E-Berpadu ke SIPP.', 'Bertugas sebagai petugas Pelayanan Terpadu Satu Pintu.\r\nMenerima berkas perkara pidana biasa, pidana anak, pidana singkat, pidana cepat, lalu lintas, dan praperadilan.\r\nMelakukan pendaftaran perkara pidana biasa, pidana anak, pidana singkat, pidana cepat, lalu lintas, dan praperadilan.\r\nMenginput data perkara pidana biasa, pidana anak, pidana singkat, pidana cepat, lalu lintas, dan praperadilan dari E-Berpadu ke SIPP.\r\nMemproses/memvalidasi surat izin besuk melalui E-Berpadu.\r\nMembantu Panitera Muda Pidana dalam melaksanakan tugas-tugas lainnya.\r\nMelaksanakan tugas yang diberikan pimpinan berkaitan dengan dinas.', NULL, 'Pelayanan PTSP, penerimaan dan pendaftaran perkara pidana, input data perkara E-Berpadu ke SIPP, validasi izin besuk, administrasi perkara pidana', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(14, 54, NULL, 'Memproses administrasi upaya hukum banding dan peninjauan kembali perkara pidana melalui E-Berpadu dan SIPP.', 'Bertugas sebagai petugas Pelayanan Terpadu Satu Pintu.\r\nMenerima permohonan banding dan peninjauan kembali dari terdakwa/keluarganya, penuntut umum, dan penasihat hukum melalui E-Berpadu.\r\nMembuat akta permohonan banding dan peninjauan kembali.\r\nMembuat surat laporan pemberitahuan banding dan peninjauan kembali ke Pengadilan Tinggi/Mahkamah Agung Republik Indonesia.\r\nMenerima dan membuatkan tanda terima memori/kontra memori banding.\r\nMelengkapi berkas perkara Bundel A dan Bundel B di E-Berpadu dan SIPP yang akan dikirim ke Pengadilan Tinggi/Mahkamah Agung Republik Indonesia.\r\nMengirim surat tembusan pengiriman banding dan peninjauan kembali kepada para pihak melalui Bagian Umum.\r\nMeregister perkara banding dan peninjauan kembali ke dalam buku register/buku bantu perkara.\r\nMenyerahkan berkas perkara banding dan peninjauan kembali yang telah berkekuatan hukum tetap kepada Kepaniteraan Hukum serta mencatatnya ke dalam SIPP.\r\nMembantu Panitera Muda Pidana dalam melaksanakan tugas-tugas lainnya.\r\nMelaksanakan tugas yang diberikan pimpinan berkaitan dengan dinas.', NULL, 'Pelayanan PTSP, penerimaan permohonan banding dan PK melalui E-Berpadu, pembuatan akta dan surat laporan upaya hukum, pengelolaan Bundel A dan B, registrasi perkara upaya hukum, penguasaan SIPP', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(15, 10, NULL, 'Melaksanakan administrasi perkara di bidang perkara khusus penyelesaian perselisihan hubungan industrial.', 'Membantu hakim dalam persidangan dengan mengikuti dan mencatat jalannya persidangan.\r\nMemonitor pendaftaran perkara gugatan secara elektronik.\r\nMemonitor panjar biaya gugatan yang diajukan.\r\nMemonitor register perkara PHI secara elektronik.\r\nMemeriksa berkas perkara yang diajukan upaya hukum kasasi.\r\nMenerima dan memeriksa berkas perkara yang telah diminutasi.\r\nMemonitor penyerahan berkas perkara inaktif yang sudah berkekuatan hukum tetap (BHT) ke Kepaniteraan Hukum.\r\nMemeriksa permohonan eksekusi yang diajukan oleh pemohon.\r\nMembuat resume atas permohonan eksekusi.\r\nMemonitor surat masuk dan surat kembali, serta mendistribusikannya.\r\nMembuat surat balasan atas surat masuk.', NULL, 'Administrasi perkara PHI, pendaftaran dan monitoring perkara secara elektronik, pemeriksaan berkas kasasi dan minutasi, pengelolaan berkas inaktif, penanganan permohonan eksekusi, pembuatan resume dan surat', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(16, 55, NULL, 'Mendaftarkan perkara PHI melalui E-Court, memproses berkas perkara, serta menyusun laporan pengawasan bidang dan TLHP.', 'Mendaftarkan perkara perdata khusus/PHI melalui E-Court.\r\nMenyiapkan dan memproses berkas pendaftaran perkara untuk didistribusikan ke Panitera Pengganti.\r\nMemeriksa checklist berkas perkara yang diserahkan Panitera Pengganti sebelum diarsipkan di Kepaniteraan Hukum.\r\nMembuat berita acara penyerahan berkas inaktif ke Kepaniteraan Hukum.\r\nMembuat laporan pengawasan bidang Kepaniteraan PHI.\r\nMembuat laporan TLHP Kepaniteraan PHI.\r\nMelaksanakan tugas sebagai petugas PTSP.\r\nMelaksanakan tugas-tugas kedinasan yang diberikan oleh atasan.', NULL, 'Pendaftaran perkara melalui e-Court, pemeriksaan kelengkapan berkas perkara, pembuatan berita acara penyerahan berkas inaktif, penyusunan laporan pengawasan bidang dan TLHP, pelayanan PTSP', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(17, 56, NULL, 'Mendaftarkan upaya hukum kasasi perkara PHI secara elektronik, memproses akta Perjanjian Bersama, dan menyusun monev serta laporan Kepaniteraan PHI.', 'Mendaftarkan upaya hukum kasasi perkara PHI secara elektronik.\r\nMenyiapkan dan memproses berkas perkara upaya hukum untuk dikirim secara elektronik.\r\nMemproses permohonan pendaftaran akta Perjanjian Bersama (Bipartit/Mediasi).\r\nMencatat pendaftaran Perjanjian Bersama (Bipartit/Mediasi) ke buku register.\r\nMembuat monev dan laporan yang ada di Kepaniteraan PHI.\r\nMelaksanakan tugas sebagai petugas PTSP.\r\nMembantu tugas lainnya yang dilaksanakan oleh Panitera Muda PHI.\r\nMelaksanakan tugas-tugas kedinasan yang diberikan oleh atasan.', NULL, 'Pendaftaran kasasi perkara PHI secara elektronik, pemrosesan berkas upaya hukum, pendaftaran akta Perjanjian Bersama, penyusunan monev dan laporan, pelayanan PTSP', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(18, 57, NULL, 'Mendaftarkan upaya hukum kasasi perkara PHI secara elektronik, memproses akta Perjanjian Bersama, dan menyusun monev serta laporan Kepaniteraan PHI.', 'Mendaftarkan upaya hukum kasasi perkara PHI secara elektronik.\r\nMenyiapkan dan memproses berkas perkara upaya hukum untuk dikirim secara elektronik.\r\nMemproses permohonan pendaftaran akta Perjanjian Bersama (Bipartit/Mediasi).\r\nMencatat pendaftaran Perjanjian Bersama (Bipartit/Mediasi) ke buku register.\r\nMembuat monev dan laporan yang ada di Kepaniteraan PHI.\r\nMelaksanakan tugas sebagai petugas PTSP.\r\nMembantu tugas lainnya yang dilaksanakan oleh Panitera Muda PHI.\r\nMelaksanakan tugas-tugas kedinasan yang diberikan oleh atasan.', NULL, 'Pendaftaran kasasi perkara PHI secara elektronik, pemrosesan berkas upaya hukum, pendaftaran akta Perjanjian Bersama, penyusunan monev dan laporan, pelayanan PTSP', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(19, 11, NULL, 'Melaksanakan administrasi perkara di bidang perkara khusus tindak pidana korupsi.', 'Mengoordinasikan pelayanan PTSP.\r\nMemonitor dan mengevaluasi pelaksanaan administrasi pada Kepaniteraan TIPIKOR.\r\nMemonitor dan mengevaluasi pelimpahan perkara TIPIKOR melalui E-Berpadu.\r\nMemonitor dan mengevaluasi pemrosesan izin besuk, izin penyitaan, izin penggeledahan, dan penahanan melalui E-Berpadu.\r\nMemonitor dan mengevaluasi pemrosesan upaya hukum perkara TIPIKOR secara elektronik.', NULL, 'Administrasi perkara Tipikor, koordinasi pelayanan PTSP, monitoring dan evaluasi pelimpahan perkara melalui E-Berpadu, pemrosesan izin besuk/penyitaan/penggeledahan/penahanan, penanganan upaya hukum elektronik', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(20, 58, NULL, 'Melayani PTSP Tipikor, memproses izin penyitaan dan penggeledahan serta upaya hukum secara elektronik, dan menyusun monev dan laporan.', 'Melaksanakan pelayanan PTSP TIPIKOR.\r\nMemproses penyelesaian permohonan izin/persetujuan penyitaan dan izin/persetujuan penggeledahan melalui E-Berpadu.\r\nMemproses permohonan upaya hukum secara elektronik.\r\nMembuat monev dan laporan Kepaniteraan TIPIKOR.\r\nMelaksanakan tugas-tugas kedinasan yang diberikan oleh atasan.', NULL, 'Pelayanan PTSP, pemrosesan izin penyitaan dan penggeledahan melalui E-Berpadu, pemrosesan upaya hukum elektronik, penyusunan monev dan laporan Kepaniteraan Tipikor', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(21, 59, NULL, 'Melayani PTSP Tipikor, memproses izin besuk dan berkas perkara inaktif, serta menyusun laporan pengawasan bidang dan TLHP.', 'Melaksanakan pelayanan PTSP TIPIKOR.\r\nMemproses permohonan izin/persetujuan besuk melalui E-Berpadu.\r\nMemproses berkas perkara inaktif ke Kepaniteraan Hukum.\r\nMembuat laporan pengawasan bidang dan TLHP Kepaniteraan TIPIKOR.\r\nMelaksanakan tugas-tugas kedinasan yang diberikan oleh atasan.', NULL, 'Pelayanan PTSP, pemrosesan izin besuk melalui E-Berpadu, pengelolaan berkas inaktif ke Kepaniteraan Hukum, penyusunan laporan pengawasan bidang dan TLHP', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(22, 9, NULL, 'Melaksanakan pengumpulan, pengolahan, dan penyajian data perkara, kehumasan, penataan arsip perkara, serta pelaporan.', 'Melaksanakan pengumpulan, pengelolaan, dan penyajian data perkara.\r\nMelaksanakan penyajian statistik perkara.\r\nMelaksanakan penyusunan dan pengiriman pelaporan perkara.\r\nMelaksanakan penataan, penyimpanan, dan pemeliharaan arsip perkara.\r\nMelaksanakan kerja sama dengan arsip daerah untuk penitipan berkas perkara.\r\nMelaksanakan penyiapan, pengelolaan, dan penyajian bahan-bahan yang berkaitan dengan transparansi perkara.\r\nMelaksanakan penghimpunan pengaduan dari masyarakat.\r\nMelaksanakan fungsi lain yang diberikan oleh Panitera.', NULL, 'Pengumpulan, pengolahan, dan penyajian data perkara, statistik perkara, pelaporan perkara, kearsipan perkara, transparansi perkara, penanganan pengaduan masyarakat', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(23, 60, NULL, 'Melayani PTSP dan layanan hukum, meregister berkas BHT, serta menyusun laporan monev dan statistik layanan hukum.', 'Melaksanakan tugas sebagai petugas PTSP Kepaniteraan Muda Hukum.\r\nMemberikan pelayanan Eraterang Badilum.\r\nMelaksanakan pelayanan akta di bawah tangan (waarmerking).\r\nMemproses permintaan surat keterangan inkracht.\r\nMelayani pendaftaran surat kuasa khusus.\r\nMelakukan pembuatan permohonan surat kuasa insidentil.\r\nMeregister berkas perkara pidana, perdata, Tipikor, dan PHI yang telah BHT ke dalam buku register.\r\nMelakukan pencatatan peminjaman berkas perkara/arsip.\r\nMembuat dan mengarsipkan jawaban surat masuk.\r\nMenyusun laporan monitoring dan evaluasi (monev) berikut: Monev SPKP dan SPAK, Monev Eraterang, Monev SIPP, Monev PTSP, Monev Uraian Tugas, dan Monev Posbakum.\r\nMembuat statistik layanan hukum.\r\nMembuat laporan Hawasbid dan TLHP Kepaniteraan Hukum.\r\nMembuat laporan ke Pengadilan Tinggi setiap bulan.\r\nMembuat laporan survei harian terkait pelayanan.\r\nMembuat notulen rapat berjenjang kepaniteraan.\r\nMelaksanakan perintah atasan sehubungan dengan kedinasan.', NULL, 'Pelayanan PTSP, pelayanan Eraterang, waarmerking, surat keterangan inkracht, pendaftaran surat kuasa, registrasi berkas BHT, pencatatan peminjaman arsip, penyusunan laporan monev dan statistik layanan hukum, pembuatan notulen', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(24, 61, NULL, 'Menangani pengaduan dan operator SIWAS, menyusun laporan berkala, serta melayani waarmerking, PTSP, dan penataan arsip.', 'Menangani pengaduan dan menjadi operator SIWAS.\r\nMenangani pengaduan pada meja pengaduan.\r\nMembuat laporan bulanan dan laporan tahunan.\r\nMembuat laporan triwulan SPKP dan SPAK.\r\nMelaksanakan pelayanan akta di bawah tangan (waarmerking).\r\nMelaksanakan tugas PTSP.\r\nMelaksanakan pendaftaran surat kuasa khusus.\r\nMenjadi operator layanan elektronik.\r\nMenata arsip dan menginput arsip ke dalam SIPP.\r\nMembuat laporan semester berkaitan dengan gratifikasi.\r\nMelaksanakan perintah atasan sehubungan dengan kedinasan.', NULL, 'Penanganan pengaduan dan operator SIWAS, penyusunan laporan bulanan/triwulan/semester/tahunan, pelayanan waarmerking, pelayanan PTSP, pendaftaran surat kuasa khusus, penataan arsip dan penginputan SIPP, laporan gratifikasi', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(25, 13, NULL, 'Melaksanakan semua perintah Ketua/Hakim Ketua Majelis untuk menyampaikan pengumuman, teguran, protes, dan pemberitahuan putusan pengadilan menurut cara yang ditentukan undang-undang.', 'Membuat relaas panggilan sidang.\r\nMembuat relaas pemberitahuan putusan Pengadilan Negeri, Pengadilan Tinggi, dan Mahkamah Agung.\r\nMembuat relaas pemberitahuan yang berkaitan dengan banding, kasasi, PK, dan eksekusi.\r\nMembuat berita acara penyitaan yang salinannya diserahkan kepada pihak-pihak yang berkepentingan.\r\nMembuat penetapan-penetapan dan berita acara yang berkaitan dengan pelaksanaan eksekusi.', NULL, 'Kejurusitaan, pembuatan relaas panggilan dan pemberitahuan, pembuatan berita acara penyitaan, pelaksanaan eksekusi, pemahaman hukum acara', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(26, 12, NULL, 'Membantu hakim dalam persidangan perkara perdata maupun pidana serta melaporkan kegiatan persidangan.', 'Membantu hakim dalam persidangan dan melaporkan kegiatan persidangan tersebut kepada bagian pidana (Panmud Pidana).\r\nMembantu hakim dalam membuat penetapan hari sidang, berita acara persidangan, serta penetapan terdakwa tetap ditahan, dikeluarkan dari tahanan, atau diubah jenis tahanannya.\r\nMelaporkan barang bukti.\r\nMelaporkan kepada Panitera Muda Pidana maupun Panitera Muda Perdata untuk mencatat perkara yang sudah putus beserta amar putusannya.\r\nMenyerahkan berkas yang telah diminutasi.', NULL, 'Pendampingan hakim dalam persidangan, pembuatan berita acara sidang dan penetapan, pelaporan barang bukti, pencatatan putusan, minutasi berkas perkara, pemahaman hukum acara perdata dan pidana', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(27, 15, NULL, 'Melaksanakan pemberian dukungan di bidang administrasi, organisasi, keuangan, sumber daya manusia, serta sarana dan prasarana di lingkungan Pengadilan Negeri Kelas IA.', 'Menyiapkan bahan pelaksanaan urusan perencanaan program dan anggaran.\r\nMelaksanakan urusan kepegawaian.\r\nMelaksanakan urusan keuangan.\r\nMenyiapkan bahan pelaksanaan penataan organisasi dan tata laksana.\r\nMelaksanakan pengelolaan teknologi informasi dan statistik.\r\nMelaksanakan urusan surat menyurat, arsip, perlengkapan, rumah tangga, keamanan, keprotokolan, hubungan masyarakat, dan perpustakaan.\r\nMenyiapkan bahan pelaksanaan pemantauan, evaluasi, dokumentasi, serta pelaporan di lingkungan kesekretariatan Pengadilan Negeri Kelas IA.', NULL, 'Kepemimpinan, perencanaan program dan anggaran, manajemen kepegawaian dan keuangan, penataan organisasi dan tata laksana, pengelolaan teknologi informasi, persuratan, kearsipan, perlengkapan, keprotokolan, kehumasan, pemantauan dan pelaporan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(28, 17, NULL, 'Melaksanakan penyiapan bahan pelaksanaan program dan anggaran, pengelolaan teknologi informasi dan statistik, serta pelaksanaan pemantauan, evaluasi, dokumentasi, dan pelaporan.', 'Melaksanakan penyiapan bahan pelaksanaan program dan anggaran.\r\nMelaksanakan penyusunan perencanaan program dan anggaran/kegiatan/RKAKL serta penyusunan TOR, RAB, bahan, dan data dukung pengajuan anggaran.\r\nMelaksanakan revisi DIPA dan DIPA Halaman III berupa perubahan/pergeseran rincian anggaran yang telah ditetapkan berdasarkan APBN/DIPA.\r\nMelaksanakan revisi POK/komponen dalam satu output yang disesuaikan dengan rencana kegiatan dan dana yang tersedia.\r\nMelaksanakan perawatan dan pemeliharaan jaringan agar kualitas dan kinerja perangkat jaringan stabil dan tidak terjadi kerusakan pada komponen di dalamnya.\r\nMelaksanakan perawatan dan pengelolaan CCTV, yaitu menjaga sarana prasarana, sistem, aplikasi jaringan, dan database CCTV dalam kondisi baik dan berfungsi dengan baik.\r\nMemonitor dan mengevaluasi terlaksananya keterbukaan informasi publik (KIP) dengan menjaga keamanan website beserta aplikasi yang terintegrasi, serta publikasi informasi pada website dan media sosial.\r\nMelaksanakan pengelolaan server, yaitu menjaga sarana prasarana, sistem, aplikasi, jaringan, dan database server dalam kondisi baik dan berfungsi dengan baik.\r\nMemonitor dan mengevaluasi terlaksananya koordinasi dengan Bagian Umum terkait pengelolaan jaringan listrik dan genset yang mendukung perangkat/sarana prasarana TI.\r\nMemonitor dan mengevaluasi terlaksananya pengumpulan data statistik yang akurat dan benar.\r\nMemonitor dan mengevaluasi terkoordinirnya pengelolaan dokumen surat keputusan dalam aplikasi JDIH.\r\nMelaksanakan perawatan dan pengelolaan sistem TI, yaitu menjaga sarana prasarana, sistem aplikasi, jaringan, dan database dalam kondisi dan fungsi baik, termasuk sinkronisasi SIPP (Web, PT, MA, E-Court, Direktori Putusan, SPPT-TI), media sosial, layanan PTSP, dan sidang online.\r\nMelaksanakan penyusunan dan pengiriman laporan Badilum selain perkara secara sesuai, benar, dan tepat waktu.\r\nMemonitor dan mengevaluasi terlaksananya penyusunan dan pengiriman laporan Monev SMART secara sesuai, benar, dan tepat waktu.\r\nMemonitor dan mengevaluasi terlaksananya penyusunan laporan kegiatan bulanan secara sesuai, benar, dan tepat waktu.\r\nMemonitor dan mengevaluasi terlaksananya penyusunan dan pengiriman data dan dokumen SAKIP pada Komdanas secara sesuai, benar, dan tepat waktu.\r\nMelaksanakan penyusunan dan pengiriman laporan tahunan/kegiatan secara sesuai, benar, dan tepat waktu.\r\nMelaksanakan penyusunan dokumen evaluasi kinerja pada aplikasi SEMAR secara sesuai, benar, dan tepat waktu.\r\nMenyelesaikan tindak lanjut atas hasil monitoring dan evaluasi/assessment surveilans AMPUH Badilum.\r\nMenyelesaikan tindak lanjut atas hasil pemeriksaan Pengadilan Tinggi.\r\nMenyelesaikan tindak lanjut atas hasil monitoring dan evaluasi/assessment Sistem Manajemen Anti Penyuapan.\r\nMenyelesaikan tindak lanjut atas hasil pemeriksaan Bawas.\r\nMemonitor dan mengevaluasi penyelesaian tindak lanjut atas hasil pengawasan bidang.\r\nMemonitor dan mengevaluasi terlaksananya administrasi dan dokumentasi persuratan yang tertata dengan rapi.\r\nMelaksanakan tugas-tugas lain di bidang perencanaan, TI, dan pelaporan serta tugas lain yang diberikan pimpinan.\r\nMelaksanakan monitoring dan evaluasi secara berjenjang di kesekretariatan, terutama bagian PTIP.\r\nMenyelesaikan pengembangan sistem/aplikasi nasional (SIPP, E-Court, E-Berpadu, SIAP).', NULL, 'Perencanaan program dan anggaran (RKAKL, TOR, RAB, revisi DIPA/POK), pengelolaan jaringan, server, CCTV dan website, keterbukaan informasi publik, statistik dan pelaporan, SAKIP dan SEMAR, penyelesaian tindak lanjut hasil pengawasan, monitoring dan evaluasi', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(29, 20, NULL, 'Mempersiapkan sistem, sarana, dan prasarana teknologi informasi serta melaksanakan pemantauan, evaluasi, dokumentasi, dan pelaporan.', 'Mempersiapkan sistem, sarana, dan prasarana teknologi informasi.\r\nMelaksanakan pemantauan, evaluasi, dan dokumentasi serta pelaporan.\r\nMengedit dan mengunggah berita serta kegiatan pengadilan di media sosial dan website.\r\nMenyusun dan membuat video.\r\nMelakukan pemeliharaan CCTV.\r\nMelakukan instalasi dan/atau upgrade sistem operasi komputer dan aplikasi pendukungnya.\r\nMenangani keluhan dan gangguan serta melakukan troubleshooting peripheral, jaringan, sistem operasi, dan aplikasi komputer.\r\nMelakukan pengelolaan di bidang perangkat keras (hardware).\r\nMembuat laporan pengawasan dan tindak lanjut Hawasbid.\r\nMelakukan penyusunan dokumen laporan.\r\nMelaksanakan tugas dari atasan sehubungan dengan kedinasan.', NULL, 'Pengelolaan sarana dan prasarana TI, troubleshooting perangkat keras, jaringan, sistem operasi dan aplikasi, instalasi dan upgrade sistem, pengelolaan konten website dan media sosial, produksi video, pemeliharaan CCTV, penyusunan laporan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(30, 62, NULL, 'Mengelola perangkat pendukung TI, dokumen potongan gaji dan uang makan, laporan bendahara penerimaan, serta kode billing PNBP.', 'Melakukan pengelolaan di bidang perangkat pendukung teknologi informasi.\r\nMelakukan pengelolaan di bidang perangkat lunak.\r\nMelakukan pembuatan dokumen potongan gaji.\r\nMelakukan pembuatan laporan bendahara penerimaan.\r\nMelakukan pembuatan dan pengajuan dokumen uang makan.\r\nMelakukan pembuatan kode billing PNBP dan laporan penerimaan.\r\nMelakukan pembuatan laporan pengawasan dan tindak lanjut Hawasbid.\r\nMelakukan pengumpulan dan pembuatan laporan tahunan.\r\nMelaksanakan tugas dari atasan sehubungan dengan kedinasan.', NULL, 'Pengelolaan perangkat pendukung TI, pengelolaan perangkat lunak, pembuatan dokumen potongan gaji dan uang makan, laporan bendahara penerimaan, kode billing PNBP, penyusunan laporan tahunan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(31, 16, NULL, 'Melaksanakan penyiapan bahan pelaksanaan urusan kepegawaian, penataan organisasi, dan tata laksana.', 'Merencanakan dan memimpin pelaksanaan tugas bagian Kepegawaian, Organisasi, dan Tata Laksana.\r\nMenetapkan sasaran kegiatan setiap tahun.\r\nMenyusun dan menjadwalkan rencana kegiatan.\r\nMembagi tugas kepada bawahan dan menentukan penanggung jawab kegiatan Subbagian Kepegawaian, Organisasi, dan Tata Laksana.\r\nMenggerakkan dan mengarahkan pelaksanaan kegiatan Subbagian Kepegawaian, Organisasi, dan Tata Laksana.\r\nMengadakan konsultasi dengan atasan setiap waktu diperlukan.\r\nMelakukan tugas khusus dari atasan/pimpinan.\r\nMengevaluasi pelaksanaan tugas bawahan.\r\nMemantau dan melakukan evaluasi pelaksanaan tugas para bawahan di lingkungan Subbagian Kepegawaian, Organisasi, dan Tata Laksana.\r\nMelaksanakan tugas dari atasan sehubungan dengan kedinasan.', NULL, 'Kepemimpinan dan perencanaan kerja, pembagian tugas, pengelolaan kepegawaian, organisasi dan tata laksana, monitoring dan evaluasi kinerja bawahan, koordinasi dengan atasan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(32, 63, NULL, 'Mengelola persuratan, surat tugas, data kepegawaian pada aplikasi KOMDANAS dan SIKEP, serta laporan kedisiplinan dan kepegawaian.', 'Menjawab surat masuk.\r\nMembuat surat tugas.\r\nMenginput presensi pada aplikasi KOMDANAS.\r\nMembuat laporan kedisiplinan.\r\nMengupdate data pada aplikasi SIKEP Mahkamah Agung Republik Indonesia.\r\nMendistribusikan surat keputusan ketua, surat tugas, dan surat-surat lainnya.\r\nMenginput PCKB pada aplikasi KOMDANAS.\r\nMenginput surat cuti pada aplikasi SIKEP.\r\nMembuat laporan TLHP pengawasan.\r\nMembuat laporan mutasi internal.\r\nMembuat laporan pengembangan kompetensi.\r\nMengupdate data pegawai, KP, KGB, dan pensiun pada monitor.\r\nMelaksanakan tugas dari atasan sehubungan dengan kedinasan.', NULL, 'Persuratan dan distribusi surat, penyusunan surat tugas, penguasaan aplikasi KOMDANAS dan SIKEP, penyusunan laporan kedisiplinan/TLHP/mutasi/pengembangan kompetensi, pengelolaan data pegawai, KP, KGB, dan pensiun', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(33, 21, NULL, 'Melaksanakan pengelolaan arsip dinamis dan arsip statis serta pembinaan kearsipan.', 'Melakukan pengelolaan arsip dinamis.\r\nMelakukan pengelolaan arsip statis.\r\nMelakukan pembinaan kearsipan.\r\nMelaksanakan tugas dari atasan sehubungan dengan kedinasan.', NULL, 'Pengelolaan arsip dinamis, pengelolaan arsip statis, pembinaan kearsipan, penataan dan pemeliharaan arsip', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(34, 18, NULL, 'Melaksanakan penyiapan urusan surat menyurat, arsip, perlengkapan, rumah tangga, keamanan, keprotokolan, hubungan masyarakat, perpustakaan, serta pengelolaan keuangan.', 'Membuat perencanaan pencairan anggaran seminggu sebelum pencairan dana, menginputnya dalam aplikasi AFS, kemudian mengirimkannya ke KPPN.\r\nMengajukan uang persediaan dan mengantarkan SPM-nya ke KPPN.\r\nMembuat dan mengajukan tambahan uang persediaan yang bersifat mendesak dan mengantarkan SPM-nya ke KPPN.\r\nMengajukan ganti uang persediaan dan mengantarkan SPM-nya ke KPPN.\r\nMembuat SPM langsung dan mengantarkannya ke KPPN.\r\nMengisi setiap transaksi ke dalam buku: Buku Kas Umum, Buku Pembantu Kas Tunai, Buku Pembantu Bank, Buku Pembantu Pajak, Buku Pembantu Uang Persediaan, dan Buku Pembantu Kas LS.\r\nMembuat laporan pertanggungjawaban bendahara pengeluaran, realisasi anggaran manual, dan penyerapan anggaran manual.\r\nMenerima dan menyetorkan pajak melalui kantor pos/bank.\r\nMembuat daftar dan pertanggungjawaban remunerasi serta mengirimkannya ke Korwil.', NULL, 'Perencanaan pencairan anggaran dan aplikasi AFS, pengajuan UP/TUP/GUP dan SPM ke KPPN, pembukuan kas (BKU dan buku pembantu), pertanggungjawaban bendahara pengeluaran, penyetoran pajak, remunerasi', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(35, 64, NULL, 'Menyiapkan informasi dan pemberitaan, layanan kehumasan, serta dokumen pembayaran dan pengajuan keuangan.', 'Menyiapkan penyelenggaraan informasi dan pemberitaan.\r\nMenyiapkan laporan analisis dan evaluasi pelaksanaan kehumasan.\r\nMenyiapkan rumusan pelayanan dan pemberitaan.\r\nMengelola data pemohon informasi kehumasan.\r\nMemeriksa dan menyediakan berkas/dokumen pembayaran gaji induk, gaji susulan, gaji ke-13, THR, uang penghargaan Hakim Ad Hoc, dan PPNPN.\r\nMemeriksa data pegawai dan menyediakan berkas/dokumen pengajuan tunjangan khusus kinerja bagi ASN dan transport bagi hakim di tingkat satuan kerja.\r\nMemeriksa bukti pendukung, menguji SPP, memvalidasi, memproses SPM ke KPPN melalui aplikasi SAKTI, dan mengarsipkan dokumen SPM.', NULL, 'Kehumasan dan layanan informasi, administrasi gaji dan tunjangan, verifikasi SPP/SPM, penguasaan SAKTI, pengarsipan', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(36, 65, NULL, 'Menyusun laporan realisasi anggaran dan rekonsiliasi keuangan DIPA 01 dan DIPA 03 serta memproses SPP/SPM melalui SAKTI.', 'Melaksanakan laporan manual realisasi anggaran DIPA 01 dan DIPA 03.\r\nMelaksanakan rekonsiliasi keuangan DIPA 01 dan DIPA 03.\r\nMenyampaikan neraca, laporan realisasi anggaran, laporan operasional, dan laporan perubahan ekuitas.\r\nMenyampaikan berita acara rekonsiliasi keuangan DIPA 01 dan DIPA 03.\r\nMenerima bukti pembayaran sebagai bahan untuk membuat surat perintah pembayaran (SPBy).\r\nMembuat SPP sebagai dasar pembuatan SPM ke KPPN melalui aplikasi SAKTI untuk belanja 52.\r\nMengarsipkan SPM beserta lampiran-lampirannya sesuai bulan.\r\nMemeriksa berkas-berkas relaas DIPA 03.', NULL, 'Pelaporan realisasi anggaran DIPA 01 dan DIPA 03, rekonsiliasi keuangan, penyusunan laporan keuangan (neraca, LRA, LO, LPE), penyusunan SPP/SPM melalui SAKTI, pengarsipan SPM, pemeriksaan berkas relaas DIPA 03', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(37, 66, NULL, 'Mengelola transaksi dan laporan Barang Milik Negara (BMN), inventarisasi, serta rekonsiliasi internal pada aplikasi SAKTI.', 'Memeriksa dokumen dan menginput data transaksi BMN.\r\nMelaksanakan permohonan usulan: Penetapan Status Penggunaan (PSP), asuransi, hibah masuk, sewa, rencana kebutuhan, dan penghapusan BMN.\r\nMenyusun laporan pengawasan dan pengendalian (Wasdal) untuk periode semesteran dan tahunan.\r\nMenyusun laporan BMN untuk periode semesteran dan tahunan.\r\nMembuat daftar barang ruangan/daftar barang lainnya, kartu identitas barang, labelisasi, dan SK penggunaan BMN.\r\nMelakukan inventarisasi BMN.\r\nMelakukan rekonsiliasi internal antara modul GLP dengan modul Aset Tetap dan Persediaan pada aplikasi SAKTI setiap bulan, semester, dan tahunan.', NULL, 'Pengelolaan BMN (input transaksi, PSP, asuransi, hibah, sewa, penghapusan), penyusunan laporan BMN dan Wasdal, inventarisasi, penyusunan daftar barang dan labelisasi, rekonsiliasi internal SAKTI', '2026-10-13 04:03:38', '2026-10-13 04:03:38'),
(44, 48, 64, 'Melaksanakan Penyiapan Bahan Pelaksanaan, Program, Dan Anggaran, Pengelolaan Teknologi Informasi, Dan Statistik, Serta Pelaksanaan Pemantauan, Evaluasi Dan Dokumentasi Serta Pelaporan', 'Melaksanakan Penyiapan Bahan Pelaksanaan Program Dan Anggaran.\r\nMelaksanakan Penyusunan Perencanaan Program Dan Anggaran/ Kegiatan/RKAKL Dan Penyusunan TOR, RAB, Bahan Dan Data Dukung Pengajuan Anggaran.\r\nMelaksanakan Revisi DIPA Dan DIPA Hal III Berupa Perubahan/Pergeseran Rincian Anggaran Yang Telah Ditetapkan Berdasarkan APBN/DIPA.\r\nMelaksanakan Revisi POK/Komponen Dalam Satu Output Yang Disesuaikan Dengan Output Yang Disesuaikan Dengan Rencana Kegiatan Dan Dana Yang Tersedia.\r\nMelaksanakan Perawatan Dan Pemeliharaan Jaringan Sehingga Kualitas Dan Kinerja Perangkat Jaringan Stabil Dan Tidak Terjadi Kerusakan Pada Komponen Yang Ada Didalamnya.\r\nMelaksanakan Perawatan Dan Pengelolaan CCTV, Yaitu Menjaga Sarpras, Sistem, Aplikasi Jaringan, Database CCTV Dalam Kondisi Baik Dan Dapat Berfungsi Dengan Baik.\r\nMonev Terlaksananya Keterbukaan Informasi Publik (KIP) Yaitu Dengan Menjaga Keamanan Website Berikut Aplikasi Yang Terintegrasi, Publikasi Informasi Website Dan Media Sosial.\r\nMelaksanakan Pengelolaan Server Yaitu Dengan Menjaga Sarpras, Sistem, Aplikasi, Jaringan, Database Server Dalam Kondisi Baik Dan Dapat Berfungsi Dengan Baik.\r\nMonev Terlaksananya Koordinasi Dengan Bagian Umum Terkait Pengelolaan Jaringan Listrik Dan Genset Yang Mendukung Perangkat/Sarpras IT Dapat Berfungsi Dengan Baik.\r\nMonev Terlaksananya Pengumpulan Data Statistik Akurat Dan Benar.\r\nMonev Terkoordinirnya Pengelolaan Dokumen Surat Keputusan Dalam Aplikasi JDIH.\r\nMelaksanakan Perawatan Dan Pengelolaan Sistem IT Yaitu Menjaga Sarpras, Sistem Aplikasi, Jaringan, Database dll Dalam Kondisi Dan Fungsi Baik: Sinkronisasi SIPP (Web, PT, MA, Ecourt, Direktori Putusan, SPPT-TI), Media Sosial, Layanan Ptsp, Sidang Online.\r\nMelaksanakan Penyusunan Dan Pengiriman Laporan Badilum Selain Perkara Sesuai, Benar Dan Tepat Waktu.\r\nMonev Terlaksananya Penyusunan Dan Pengiriman Laporan Monev Smart Sesuai Benar Dan Tepat Waktu.\r\nMonev Terlaksananya Penyusunan Laporan Kegiatan Bulanan Sesuai, Benar Dan Tepat Waktu.\r\nMonev Terlaksananya Penyusunan Dan Pengiriman Data Dan Dokumen SAKIP Pada Komdanas Sesuai, Benar Dan Tepat Waktu.\r\nMelaksanakan Penyusunan Dan Pengiriman Laporan Tahunan/ Kegiatan Sesuai, Benar Dan Tepat Waktu.\r\nMelaksanakan Penyusunan Dokumen Evaluasi Kinerja Pada Aplikasi SEMAR Sesuai, Benar Dan Tepat Waktu.\r\nPenyelesaian Tindak Lanjut Atas Hasil Monitoring Dan Evaluasi/ Assesment Surveilance AMPUH Badilum.\r\nPenyelesaian Tindak Lanjut Atas Hasil Pemeriksaan Pengadilan Tinggi.\r\nPenyelesaian Tindak Lanjut Atas Hasil Monitoring Dan Evaluasi/ Assesment Sistem Manajemen Anti Penyuapan.\r\nPenyelesaian Tindak Lanjut Atas Hasil Pemeriksaan Bawas.\r\nMonev Penyelesaian Tindak Lanjut Atas Hasil Pengawasan Bidang.\r\nMonev Terlaksananya Administrasi Dan Dokumentasi Persuratan Tertata Dengan Rapi.\r\nTerlaksananya Tugas-Tugas Lain Yang Ada Di Perencanaan, IT Dan Pelaporan Serta Tugas Lain Yang Diberikan Pimpinan.\r\nTerlaksananya Monitoring & Evaluasi Secara Berjenjang Di Kesekretariatan, Terutama Bagian PTIP.\r\nPenyelesaian Pengembangan Sistem/Aplikasi Nasional (SIPP, E-Court, E-Berpadu, SIAP.', 'Penguasaan TI, analisis data, perencanaan program', 'Monitoring evaluasi, pengelolaan TI, penyusunan laporan kinerja', '2026-09-21 18:59:58', '2026-09-21 18:59:58'),
(45, 18, 69, 'Pengelolaan BMN dan keuangan', 'Membuat Perencanaan Pencairan Anggaran Seminggu Sebelum Pencairan Dana Dan Menginput Dalam Aplikasi AFS,Kemudian Mengirimkan Ke KPPN.\r\nMengajukan Uang Persediaan Dan Mengantarkan Spmnya Ke KPPN.\r\nMembuat Dan Mengajukan Tambahan Uang Persediaan Yang Bersifat Mendesak Dan Mengantarkannya Spmnya Ke KPPN.\r\nMengajukan Ganti Uang Persediaan Dan Mengatarkan Spmnya Ke KPPN.\r\nMembuat SPM Langsung Dan Mengantarkannya Ke KPPN.\r\nMengisi Setiap Transaksi Ke Dalam Buku: Buku Kas Umum, Buku Kas Umum, Buku Pembantu Kas Tunai, Buku Pembantu Bank, Buku Pembantu Pajak, Buku Pembantu Uang Persediaan, Buku Pembantu Kas LS.\r\nMembuat Laporan Pertanggungjawaban Bendahara Pengeluaran, Membuat Realisasi Anggaran Manual, Membuat Penyerapan Anggaran Manual.\r\nMenerima Dan Menyetorkan Pajak Melalui Kantor Pos Bank.\r\nMembuat Daftar Dan Pertanggungjawaban Remnunisasi Dan Mengirimkannya ke KORWIL.', 'Pemahaman keuangan negara, ketelitian, akuntabilitas', 'Pengelolaan BMN, keuangan, akuntabilitas anggaran', '2026-09-21 18:59:58', '2026-09-21 18:59:58'),
(46, 23, 70, 'Pelayanan publik dan informasi', 'Pelayanan informasi perkara.\r\nPenerimaan tamu.\r\nPengaduan masyarakat.\r\nPelayanan dokumen.', 'Komunikasi efektif, pelayanan publik, etika layanan', 'Service excellent, komunikasi publik, penanganan pengaduan', '2026-09-21 18:59:58', '2026-09-21 18:59:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_log`
--

CREATE TABLE `audit_log` (
  `id_log` bigint(20) UNSIGNED NOT NULL,
  `id_user` int(10) UNSIGNED DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `aksi` varchar(50) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `audit_log`
--

INSERT INTO `audit_log` (`id_log`, `id_user`, `nip`, `aksi`, `keterangan`, `ip`, `created_at`) VALUES
(1, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-15 01:35:47'),
(2, 1, '12323', 'logout', NULL, '::1', '2026-10-15 03:02:22'),
(3, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-15 03:02:31'),
(4, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-16 01:44:11'),
(5, 1, '12323', 'logout', NULL, '::1', '2026-10-16 01:58:10'),
(6, 3, '197303151992032001', 'login', 'Login berhasil', '::1', '2026-10-16 01:58:15'),
(7, 3, '197303151992032001', 'logout', NULL, '::1', '2026-10-16 01:58:35'),
(8, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-16 01:58:38'),
(9, 1, '12323', 'logout', NULL, '::1', '2026-10-16 02:03:25'),
(10, 3, '197303151992032001', 'login', 'Login berhasil', '::1', '2026-10-16 02:03:30'),
(11, 3, '197303151992032001', 'logout', NULL, '::1', '2026-10-16 02:07:06'),
(12, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-16 02:07:08'),
(13, 1, '12323', 'logout', NULL, '::1', '2026-10-16 02:08:49'),
(14, 3, '197303151992032001', 'login', 'Login berhasil', '::1', '2026-10-16 02:08:52'),
(15, 3, '197303151992032001', 'logout', NULL, '::1', '2026-10-16 02:16:26'),
(16, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-16 02:16:30'),
(17, 1, '12323', 'logout', NULL, '::1', '2026-10-16 02:17:37'),
(18, 3, '197303151992032001', 'login', 'Login berhasil', '::1', '2026-10-16 02:17:41'),
(19, 3, '197303151992032001', 'logout', NULL, '::1', '2026-10-16 02:19:46'),
(20, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-16 02:19:49'),
(21, 1, '12323', 'logout', NULL, '::1', '2026-10-16 02:56:52'),
(22, 3, '197303151992032001', 'login', 'Login berhasil', '::1', '2026-10-16 02:56:55'),
(23, 3, '197303151992032001', 'logout', NULL, '::1', '2026-10-16 03:26:22'),
(24, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-20 04:48:41'),
(25, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-20 06:21:08'),
(26, 1, '12323', 'logout', NULL, '::1', '2026-10-20 06:27:33'),
(27, 5, '197308161994031001', 'login', 'Login berhasil', '::1', '2026-10-20 06:28:10'),
(28, 5, '197308161994031001', 'logout', NULL, '::1', '2026-10-20 06:28:56'),
(29, 1, '12323', 'login', 'Login berhasil', '::1', '2026-10-20 06:29:03');

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

--
-- Dumping data untuk tabel `bukti_wawancara`
--

INSERT INTO `bukti_wawancara` (`id_bukti`, `id_wawancara`, `file_bukti`, `created_at`) VALUES
(5, 4, 'assets/uploads/wawancara/3b87430647a87de0495508b101667138.pdf', '2026-10-16 02:01:18');

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

--
-- Dumping data untuk tabel `diklat`
--

INSERT INTO `diklat` (`id_diklat`, `id_kompetensi`, `nama_diklat`, `penyelenggara`, `deskripsi`, `created_at`) VALUES
(1, NULL, 'Diklat Administrasi Perkara Perdata', NULL, NULL, '2026-09-24 02:06:28'),
(2, NULL, 'Pelatihan SIPP dan e-Berpadu', NULL, NULL, '2026-09-24 02:06:28'),
(3, NULL, 'Diklat Administrasi Perkara Tipikor', NULL, NULL, '2026-09-24 02:06:28'),
(4, NULL, 'Pelatihan Teknis Perkara PHI', NULL, NULL, '2026-09-24 02:06:28'),
(5, NULL, 'Pelatihan Dokumentasi dan Pelaporan', NULL, NULL, '2026-09-24 02:06:28'),
(6, NULL, 'Diklat Administrasi Kepegawaian', NULL, NULL, '2026-09-24 02:06:28'),
(7, NULL, 'Pelatihan Monitoring Evaluasi dan TI', NULL, NULL, '2026-09-24 02:06:28'),
(8, NULL, 'Diklat Pengelolaan BMN dan Keuangan', NULL, NULL, '2026-09-24 02:06:28'),
(9, NULL, 'Pelatihan Service Excellent, Pelatihan PTSP', NULL, NULL, '2026-09-24 02:06:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumentasi_kuesioner`
--

CREATE TABLE `dokumentasi_kuesioner` (
  `id_dokumentasi` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_dokumentasi` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokumentasi_kuesioner`
--

INSERT INTO `dokumentasi_kuesioner` (`id_dokumentasi`, `id_wawancara`, `file_dokumentasi`, `created_at`) VALUES
(1, 9, 'assets/uploads/kuesioner/2c15d114adf605f8d59f51d99052b24c.png', '2026-10-20 06:25:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumentasi_wawancara`
--

CREATE TABLE `dokumentasi_wawancara` (
  `id_dokumentasi` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_dokumentasi` varchar(255) NOT NULL,
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
  `daftar_nilai` text DEFAULT NULL,
  `daftar_jawaban` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_kuesioner`
--

INSERT INTO `hasil_kuesioner` (`id_hasil`, `id_wawancara`, `file_bukti`, `status_kompetensi`, `created_at`, `updated_at`, `daftar_nilai`, `daftar_jawaban`) VALUES
(1, 6, NULL, 'Tidak Kompeten', '2026-10-16 02:56:43', '2026-10-16 03:23:21', '[null,null,null]', '{\"9\":\"asd\",\"8\":\"\",\"7\":\"asd\"}'),
(6, 9, 'assets/uploads/kuesioner/77a7022eaafc38e6f08620d6e4323f9a.png', 'Kompeten', '2026-10-20 06:25:50', '2026-10-20 06:25:50', '[95,80,80]', '{\"6\":\"iya\",\"5\":\"iya\",\"4\":\"iya\"}');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_wawancara`
--

CREATE TABLE `hasil_wawancara` (
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `kompetensi` varchar(200) NOT NULL,
  `isi_penilaian` text NOT NULL,
  `jawaban` text DEFAULT NULL,
  `nilai` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Nilai 0-100',
  `file_bukti` varchar(255) DEFAULT NULL,
  `status_kompetensi` enum('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hasil_wawancara`
--

INSERT INTO `hasil_wawancara` (`id_hasil`, `id_wawancara`, `kompetensi`, `isi_penilaian`, `jawaban`, `nilai`, `file_bukti`, `status_kompetensi`, `created_at`, `updated_at`) VALUES
(0, 4, 'Penguasaan SIPP', 'Bagaimana prosedur minutasi perkara sampai selesai?', NULL, 20, NULL, 'Tidak Kompeten', '2026-09-22 03:05:26', '2026-10-16 02:01:18'),
(1, 1, 'Pelayanan Prima', 'Bagaimana menangani ublicn masyarakat yang tidak puas?', NULL, NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:42'),
(2, 1, 'Pelayanan Prima', 'Apa yang dimaksud dengan standar pelayanan prima?', NULL, NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 03:05:26'),
(3, 1, 'Pelayanan Prima', 'Bagaimana prosedur pelayanan informasi perkara kepada masyarakat?', NULL, NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:37'),
(4, 2, 'Pelayanan Prima', 'Bagaimana menangani masyarakat yang tidak puas?', NULL, NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:55'),
(5, 2, 'Pelayanan Prima', 'Apa yang dimaksud dengan standar pelayanan prima?', NULL, NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 03:05:26'),
(6, 2, 'Pelayanan Prima', 'Bagaimana prosedur pelayanan informasi perkara kepada masyarakat?', NULL, NULL, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-09-22 04:06:52'),
(7, 3, 'Teknisi Sarana dan Prasarana Umum', 'Apa langkah yang Anda lakukan dalam pemiliharaan preventif system kelistrikan gedung?', NULL, NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 04:07:31'),
(8, 3, 'Teknisi Sarana dan Prasarana Umum', 'Bagaimana Anda beradaptasi dengan teknologi atau peralatan baru dalam system pemeliharaan sarana?', NULL, NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 03:05:26'),
(9, 3, 'Teknisi Sarana dan Prasarana Umum', 'Bagaimana Anda memastikan standar keamanan (K3) terpenuhi saat Anda melakukan perbaikan prasarana?', NULL, NULL, NULL, 'Cukup', '2026-09-22 03:05:26', '2026-09-22 04:07:04'),
(10, 4, 'Penguasaan SIPP', 'Bagaimana cara melakukan koreksi data perkara yang salah input?', NULL, 20, NULL, 'Tidak Kompeten', '2026-09-22 03:05:26', '2026-10-16 02:01:18'),
(12, 4, 'Penguasaan SIPP', 'Apakah Saudara memahami seluruh tahapan input perkara pada SIPP?', NULL, 40, NULL, 'Tidak Kompeten', '2026-09-22 03:05:26', '2026-10-16 02:01:18'),
(13, 5, 'Penguasaan SIPP', 'Apakah Saudara memahami seluruh tahapan input perkara pada SIPP?', NULL, 80, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-10-14 04:02:46'),
(14, 5, 'Penguasaan SIPP', 'Bagaimana prosedur minutasi perkara sampai selesai?', 'betul kawab', 80, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-10-14 03:56:06'),
(15, 5, 'Penguasaan SIPP', 'Bagaimana cara melakukan koreksi data perkara yang salah input?', 'mantao jiwa', 80, NULL, 'Kompeten', '2026-09-22 03:05:26', '2026-10-14 03:56:06');

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
(48, 'KSB', 'Kepala Sub Bagian', 0, '2026-09-22 08:00:00'),
(49, 'KSR-PDT', 'Kasir Kepaniteraan Perdata', 0, '2026-10-13 02:37:15'),
(50, 'JSP-PDT', 'Jurusita Pengganti Kepaniteraan Perdata', 0, '2026-10-13 02:37:15'),
(51, 'APP-PDT', 'Klerek Analis Perkara Peradilan Kepaniteraan Perdata', 0, '2026-10-13 02:37:15'),
(52, 'JSP-PID', 'Jurusita Pengganti Kepaniteraan Pidana', 0, '2026-10-13 02:37:15'),
(53, 'PGP-PID', 'Klerek Pengelolaan Perkara Kepaniteraan Pidana', 0, '2026-10-13 02:37:15'),
(54, 'APP-PID', 'Klerek Analis Perkara Peradilan Kepaniteraan Pidana', 0, '2026-10-13 02:37:15'),
(55, 'APP-PHI', 'Klerek Analis Perkara Peradilan PHI', 0, '2026-10-13 02:37:15'),
(56, 'PGP-PHI', 'Klerek Pengelola Perkara Kepaniteraan PHI', 0, '2026-10-13 02:37:15'),
(57, 'OLO-PHI-SK', 'Operator Layanan Operasional Kepaniteraan PHI', 0, '2026-10-13 02:37:15'),
(58, 'APP-TPK', 'Klerek Analis Perkara Peradilan Kepaniteraan TIPIKOR', 0, '2026-10-13 02:37:15'),
(59, 'DOK-TPK', 'Klerek Dokumentasi Hukum Kepaniteraan TIPIKOR', 0, '2026-10-13 02:37:15'),
(60, 'APP-HKM', 'Klerek Analis Perkara Peradilan Kepaniteraan Muda Hukum', 0, '2026-10-13 02:37:15'),
(61, 'PAK-HKM', 'Pengadministrasi Perkantoran Kepaniteraan Muda Hukum', 0, '2026-10-13 02:37:15'),
(62, 'PLO-PTIP-SK', 'Operator Penata Layanan Operasional Subbagian Perencanaan, Teknologi Informasi, dan Pelaporan', 0, '2026-10-13 02:37:15'),
(63, 'PLO-KOT', 'Operator Penata Layanan Operasional Subbagian Kepegawaian, Organisasi, dan Tata Laksana', 0, '2026-10-13 02:37:15'),
(64, 'OLO-UK-SK', 'Operator Penata Layanan Operasional Subbagian Umum dan Keuangan', 0, '2026-10-13 02:37:15'),
(65, 'APKEU-UK', 'Analis Pengelolaan Keuangan APBN Subbagian Umum dan Keuangan', 0, '2026-10-13 02:37:15'),
(66, 'PDI-UK', 'Klerek Pengolah Data dan Informasi Subbagian Umum dan Keuangan', 0, '2026-10-13 02:37:15');

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
-- Struktur dari tabel `jenis_kegiatan_hakim`
--

CREATE TABLE `jenis_kegiatan_hakim` (
  `nama_jenis` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jenis_kegiatan_hakim`
--

INSERT INTO `jenis_kegiatan_hakim` (`nama_jenis`) VALUES
('Bimtek/Pelatihan'),
('Narasumber'),
('Pengajar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kebutuhan_diklat`
--

CREATE TABLE `kebutuhan_diklat` (
  `id_kebutuhan` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `id_kesenjangan` int(10) UNSIGNED DEFAULT NULL,
  `id_diklat` int(10) UNSIGNED DEFAULT NULL,
  `diklat_lainnya` varchar(200) DEFAULT NULL,
  `metode_pengembangan` varchar(100) DEFAULT NULL,
  `prioritas` enum('Sangat Tinggi','Tinggi','Sedang','Rendah') NOT NULL DEFAULT 'Sedang',
  `status` enum('Diusulkan','Disetujui','Terlaksana','Ditolak') NOT NULL DEFAULT 'Diusulkan',
  `tahun_rencana` year(4) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kebutuhan_diklat`
--

INSERT INTO `kebutuhan_diklat` (`id_kebutuhan`, `id_pegawai`, `id_kesenjangan`, `id_diklat`, `diklat_lainnya`, `metode_pengembangan`, `prioritas`, `status`, `tahun_rencana`, `catatan`, `created_at`, `updated_at`) VALUES
(10, 24, NULL, 1, NULL, 'Diklat teknis kepaniteraan', 'Tinggi', 'Terlaksana', '2026', 'Perlu peningkatan administrasi perkara dan ketelitian kerja', '2026-09-24 02:34:12', '2026-10-13 07:41:36'),
(11, 23, NULL, 2, NULL, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', '2026', 'Perlu pemahaman aplikasi pidana', '2026-09-24 02:34:12', '2026-09-24 02:34:12'),
(12, 25, NULL, 3, NULL, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', '2026', 'Perlu peningkatan administrasi perkara dan ketelitian kerja', '2026-09-24 02:34:12', '2026-09-24 02:34:12'),
(13, 21, NULL, 4, NULL, 'Diklat teknis kepaniteraan', 'Sedang', 'Diusulkan', '2026', 'Perlu peningkatan administrasi perkara PHI dan ketelitian kerja', '2026-09-24 02:34:12', '2026-09-24 02:34:12'),
(14, 22, NULL, 5, NULL, 'Diklat teknis kepaniteraan', 'Sedang', 'Diusulkan', '2026', 'Perlu arsiparis perkara', '2026-09-24 02:34:12', '2026-09-24 02:34:12'),
(15, 63, NULL, 6, NULL, 'Diklat + coaching', 'Tinggi', 'Diusulkan', '2026', 'Administrasi ASN, Penguasaan Aplikasi SIKEP, SI ASN, KOMDANAS, E KINERJA', '2026-09-24 02:34:12', '2026-09-24 02:34:12'),
(16, 117, NULL, 7, NULL, 'Bimtek Kepegawaian', 'Sedang', 'Diusulkan', '2026', 'Monitoring Perencanaan, TI dan Pelaporan', '2026-09-24 02:34:12', '2026-09-24 02:34:12'),
(17, 69, NULL, 8, NULL, 'Bimtek Kepegawaian', 'Tinggi', 'Diusulkan', '2026', 'Perlu penambahan personil dan pelatihan untuk Aplikasi di Keuangan', '2026-09-24 02:34:12', '2026-09-24 02:34:12'),
(18, 88, NULL, 1, NULL, NULL, 'Sedang', 'Diusulkan', '2027', '', '2026-10-13 07:17:33', '2026-10-13 07:17:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan_hakim`
--

CREATE TABLE `kegiatan_hakim` (
  `id_kegiatan` int(10) UNSIGNED NOT NULL,
  `id_pegawai` int(10) UNSIGNED NOT NULL,
  `jenis_kegiatan` varchar(100) NOT NULL,
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

--
-- Dumping data untuk tabel `kegiatan_hakim`
--

INSERT INTO `kegiatan_hakim` (`id_kegiatan`, `id_pegawai`, `jenis_kegiatan`, `nama_kegiatan`, `penyelenggara`, `peran_topik`, `tanggal_mulai`, `tanggal_selesai`, `lokasi`, `file_bukti`, `keterangan`, `created_at`) VALUES
(1, 18, 'Narasumber', 'sosialisasi tindak pidana', 'pny', NULL, '2026-10-12', '2026-10-13', 'kalasan', NULL, NULL, '2026-10-12 01:59:50'),
(2, 93, 'Bimtek/Pelatihan', 'pelatihan hakim', 'pnpusat', NULL, '2026-10-12', '2026-10-13', 'sch', NULL, NULL, '2026-10-12 02:00:34'),
(3, 15, 'Bimtek/Pelatihan', 'anu', 'anu', NULL, '2026-11-21', '2026-11-21', 'anu', NULL, NULL, '2026-10-15 03:32:11');

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
(2, '196804141996031002', 'SYAFRIZAL, S.H.', 1, 'Aktif', 1, '2026-09-15 01:53:35', '2026-10-09 01:52:21'),
(3, '197809112001122002', 'MELINDA ARITONANG, S.H.', 2, 'Aktif', 1, '2026-09-15 01:53:35', '2026-10-13 06:25:11'),
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
(20, '197303151992032001', 'JOHANA CAROLINA LEKBILA, S.IP.,SH', 6, 'Aktif', 1, '2026-09-15 01:53:35', '2026-09-29 02:54:16'),
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
-- Struktur dari tabel `pemeriksaan_bukti_kuesioner`
--

CREATE TABLE `pemeriksaan_bukti_kuesioner` (
  `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_bukti` varchar(255) NOT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `diperiksa_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemeriksaan_bukti_kuesioner`
--

INSERT INTO `pemeriksaan_bukti_kuesioner` (`id_pemeriksaan`, `id_wawancara`, `file_bukti`, `id_user_penilai`, `diperiksa_at`) VALUES
(1, 6, 'assets/uploads/kuesioner/63178f8710498ac880bc0422717410ef.pdf', 3, '2026-10-16 03:05:28'),
(7, 9, 'assets/uploads/kuesioner/77a7022eaafc38e6f08620d6e4323f9a.png', 5, '2026-10-20 06:28:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeriksaan_bukti_wawancara`
--

CREATE TABLE `pemeriksaan_bukti_wawancara` (
  `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_bukti` varchar(255) NOT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `diperiksa_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemeriksaan_bukti_wawancara`
--

INSERT INTO `pemeriksaan_bukti_wawancara` (`id_pemeriksaan`, `id_wawancara`, `file_bukti`, `id_user_penilai`, `diperiksa_at`) VALUES
(1, 4, 'assets/uploads/wawancara/3b87430647a87de0495508b101667138.pdf', 3, '2026-10-16 02:03:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeriksaan_jawaban_kuesioner`
--

CREATE TABLE `pemeriksaan_jawaban_kuesioner` (
  `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL,
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `jawaban_hash` char(64) NOT NULL,
  `diperiksa_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemeriksaan_jawaban_kuesioner`
--

INSERT INTO `pemeriksaan_jawaban_kuesioner` (`id_pemeriksaan`, `id_hasil`, `id_user_penilai`, `jawaban_hash`, `diperiksa_at`) VALUES
(1, 1, 3, 'b04aa861fe72bae7547452cd9c16e715ea48b16bfbd21cb233f1c58994d96bac', '2026-10-16 03:26:16'),
(7, 6, 5, 'c7c030d677373c989114717aed285610a63577e0b87378c4566ec775005ec3b2', '2026-10-20 06:28:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeriksaan_jawaban_wawancara`
--

CREATE TABLE `pemeriksaan_jawaban_wawancara` (
  `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL,
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `jawaban_hash` char(64) NOT NULL,
  `diperiksa_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemeriksaan_jawaban_wawancara`
--

INSERT INTO `pemeriksaan_jawaban_wawancara` (`id_pemeriksaan`, `id_hasil`, `id_user_penilai`, `jawaban_hash`, `diperiksa_at`) VALUES
(1, 14, 3, '94a5fe3405667a27405617337512766b142789961b5c90eaf27095a756278fe5', '2026-10-16 03:26:10'),
(2, 15, 3, '1118e46cda0861c2c4cf603c79414d38d892a6153736b01b658e147e5a8fd565', '2026-10-16 03:26:10');

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
-- Struktur dari tabel `schema_migrations`
--

CREATE TABLE `schema_migrations` (
  `filename` varchar(190) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `schema_migrations`
--

INSERT INTO `schema_migrations` (`filename`, `applied_at`) VALUES
('20261009_001_keamanan.sql', '2026-10-14 03:46:10'),
('20261014_001_jawaban_wawancara.sql', '2026-10-14 03:46:10');

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
  `nomor_sertifikat` varchar(100) DEFAULT NULL,
  `jalur_pelatihan` varchar(80) DEFAULT NULL,
  `status_validasi` varchar(40) DEFAULT NULL,
  `jenis` enum('Pelatihan','Sertifikasi') DEFAULT NULL,
  `file_sertifikat` varchar(255) DEFAULT NULL COMMENT 'Path file upload',
  `dibuat_oleh` int(10) UNSIGNED DEFAULT NULL COMMENT 'users.id_user (role Admin)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sertifikat_pegawai`
--

INSERT INTO `sertifikat_pegawai` (`id_sertifikat`, `id_pegawai`, `nama_sertifikat`, `penyelenggara`, `tanggal_terbit`, `tanggal_kadaluarsa`, `nomor_sertifikat`, `jalur_pelatihan`, `status_validasi`, `jenis`, `file_sertifikat`, `dibuat_oleh`, `created_at`) VALUES
(3, 2, 'PRAJABATAN TK III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1997-01-04', NULL, '03.523/PRAJAB III/II/LAN/1996', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196804141996031002/PRAJABATAN_TK_III.pdf', 1, '2026-09-28 02:14:37'),
(4, 2, 'Diklat Akses Perempuan Terhadap Keadilan', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2008-12-10', NULL, '040070130', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196804141996031002/Diklat_Akses_Perempuan_Terhadap_Keadilan.pdf', 1, '2026-09-28 02:14:37'),
(5, 2, 'Diklat Arsip Berbasis IT', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2009-02-26', NULL, '-', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196804141996031002/Diklat_Arsip_Berbasis_IT.pdf', 1, '2026-09-28 02:14:37'),
(6, 2, 'Diklat Terpadu Apgakum Dalam Penanganan Perkara Korupsi Kerjasama dengan Kejaksaan Agung RI', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2012-03-04', NULL, '-', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196804141996031002/Diklat_Terpadu_Apgakum_Dalam_Penanganan_Perkara_Korupsi_Kerjasama_dengan_Kejaksaan_Agung_RI.pdf', 1, '2026-09-28 02:14:37'),
(7, 2, 'Pelatihan Sertifikasi Hakim Tipikor', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2013-06-05', NULL, '59/Bld/MA-RI/2013', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196804141996031002/Pelatihan_Sertifikasi_Hakim_Tipikor.pdf', 1, '2026-09-28 02:14:37'),
(8, 2, 'Pelatihan Manajemen Risiko Bagi Pegawai Di Lingkungan Mahkamah Agung Batch II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-06-13', NULL, '332/DPK-E/MDN/06/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196804141996031002/Pelatihan_Manajemen_Risiko_Bagi_Pegawai_Di_Lingkungan_Mahkamah_Agung_Batch_II.pdf', 1, '2026-09-28 02:14:37'),
(9, 2, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-01', NULL, '520/BSDK/PS.KUHP.G1/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196804141996031002/Pelatihansingkat.pdf', 1, '2026-09-28 02:14:37'),
(10, 4, 'PENDIDIKAN DAN PELATIHAN PTRAJABATAN GOLONGAN III ANGKATAN III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1997-11-03', NULL, '13.0667/PRAJAB III/III/LAN/1997', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/PENDIDIKAN_DAN_PELATIHAN_PTRAJABATAN_GOLONGAN_III_ANGKATAN_III.pdf', 1, '2026-09-28 02:14:37'),
(11, 4, 'Pelatihan Hakim Dalam Perkara Korupsi Angkatan VI Hakim Peradilan Umum Tingkat Pertama', 'Mahkamah Agung RI', '2009-07-03', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196905311996031001/Pelatihan_Hakim_Dalam_Perkara_Korupsi_Angkatan_VI_Hakim_Peradilan_Umum_Tingkat_Pertama.pdf', 1, '2026-09-28 02:14:37'),
(12, 4, 'Pelatihan Hakim Dalam Perkara Korupsi Angkatan VI Hakim Peradilan Umum Tingkat Pertama Se-Indonesia', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2009-07-03', NULL, '140/Bld/MA-RI/2009', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Pelatihan_Hakim_Dalam_Perkara_Korupsi_Angkatan_VI_Hakim_Peradilan_Umum_Tingkat_Pertama_Se-Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(13, 4, 'Sosialisasi Perma No. 01 Tahun 2008 tentang Mediasi', 'Direktorat Jenderal Badan Peradilan Umum', '2009-07-10', NULL, '-', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Sosialisasi_Perma_No._01_Tahun_2008_tentang_Mediasi.pdf', 1, '2026-09-28 02:14:37'),
(14, 4, 'Pendidikan dan Pelatihan Terpadu Hakim dan Jaksa Angkatan I Tahun 2012', 'Badan Diklat Kejaksaan R.I.', '2012-02-20', NULL, 'B-073/J/J.3/TERPADU i/02/2012', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Pendidikan_dan_Pelatihan_Terpadu_Hakim_dan_Jaksa_Angkatan_I_Tahun_2012.pdf', 1, '2026-09-28 02:14:37'),
(15, 4, 'Pelatihan Teknis Fungsional Perkara Tindak Pidana Narkotika Bagi Hakim Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan Militer Seluruh Indonesia', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2014-06-27', NULL, '68/Bld/MA-RI/2014', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Pelatihan_Teknis_Fungsional_Perkara_Tindak_Pidana_Narkotika_Bagi_Hakim_Tingkat_Pertama_dan_Tingkat_Banding_Peradilan_Umu.pdf', 1, '2026-09-28 02:14:37'),
(16, 4, 'Pendidikan dan Pelatihan Teknis Fungsional Perkara Gugatan Sederhana (Small Claim Court) Untuk Hakim Peradilan Umum Seluruh Indonesia', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2016-11-26', NULL, '2135/Bid/MA-RI/2016', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Pendidikan_dan_Pelatihan_Teknis_Fungsional_Perkara_Gugatan_Sederhana_Small_Claim_Court_Untuk_Hakim_Peradilan_Umum_Seluru.pdf', 1, '2026-09-28 02:14:37'),
(17, 4, 'Pendidikan dan Pelatihan Sertifikasi Hakim Lingkungan Hidup Bagi Hakim Lingkungan Peradilan Umum dan Peradilan Tata Usaha Negara Seluruh Indonesia', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2017-11-22', NULL, '45/Bld/MA-RI/2017', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Pendidikan_dan_Pelatihan_Sertifikasi_Hakim_Lingkungan_Hidup_Bagi_Hakim_Lingkungan_Peradilan_Umum_dan_Peradilan_Tata_Usah.pdf', 1, '2026-09-28 02:14:37'),
(18, 4, 'Sertifikasi Hakim Lingkungan Hidup', 'Mahkamah Agung R.I.', '2017-11-22', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196905311996031001/Sertifikasi_Hakim_Lingkungan_Hidup.pdf', 1, '2026-09-28 02:14:37'),
(19, 4, 'Workshop Tindak Pidana Pemilu Bagi Hakim Peradilan Umum', 'Komisi Yudisial RI dan Mahkamah Agung RI', '2019-03-15', NULL, '072/PPKH-T.TPP/KY/2019', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Workshop_Tindak_Pidana_Pemilu_Bagi_Hakim_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(20, 4, 'Workshop Eksplorasi KEPPH: \'Studi Kasus Laporan Masyarakat di Komisi Yudisial\'', 'Komisi Yudisial RI', '2019-07-26', NULL, '221/PPKH-E.KEPPH/KY/2019', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Workshop_Eksplorasi_KEPPH___Studi_Kasus_Laporan_Masyarakat_di_Komisi_Yudisial_.pdf', 1, '2026-09-28 02:14:37'),
(21, 4, 'Pelatihan Teknis Yudisial Penistaan Agama Bagi Hakim Peradilan Umum dan Peradilan Militer Seluruh Indonesia', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2023-03-10', NULL, '35/BLD/MA-RI/PTY.Blasphemy/2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Pelatihan_Teknis_Yudisial_Penistaan_Agama_Bagi_Hakim_Peradilan_Umum_dan_Peradilan_Militer_Seluruh_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(22, 4, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-08', NULL, '259/BSDK/PS.KUHP.G2/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional_UU_Nomor_1_Tahun_2023_bagi_Hakim_Pemeriksa_Per.pdf', 1, '2026-09-28 02:14:37'),
(23, 4, 'TEMU WICARA MAHKAMAH AGUNG RI-BANK INDONESIA', 'BANK INDONESIA', NULL, NULL, '(tidak diset)', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196905311996031001/TEMU_WICARA_MAHKAMAH_AGUNG_RI-BANK_INDONESIA.pdf', 1, '2026-09-28 02:14:37'),
(24, 5, 'Prajab III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2000-10-29', NULL, '10.2309/PRAJAB III/VI/LAN/2000', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197501272000032003/Prajab_III.pdf', 1, '2026-09-28 02:14:37'),
(25, 5, 'Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2001-11-23', NULL, 'A6-142/PUSDIKLAT/CAKIM/XII/2001', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(26, 5, 'Sertifikasi Mediator', 'Mahkamah Agung RI', '2015-05-23', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197501272000032003/Sertifikasi_Mediator.pdf', 1, '2026-09-28 02:14:37'),
(27, 6, 'DIKLAT PRAJABATAN TK II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1994-02-08', NULL, 'W9-087-II-DL.04.09. TAHUN 1994', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(28, 6, 'Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2000-01-04', NULL, '30/PUSDIKLAT/CAKIM/XI/1999', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(29, 6, 'SERTIFIKAT PELATIHAN PHI', 'MAHKAMAH AGUNG', '2014-05-09', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197204271993031003/SERTIFIKAT_PELATIHAN_PHI.pdf', 1, '2026-09-28 02:14:37'),
(30, 6, 'IMPLEMENTASI MANAJEMEN RISIKO DI SEKTOR PUBLIK', '(tidak diset)', '2020-06-04', NULL, 'SER-PPD/MA/00356/06/2020', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197204271993031003/IMPLEMENTASI_MANAJEMEN_RISIKO_DI_SEKTOR_PUBLIK.pdf', 1, '2026-09-28 02:14:37'),
(31, 6, 'Pelatihan Singkat Tentang Eksekusi Putusan Perdata Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2022-08-26', NULL, '10/BLD//MA-RI/PTY.Eks Pdt/2022', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197204271993031003/Pelatihan_Singkat_Tentang_Eksekusi_Putusan_Perdata_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(32, 6, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-01', NULL, '185/BSDK/PS.KUHP.G1/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197204271993031003/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional.pdf', 1, '2026-09-28 02:14:37'),
(33, 7, 'BIMTEK HAKIM', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2013-05-29', NULL, '(tidak diset)', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(34, 7, 'Hakim Lingkungan Hidup Bagi Hakim Tingkat Pertama dan Banding', 'Bld Mahkamah Agung RI', '2013-10-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(35, 7, 'Pendidikan dan Pelatihan Teknis Fungsional Perkara Terorisme Bagi Hakim Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan Militer Seluruh Indonesia', 'Badan Penelitian dan Pengembangan & Pendidikan dan Pelatihan Hukum dan Peradilan MA RI', '2015-08-29', NULL, '1615 /Bld/MA-RI/2015', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908101990031006/Pendidikan_dan_Pelatihan_Teknis_Fungsional_Perkara_Terorisme_Bagi_Hakim_Tingkat_Pertama_dan_Tingkat_Banding_Peradilan_Um.pdf', 1, '2026-09-28 02:14:37'),
(36, 7, 'Pelatihan Teknis Fungsional Keterbukaan Informasi Publik (KIP)', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2016-02-16', NULL, '21/Bld/MARI/2016', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(37, 7, 'Sistem Peradilan Pidana Anak Lingkunagn Peradilan Umum Seluruh Indonesia', 'gung RI', '2020-11-05', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(38, 7, 'Pelatihan Sertifikasi Hakim Mediator', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2022-11-16', NULL, '102/BLD/MA-RI/MEDIATOR/2022', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908101990031006/Pelatihan_Sertifikasi_Hakim_Mediator.pdf', 1, '2026-09-28 02:14:37'),
(39, 7, 'Pelatihan Sertifikasi Hakim Niaga (Kepailitan dan HAKI)', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2023-05-12', NULL, '77/BLD/MA-RI/Niaga-Kepailitan & PKPU/2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908101990031006/Pelatihan_Sertifikasi_Hakim_Niaga_Kepailitan_dan_HAKI_.pdf', 1, '2026-09-28 02:14:37'),
(40, 7, 'Training Of Tutor dan Training Of Mentor Program Pendidikan Calon Hakim(PPCH) Terpadu Peradilan Umum Angkatan IV', 'Pusat Penelitian dan Pengembangan Hukum dan Peradilan', '2024-02-18', NULL, '346/BLD/MA-RI/TOT-TOM DILUM/2024', 'Mentoring', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908101990031006/Training_Of_Tutor_dan_Training_Of_Mentor_Program_Pendidikan_Calon_Hakim_PPCH_Terpadu_Peradilan_Umum_Angkatan_IV.pdf', 1, '2026-09-28 02:14:37'),
(41, 7, 'Pelatihan Sertifikasi Hakim Tindak Pidana Korupsi Angkatan XXVI Bagi Hakim Karier dan Hakim Adhoc Tingkat Pertama dan Banding seluruh Indonesia', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2024-11-23', NULL, '45/BSDK/MARI/TIPIKOR.XXVI/2024', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908101990031006/Pelatihan_Sertifikasi_Hakim_Tindak_Pidana_Korupsi_Angkatan_XXVI_Bagi_Hakim_Karier_dan_Hakim_Adhoc_Tingkat_Pertama_dan_Ba.pdf', 1, '2026-09-28 02:14:37'),
(42, 7, 'Pelatihan Singkat Eksekusi Putusan Perdata Bagi Hakim Tingkat Pertama Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2024-12-14', NULL, '25/BSDK/PS.Eks Pts Pdt dILUM/2024', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908101990031006/Pelatihan_Singkat_Eksekusi_Putusan_Perdata_Bagi_Hakim_Tingkat_Pertama_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(43, 7, 'Tutor Program Pendidikan Calon Hakim Terpadu Angkatan IV Tahun 2024-2025', 'Badan Strategi Kebijakan Diklat Hukum dan Peradilan', '2025-05-05', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196908101990031006/Tutor_Program_Pendidikan_Calon_Hakim_Terpadu_Angkatan_IV_Tahun_2024-2025.pdf', 1, '2026-09-28 02:14:37'),
(44, 7, 'Bimbingan Teknis Pelayanan Hukum Disabilitas Tahun Anggaran 2025', 'DIREKTORAT JENDERAL BADAN PERADILAN UMUM MA RI', '2025-08-22', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196908101990031006/Bimbingan_Teknis_Pelayanan_Hukum_Disabilitas_Tahun_Anggaran_2025.pdf', 1, '2026-09-28 02:14:37'),
(45, 7, 'Elerning jadi paham gratitikasi itu bukan rejeki', 'Pusat Edukasi Anti Korupsi KPK', '2025-08-28', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196908101990031006/Elerning_jadi_paham_gratitikasi_itu_bukan_rejeki.pdf', 1, '2026-09-28 02:14:37'),
(46, 7, 'DIKLAT CALON HAKIM', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', NULL, NULL, '(tidak diset)', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(47, 7, 'DIKLAT LINGKUNGAN HIDUP', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', NULL, NULL, '(tidak diset)', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(48, 7, 'PELATIHAN YUDISIAL BERKELANJUTAN', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', NULL, NULL, '(tidak diset)', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(49, 7, 'TEKNIS', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', NULL, NULL, '(tidak diset)', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(50, 7, 'Training of Trainer (ToT) Mediasi Hakim dan Panitera Bagi Hakim Peradilan Umum dan Peradilan Agama', 'Badan Penelitian dan Pengembangan & Pendidikan dan Pelatihan Hukum dan Peradilan Mahkamah Agung RI', NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196908101990031006/Training_of_Trainer_ToT_Mediasi_Hakim_dan_Panitera_Bagi_Hakim_Peradilan_Umum_dan_Peradilan_Agama.pdf', 1, '2026-09-28 02:14:37'),
(51, 8, 'PRAJABATAN GOLONGAN III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2001-07-07', NULL, '12-081/PRAJAB III/VII/LAN/2001', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197510282000122002/PRAJABATAN_GOLONGAN_III.pdf', 1, '2026-09-28 02:14:37'),
(52, 8, 'Pelatihan Sertifikasi Hakim Tipikor', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-05-01', NULL, 'No.64/Bld/MA-RI/TIPIKOR-XIX/2018', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197510282000122002/Pelatihan_Sertifikasi_Hakim_Tipikor.pdf', 1, '2026-09-28 02:14:37'),
(53, 8, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-08', NULL, '257/BSDK/PS.KUHP.G2/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197510282000122002/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional_Fitri.pdf', 1, '2026-09-28 02:14:37'),
(54, 8, 'Pendidikan dan Pelatihan Sertifikasi Hakim dan Hakim Ad-Hoc Tipikor', NULL, NULL, NULL, NULL, NULL, NULL, 'Pelatihan', 'uploads/sertifikat/197510282000122002/Pendidikan_dan_Pelatihan_Sertifikasi_Hakim_dan_Hakim_Ad-Hoc_Tipikor.pdf', 1, '2026-09-28 02:14:37'),
(55, 9, 'Pelatihan CALON HAKIM', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2003-10-18', NULL, '057-B/PUSDIKLAT/CAKIM/XVI/2003', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197701312001121002/Pelatihan_CALON_HAKIM.pdf', 1, '2026-09-28 02:14:37'),
(56, 9, 'Workshop Hakim Provinsi Kalsel \'Hukun Persaingan Usaha\'', 'Mahkamah Agung RI Bekerjasama KPPU RI', '2009-10-23', NULL, NULL, 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197701312001121002/Workshop_Hakim_Provinsi_Kalsel_Hukun_Persaingan_Usaha.pdf', 1, '2026-09-28 02:14:37'),
(57, 9, 'PELATIHAN HAKIM PIDANA ANAK', 'Mahkamah Agung RI', '2014-07-03', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197701312001121002/PELATIHAN_HAKIM_PIDANA_ANAK.pdf', 1, '2026-09-28 02:14:37'),
(58, 9, 'PELATIHAN HAKIM TINDAK PIDANA KORUPSI ANGKATAN XVI', 'MAHKAMAH AGUNG RI', '2015-09-08', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197701312001121002/PELATIHAN_HAKIM_TINDAK_PIDANA_KORUPSI_ANGKATAN_XVI.pdf', 1, '2026-09-28 02:14:37'),
(59, 9, 'Short Course/Pendalaman Materi Hak Kekayaan Intelektual(HKI)', 'Mahkamah Agung RI', '2020-11-19', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197701312001121002/Short_Course_Pendalaman_Materi_Hak_Kekayaan_Intelektual_HKI_.pdf', 1, '2026-09-28 02:14:37'),
(60, 10, 'SERTIFIKASI HAKIM LINGKUNGAN HIDUP', 'Mahkamah Agung RI', '2018-05-01', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197609172001122003/SERTIFIKASI_HAKIM_LINGKUNGAN_HIDUP.pdf', 1, '2026-09-28 02:14:37'),
(61, 10, 'SERTIFIKASI MEDIATOR', 'Mahkamah Agung RI', '2021-09-03', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197609172001122003/SERTIFIKASI_MEDIATOR.pdf', 1, '2026-09-28 02:14:37'),
(62, 11, 'DIKLAT PRAJAB GOL. III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2003-11-20', NULL, '15701/DIKLAT PRAJAB III/I.17/LAN/2003', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/DIKLAT_PRAJAB_GOL._III.pdf', 1, '2026-09-28 02:14:37'),
(63, 11, 'Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2004-08-02', NULL, '015-A/PUSDIKLAT/CAKIM/XVII/2004', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(64, 11, 'PELATIHAN YUDISIAL BERKELANJUTAN', 'MAHKAMAH AGUNG RI BEKERJA SAMA DENGAN EUROPEAN UNION', '2008-08-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/PELATIHAN_YUDISIAL_BERKELANJUTAN.pdf', 1, '2026-09-28 02:14:37'),
(65, 11, 'BIMTEK HAKIM TINGKAT PERTAMA DI LINGKUNGAN PERADILAN SE KALIMANTAN BARAT', 'PENGADILAN TINGGI PONTIANAK', '2011-10-26', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/BIMTEK_HAKIM_TINGKAT_PERTAMA_DI_LINGKUNGAN_PERADILAN_SE_KALIMANTAN_BARAT.pdf', 1, '2026-09-28 02:14:37'),
(66, 11, 'PELATIHAN TEKNIS FUNGSIONAL PENGHAPUSAN KEKERASAN DALAM RUMAH TANGGA HAKIM TINGKAR PERTAMA PERADILAN UMUM SELURUH INDONESIA', 'BADAN LITBANG DIKLAT HUKUM DAN PERADILAN MAHKAMAH AGUNG RI', '2013-05-18', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/PELATIHAN_TEKNIS_FUNGSIONAL_PENGHAPUSAN_KEKERASAN_DALAM_RUMAH_TANGGA_HAKIM_TINGKAR_PERTAMA_PERADILAN_UMUM_SELURUH_INDONE.pdf', 1, '2026-09-28 02:14:37'),
(67, 11, 'DIKLAT TEKNIS FUNGSIONAL KOMPUTER FORENSIK LINGKUNGAN PERADILAN UMUM SELURUH INDONESIA', 'BADAN LITBANG DIKLAT HUKUM DAN PERADILAN MAHKAMAH AGUNG RI', '2014-06-06', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/DIKLAT_TEKNIS_FUNGSIONAL_KOMPUTER_FORENSIK_LINGKUNGAN_PERADILAN_UMUM_SELURUH_INDONESIA.pdf', 1, '2026-09-28 02:14:37'),
(68, 11, 'Pelatihan Sertifikasi Hakim Tipikor', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2016-05-24', NULL, '607/Bld/MA-RI/2016', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/Pelatihan_Sertifikasi_Hakim_Tipikor.pdf', 1, '2026-09-28 02:14:37'),
(69, 11, 'Diklat SPPA Kerjasama Mahkamah Agung dan Kejaksaan Agung RI', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2018-04-03', NULL, 'B-781/J/J.3/SPPA-I/04/2018', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/Diklat_SPPA_Kerjasama_Mahkamah_Agung_dan_Kejaksaan_Agung_RI.pdf', 1, '2026-09-28 02:14:37'),
(70, 11, 'TRAINING OF MENTOR I GEL.I PROGRAM PPC TERPADU ANGKATAN 3 LINGKUNGAN PERADILAN UMUM SELURUH INDONESIA', 'BADAN LITBANG DIKLAT HUKUM DAN PERADILAN MAHKAMAH AGUNG RI', '2018-07-27', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/TRAINING_OF_MENTOR_I_GEL.I_PROGRAM_PPC_TERPADU_ANGKATAN_3_LINGKUNGAN_PERADILAN_UMUM_SELURUH_INDONESIA.pdf', 1, '2026-09-28 02:14:37'),
(71, 11, 'Diklat Sertifikasi Hakim Lingkungan Hidup', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-03-08', NULL, 'No.38/Bld/MA-RI/LH-XII/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/Diklat_Sertifikasi_Hakim_Lingkungan_Hidup.pdf', 1, '2026-09-28 02:14:37'),
(72, 11, 'SERTIFIKASI HAKIM LINGKUNGAN HIDUP', 'BADAN LITBANG DIKLAT HUKUM DAN PERADILAN MAHKAMAH AGUNG RI', '2021-03-08', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/SERTIFIKASI_HAKIM_LINGKUNGAN_HIDUP.pdf', 1, '2026-09-28 02:14:37'),
(73, 11, 'Pelatihan Teknis Yustisial Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-06-28', NULL, '43/Bld/MA-RI/TY.PT/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/Pelatihan_Teknis_Yustisial_Hakim.pdf', 1, '2026-09-28 02:14:37'),
(74, 11, 'PELATIHAN TEKNIS YUDISIAL HAK KEKAYAAN INTELEKTUAL', 'BALITBANG DIKLAT KUMDIL MAHKAMAH AGUNG RI', '2021-10-01', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/PELATIHAN_TEKNIS_YUDISIAL_HAK_KEKAYAAN_INTELEKTUAL.pdf', 1, '2026-09-28 02:14:37'),
(75, 11, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-01', NULL, '413/BSDK/PS.KUHP.G1/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional_UU_Nomor_1_Tahun_2023_.pdf', 1, '2026-09-28 02:14:37'),
(76, 11, 'Pelatihan Sertifikasi Niaga Bidang Kepailitan dan PKPU', 'Mahkamah Agung RI', '2025-11-14', NULL, '45/BSDK/SF.Niaga.Kepailitan.PKPU.G2/XI/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/Pelatihan_Sertifikasi_Niaga_Bidang_Kepailitan_dan_PKPU.pdf', 1, '2026-09-28 02:14:37'),
(77, 11, 'Sertifikasi Niaga Bidang Kepailitan dan PKPU', 'Mahkamah Agung RI', '2025-11-14', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/Sertifikasi_Niaga_Bidang_Kepailitan_dan_PKPU.pdf', 1, '2026-09-28 02:14:37'),
(78, 11, 'Pelatihan Sertifikasi Niaga Bidang Hak Kekayaan Intelektual', 'Mahkamah Agung RI', '2025-12-11', NULL, '35/BSDK/SF.Niaga.HKI.G2/XII/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/Pelatihan_Sertifikasi_Niaga_Bidang_Hak_Kekayaan_Intelektual.pdf', 1, '2026-09-28 02:14:37'),
(79, 11, 'Sertifikasi Niaga Bidang HAKI', 'Mahkamah Agung RI', '2025-12-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197801122002121002/Sertifikasi_Niaga_Bidang_HAKI.pdf', 1, '2026-09-28 02:14:37'),
(80, 11, 'PELATIHAN TEKNIS YUDISIAL IMPLEMENTASI KUHAP DAN PENDALAMAN PASAL TERTENTU KUHAP', 'Badan Strajak Diklat Kumdil MA RI', '2026-03-06', NULL, '363/BSDK/TY.KUHAP.UA.G1/III/2026', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/PELATIHAN_TEKNIS_YUDISIAL_IMPLEMENTASI_KUHAP_DAN_PENDALAMAN_PASAL_TERTENTU_KUHAP.pdf', 1, '2026-09-28 02:14:37'),
(81, 11, 'LOKAKARYA PENGKATAN KAPASITAS HAKIM', 'Komisi Yudisial RI', NULL, NULL, '(tidak diset)', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801122002121002/LOKAKARYA_PENGKATAN_KAPASITAS_HAKIM.pdf', 1, '2026-09-28 02:14:37'),
(82, 12, 'PRAJAB GOL.III ANGKATAN I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2003-11-04', NULL, '7452/DIKLAT PRAJAB III/1-II/LAN/2003', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197505162002122001/PRAJAB_GOL.III_ANGKATAN_I.pdf', 1, '2026-09-28 02:14:37'),
(83, 12, 'Diklat Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2004-08-04', NULL, '002-B/PUSDIKLAT/CAKIM/XVII/2004', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197505162002122001/Diklat_Calon_Hakim.pdf', 1, '2026-09-28 02:14:37'),
(84, 12, 'Pelatihan Yudisial Berkelanjutan', 'Mahkamah Agung Bekerjasama dengan European Union', '2008-08-20', NULL, NULL, 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197505162002122001/Pelatihan_Yudisial_Berkelanjutan.pdf', 1, '2026-09-28 02:14:37'),
(85, 12, 'Sosialisasi Penanganan Dugaan Tindak Pidana Perbankan', 'Otoritas Jasa Keuangan', '2017-05-04', NULL, NULL, 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197505162002122001/Sosialisasi_Penanganan_Dugaan_Tindak_Pidana_Perbankan.pdf', 1, '2026-09-28 02:14:37'),
(86, 12, 'Pelatihan Sensifitas Gender Aparat Penegak Hukum Dalam Rangka Perlindungan Perempuan Korban Kekerasan', 'Kementerian Pemberdayaan Perempuan dan Perlindungan Anak Republik Indonesia', '2017-07-17', NULL, NULL, 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197505162002122001/Pelatihan_Sensifitas_Gender_Aparat_Penegak_Hukum_Dalam_Rangka_Perlindungan_Perempuan_Korban_Kekerasan.pdf', 1, '2026-09-28 02:14:37'),
(87, 12, 'Diklat Peradilan Pidana Anak Bagi Aparatur Penegak Hukum', 'Kepala Badan Pengembangan Sumber Daya Manusia Hukum dan Hak Asasi Manusia', '2018-05-03', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(88, 12, 'PELATIHAN SERTIFIKASI HAKIM LINGKUNGAN HIDUP ANGKATAN XIII BAGI HAKIM PERADILAN UMUM, PERADILAN TATA USAHA NEGARA DAN PERADILAN MILITER SELURUH INDONESIA', 'Tahap I : 27 Mei s.d 9 Juni 2021 E-Learning Mandiri', '2021-07-09', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197505162002122001/PELATIHAN_SERTIFIKASI_HAKIM_LINGKUNGAN_HIDUP_ANGKATAN_XIII_BAGI_HAKIM_PERADILAN_UMUM_PERADILAN_TATA_USAHA_NEGARA_DAN_PER.pdf', 1, '2026-09-28 02:14:37'),
(89, 12, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial', 'Badan Penelitian dan Pengembangan dan Pendidikan dan Pelatihan Hukum dan Peradilan Mahkamah Agung RI', '2021-10-08', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197505162002122001/Pelatihan_Sertifikasi_Hakim_Pengadilan_Hubungan_Industrial.pdf', 1, '2026-09-28 02:14:37'),
(90, 12, 'Pelatihan Jarak Jauh Eksplorasi Pelanggaran KEPPH: Studi Kasus Laporan Masyarakat di Komisi Yudisial', 'Komisi Yudisial', '2022-02-11', NULL, '72/STF/PIM/HK.03.01/02/2022', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197505162002122001/Pelatihan_Jarak_Jauh_Eksplorasi_Pelanggaran_KEPPH__Studi_Kasus_Laporan_Masyarakat_di_Komisi_Yudisial.pdf', 1, '2026-09-28 02:14:37'),
(91, 12, 'Pelatihan Singkat Eksekusi Putusan Perdata Bagi Hakim dan Panitera Peradilan Umum Wilayah Hukum PT. Tanjungkarang dan Bengkulu', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2023-04-13', NULL, '17/BLD/MA-RI/PS.Eks Pts Pdt/2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197505162002122001/Pelatihan_Singkat_Eksekusi_Putusan_Perdata_Bagi_Hakim_dan_Panitera_Peradilan_Umum_Wilayah_Hukum_PT._Tanjungkarang_dan_Be.pdf', 1, '2026-09-28 02:14:37'),
(92, 12, 'Diklat Pelatihan Sertifikasi Mediator', 'Badan Penelitian dan Pengembangan dan Pendidikan dan Pelatihan Hukum dan Peradilan Mahkamah Agung RI', '2023-05-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(93, 12, 'HAKIM TINDAK PIDANA PEMILIHAN', NULL, NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197505162002122001/HAKIM_TINDAK_PIDANA_PEMILIHAN.pdf', 1, '2026-09-28 02:14:37'),
(94, 12, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial(1)', NULL, NULL, NULL, NULL, NULL, NULL, 'Pelatihan', 'uploads/sertifikat/197505162002122001/Pelatihan_Sertifikasi_Hakim_Pengadilan_Hubungan_Industrial_1_.pdf', 1, '2026-09-28 02:14:37'),
(95, 13, 'Pendidikan dan Pelatihan Prajabatan Golongan III Angkatan I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2003-11-04', NULL, '70433/DIKLAT PRAJAB III/I.II/LAN/2003', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197610092002121004/Pendidikan_dan_Pelatihan_Prajabatan_Golongan_III_Angkatan_I.pdf', 1, '2026-09-28 02:14:37'),
(96, 13, 'Certificate of Achievment', 'AMERICAN ENGLISH COURSE Indonesia Institute of English Language', '2006-11-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Certificate_of_Achievment.pdf', 1, '2026-09-28 02:14:37'),
(97, 13, 'Penerapan Prinsip dan Kerangka Hukum HAM', 'MAHKAMAH AGUNG RI', '2020-10-19', NULL, NULL, 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197610092002121004/Penerapan_Prinsip_dan_Kerangka_Hukum_HAM.pdf', 1, '2026-09-28 02:14:37'),
(98, 13, 'Penerapan Prinsip dan Kerangka Hukum Hak Asasi Manusia bagi hakim pada peradilan umum', 'Badan Litbang Diklat Kumdil MA RI', '2020-10-19', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Penerapan_Prinsip_dan_Kerangka_Hukum_Hak_Asasi_Manusia_bagi_hakim_pada_peradilan_umum.pdf', 1, '2026-09-28 02:14:37'),
(99, 13, 'Pelatihan Membangun Kapasitas Hakim Dalam Mengadili Tindak Pidana Eksploitasi Seksual Anak (TPESA).', 'MA. RI, ECPAT dan MaPPI', '2020-10-26', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(100, 13, 'Pelatihan Sertifikasi Hakim Lingkungan Hidup angkatan XI', 'Badan Litbang Diklat Kumdil MA RI', '2020-11-09', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Pelatihan_Sertifikasi_Hakim_Lingkungan_Hidup_angkatan_XI.pdf', 1, '2026-09-28 02:14:37'),
(101, 13, 'SERTIFIKASI HAKIM LINGKUNGAN HIDUP untuk Hakim Peradilan Umum', 'Mahkamah Agung RI', '2020-12-21', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/SERTIFIKASI_HAKIM_LINGKUNGAN_HIDUP_untuk_Hakim_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(102, 13, 'Meeting and Presentations for Court Officials Tahun 2021', 'DEPUTI BIDANG PENYELENGGARA PENGEMBANGAN KOMPETENSI', '2021-03-26', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Meeting_and_Presentations_for_Court_Officials_Tahun_2021.pdf', 1, '2026-09-28 02:14:37'),
(103, 13, 'PELATIHAN TERPADU SERTIFIKASI SISTEM PERADILAN PIDANA ANAK (SPPA) GELOMBANG II SELURUH INDONESIA', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-06-15', NULL, '22/Bld/MA-RI/SPPA.GEL.II/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197610092002121004/PELATIHAN_TERPADU_SERTIFIKASI_SISTEM_PERADILAN_PIDANA_ANAK_SPPA_GELOMBANG_II_SELURUH_INDONESIA.pdf', 1, '2026-09-28 02:14:37'),
(104, 13, 'Pelatihan Terpadu Sertifikat Sistem Peradilan Pidana Anak SPPA', 'badan penelitian dan pengembangan pendidikan dan pelatihan hukum dan peradilan MA RI', '2021-06-15', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Pelatihan_Terpadu_Sertifikat_Sistem_Peradilan_Pidana_Anak_SPPA.pdf', 1, '2026-09-28 02:14:37'),
(105, 13, 'Bimbingan Teknis Administrasi Persidangan Sebagai Upaya Mewujudkan Badan Peradilan Yang Agung Pada Satuan Kerja Se-Wilayah Hukum Pengadilan Tinggi Lampung Tahun 2021', 'Ppengadilan Tinggi Tanjung Karang', '2021-11-24', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Bimbingan_Teknis_Administrasi_Persidangan_Sebagai_Upaya_Mewujudkan_Badan_Peradilan_Yang_Agung_Pada_Satuan_Kerja_Se-Wilay.pdf', 1, '2026-09-28 02:14:37'),
(106, 13, 'Pelatihan Jarak Jauh Ekplorasi pelanggaran KEPPH Studi Kasus Laporan Masyarakat Dikomisi Yudisial', 'ketua komisi yudisial RI', '2022-02-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Pelatihan_Jarak_Jauh_Ekplorasi_pelanggaran_KEPPH_Studi_Kasus_Laporan_Masyarakat_Dikomisi_Yudisial.pdf', 1, '2026-09-28 02:14:37'),
(107, 13, 'pelatihan teknis yudisial bukti elektronik bagi hakim peradilan umum dan peradilan agama seluruh indonesia', 'badan penelitian dan pengembangan dan pendidikan dan pelatihan hukum dan peradilan MA RI', '2022-05-20', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/pelatihan_teknis_yudisial_bukti_elektronik_bagi_hakim_peradilan_umum_dan_peradilan_agama_seluruh_indonesia.pdf', 1, '2026-09-28 02:14:37'),
(108, 13, 'Pelatihan Teknis Yudisial Tindak Pidana Narkotika Bagi Hakim Peradilan Umum Seluruh Indonesia', 'Mahkamah Agung RI', '2023-04-06', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Pelatihan_Teknis_Yudisial_Tindak_Pidana_Narkotika_Bagi_Hakim_Peradilan_Umum_Seluruh_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(109, 13, 'Pelatihan SERTIFIKASI HAKIM NIAGA Bidang Kepailitan Dan Penundaan Kewajiban Pembayaran Utang/PKPU Bagi Hakim Peradilan Umum Seluruh Indonesia', 'Badan Penelitian Dan Pengembangan & Pendidikan dan pelatihan Hukum Dan Peradilan Mahkamah Agung RI', '2023-05-12', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Pelatihan_SERTIFIKASI_HAKIM_NIAGA_Bidang_Kepailitan_Dan_Penundaan_Kewajiban_Pembayaran_Utang.pdf', 1, '2026-09-28 02:14:37'),
(110, 13, 'Pelatihan SERTIFIKASI HAKIM NIAGA Bidang Hak Kekayaan Intelektual (HKI) Bagi Hakim Peradilan Umum Seluruh Indonesia', 'Badan Penelitian Dan Pengembangan & Pendidikan Dan Pelatihan Hukum Dan Perdilan Mahkamah Agung RI', '2023-06-23', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Pelatihan_SERTIFIKASI_HAKIM_NIAGA_Bidang_Hak_Kekayaan_Intelektual_HKI_Bagi_Hakim_Peradilan_Umum_Seluruh_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(111, 13, 'The Supreme Court Of The Republic Of Indonesia And Japan International Cooperation Angency', 'JICA Expert', '2023-09-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/The_Supreme_Court_Of_The_Republic_Of_Indonesia_And_Japan_International_Cooperation_Angency.pdf', 1, '2026-09-28 02:14:37'),
(112, 13, 'Training Of Tutor dan Training Of Mentor Program Pendidkan Calon Hakimn (PPCH) Terpadu Peradilan Umum Angkatan IV', 'Mahkamah Agung RI', '2024-02-18', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Training_Of_Tutor_dan_Training_Of_Mentor_Program_Pendidkan_Calon_Hakimn_PPCH_Terpadu_Peradilan_Umum_Angkatan_IV.pdf', 1, '2026-09-28 02:14:37'),
(113, 13, 'Training ESQ pada Pelatihan Sertifikasi Hakim Persaingan Usaha (HPU)', 'ESQ Group', '2024-11-30', NULL, '2299-12027-0023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197610092002121004/Training_ESQ_pada_Pelatihan_Sertifikasi_Hakim_Persaingan_Usaha_HPU_.pdf', 1, '2026-09-28 02:14:37'),
(114, 13, 'Pelatihan SERTIFIKASI HAKIM NIAGA Bidang Hukum Persaingan usaha Bagi Hakim Tingkat Pertama Peradilan Umum Seluruh Indonesia (NIAGA)', 'Mahkamah Agung RI', '2024-12-08', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197610092002121004/Pelatihan_SERTIFIKASI_HAKIM_NIAGA_Bidang_Hukum_Persaingan_usaha_Bagi_Hakim_Tingkat_Pertama_Peradilan_Umum_Seluruh_Indone.pdf', 1, '2026-09-28 02:14:37'),
(115, 13, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-08', NULL, '260/BSDK/PS.KUHP.G2/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197610092002121004/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional_UU_Nomor_1_Tahun_2023_.pdf', 1, '2026-09-28 02:14:37'),
(116, 14, 'Surat Keterangan Hakim Tindak Pidana Pemilihan', 'Mahkamah Agung R.I.', '2015-09-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197709242002122003/Surat_Keterangan_Hakim_Tindak_Pidana_Pemilihan.pdf', 1, '2026-09-28 02:14:37'),
(117, 14, 'Pelatihan Teknis Fungsional Gugatan Sederhana bagi Hakim Lingkungan Peradilan Umum dan Peradilan Agama Seluruh Indonesia', 'Badan Penelitian dan Pengembangan dan Pendidikan dan Pelatihan Hukum dan Peradilan Mahkamah Agung Republik Indonesia', '2020-10-16', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197709242002122003/Pelatihan_Teknis_Fungsional_Gugatan_Sederhana_bagi_Hakim_Lingkungan_Peradilan_Umum_dan_Peradilan_Agama_Seluruh_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(118, 14, 'HAKIM MEDIATOR', 'Puslitbang Diklat Kumdil MA RI', '2023-05-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197709242002122003/HAKIM_MEDIATOR.pdf', 1, '2026-09-28 02:14:37'),
(119, 14, 'Pelatihan Teknis Yudisial Tindak Pidana Penodaan/Penistaan Agama (Blassphemy)', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2023-05-31', NULL, '12/BLD/MA-RI/PTY.Blasphemy/2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197709242002122003/Pelatihan_Teknis_Yudisial_Tindak_Pidana_Penodaan_Penistaan_Agama_Blassphemy_.pdf', 1, '2026-09-28 02:14:37'),
(120, 14, 'Diklat Sertifikasi SPPA', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2023-06-23', NULL, '09/BLD/MA-RI/SPPAGel.1/2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197709242002122003/Diklat_Sertifikasi_SPPA.pdf', 1, '2026-09-28 02:14:37'),
(121, 14, 'Pelatihan Hakim PHI', 'Mahkamah Agung RI', '2023-08-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197709242002122003/Pelatihan_Hakim_PHI.pdf', 1, '2026-09-28 02:14:37'),
(122, 14, 'Surat Keterangan Hakim SPPA', 'MAHKAMAH AGUNG REPUBLIK INDONESIA', '2024-03-25', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197709242002122003/Surat_Keterangan_Hakim_SPPA.pdf', 1, '2026-09-28 02:14:37'),
(123, 15, 'PENDIDIKAN DAN PELATIHAN PRAJABATAN GOLONGAN III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2003-11-04', NULL, '7496/DIKLAT PRAJAB III/I-11/LAN/2003', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197912282002122001/PENDIDIKAN_DAN_PELATIHAN_PRAJABATAN_GOLONGAN_III.pdf', 1, '2026-09-28 02:14:37'),
(124, 15, 'Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2004-11-11', NULL, '033-E/PUSDIKLAT/CAKIM/XVII/2004', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197912282002122001/Calon_Hakim.pdf', 1, '2026-09-28 02:14:37'),
(125, 15, 'Sertifikasi Sistem Peradilan ANAK (SPPA)', 'Mahakamah Agung RI', '2021-06-15', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197912282002122001/Sertifikasi_Sistem_Peradilan_ANAK_SPPA_.pdf', 1, '2026-09-28 02:14:37'),
(126, 15, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-08', NULL, '263/BSDK/PS.KUHP.G2/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197912282002122001/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional.pdf', 1, '2026-09-28 02:14:37'),
(127, 16, 'Diklat Pendidikan dan Pelatihan Prajabatan Golongan III (Diklat Prajab III) Angkatan III Tahun 2003', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2003-10-29', NULL, '14133/DIKLAT PRAJAB III/I.08/LAN/2003', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197703192002122003/Diklat_Pendidikan_dan_Pelatihan_Prajabatan_Golongan_III_Diklat_Prajab_III_Angkatan_III_Tahun_2003.pdf', 1, '2026-09-28 02:14:37'),
(128, 16, 'Pendidikan dan Pelatihan Calon Hakim Departemen Kehakiman dan HAM RI Angkatan XVII Tahun Anggaran 2004', 'Kementrian Kehakiman dan HAM RI', '2004-11-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197703192002122003/Pendidikan_dan_Pelatihan_Calon_Hakim_Departemen_Kehakiman_dan_HAM_RI_Angkatan_XVII_Tahun_Anggaran_2004.pdf', 1, '2026-09-28 02:14:37'),
(129, 17, 'PRAJABATAN GOLONGAN III ANGKATAN I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2003-09-15', NULL, '2163/DIKLAT PRAJAB III/I.16/LAN/2003', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(130, 17, 'PELATIHAN DAN SOSIALISASI PPH BAGI HAKIM TINGKAT PERTAMA', 'Pengadilan Tinggi Kupang', '2012-05-11', NULL, 'W26-U/02/UM.11.01/V/2012', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197701192002121004/PELATIHAN_DAN_SOSIALISASI_PPH_BAGI_HAKIM_TINGKAT_PERTAMA.pdf', 1, '2026-09-28 02:14:37'),
(131, 17, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial (PHI)', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2017-08-29', NULL, 'No.34/Bld/MA-RI/2017', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197701192002121004/Pelatihan_Sertifikasi_Hakim_Pengadilan_Hubungan_Industrial_PHI_.pdf', 1, '2026-09-28 02:14:37'),
(132, 17, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-01', NULL, '414/BSDK/PS.KUHP.G1/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197701192002121004/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional_UU_Nomor_1_Tahun_2023_bagi_Hakim_Pemeriksa_Per.pdf', 1, '2026-09-28 02:14:37'),
(133, 17, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial(PHI)', NULL, NULL, NULL, NULL, NULL, NULL, 'Pelatihan', 'uploads/sertifikat/197701192002121004/Pelatihan_Sertifikasi_Hakim_Pengadilan_Hubungan_Industrial_PHI__2.pdf', 1, '2026-09-28 02:14:37'),
(134, 18, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-08', NULL, '261/BSDK/PS.KUHP.G2/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197807152003121002/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional_UU_Nomor_1_Tahun_2023_bagi_Hakim_Pemeriksa_Per.pdf', 1, '2026-09-28 02:14:37'),
(135, 18, 'Sertifikasi Hakim Anak', 'Mahkamah Agung', NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(136, 18, 'Sertifikasi Mediator', 'Mahkamah Agung', NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(137, 20, 'Pelatihan Teknis Yudisial Eksekusi Putusan perdata', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2022-08-26', NULL, '13/BLD/MA-RI/PTY.EKS.PDT/2022', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197303151992032001/PELATIHAN_TEKNIS_YUDISIAL_EKSEKUSI.pdf', 1, '2026-09-28 02:14:37'),
(138, 20, 'E-Learning Peningkatan Pemahaman Gratifikasi 2025', 'Komisi Pemberantasan Korupsi', '2025-09-04', NULL, '001/IDN/IX/2025', 'Belajar Mandiri', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197303151992032001/elearning.pdf', 1, '2026-09-28 02:14:37'),
(139, 21, 'PEND.PELAT.PRAJABATAN GOLONGAN III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2007-03-17', NULL, '8943/II.31/DIKLAT PRAJAB III/LAN/2007', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197807082006042001/PEND.PELAT.PRAJABATAN_GOLONGAN_III.pdf', 1, '2026-09-28 02:14:37'),
(140, 21, 'Pelatihan Kepemimpinan Pengawas', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2021-06-14', NULL, '00004812/PKP/4007/012/LAN-MA/2021', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197807082006042001/Pelatihan_Kepemimpinan_Pengawas.pdf', 1, '2026-09-28 02:14:37'),
(141, 21, 'Pelatihan teknis yudisial Eksekusi Perdata Bagi Panitera Muda Perdata Dan Jurusita', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2022-07-25', NULL, '21/BLD/MA-RI/PTY.Eksekusi.Pdt/2022', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197807082006042001/Pelatihan_teknis_yudisial_Eksekusi_Perdata_Bagi_Panitera_Muda_Perdata_Dan_Jurusita.pdf', 1, '2026-09-28 02:14:37'),
(142, 23, 'Diklat Prajab Gol.III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2009-03-19', NULL, '6168/II.1/DIKLAT PRAJAB III/LAN/2009', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198010092008051002/Diklat_Prajab_Gol.III.pdf', 1, '2026-09-28 02:14:37'),
(143, 24, 'Diklat prajab gol III angk XIX', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2012-06-12', NULL, '22.503/II.47/DIKLAT PRAJAB III/LAN/2012', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198204152011011005/Diklat_prajab_gol_III_angk_XIX.pdf', 1, '2026-09-28 02:14:37'),
(144, 25, 'PRA JABATAN TK II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1990-11-22', NULL, 'W9-015-II-DL.04.09.TH.1990', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(145, 27, 'Pendidikan dan Pelatihan Prajabatan Golongan III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2009-03-19', NULL, '6.128/II.11/DIKLAT PRAJAB III/LAN/2009', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198310262008012008/Pendidikan_dan_Pelatihan_Prajabatan_Golongan_III.pdf', 1, '2026-09-28 02:14:37'),
(146, 28, 'Pelatihan Panitera Berkelanjutan', 'Mahkamah Agung', '2008-09-01', NULL, '000', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196710201993032005/Pelatihan_Panitera_Berkelanjutan.pdf', 1, '2026-09-28 02:14:37'),
(147, 28, 'Pelatihan Panitera Berkelanjutan 2', NULL, NULL, NULL, NULL, NULL, NULL, 'Pelatihan', 'uploads/sertifikat/196710201993032005/Pelatihan_Panitera_Berkelanjutan_2.pdf', 1, '2026-09-28 02:14:37'),
(148, 29, 'LATIHAN PRA JABATAN TK III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1994-12-28', NULL, 'A6-46.DL.04.09/III/LAT-PRAJAB.DEP-KEH/1994', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(149, 31, 'DIKLAT PARAJABATAN GOL II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1993-02-20', NULL, 'W22-31-DL.04.09.Th.1993', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196911151992032004/DIKLAT_PARAJABATAN_GOL_II.pdf', 1, '2026-09-28 02:14:37'),
(150, 31, 'Pelatihan Teknis Yudisial Panitera/Panitera Pengganti Tingkat Pertama Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2012-03-31', NULL, 'No.06/Bld/MA-RI/2012', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196911151992032004/Pelatihan_Teknis_Yudisial_PaniteraatauPanitera_Pengganti_Tingkat_Pertama_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(151, 32, 'Latihan Pra Jabatan TK II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1993-02-16', NULL, '28/PAN.LPJ/II/PR-92/93', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908051992031004/Latihan_Pra_Jabatan_TK_II.pdf', 1, '2026-09-28 02:14:37');
INSERT INTO `sertifikat_pegawai` (`id_sertifikat`, `id_pegawai`, `nama_sertifikat`, `penyelenggara`, `tanggal_terbit`, `tanggal_kadaluarsa`, `nomor_sertifikat`, `jalur_pelatihan`, `status_validasi`, `jenis`, `file_sertifikat`, `dibuat_oleh`, `created_at`) VALUES
(152, 32, 'Diklat Pim Tingkat IV', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2002-10-01', NULL, '7209/DIKLATPIM TK.IV/LAN/2002', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908051992031004/Diklat_Pim_Tingkat_IV.pdf', 1, '2026-09-28 02:14:37'),
(153, 32, 'Pelatihan Teknis Yudisial Panitera/Panitera Pengganti Tingkat Pertama Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2011-07-08', NULL, 'No.14/Bld/MA-RI/2011', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196908051992031004/Pelatihan_Teknis_Yudisial_PaniteraatauPanitera_Pengganti_Tingkat_Pertama_Peradilan_Umum_kuwat.pdf', 1, '2026-09-28 02:14:37'),
(154, 33, 'DIKLAT PARAJABATAN GOL II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1993-02-17', NULL, 'W9.068.II-DL.04.09 Thun 1993', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197001191992032002/DIKLAT_PARAJABATAN_GOL_II.pdf', 1, '2026-09-28 02:14:37'),
(155, 33, 'Pelatihan Teknis Yudisial Panitera/Panitera Pengganti Tingkat Pertama Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2014-10-03', NULL, 'No.53/Bld/MA-RI/2014', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197001191992032002/Pelatihan_Teknis_Yudisial_PaniteraatauPanitera_Pengganti_Tingkat_Pertama_Peradilan_Umum_yani.pdf', 1, '2026-09-28 02:14:37'),
(156, 34, 'PRAJAB', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1993-03-17', NULL, 'W.12-893.KP.10.10.TH.1993', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197006101992032002/PRAJAB.pdf', 1, '2026-09-28 02:14:37'),
(157, 35, 'PENDIDIKAN DAN PELATIHAN PRAJABATAN GOL.III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2002-11-26', NULL, '3880/DIKLAT PRAJAB III/12/LAN/2002', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197509052001122001/PENDIDIKAN_DAN_PELATIHAN_PRAJABATAN_GOL.III.pdf', 1, '2026-09-28 02:14:37'),
(158, 37, 'DIKLAT PRAJAB GOL III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2007-03-17', NULL, '8939/II.31/DIKLAT PRAJAB III/LAN/2007', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197111102006041001/DIKLAT_PRAJAB_GOL_III.pdf', 1, '2026-09-28 02:14:37'),
(159, 37, 'Bendaharawan', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2010-01-15', NULL, '0104/2.3.1.2.8/03/11/2010', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197111102006041001/Bendaharawan.pdf', 1, '2026-09-28 02:14:37'),
(160, 37, 'Pelatinhan Singkat Panitera / Panitera Muda / Penitera Pengganti Geombang I 4 (empat) Lingkungan Peradilan Selurah Indinesia', 'Badan Peneliti dan Pengembangan & Pendidikan dan Pelatihan Hukum dan Peraduan Mahamah Agung RI', '2022-03-17', NULL, 'No: 395/Bld/MARI/PS.Pan/Panmud/PP-Gel1/2022', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197111102006041001/Pelatinhan_Singkat_Panitera_atau_Panitera_Muda_atau_Penitera_Pengganti_Geombang_4_empat_Lingkungan_Peradilan_Selurah_Ind.pdf', 1, '2026-09-28 02:14:37'),
(161, 38, 'Prajabatan Tingkat I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1993-10-28', NULL, 'W.13-DL.04.09-3936', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196911161993031002/Prajabatan_Tingkat_I.pdf', 1, '2026-09-28 02:14:37'),
(162, 38, 'Sistem Akuntabilitas Kinerja Instansi Pemerintah', 'Diklat Kumdil Mahkamah Agung RI', '2020-02-07', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196911161993031002/Sistem_Akuntabilitas_Kinerja_Instansi_Pemerintah.pdf', 1, '2026-09-28 02:14:37'),
(163, 38, 'Mentor bagi Kepemimpinan Pengawas', 'Mahkamah Agung R.I.', '2020-03-06', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196911161993031002/Mentor_bagi_Kepemimpinan_Pengawas.pdf', 1, '2026-09-28 02:14:37'),
(164, 38, 'Bimbingan Tehnis Kepegawaian dan Perencanaan', 'Pengadilan Tinggi Kalimantan Timur', '2020-10-28', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196911161993031002/Bimbingan_Tehnis_Kepegawaian_dan_Perencanaan.pdf', 1, '2026-09-28 02:14:37'),
(165, 38, 'Bimtek Bidang Sekretariat dibidang Umum', 'Pengadilan Tinggi Yogyakarta', '2021-06-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/196911161993031002/Bimtek_Bidang_Sekretariat_dibidang_Umum.pdf', 1, '2026-09-28 02:14:37'),
(166, 40, 'PEND. DAN PELAT PRAJABATAN GOL III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2010-03-08', NULL, '5078/II.31/DIKLAT PRAJAB III/LAN/2010', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(167, 41, 'Pendidikan dan Pelatihan Prajabatan Golongan II Angkatan IV Tahun 2007', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2007-03-17', NULL, '6.566/DIKLAT PRAJAB II/23/LAN/2007', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197606152006042002/Pendidikan_dan_Pelatihan_Prajabatan_Golongan_II_Angkatan_IV_Tahun_2007.pdf', 1, '2026-09-28 02:14:37'),
(168, 41, 'Bimbingan tehnis Panitera Pengganti dan Jurusita', 'Kepaniteraan', '2013-09-20', NULL, NULL, 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197606152006042002/Bimbingan_tehnis_Panitera_Pengganti_dan_Jurusita.pdf', 1, '2026-09-28 02:14:37'),
(169, 41, 'Pelatihan berkelanjutan untuk Panitera Pengganti & jurusita', 'Kepaniteraan', '2017-12-14', NULL, '00', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197606152006042002/Pelatihan_berkelanjutan_untuk_Panitera_Pengganti_jurusita.pdf', 1, '2026-09-28 02:14:37'),
(170, 41, 'Pelatihan Teknis Fungsional Panitera Pengganti Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2022-04-07', NULL, '408/BLD/MARI/PS.Pan/Panmud/PP-Gel4/2022', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197606152006042002/Pelatihan_Teknis_Fungsional_Panitera_Pengganti_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(171, 42, 'Pendidikan dan pelatihan Prajabatan Gol II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2001-12-10', NULL, 'W8.1157-KP.04.02 Tahun 2002', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197706072000122002/Pendidikan_dan_pelatihan_Prajabatan_Gol_II.pdf', 1, '2026-09-28 02:14:37'),
(172, 43, 'Pelatihan Teknis Fungsional Panitera Pengganti Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2020-11-19', NULL, '85/BLD/MA-RI/T.F.PP.Um/2020', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198508052009122005/Pelatihan_Teknis_Fungsional_Panitera_Pengganti_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(173, 43, 'BIMBINGAN TEKNIS TENTANG PENANGANAN PERKARA PHI', 'Direktorat Jenderal Badan Peradilan Umum', '2024-12-05', NULL, '2233/DJU/DLI.2/XI/2024', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198508052009122005/BIMBINGAN_TEKNIS_TENTANG_PENANGANAN_PERKARA_PHI_rike.pdf', 1, '2026-09-28 02:14:37'),
(174, 44, 'Diklat Prajab Gol III angk XXII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2012-06-12', NULL, '22.632/II.47/DIKLAT PRAJAB III/LAN/2012', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198309172011011008/Diklat_Prajab_Gol_III_angk_XXII.pdf', 1, '2026-09-28 02:14:37'),
(175, 44, 'Pelatihan Teknis Yudisial Panitera/Panitera Pengganti Tingkat Pertama Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2022-04-14', NULL, '389/Bld/MARI/PS.Pan/Panmud/PP-Gel5/2022', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198309172011011008/Pelatihan_Teknis_Yudisial_PaniteraatauPanitera_Pengganti_Tingkat_Pertama_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(176, 45, 'PEND. PELAT PRAJABATAN GOL. III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2012-06-12', NULL, '22522/II.47/DIKLAT PRAJAB III/LAN/2012', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198207062011012009/PEND._PELAT_PRAJABATAN_GOL._III.pdf', 1, '2026-09-28 02:14:37'),
(177, 45, 'BIMBINGAN TEKNIS TENTANG PENANGANAN PERKARA PHI', 'Direktorat Jenderal Badan Peradilan Umum', '2024-12-05', NULL, '2233/DJU/DLI.2/XI/2024', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198207062011012009/BIMBINGAN_TEKNIS_TENTANG_PENANGANAN_PERKARA_PHI.pdf', 1, '2026-09-28 02:14:37'),
(178, 46, 'BIMBINGAN TEKNIS TENTANG PENANGANAN PERKARA PHI', 'Direktorat Jenderal Badan Peradilan Umum', '2024-12-05', NULL, '2233/DJU/DL1.2/XI/2024', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198711122006041001/BIMBINGAN_TEKNIS_TENTANG_PENANGANAN_PERKARA_PHI.pdf', 1, '2026-09-28 02:14:37'),
(179, 47, 'Latihan Prajabatan Tk. I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1993-11-20', NULL, 'W.22-339-DL.04.09 Th. 1993', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197111061993031002/Latihan_Prajabatan_Tk._I.pdf', 1, '2026-09-28 02:14:37'),
(180, 48, 'Pendidikan dan Pelatihan Prajabatan Golongan II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2007-03-13', NULL, '12.282/II/.31/Diklat Prajab II/LAN/2007', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(181, 49, 'DIKLAT PRAJAB GOL III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2016-04-12', NULL, '00003188/DIKLAT PRAJABATAN III/005/3201/LAN/2016', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198803252015032001/DIKLAT_PRAJAB_GOL_III.pdf', 1, '2026-09-28 02:14:37'),
(182, 50, 'PRAJABATAN GOLONGAN II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2010-02-24', NULL, '2.699/II.4/DIKLAT PRAJAB II/LAN/2010', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(183, 51, 'PRAJABATAN GOLL. II ANGKATAN XLVI', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2010-02-20', NULL, '2.181/II.23/DIKLAT PRAJAB II/LAN/2010', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198409262009041004/PRAJABATAN_GOLL._II_ANGKATAN_XLVI.pdf', 1, '2026-09-28 02:14:37'),
(184, 51, 'Bimbingan Teknis Kepaniteraan Pengadilan Negeri', 'Direktorat Jenderal Badan Peradilan Umum', '2023-03-09', NULL, '313/DJU/KP05.8/2/2023', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198409262009041004/Bimbingan_Teknis_Kepaniteraan_Pengadilan_Negeri.pdf', 1, '2026-09-28 02:14:37'),
(185, 52, 'DIKLAT PRAJAB II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1995-01-26', NULL, 'W22-33-DL.04.09 Th.1995', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197306261994031003/DIKLAT_PRAJAB_II.pdf', 1, '2026-09-28 02:14:37'),
(186, 53, 'DIKLAT PRAJAB GOL III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2007-03-17', NULL, '8971/II31/DIKLAT PRAJAB III/LAN/2007', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197508252006042003/DIKLAT_PRAJAB_GOL_III.pdf', 1, '2026-09-28 02:14:37'),
(187, 54, 'DIKLAT PRAJAB GOL III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2009-03-19', NULL, '6122/II.11/DIKLAT PRAJAB III/LAN/2009', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198007072008051001/DIKLAT_PRAJAB_GOL_III.pdf', 1, '2026-09-28 02:14:37'),
(188, 54, 'Diklat Teknis Fungsional Jurusita/Jurusita Pengganti Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2013-04-27', NULL, 'No.08/Bld/MA-RI/2013', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198007072008051001/Diklat_Teknis_Fungsional_Jurusita_Jurusita_Pengganti_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(189, 54, 'PEIATIHAN TEKNIS FUNGSIONAL JURUSITA/JURUSITA PENGGANTI PERADILAN UMUM SELURUH INDONESIA', 'Mahkamah Agung', NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198007072008051001/PEIATIHAN_TEKNIS_FUNGSIONAL_JURUSITA_JURUSITA_PENGGANTI_PERADILAN_UMUM_SELURUH_INDONESIA.pdf', 1, '2026-09-28 02:14:37'),
(190, 56, 'FGD Kepaniteraan di Lingkungan Peradilan Umum', '(tidak diset)', '2022-12-12', NULL, '1350/DJU/KP.05.8/11/2022', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197210041993031005/FGD_Kepaniteraan_di_Lingkungan_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(191, 57, 'Diklat Pra Jabatan bagi CPNS', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2009-03-20', NULL, '7.191/II.36/Diklat Prajab II/LAN/2005', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198001012008052002/Diklat_Pra_Jabatan_bagi_CPNS.pdf', 1, '2026-09-28 02:14:37'),
(192, 57, 'PELATIHAN TEKNIS FUNGSIONAL JURUSITA/ JURUSITA PENGGANTI LINGKUNGAN PERADILAN UMUM SELURUH INDONESIA', 'Mahkamah Agung RI', '2020-10-09', NULL, '03/Bld/MA-RI/T.F.JS/JSP.Um/2O2O', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198001012008052002/PELATIHAN_TEKNIS_FUNGSIONAL_JURUSITA__JURUSITA_PENGGANTI_LINGKUNGAN_PERADILAN_UMUM_SELURUH_INDONESIA.pdf', 1, '2026-09-28 02:14:37'),
(193, 58, 'Pendidikan dan Pelatihan Prajabatan Gol III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2010-03-18', NULL, '8702/II.4/DIKLAT PRAJAB III/LAN/2010', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198209222009042008/Pendidikan_dan_Pelatihan_Prajabatan_Gol_III.pdf', 1, '2026-09-28 02:14:37'),
(194, 58, 'KODE ETIK PANITERA DAN JURUSITA', 'Direktorat Jenderal Badan Peradilan Umum', '2026-02-09', NULL, '336/DJU/DL1.9/II/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198209222009042008/KODE_ETIK_PANITERA_DAN_JURUSITA.pdf', 1, '2026-09-28 02:14:37'),
(195, 58, 'PERDAMAIAN MELALUI MEDIASI', 'Direktorat Jenderal Badan Peradilan Umum', '2026-02-09', NULL, '336/DJU/DL1.9/II/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198209222009042008/PERDAMAIAN_MELALUI_MEDIASI.pdf', 1, '2026-09-28 02:14:37'),
(196, 59, 'DIKLAT PRAJAB GOL II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1990-10-13', NULL, 'W22-341-DL.04.09 Th.1990', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197001171990032001/DIKLAT_PRAJAB_GOL_II.pdf', 1, '2026-09-28 02:14:37'),
(197, 61, 'DIKLAT PRAJAB TK I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1990-10-13', NULL, 'W22-349-DL.04.09 Th.1990', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196812211990031002/DIKLAT_PRAJAB_TK_I.pdf', 1, '2026-09-28 02:14:37'),
(198, 61, 'Ujian Dinas Tk.I', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2017-07-10', NULL, '09/UD.TK.I/2017', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(199, 62, 'Latihan Pra Jabatan Tk.II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '1995-01-26', NULL, 'W22-18-DL.04.09 Th.1995', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Latihan_Pra_Jabatan_Tk.II.pdf', 1, '2026-09-28 02:14:37'),
(200, 62, 'Jurusita', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2004-07-21', NULL, '24/DP/MA-RI/2004', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Jurusita.pdf', 1, '2026-09-28 02:14:37'),
(201, 62, 'Bimbingan Teknis Pengelolaan Perpustakaan dan Kearsipan', 'Direktorat Jenderal Badan Peradilan Agama', '2013-06-05', NULL, 'No.05/S.SPP/BUA.6/Hm.02.2/VI/2013', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Bimbingan_Teknis_Pengelolaan_Perpustakaan_dan_Kearsipan.pdf', 1, '2026-09-28 02:14:37'),
(202, 62, 'Diklat Sekretaris Pengadilan Tingkat Pertama', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2016-09-10', NULL, 'Nomor : 34/Bld/MA-RI/IX/2016', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Diklat_Sekretaris_Pengadilan_Tingkat_Pertama.pdf', 1, '2026-09-28 02:14:37'),
(203, 62, 'Diklat Pim Tingkat IV', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2017-12-21', NULL, '00013958/DIKLATPIM TK.IV/34/3402/LAN/2017', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(204, 62, 'Diklat Pim Tingkat III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2018-09-07', NULL, '00003125/DIKLATPIM TK.III/4007/011/LAN-MA/2018', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(205, 62, 'PELATIHAN PENGELOLAAN BARANG MILIK NEGARA (BMN) BAGI PEGAWAI DI LINGKUNGAN MAHKAMAH AGUNG RI', 'BADAN DIKLAT PEMERIKSAAN KEUANGAN NEGARA BADAN PEMERIKSA KEUANGAN RI', '2023-05-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197308161994031001/Pelatihan_Pengelolaan_Barang_Milik_Negara_BMN_.pdf', 1, '2026-09-28 02:14:37'),
(206, 62, 'Pengelolaan Barang Milik Negara', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2023-05-31', NULL, '371/DPK-E/BDY/05/2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Pengelolaan_Barang_Milik_Negara.pdf', 1, '2026-09-28 02:14:37'),
(207, 62, 'MANAJEMEN RESIKO', 'BADAN PENGAWASAN KEUANGAN DAN PEMBANGUNAN', '2024-07-12', NULL, 'SERT-5048/DL/3/2024', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Manaiemen_Risiko.pdf', 1, '2026-09-28 02:14:37'),
(208, 62, 'E-Learning Peningkatan Pemahaman Gratifikasi 2025', 'Komisi Pemberantasan Korupsi', '2025-09-12', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197308161994031001/E-Learning_Peningkatan_Pemahaman_Gratifikasi_2025.pdf', 1, '2026-09-28 02:14:37'),
(209, 62, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '9213/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian.pdf', 1, '2026-09-28 02:14:37'),
(210, 62, 'DISKUSI PUBLIK', 'KOMISI YUDISIAL RI', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(211, 62, 'Diklat Pim Tingkat III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', NULL, NULL, '(tidak diset)', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Diklat_Pim_Tingkat_III.pdf', 1, '2026-09-28 02:14:37'),
(212, 62, 'Diklat Pim Tingkat IV', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', NULL, NULL, '(tidak diset)', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308161994031001/Diklat_Pim_Tingkat_IV.pdf', 1, '2026-09-28 02:14:37'),
(213, 63, '-', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2009-11-07', NULL, '49/K-PKP RI/B.XI/2009', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/PENDIDIKANatauDIKLAT_PENINGKATAN.pdf', 1, '2026-09-28 02:14:37'),
(214, 63, 'PEND. DAN PELAT. PRAJABATAN GOLONGAN III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2010-03-08', NULL, '5115/II.31/DIKLAT PRAJAB III/LAN/2010', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/PEND._DAN_PELAT._PRAJABATAN_GOLONGAN_III.pdf', 1, '2026-09-28 02:14:37'),
(215, 63, '-', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2010-09-29', NULL, '022o/2.3.1.2.35/03/03/2010', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Pelatihan_Akuntansi_Pemerintahan_pada_Program_Percepatan_Akuntabilitas_Keuangan_Pemerintah_dengan_Pendekatan_e-Training.pdf', 1, '2026-09-28 02:14:37'),
(216, 63, 'Ahli Pengadaan Nasional', 'Lembaga Kebijakan Pengadaan Barang / Jasa Pemerintah', '2013-05-10', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198404102009042016/Ahli_Pengadaan_Nasionalyenny.pdf', 1, '2026-09-28 02:14:37'),
(217, 63, 'Barang dan Jasa', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2013-05-10', NULL, '041321299920692', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Barang_dan_Jasa2.pdf', 1, '2026-09-28 02:14:37'),
(218, 63, 'Diklat Pengadaan Barang/Jasa', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2015-11-17', NULL, 'JFPBJP/VII/0856/11/2015', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Diklat_Pengadaan_BarangatauJasa.pdf', 1, '2026-09-28 02:14:37'),
(219, 63, 'Diklat Kepemimpinan Tingkat IV', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2021-02-03', NULL, '0 0 0 0 4 1 1 6 / P K P / 4 0 0 7 / 0 1 2 / L A N - M A / 2 0 2 0', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Diklat_Kepemimpinan_Tingkat_IV.pdf', 1, '2026-09-28 02:14:37'),
(220, 63, 'Pelatihan Kepemimpinan Pengawas', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2021-02-03', NULL, '0 0 0 0 4 1 1 6 / P K P / 4 0 0 7 / 0 1 2 / L A N - M A / 2 0 2 0', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Pelatihan_Kepemimpinan_Pengawas.pdf', 1, '2026-09-28 02:14:37'),
(221, 63, 'Manajemen Barang Milik Negara', 'Badan Pengawasan Keuangan dan Pembangunan', '2021-03-19', NULL, 'SERT-0613/DL/4/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Manajemen_Barang_Milik_Negara.pdf', 1, '2026-09-28 02:14:37'),
(222, 63, 'Pelatihan Pejabat Pembuat Komitmen Satuan Kerja', 'Kementerian Keuangan', '2021-05-04', NULL, 'SKP-00169/2.3.1.2.058.R/03/03/2021/00169', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Pelatihan_Pejabat_Pembuat_Komitmen_Satuan_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(223, 63, 'Diklat Audit Pengadaan Barang dan Jasa', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2023-04-06', NULL, '230406010463', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Diklat_Audit_Pengadaan_Barang_dan_Jasa.pdf', 1, '2026-09-28 02:14:37'),
(224, 63, 'Diklat Pengadaan Barang/Jasa', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2023-04-06', NULL, '230406010463', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Diklat_Pengadaan_BarangatauJasa2.pdf', 1, '2026-09-28 02:14:37'),
(225, 63, 'Diklat Pengadaan Barang/Jasa', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2023-06-22', NULL, '230620010059', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Diklat_Pengadaan_BarangatauJasa3.pdf', 1, '2026-09-28 02:14:37'),
(226, 63, 'Diklat Pengadaan Barang/Jasa', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2023-09-07', NULL, '230930010232', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Diklat_Pengadaan_BarangatauJasa4.pdf', 1, '2026-09-28 02:14:37'),
(227, 63, 'Manajemen Resiko', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2024-07-12', NULL, 'SERT-5054/DL/3/2024', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Manajemen_Resiko.pdf', 1, '2026-09-28 02:14:37'),
(228, 63, 'Dari Pengalaman Menuju Pengakuan : Optimalisasi Portofolio Sertifikasi PPK Tipe B', 'Lembaga Kebijakan Pengadaan Barang /Jasa Pemerintah', '2026-01-29', NULL, '010730/SERASI-PBJ/LKPP/I/2026', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Dari_Pengalaman_Menuju_Pengakuan_Optimalisasi_Portofolio_Sertifikasi_PPK_Tipe_B.pdf', 1, '2026-09-28 02:14:37'),
(229, 63, 'Bincang Fungsional PBJ: Pahami Kebijakan dan Raih Kompetensinya', 'Lembaga Kebijakan. Pengadaan Barang dan Jasa', '2026-03-11', NULL, '030448/SERASI-PBJ/LKPP/III/2026', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Bincang_Fungsional_PBJ_Pahami_Kebijakan_dan_Raih_Kompetensinya.pdf', 1, '2026-09-28 02:14:37'),
(230, 63, 'PPK Negara Tersertifikasi (PNT)', 'KEMENTERIAN KEUANGAN REPUBLIK INDONESIA', '2026-03-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198404102009042016/PPK_Negara_Tersertifikasi_PNT_.pdf', 1, '2026-09-28 02:14:37'),
(231, 63, 'Pengembangan Kompetensi melalui Pelatihan dan Uji Kompetensi bagi Fungsional Pengelola PBJ berdasarkan Permenpan RB Nomor 6 Tahun 2026', 'Lembaga Kebijakan Pengadaan Barang / Jasa Pemerintah', '2026-06-25', NULL, '061122/SERASI-PBJ/LKPP/VI/2026', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Pengembangan_Kompetensi_melalui_Pelatihan_dan_Uji_Kompetensi_bagi_Fungsional_Pengelola_PBJ_berdasarkan_Permenpan_RB_Nomo.pdf', 1, '2026-09-28 02:14:37'),
(232, 63, 'Penyelenggaraan Sistem Akuntabilitas Kinerja Instansi Pemerintah', NULL, '2026-07-30', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198404102009042016/Penyelenggaraan_Sistem_Akuntabilitas_Kinerja_Instansi_Pemerintah.pdf', 1, '2026-09-28 02:14:37'),
(233, 63, 'Awareness dan Interest Probity Advice Pengadaan Barang / Jasa Pemerintah bagi Advisee', 'Direktorat Advokasi dan Penyelesaian Sanggah Wilayah II LKPP', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Awareness_dan_Interest_Probity_Advice_Pengadaan_Barang_atau_Jasa_Pemerintah_bagi_Advisee.pdf', 1, '2026-09-28 02:14:37'),
(234, 63, 'Indonesia Procurement Forum & Expo', 'Ikatan Ahli Pengadaan Indonesia', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Indonesia_Procurement_Forum_Expo.pdf', 1, '2026-09-28 02:14:37'),
(235, 63, 'Optimizing Procurement in the Digital Economy Era - IPFE', 'Ikatan Ahli Pengadaan Indonesia', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Optimizing_Procurement_in_the_Digital_Economy_Era_-_IPFE.pdf', 1, '2026-09-28 02:14:37'),
(236, 63, 'Peningkatan Kapasitas Stakeholder Pengadaan Terkait Permasalahan Hukum', 'Direktorat Penanganan Permasalahan Hukum LKPP', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Peningkatan_Kapasitas_Stakeholder_Pengadaan_Terkait_Permasalahan_Hukum.pdf', 1, '2026-09-28 02:14:37'),
(237, 63, 'Seminar Nasional Teknologi Terapan 2014 \'Membangun Kedaulatan Bangsa melalui Budaya, Sains danTeknologi\'', 'Sekolah Vokasi Universitas Gajah Mada', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Seminar_Nasional_Teknologi_Terapan_2014_Membangun_Kedaulatan_Bangsa_melalui_Budaya_Sains_danTeknologi.pdf', 1, '2026-09-28 02:14:37'),
(238, 63, 'Seminar on Cybercrime \'Prosecuting Cybercrime : Collecting and Presenting Digital Evidence\'', 'Direktur Jenderal Aplikasi Telematika - KEMKOMINFO', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Seminar_on_Cybercrime_Prosecuting_Cybercrime_Collecting_and_Presenting_Digital_Evidence.pdf', 1, '2026-09-28 02:14:37'),
(239, 63, 'Sosialisasi Peraturan Presiden Nomor 16 Tahun 2018 tentang Pengadaan Barang / Jasa Pemerintah', 'Direktur Pengembangan Strategi dan Kebijakan Pengadaan Umum LKPP', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(240, 63, 'Sosialisasi Sertifikasi Kompetensi Pengadaan Barang / Jasa Pemerintah', 'Direktorat Sertifikasi Profesi - LKPP', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Sosialisasi_Sertifikasi_Kompetensi_Pengadaan_Barang_atau_Jasa_Pemerintah.pdf', 1, '2026-09-28 02:14:37'),
(241, 63, 'Teknisi Komputer', 'Gama Informatika', NULL, NULL, '(tidak diset)', 'Kursus', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Teknisi_Komputer.pdf', 1, '2026-09-28 02:14:37'),
(242, 63, 'Training on Change Management and Leadership', 'LUTAN EDUKASI (MA RI & EU-UNDP SUSTAIN)', NULL, NULL, '(tidak diset)', 'Kursus', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198404102009042016/Training_on_Change_Management_and_Leadership.pdf', 1, '2026-09-28 02:14:37'),
(243, 65, 'PEND. DAN PELAT. PRAJABATAN GOLONGAN III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2012-06-12', NULL, '22590/II.47/DIKLAT PRAJAB III/LAN/2012', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198607242011011005/PEND._DAN_PELAT._PRAJABATAN_GOLONGAN_III.pdf', 1, '2026-09-28 02:14:37'),
(244, 65, 'SERTIFIKAT PELATIHAN PPSPM', 'Badan Pendidikan dan Pelatihan Keuangan', '2023-09-12', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198607242011011005/SERTIFIKAT_PELATIHAN_PPSPM.pdf', 1, '2026-09-28 02:14:37'),
(245, 65, 'PPSPM Negara Tersertifikasi (SNT)', 'Kementrian Keuangan Republik Indonesia', '2023-11-08', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198607242011011005/PPSPM_Negara_Tersertifikasi_SNT_.pdf', 1, '2026-09-28 02:14:37'),
(246, 65, 'Bimbingan Teknis Kepegawaian Bidang Jabatan Fungsional', 'Badan Urusan Administrasi', '2025-12-01', NULL, '12040/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(247, 67, 'Bimbingan Teknis Pengawasan Kearsipan Internal', 'Arsip Nasional Republik Indonesia', '2018-10-05', NULL, 'AK.01.02/1648/2018', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Bimbingan_Teknis_Pengawasan_Kearsipan_Internal.pdf', 1, '2026-09-28 02:14:37'),
(248, 67, 'Pelatihan Pembekalan Arsiparis dan Pengelola Arsip', 'Biro Sumber Daya Manusia dan Umum Kementerian Desa, Pembangunan Daerah Tertinggal dan Transmigrasi', '2019-05-24', NULL, '60/UM.00.66/V/2019', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Pelatihan_Pembekalan_Arsiparis_dan_Pengelola_Arsip.pdf', 1, '2026-09-28 02:14:37'),
(249, 67, 'Membangun Pola Pikir Berkelanjutan dan Kecepatan Pembelajaran', 'Badan Penelitian, Pengembangan, Pendidikan dan Pelatihan Hukum dan Peradilan MA RI', '2021-08-05', NULL, 'SER-EDP/MA/00181/08/2021', 'Pelatihan Sosial Kultural', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Membangun_Pola_Pikir_Berkelanjutan_dan_Kecepatan_Pembelajaran.pdf', 1, '2026-09-28 02:14:37'),
(250, 67, 'Implementasi Pengembangan Kompetensi PNS dalam Sistem Manajemen Kinerja berdasarkan PP 30 Tahun 2019 dan Permen PAN RB Nomor 8 Tahun 2021', 'Pusat Pengembangan Kepegawaian ASN Badan Kepegawaian Negara', '2021-12-21', NULL, '1076/SS-MASN/XII/2021', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Implementasi_Pengembangan_Kompetensi_PNS_dalam_Sistem_Manajemen_Kinerja_berdasarkan_PP_30_Tahun_2019_dan_Permen_PAN_RB_N.pdf', 1, '2026-09-28 02:14:37'),
(251, 67, 'Latihan Dasar CPNS Golongan II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2022-06-28', NULL, '00001508/LATSAR CPNS II/4007/018/LAN-MA/2022', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Latihan_Dasar_CPNS_Golongan_II.pdf', 1, '2026-09-28 02:14:37'),
(252, 67, 'Rakornas Kepegawaian 2022 Birokrasi dan Manajemen ASN di Masa Depan', 'Badan Kepegawaian Negara', '2022-07-21', NULL, '-', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Rakornas_Kepegawaian_2022_Birokrasi_dan_Manajemen_ASN_di_Masa_Depan.pdf', 1, '2026-09-28 02:14:37'),
(253, 67, 'Bimbingan dan Konsultasi Teknis Penilaian Kinerja Jabatan Fungsional Arsiparis', 'Arsip Nasional Republik Indonesia', '2022-09-29', NULL, 'PK.01.04/20/2022', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Bimbingan_dan_Konsultasi_Teknis_Penilaian_Kinerja_Jabatan_Fungsional_Arsiparis.pdf', 1, '2026-09-28 02:14:37'),
(254, 67, 'Islam & Ilmu Terapan Bidang Kearsipan', 'Masjid Kampus Universitas Gadjah Mada', '2023-01-18', NULL, '-', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Islam_Ilmu_Terapan_Bidang_Kearsipan.pdf', 1, '2026-09-28 02:14:37'),
(255, 67, 'Bimbingan Teknis Jabatan Fungsional Arsiparis', 'Badan Urusan Administrasi', '2023-02-28', NULL, '1847/Bua.2/Pp.01.3/2/2023', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Bimbingan_Teknis_Jabatan_Fungsional_Arsiparis.pdf', 1, '2026-09-28 02:14:37'),
(256, 67, 'Seminar Nasional Kearsipan \"Dekonstruksi Peran Arsiparis, Pengenalan dan Prospek Kearsipan Serta Digitalisasi Arsip dan Pengelolaan Arsip Digital\"', 'Nagari Global Trust', '2023-03-11', NULL, '517 -VIP/NGT-13/11.Mar.23', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Seminar_Nasional_Kearsipan_Dekonstruksi_Peran_Arsiparis_Pengenalan_dan_Prospek_Kearsipan_Serta_Digitalisasi_Arsip_dan_Pe.pdf', 1, '2026-09-28 02:14:37'),
(257, 67, 'Seminar Nasional Kearsipan \"Kunci Sukses Pengelolaan Arsip Dinamis\"', 'Nagari Global Trust', '2023-08-05', NULL, '0823.ISTW/ NGT.DIR/SRT/SN.00/VII/2023', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Seminar_Nasional_Kearsipan_Kunci_Sukses_Pengelolaan_Arsip_Dinamis.pdf', 1, '2026-09-28 02:14:37'),
(258, 67, 'Sosialisasi Peraturan Bidang Kearsipan \"Peraturan Arsip Nasional Nomor 1 Tahun 2023 tentang Pedoman Autentikasi Arsip Statis\"', 'Arsip Nasional Republik Indonesia', '2023-08-08', NULL, 'HK.03/285/2023', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Sosialisasi_Peraturan_Bidang_Kearsipan_Peraturan_Arsip_Nasional_Nomor_1_Tahun_2023_tentang_Pedoman_Autentikasi_Arsip_Sta.pdf', 1, '2026-09-28 02:14:37'),
(259, 67, 'Webinar Perpustakaan #8 : Manajemen Arsip di Era Digital', 'PIB College School of Tourism, Digital & Creative Arts', '2023-08-09', NULL, '034/SK-PIB/BL/VIII/2023', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Webinar_Perpustakaan_8_Manajemen_Arsip_di_Era_Digital.pdf', 1, '2026-09-28 02:14:37'),
(260, 67, 'Sosialisasi Penilaian Kinerja Jabatan Fungsional Arsiparis Tahun 2024', 'Arsip Nasional Republik Indonesia', '2024-01-25', NULL, 'BA.00.04/1/2024', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Sosialisasi_Penilaian_Kinerja_Jabatan_Fungsional_Arsiparis_Tahun_2024.pdf', 1, '2026-09-28 02:14:37'),
(261, 67, 'Diskusi Online Pusdipres #6 Eksplorasi Koleksi Foto Berharga Jacob Cass @Koleksi_sejarah_indo', 'Arsip Nasional Republik Indonesia', '2024-03-27', NULL, 'KN.04.01/4/2024', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Diskusi_Online_Pusdipres_6_Eksplorasi_Koleksi_Foto_Berharga_Jacob_Cass_Koleksi_sejarah_indo.pdf', 1, '2026-09-28 02:14:37'),
(262, 67, 'Focus Group Discussion (FGD) dalam rangka konsultasi kebijakan tentang Jabatan Fungsional Arsiparis', 'Arsip Nasional Republik Indonesia', '2024-07-16', NULL, 'KK.00.02/02/2024', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Focus_Group_Discussion_FGD_dalam_rangka_konsultasi_kebijakan_tentang_Jabatan_Fungsional_Arsiparis.pdf', 1, '2026-09-28 02:14:37'),
(263, 67, 'Seminar Transformasi Layanan dan Pemanfaatan Arsip Statis di Indonesia', 'Arsip Nasional Republik Indonesia', '2024-10-10', NULL, 'KP.04.03/6/2024', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Seminar_Transformasi_Layanan_dan_Pemanfaatan_Arsip_Statis_di_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(264, 67, 'Seminar Nasional Urgensi Penataan Arsip dalam Mewujudkan Tata Kelola Administrasi yang Baik', 'Ruang Arsiparis', '2025-07-18', NULL, '152/SM.02/EGURUID/VII/2025', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Seminar_Nasional_Urgensi_Penataan_Arsip_dalam_Mewujudkan_Tata_Kelola_Administrasi_yang_Baik.pdf', 1, '2026-09-28 02:14:37'),
(265, 67, 'Workshop Nasional Tata Kelola Pemberkasan Arsip Aktif, Inaktif, dan Usul Musnah', 'Ruang Arsiparis', '2025-07-25', NULL, '293/DM.02/EGURUID/VII/2025', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Workshop_Nasional_Tata_Kelola_Pemberkasan_Arsip_Aktif_Inaktif_dan_Usul_Musnah.pdf', 1, '2026-09-28 02:14:37'),
(266, 67, 'Bimbingan Teknis Kepegawaian Bidang Jabatan Fungsional', 'Badan Urusan Administrasi', '2025-12-01', NULL, '12042/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', NULL, 1, '2026-09-28 02:14:37'),
(267, 67, 'Community of Practices Seri 4 “Mengulik Keamanan Siber di Bidang Kearsipan”', 'Arsip Nasional Republik Indonesia', '2025-12-03', NULL, 'KP.04.03/321/2025', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Community_of_Practices_Seri_4_U201cMengulik_Keamanan_Siber_di_Bidang_Kearsipan_U201d.pdf', 1, '2026-09-28 02:14:37'),
(268, 67, 'Sosialisasi Sertifikasi SDM Kearsipan Tahun 2026', 'Arsip Nasional Republik Indonesia', '2026-01-26', NULL, 'BA.01.00/ 1 /2025', 'Sosialisasi', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Sosialisasi_Sertifikasi_SDM_Kearsipan_Tahun_2026.pdf', 1, '2026-09-28 02:14:37'),
(269, 67, 'Seminar Nasional Langkah Strategis Menyusun Instrumen Kearsipan untuk Arsip yang Tertib dan Akuntabel', 'e-Guru.id', '2026-05-19', NULL, '268/SM.02/EGURUID/V/2026', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Seminar_Nasional_Langkah_Strategis_Menyusun_Instrumen_Kearsipan_untuk_Arsip_yang_Tertib_dan_Akuntabel.pdf', 1, '2026-09-28 02:14:37'),
(270, 67, 'Bimbingan Teknis Kearsipan Secara Daring dengan tema “Pengelolaan Arsip Inaktif Tidak Teratur”', 'Arsip Nasional Republik Indonesia (ANRI)', '2026-06-11', NULL, 'BA.02.04/50/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199509072020121004/Bimbingan_Teknis_Kearsipan_Secara_Daring_dengan_tema_U201cPengelolaan_Arsip_Inaktif_Tidak_Teratur_U201d.pdf', 1, '2026-09-28 02:14:37'),
(271, 68, 'Diklat Pra Jabatan bagi CPNS', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2009-03-20', NULL, '7.191/II.36/Diklat Prajab II/LAN/2005', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198208182010121002/Diklat_Pra_Jabatan_bagi_CPNS.pdf', 1, '2026-09-28 02:14:37'),
(272, 68, 'PELATIHAN TEKNIS FUNGSIONAL JURUSITA/ JURUSITA PENGGANTI LINGKUNGAN PERADILAN UMUM SELURUH INDONESIA', 'Mahkamah Agung RI', '2020-10-09', NULL, '03/Bld/MA-RI/T.F.JS/JSP.Um/2O2O', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198208182010121002/PELATIHAN_TEKNIS_FUNGSIONAL_JURUSITA__JURUSITA_PENGGANTI_LINGKUNGAN_PERADILAN_UMUM_SELURUH_INDONESIA.pdf', 1, '2026-09-28 02:14:37'),
(273, 69, 'DIKLAT PRAJABATAN', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2016-04-12', NULL, '00003078/DIKLAT PRAJABATAN III/005/3201/LAN/2016', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/DIKLAT_PRAJABATAN.pdf', 1, '2026-09-28 02:14:37'),
(274, 69, 'Bimbingan Teknis Implementasi Sistem Informasi Penelusuran Perkara', 'Pengadilan Tinggi Makassar', '2016-10-26', NULL, '-', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Bimbingan_Teknis_Implementasi_Sistem_Informasi_Penelusuran_Perkara.pdf', 1, '2026-09-28 02:14:37'),
(275, 69, 'E-Learning Pengantar Manajemen Keuangan Negara', 'Kementerian Keuangan', '2020-07-17', NULL, '00065635/110/3005/114/2020', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/E-Learning_Pengantar_Manajemen_Keuangan_Negara.pdf', 1, '2026-09-28 02:14:37'),
(276, 69, 'E-Learning Dasar-Dasar Penyusunan APBN', 'Kementerian Keuangan', '2020-08-14', NULL, '00009072/112/3005/114/2020', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/E-Learning_Dasar-Dasar_Penyusunan_APBN.pdf', 1, '2026-09-28 02:14:37'),
(277, 69, 'E-Learning Prinsip-Prinsip Manajemen Keuangan Satuan Kerja Pemerintah Pusat', 'Kementerian Keuangan', '2020-09-09', NULL, '00016528/121/3005/114/2020', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/E-Learning_Prinsip-Prinsip_Manajemen_Keuangan_Satuan_Kerja_Pemerintah_Pusat.pdf', 1, '2026-09-28 02:14:37'),
(278, 69, 'Basic Video Editing', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2020-09-18', NULL, 'IDS-2020-SEP-BVE-01-11', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Basic_Video_Editing.pdf', 1, '2026-09-28 02:14:37'),
(279, 69, 'Kemenkeu Corpu Talk: Asuransi BMN sebagai Langkah Pengamanan Aset Negara Tahun 2020', 'Pusdiklat Kekayaan Negara dan Perimbangan Keuangan', '2020-09-22', NULL, 'SKP-00232/5.0.0.0.008.R/06/06/2020/00232', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Kemenkeu_Corpu_Talk_Asuransi_BMN_sebagai_Langkah_Pengamanan_Aset_Negara_Tahun_2020.pdf', 1, '2026-09-28 02:14:37'),
(280, 69, 'Pelatihan Kepemimpinan Pengawas', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2021-02-03', NULL, '00004263/PKP/4007/012/LAN-MA/2020', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Pelatihan_Kepemimpinan_PengawasArdi.pdf', 1, '2026-09-28 02:14:37'),
(281, 69, 'Basic Video Production', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2021-02-05', NULL, 'IDS-2021-FEB-BVP-03-00320', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Basic_Video_Production.pdf', 1, '2026-09-28 02:14:37'),
(282, 69, 'Open Class (AP Corner) Kinerja Pelaksanaan Anggaran 2021', 'Pusat Pendidikan dan Pelatihan Anggaran dan Perbendaharaan, Badan Pendidikan dan Pelatihan Keuangan, Kementerian Keuangan', '2021-04-12', NULL, 'SKP-01314/5.0.0.0.035.R/03/03/2021/01314', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Open_Class_AP_Corner_Kinerja_Pelaksanaan_Anggaran_2021.pdf', 1, '2026-09-28 02:14:37'),
(283, 69, 'Crash Program Keringanan Utang: Ayo urus keringanan utang!Lunas hari ini, lega sampai nanti', 'Pusat Pendidikan dan Pelatihan Kekayaan Negara dan Perimbangan Keuangan,Badan Pendidikan dan Pelatihan Keuangan', '2021-04-19', NULL, 'SKP-00842/5.0.0.0.029.R/06/06/2021/00842', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Crash_Program_Keringanan_Utang_Ayo_urus_keringanan_utang_Lunas_hari_ini_lega_sampai_nanti.pdf', 1, '2026-09-28 02:14:37'),
(284, 69, 'Penyusunan Laporan Kinerja Instansi Pemerintah', 'Badan Pengawasan Keuangan dan Pembangunan', '2021-07-09', NULL, 'SERT-4008/DL/4/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Penyusunan_Laporan_Kinerja_Instansi_Pemerintah.pdf', 1, '2026-09-28 02:14:37'),
(285, 69, 'Pengelolaan BMN/BMD - Rakernas Akuntansi dan Pelaporan Keuangan Pemerintah tahun 2021', 'Kementrian Keuangan', '2021-09-08', NULL, '180/CCBMNBMD//2021', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Pengelolaan_BMNatauBMD_-_Rakernas_Akuntansi_dan_Pelaporan_Keuangan_Pemerintah_tahun_2021.pdf', 1, '2026-09-28 02:14:37'),
(286, 69, 'Aplikasi Sakti - Rakernas Akuntansi dan Pelaporan Keuangan Pemerintah tahun 2021', 'Kementrian Keuangan', '2021-09-09', NULL, 'S 396/CC BMN BMD/2021', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Aplikasi_Sakti_-_Rakernas_Akuntansi_dan_Pelaporan_Keuangan_Pemerintah_tahun_2021.pdf', 1, '2026-09-28 02:14:37'),
(287, 69, 'Bimbingan Teknis KesekretariatanTentang Penyusunan Sistem Akuntabilitas Kinerja Instansi Pemerintah (SAKIP)', 'Pengadilan Tinggi Yogyakarta', '2022-11-18', NULL, '-', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/Bimbingan_Teknis_KesekretariatanTentang_Penyusunan_Sistem_Akuntabilitas_Kinerja_Instansi_Pemerintah_SAKIP_.pdf', 1, '2026-09-28 02:14:37'),
(288, 69, 'E-Learning Proses Bisnis Penerapan PIPK', 'Pusat Pendidikan dan Pelatihan Anggaran dan Perbendaharaan, Badan Pendidikan dan Pelatihan Keuangan', '2025-06-06', NULL, 'SKP-00156/2.3.1.2.213.R/03/03/2025/00156', 'E-learning', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/E-Learning_Proses_Bisnis_Penerapan_PIPK.pdf', 1, '2026-09-28 02:14:37'),
(289, 69, 'E-Learning Pengantar PIPK', 'Pusat Pendidikan dan Pelatihan Anggaran dan Perbendaharaan, Badan Pendidikan dan Pelatihan Keuangan, Kementerian Keuangan', '2025-06-11', NULL, 'SKP-00266/2.3.1.1.092.R/03/03/2025/00266', 'E-learning', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198510182015031001/E-Learning_Pengantar_PIPK.pdf', 1, '2026-09-28 02:14:37'),
(290, 70, 'Latihan Dasar CPNS Golongan III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2022-06-14', NULL, '00000178/LATSAR CPNS I/4007/019/LAN-MA/2022', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199510162020122006/Latihan_Dasar_CPNS_Golongan_III.pdf', 1, '2026-09-28 02:14:37'),
(291, 71, 'Latihan Dasar CPNS Golongan III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2022-06-14', NULL, '00000403/LATSAR CPNS I/4007/019/LAN-MA/2022', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199511142020122005/Latihan_Dasar_CPNS_Golongan_III.pdf', 1, '2026-09-28 02:14:37'),
(292, 72, 'DIKLAT PRAJABATAN TINGKAT II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2016-04-25', NULL, '00003080/DIKLAT PRAJABAN II/005/3210/LAN/2016', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198911262015032003/DIKLAT_PRAJABATAN_TINGKAT_II.pdf', 1, '2026-09-28 02:14:37'),
(293, 72, 'BIMBINGAN TEKNIS DAN SOSIALISASI PERATURAN BKN NOMOR 9 TAHUN 2022 TENTANG TATA CARA PELAKSANAAN PENYUSUNAN KEBUTUHAN ASN', 'BADAN KEPEGAWAIAN NEGARA', '2022-08-10', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198911262015032003/BIMBINGAN_TEKNIS_DAN_SOSIALISASI_PERATURAN_BKN_NOMOR_9_TAHUN_2022_TENTANG_TATA_CARA_PELAKSANAAN_PENYUSUNAN_KEBUTUHAN_ASN.pdf', 1, '2026-09-28 02:14:37'),
(294, 72, 'COACHING-MENTORING SEBAGAI METODE PENGEMBANGAN ASN DAN BIMBINGAN TEKNIS PENYUSUNAN SKP TAHUN 2022', 'INSTITUT SENI INDONESIA YOGYAKARTA', '2022-11-02', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198911262015032003/COACHING-MENTORING_SEBAGAI_METODE_PENGEMBANGAN_ASN_DAN_BIMBINGAN_TEKNIS_PENYUSUNAN_SKP_TAHUN_2022.pdf', 1, '2026-09-28 02:14:37'),
(295, 72, 'STRATEGI INTEGRATIF PENGEMBANGAN KOMPETENSI, KINERJA, DAN TALENTA', 'PT.Kata Suhu Kita', '2025-12-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198911262015032003/STRATEGI_INTEGRATIF_PENGEMBANGAN_KOMPETENSI_KINERJA_DAN_TALENTA.pdf', 1, '2026-09-28 02:14:37'),
(296, 73, 'JUNIOR PROGRAMMING', 'LSP TIK INDONESIA', '2020-12-28', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/199711132020121003/JUNIOR_PROGRAMMING.pdf', 1, '2026-09-28 02:14:37'),
(297, 73, 'Latihan Dasar CPNS Golongan II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2021-11-23', NULL, '00020400/LATSAR CPNS II/4007/018/LAN-MA/2021', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199711132020121003/Latihan_Dasar_CPNS_Golongan_II.pdf', 1, '2026-09-28 02:14:37'),
(298, 73, 'Pengembangan Web', 'KOMINFO - PROGATE', NULL, NULL, '(tidak diset)', 'Kursus', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199711132020121003/Pengembangan_Web.pdf', 1, '2026-09-28 02:14:37'),
(299, 74, 'Latihan Dasar CPNS Golongan II', NULL, NULL, NULL, NULL, NULL, NULL, 'Pelatihan', 'uploads/sertifikat/199603252020122003/Latihan_Dasar_CPNS_Golongan_II.pdf', 1, '2026-09-28 02:14:37'),
(300, 75, 'Pelatihan Dasar Calon Pegawai Negeri Sipil', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2024-12-17', NULL, '00012333/LATSAR CPNS III/4007/017/LAN-MA/2024', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199911232024052001/Pelatihan_Dasar_Calon_Pegawai_Negeri_Sipil.pdf', 1, '2026-09-28 02:14:37'),
(301, 76, 'Pelatihan Dasar Calon Pegawai Negeri Sipil Angkatan II Tahun 2024', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2024-12-17', NULL, '00012348/LATSAR CPNS III/4007/017/LAN-MA/2024', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/200010122024052002/Pelatihan_Dasar_Calon_Pegawai_Negeri_Sipil_Angkatan_II_Tahun_2024.pdf', 1, '2026-09-28 02:14:37');
INSERT INTO `sertifikat_pegawai` (`id_sertifikat`, `id_pegawai`, `nama_sertifikat`, `penyelenggara`, `tanggal_terbit`, `tanggal_kadaluarsa`, `nomor_sertifikat`, `jalur_pelatihan`, `status_validasi`, `jenis`, `file_sertifikat`, `dibuat_oleh`, `created_at`) VALUES
(302, 77, 'Pelatihan Dasar Calon Pegawai Negeri Sipil', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2024-12-17', NULL, '00012354/LATSAR CPNS III/4007/017/LAN-MA/2024', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/200007192024051002/Pelatihan_Dasar_Calon_Pegawai_Negeri_Sipil.pdf', 1, '2026-09-28 02:14:37'),
(303, 79, 'PELATIHAN PRAJABATAN CPNS GOLONGAN II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2017-12-12', NULL, '010194/DIKLAT PRAJABATAN II/33/3374/LAN/2017', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197806192014081004/PELATIHAN_PRAJABATAN_CPNS_GOLONGAN_II.pdf', 1, '2026-09-28 02:14:37'),
(304, 79, 'Bimbingan Teknis Kepaniteraan Pengadilan Negeri', 'Direktorat Jenderal Badan Peradilan Umum', '2020-07-24', NULL, '16/BIMTEK03/PTY/VII/2020', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197806192014081004/Bimbingan_Teknis_Kepaniteraan_Pengadilan_Negeri.pdf', 1, '2026-09-28 02:14:37'),
(305, 80, 'Latihan Dasar CPNS Golongan II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2022-11-29', NULL, '00041421/LATSAR CPNS II/3009/018/LAN-KEMENHUB/2022', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199904292022032021/Latihan_Dasar_CPNS_Golongan_II.pdf', 1, '2026-09-28 02:14:37'),
(306, 80, 'Penataan Arsip', 'Badan Nasional Sertifikasi Profesi', NULL, NULL, '(tidak diset)', 'Kursus', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199904292022032021/Penataan_Arsip.pdf', 1, '2026-09-28 02:14:37'),
(307, 81, 'Latihan Dasar CPNS Golongan II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2022-11-29', NULL, '00037539/LATSAR CPNS II/3009/018/LAN-KEMENHUB/2022', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199412122022032011/Latihan_Dasar_CPNS_Golongan_II.pdf', 1, '2026-09-28 02:14:37'),
(308, 81, 'Sertifikat Kompetensi Pengadaan Barang/Jasa Pemerintah', 'Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah', '2023-11-30', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/199412122022032011/Sertifikat_Kompetensi_Pengadaan_BarangJasa_Pemerintah.pdf', 1, '2026-09-28 02:14:37'),
(309, 81, 'Sertifikat Bendahara', 'Kementerian Keuangan Republik Indonesia', '2024-07-01', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/199412122022032011/Sertifikat_Bendahara.pdf', 1, '2026-09-28 02:14:37'),
(310, 81, 'PROFESSIONALS SPEAKING ENGLISH FOR PROFESSIONALS', 'BRITON INTERNATIONAL ENGLISH SCHOOL', NULL, NULL, '(tidak diset)', 'Kursus', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199412122022032011/PROFESSIONALS_SPEAKING_ENGLISH_FOR_PROFESSIONALS.pdf', 1, '2026-09-28 02:14:37'),
(311, 82, 'Latihan Dasar CPNS Golongan II', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2022-11-29', NULL, '00037763/LATSAR CPNS II/3009/018/LAN-KEMENHUB/2022', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199112192022032006/Latihan_Dasar_CPNS_Golongan_II.pdf', 1, '2026-09-28 02:14:37'),
(312, 82, 'Sertifikasi Pengadaan Barang dan Jasa', 'LEMBAGA KEBIJAKAN PENGADAAN BARANG/JASA PEMERINTAH', '2025-11-06', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/199112192022032006/Sertifikasi_Pengadaan_Barang_dan_Jasa.pdf', 1, '2026-09-28 02:14:37'),
(313, 83, 'Pelatihan Dasar Calon Pegawai Negeri Sipil Angkatan II Tahun 2025', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-10-29', NULL, '00027570/LATSAR CPNS III/4007/017/LAN-MA/2025', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/200108022025062018/Pelatihan_Dasar_Calon_Pegawai_Negeri_Sipil_Angkatan_II_Tahun_2025.pdf', 1, '2026-09-28 02:14:37'),
(314, 84, 'Pelatihan Dasar Calon Pegawai Negeri Sipil Angkatan II Tahun 2025', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-10-29', NULL, '00027565/LATSAR CPNS III/4007/017/LAN-MA/2025', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199310022025062003/Pelatihan_Dasar_Calon_Pegawai_Negeri_Sipil.pdf', 1, '2026-09-28 02:14:37'),
(315, 85, 'Pelatihan Dasar Calon Pegawai Negeri Sipil Angkatan VII Tahun 2025', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-10-29', NULL, '00007008/LATSAR CPNS II/4007/018/LAN-MA/2025', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/199703042025062014/Pelatihan_Dasar_Calon_Pegawai_Negeri_Sipil_Angkatan_VII_Tahun_2025.pdf', 1, '2026-09-28 02:14:37'),
(316, 86, 'Pelatihan Sertifikasi Hakim Ad Hoc PHI', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2015-12-19', NULL, '25/BLD/MA-RI/2015', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/1471110904700001/Pelatihan_Sertifikasi_Hakim_Ad_Hoc_PHIHEri.pdf', 1, '2026-09-28 02:14:37'),
(317, 87, 'Sertifikat', 'American Center For International Labour Solidarity', '2002-07-12', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(318, 87, 'Sertifikat', 'Disnakertrans Propinsi DIY', '2002-10-17', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(319, 87, 'Sertifikat', 'ILO/USA', '2002-12-19', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(320, 87, 'Sertifikat', 'American Center For International Labour Solidarity', '2003-09-21', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(321, 87, 'Certificate', 'ILO/USA', '2005-04-23', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(322, 87, 'Sertifikat', 'Komnas HAM', '2005-12-14', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(323, 87, 'PENGHARGAAN', 'Pusat Studi Ekonomi Pencasila UGM', '2006-04-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(324, 87, 'Piagam Penghargaan', 'Universitas Cokroaminoto Yogyakarta', '2008-10-28', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(325, 87, 'Sertifikat', 'Labour Working Group', '2009-10-25', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(326, 87, 'Sertifikat', 'Dirjend Pengawasan & pembinaan Disnakertrans', '2011-03-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(327, 87, 'Pelatihan Sertifikasi Hakim Ad Hoc PHI', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2015-12-19', NULL, 'No.50/Bld/MA-RI/2015', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/3471085612690001/Pelatihan_Sertifikasi_Hakim_Ad_Hoc_PHI.pdf', 1, '2026-09-28 02:14:37'),
(328, 87, 'Sertifikat', 'Kementerian Hukum & HAM', '2018-03-15', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(329, 87, 'Piagam Penghargaan', 'Industrial All', '2023-08-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(330, 87, 'Certifikat Of Appreciation', 'IKAHI', '2024-04-25', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(331, 87, 'PIAGAM', 'American Center For International Labour Solidarity', NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(332, 87, 'Sertifikat', 'Dirjend PHI Depnakertrans', NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(333, 87, 'Surat keterangan', 'Kanwil Depnaker DIY', NULL, NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(334, 89, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial (PHI) Bagi Hakim Karier dan Hakim Adhoc PHI Tingkat Pertama Peradilan Umum Seluruh Indonesia', 'Badan Penelitian dan Pengembangan & Pendidikan dan Pelatihan Hukum fan Peradilan Mahkmah Agung RI', '2023-05-16', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', NULL, 1, '2026-09-28 02:14:37'),
(335, 89, 'Penguatan Teknik Penyelesaian Perselisihan Hubungan Industrial Pada Perusahaan BUMN/BUMD', 'Kementerian Ketengakerjaan Republik Indonesia', '2025-09-17', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/7106096210740001/Penguatan_Teknik_Penyelesaian_Perselisihan_Hubungan_Industrial_Pada_Perusahaan_BUMNatauBUMD.pdf', 1, '2026-09-28 02:14:37'),
(336, 91, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial (PHI)', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2015-10-16', NULL, '1782/BLD/MA-RI/2015', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/1271071003660004/Pelatihan_Sertifikasi_Hakim_Pengadilan_Hubungan_Industrial_PHI_.pdf', 1, '2026-09-28 02:14:37'),
(337, 93, 'PELATIHAN TINDAK PIDANA KORUPSI ANGKATAN XXII BAGI HAKIM KARIER DAN HAKIM AD HOC TINGKAT PERTAMA DAN TINGKAT BANDING LINGKUNGAN PERADILAN UMUM MA RI', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-03-23', NULL, '62/Bld/MA-RI/TIPIKOR-XXII/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/3322195308730001/PELATIHAN_TINDAK_PIDANA_KORUPSI_ANGKATAN_XXII_BAGI_HAKIM_KARIER_DAN_HAKIM_AD_HOC_TINGKAT_PERTAMA_DAN_TINGKAT_BANDING_LIN.pdf', 1, '2026-09-28 02:14:37'),
(338, 94, 'Penyusunan Laporan Kinerja', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2024-06-28', NULL, 'SERT-4402/DL/3/2024', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/3217022704690001/Penyusunan_Laporan_Kinerja.pdf', 1, '2026-09-28 02:14:37'),
(339, 96, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10668/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198107302025212017/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(340, 96, 'Orientasi PPPK Angkatan I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '222/Orientasi PPPK/Akt.I/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198107302025212017/Orientasi_PPPK_Angkatan_I.pdf', 1, '2026-09-28 02:14:37'),
(341, 96, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '7998/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198107302025212017/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(342, 97, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10667/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198708282025211042/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(343, 97, 'Orientasi PPPK Angkatan I', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '288/Orientasi PPPK/Akt.I/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198708282025211042/Orientasi_PPPK_Angkatan_I.pdf', 1, '2026-09-28 02:14:37'),
(344, 97, 'Orientasi Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan Mahkamah Agung RI', '2025-12-17', NULL, NULL, NULL, 'Ditolak - Perbaiki Data', 'Sertifikasi', 'uploads/sertifikat/198708282025211042/Orientasi_Pegawai_Pemerintah_dengan_Perjanjian_Kerja_PPPK_.pdf', 1, '2026-09-28 02:14:37'),
(345, 97, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '6110/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198708282025211042/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(346, 98, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10681/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198412272025211029/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(347, 98, 'ORIENTASI PPPK ANGKATAN VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '236/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198412272025211029/ORIENTASI_PPPK_ANGKATAN_VII.pdf', 1, '2026-09-28 02:14:37'),
(348, 98, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '6984/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198412272025211029/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(349, 99, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10676/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197312172025211016/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(350, 99, 'Orientasi PPPK Angkatan VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '837/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197312172025211016/Orientasi_PPPK_Angkatan_VII.pdf', 1, '2026-09-28 02:14:37'),
(351, 99, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '9184/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197312172025211016/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(352, 100, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10671/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198701202025211030/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(353, 100, 'ORIENTASI PPPK ANGKATAN VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '421/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198701202025211030/ORIENTASI_PPPK_ANGKATAN_VII.pdf', 1, '2026-09-28 02:14:37'),
(354, 101, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10683/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308302025211014/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(355, 101, 'Swajar Pegawai Pemerintah dengan Perjanjian Kerja', 'Direktorat Pembelajaran Karakter dan Sosial Kultural', '2025-12-16', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197308302025211014/Swajar_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(356, 101, 'Orientasi PPPK Angkatan VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '875 / Orientasi PPPK /Akt.VII / BSDK.4 /12 /2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308302025211014/Orientasi_PPPK_Angkatan_VII.pdf', 1, '2026-09-28 02:14:37'),
(357, 101, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '9210/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197308302025211014/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(358, 101, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', NULL, NULL, NULL, NULL, NULL, NULL, 'Pelatihan', 'uploads/sertifikat/197308302025211014/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja_2.pdf', 1, '2026-09-28 02:14:37'),
(359, 102, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10674/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198508312025211035/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(360, 102, 'Orientasi PPPK Angkatan VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '132/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198508312025211035/Orientasi_PPPK_Angkatan_VII.pdf', 1, '2026-09-28 02:14:37'),
(361, 102, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '6771/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198508312025211035/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(362, 103, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10670/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196906272025212006/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(363, 103, 'Orientasi PPPK Angkatan VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '169/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196906272025212006/Orientasi_PPPK_Angkatan_VII.pdf', 1, '2026-09-28 02:14:37'),
(364, 103, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '9482/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/196906272025212006/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(365, 104, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10672/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197305292025211010/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(366, 104, 'Orientasi PPPK Angkatan VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '178/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197305292025211010/Orientasi_PPPK_Angkatan_VII.pdf', 1, '2026-09-28 02:14:37'),
(367, 104, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '9233/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197305292025211010/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(368, 105, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10673/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197511092025211023/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(369, 105, 'ORIENTASI PPPK ANGKATAN VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '109/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197511092025211023/ORIENTASI_PPPK_ANGKATAN_VII.pdf', 1, '2026-09-28 02:14:37'),
(370, 105, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '8994/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197511092025211023/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(371, 106, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10680/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197608072025211028/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(372, 106, 'ORIENTASI PPPK ANGKATAN VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '214/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197608072025211028/ORIENTASI_PPPK_ANGKATAN_VII.pdf', 1, '2026-09-28 02:14:37'),
(373, 106, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '8904/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197608072025211028/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(374, 107, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10678/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198902142025211034/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(375, 107, 'ORIENTASI PPPK ANGKATAN VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '573/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198902142025211034/ORIENTASI_PPPK_ANGKATAN_VII.pdf', 1, '2026-09-28 02:14:37'),
(376, 107, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '5714/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198902142025211034/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(377, 108, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10675/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198107172025211034/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(378, 108, 'Orientasi PPPK Angkatan VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '166/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198107172025211034/Orientasi_PPPK_Angkatan_VII.pdf', 1, '2026-09-28 02:14:37'),
(379, 108, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '8005/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198107172025211034/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(380, 109, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10677/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198307232025211038/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(381, 109, 'ORIENTASI PPPK ANGKATAN VII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '241/Orientasi PPPK/Akt.VII/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198307232025211038/ORIENTASI_PPPK_ANGKATAN_VII.pdf', 1, '2026-09-28 02:14:37'),
(382, 110, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10682/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198109292025211037/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerjasudarmadi.pdf', 1, '2026-09-28 02:14:37'),
(383, 110, 'Orientasi PPPK Angkatan X', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '776/Orientasi PPPK/Akt.X/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198109292025211037/Orientasi_PPPK_Angkatan_Xsudarmadi.pdf', 1, '2026-09-28 02:14:37'),
(384, 110, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '7960/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198109292025211037/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_sudarmadi.pdf', 1, '2026-09-28 02:14:37'),
(385, 111, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10684/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801252025211018/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(386, 111, 'ORIENTASI PPPK ANGKATAN X', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '576/Orientasi PPPK/Akt.X/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801252025211018/ORIENTASI_PPPK_ANGKATAN_X.pdf', 1, '2026-09-28 02:14:37'),
(387, 111, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '8710/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197801252025211018/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(388, 112, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10679/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198412312025211083/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(389, 112, 'Orientasi PPPK Angkatan X', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2025-12-17', NULL, '224/Orientasi PPPK/Akt.X/BSDK.4/12/2025', 'Penataran', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198412312025211083/Orientasi_PPPK_Angkatan_X.pdf', 1, '2026-09-28 02:14:37'),
(390, 112, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '6980/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198412312025211083/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(391, 114, 'Pendidikan dan Pelatihan Prajabatan Golongan III (Diklat Prajab III) Angkatan 30', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2005-07-16', NULL, '9088/DIKLAT PRAJAB III/I.12/LAN/2005', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Pendidikan_dan_Pelatihan_Prajabatan_Golongan_III_Diklat_Prajab_III_Angkatan_30.pdf', 1, '2026-09-28 02:14:37'),
(392, 114, 'Implikasi Undang undang Praktek Kedokteran No. 29 Tahun 2004 Terhadap Praktek Medis', 'Panitia Hari Kesehatan Nasional 41', '2005-11-24', NULL, '00156/ IDI/ WIL.DIY/ SKP/ VI/ 2005', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Implikasi_Undang_undang_Praktek_Kedokteran_No._29_Tahun_2004_Terhadap_Praktek_Medis.pdf', 1, '2026-09-28 02:14:37'),
(393, 114, 'Diklat Calon Hakim Lingkungan Peradilan Umum, Agama, dan TUN', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2006-12-20', NULL, '052/Bld/MA-RI/2006', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Diklat_Calon_Hakim_Lingkungan_Peradilan_Umum_Agama_dan_TUN.pdf', 1, '2026-09-28 02:14:37'),
(394, 114, 'Kegiatan Lokakarya Membangun Komitmen Bersama Dalam Pengelolaan dan Penengakan Hukum di Bidang Lingkungan Hidup Untuk Meuwjudkan Good Governance', 'Komisi Yudisial Republik Indonesia, Pengadilan Tinggi Banjarmasin, dan Universitas Lambung Mangkurat', '2009-03-25', NULL, '-', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Kegiatan_Lokakarya_Membangun_Komitmen_Bersama_Dalam_Pengelolaan_dan_Penengakan_Hukum_di_Bidang_Lingkungan_Hidup_Untuk_Me.pdf', 1, '2026-09-28 02:14:37'),
(395, 114, 'Sosialisasi UU No.32 Tahun 2009 tentang Perlindungan dan Pengelolaan Lingkungan Hidup (2010)', 'Kementerian Lingkungan Hidup Republik Indonesia dan Pengadilan Tinggi Provinsi Kalimantan Selatan', '2010-08-25', NULL, NULL, 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Sosialisasi_UU_No.32_Tahun_2009_tentang_Perlindungan_dan_Pengelolaan_Lingkungan_Hidup_2010_.pdf', 1, '2026-09-28 02:14:37'),
(396, 114, 'Diklat Hakim Berkelanjutan I', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2010-11-26', NULL, '111/Bld/MA-RI/2010', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Diklat_Hakim_Berkelanjutan_I.pdf', 1, '2026-09-28 02:14:37'),
(397, 114, 'Bimbingan Teknis Bagi hakim Pengadilan Negeri di Wilayah Hukum Pengadilan Tinggi banjarmasin Tahun 2011', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2011-04-13', NULL, NULL, 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Bimbingan_Teknis_Bagi_hakim_Pengadilan_Negeri_di_Wilayah_Hukum_Pengadilan_Tinggi_banjarmasin_Tahun_2011.pdf', 1, '2026-09-28 02:14:37'),
(398, 114, 'Diklat Hakim Berkelanjutan Lingkungan Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2011-06-16', NULL, NULL, 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Diklat_Hakim_Berkelanjutan_Lingkungan_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(399, 114, 'Diklat Hakim Berkelanjutan Lingkungan Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2012-10-12', NULL, '44/PP.00.1/PT-KALTENG/X/2012', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Diklat_Hakim_Berkelanjutan_Lingkungan_Peradilan_Umum_3.pdf', 1, '2026-09-28 02:14:37'),
(400, 114, 'Diklat Hakim Berkelanjutan Lingkungan Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2013-10-03', NULL, '068/KPT/PP.00.1/SK/IX/2013', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Diklat_Hakim_Berkelanjutan_Lingkungan_Peradilan_Umum_2.pdf', 1, '2026-09-28 02:14:37'),
(401, 114, 'Pelatihan Sertifikasi Hakim Mediator', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2014-07-18', NULL, '71/Bld/MA-RI/2014', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Pelatihan_Sertifikasi_Hakim_Mediator.pdf', 1, '2026-09-28 02:14:37'),
(402, 114, 'Peningkatan Kapasitas Aparat Penegak Hukum Dalam Pemenuhan Hak Atas Peradilan Yang Fair Bagi Penyandang Disabilitas di Indonesia', 'Pusat Studi Hak Asasi Manusia Universitas Islam Indonesia', '2015-05-21', NULL, NULL, 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Peningkatan_Kapasitas_Aparat_Penegak_Hukum_Dalam_Pemenuhan_Hak_Atas_Peradilan_Yang_Fair_Bagi_Penyandang_Disabilitas_di_I.pdf', 1, '2026-09-28 02:14:37'),
(403, 114, 'Diklat Hakim Berkelanjutan Lingkungan Peradilan Umum', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2015-08-08', NULL, '1283/Bld/MA-RI/2015', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Diklat_Hakim_Berkelanjutan_Lingkungan_Peradilan_Umum_heri.pdf', 1, '2026-09-28 02:14:37'),
(404, 114, 'Workshop Tindak Pidana Pemilu Bagi Hakim Peradilan Umum', 'Kepala Badan Penelitian dan Pengembangan dan Pendidikan dan Pelatihan Hukum dan Peradilan MARI', '2019-03-15', NULL, NULL, 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Workshop_Tindak_Pidana_Pemilu_Bagi_Hakim_Peradilan_Umum.pdf', 1, '2026-09-28 02:14:37'),
(405, 114, 'Pelatihan Teknis Fungsional HKI Bagi Hakim Lingkungan Peradilan Umum Seluruh Indonesia', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2020-10-12', NULL, '646/BLD.3/Dik/S/12/2020', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Pelatihan_Teknis_Fungsional_HKI_Bagi_Hakim_Lingkungan_Peradilan_Umum_Seluruh_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(406, 114, 'Membedah Dunia Forensik Digital', 'PANDI Institute', '2021-03-31', NULL, NULL, 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Membedah_Dunia_Forensik_Digital.pdf', 1, '2026-09-28 02:14:37'),
(407, 114, 'SEKOLAH PASAR MODAL UNTUK NEGERI Aspek Penegakan Hukum Pasar Modal serta Upaya Perlindungan Investor untuk Pertumbuhan Investasi Pasar Modal Indonesia', 'OJK bekerja sama IKAHI', '2021-09-22', NULL, 'SPM-0072/09/2021', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/SEKOLAH_PASAR_MODAL_UNTUK_NEGERI_Aspek_Penegakan_Hukum_Pasar_Modal_serta_Upaya_Perlindungan_Investor_untuk_Pertumbuhan_I.pdf', 1, '2026-09-28 02:14:37'),
(408, 114, 'THE ROLE HUMAN RESOURCES AND INFORMATION TECHNOLOGY TO REALIZE MODERN JUDICIARY IN INTERNASIONAL PERSPECTIVE', 'DEPARTEMEN KEHAKIMAN AMERIKA SERIKAT', '2022-03-17', NULL, '-', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/THE_ROLE_HUMAN_RESOURCES_AND_INFORMATION_TECHNOLOGY_TO_REALIZE_MODERN_JUDICIARY_IN_INTERNASIONAL_PERSPECTIVE.pdf', 1, '2026-09-28 02:14:37'),
(409, 114, 'Pelatihan Teknis bersama Aparat Penegak Hukum dalam Penanganan Perkara Tindak Pidana Perpajakan', 'Pusat Pendidikan dan Pelatihan Pajak, Badan Pendidikan dan Pelatihan Keuangan', '2022-03-25', NULL, '00000382/116/3005/112/2022', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Pelatihan_Teknis_bersama_Aparat_Penegak_Hukum_dalam_Penanganan_Perkara_Tindak_Pidana_Perpajakan.pdf', 1, '2026-09-28 02:14:37'),
(410, 114, 'Pelatihan Terpadu Sistem Peradilan Pidana Anak (SPPA)', 'Badan Pengembangan Sumber Daya Manusia Hukum dan Hak Asasi Manusia', '2022-09-21', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197304212005021003/Pelatihan_Terpadu_Sistem_Peradilan_Pidana_Anak_SPPA_.pdf', 1, '2026-09-28 02:14:37'),
(411, 114, 'Pelatihan Terpadu Sistem Peradilan Pidana Anak (SPPA) Bagi Aparat Penegak Hukum dan Instansi Teknis Lainnya', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2022-09-21', NULL, 'SDM-SM.02.04-011747', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Pelatihan_Terpadu_Sistem_Peradilan_Pidana_Anak_SPPA_Bagi_Aparat_Penegak_Hukum_dan_Instansi_Teknis_Lainnya.pdf', 1, '2026-09-28 02:14:37'),
(412, 114, 'Pelatihan Sertifikasi Hakim PHI', 'Badan Penelitian dan Pengembangan & Pendidikan dan Pelatihan Hukum dan Peradilan Mahkamah Agung RI', '2023-08-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197304212005021003/Pelatihan_Sertifikasi_Hakim_PHI.pdf', 1, '2026-09-28 02:14:37'),
(413, 114, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial (PHI) Berdimensi Kode Etik Dan Pedoman Perilaku Hakim (KE-PPH) Bagi Hakim Karier Dan Hakim AD HOC PHI Tingkat Pertama Peradilan Umum Seluruh ', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2023-08-04', NULL, '36 / BLD MARI-KYRI/PHI/2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Pelatihan_Sertifikasi_Hakim_Pengadilan_Hubungan_Industrial_PHI_.pdf', 1, '2026-09-28 02:14:37'),
(414, 114, 'Progam Mentoring Berbasis Risiko Tindak Pidana Pencucian Uang dan Pendanaan Terorisme dan Tindak Pidana Siber Tahun 2025', 'PUSAT PELAPORAN DAN ANALISIS TRANSAKSI KEUANGAN', '2025-05-08', NULL, 'T/2621/DN.01/4/V/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Progam_Mentoring_Berbasis_Risiko_Tindak_Pidana_Pencucian_Uang_dan_Pendanaan_Terorisme_dan_Tindak_Pidana_Siber_Tahun_2025.pdf', 1, '2026-09-28 02:14:37'),
(415, 114, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-29', NULL, '127/BSDK/PS.KUHP.G3/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197304212005021003/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional_UU_Nomor_1_Tahun_2023_.pdf', 1, '2026-09-28 02:14:37'),
(416, 115, 'Workshop Sosialisasi Sistem Manajemen Anti Penyuapan Tahun 2025', 'Badan Pengawasan', '2025-03-06', NULL, NULL, 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197109011991031002/Workshop_Sosialisasi_Sistem_Manajemen_Anti_Penyuapan_Tahun_2025.pdf', 1, '2026-09-28 02:14:37'),
(417, 116, 'Diklat Prajab III Angkatan III', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2002-11-26', NULL, '8446/DIKLAT PRAJAB III/11/LAN/2002', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197603112001121001/Diklat_Prajab_III_Angkatan_III.pdf', 1, '2026-09-28 02:14:37'),
(418, 116, 'Pendidikan dan Pelatihan Calon Hakim', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2003-10-18', NULL, '050-B/PUSDIKLAT/CAKIM/XVI/2003', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197603112001121001/Pendidikan_dan_Pelatihan_Calon_Hakim.pdf', 1, '2026-09-28 02:14:37'),
(419, 116, 'Pelatihan Terpadu Sertifikasi Sistem Peradilan Pidana Anak (SPPA) Gelombang II', 'Mahkamah Agung', '2021-06-15', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197603112001121001/Pelatihan_Terpadu_Sertifikasi_Sistem_Peradilan_Pidana_Anak_SPPA_Gelombang_II.pdf', 1, '2026-09-28 02:14:37'),
(420, 116, 'Pelatihan Sertifikasi Hakim TIPIKOR Angkatan XXIII', 'Mahkamah Agung', '2021-12-02', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197603112001121001/Pelatihan_Sertifikasi_Hakim_TIPIKOR_Angkatan_XXIII.pdf', 1, '2026-09-28 02:14:37'),
(421, 116, 'Pelatihan Teknis Yudisial Tindak Pidana Kekerasan Seksual Bagi Hakim Peradilan Umum Wilayah Hukum Pengadilan Tinggi Bangka Belitung dan Sumatera Selatan', 'Mahkamah Agung', '2023-03-03', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197603112001121001/Pelatihan_Teknis_Yudisial_Tindak_Pidana_Kekerasan_Seksual_Bagi_Hakim_Peradilan_Umum_Wilayah_Hukum_Pengadilan_Tinggi_Bang.pdf', 1, '2026-09-28 02:14:37'),
(422, 116, 'Penanganan Perkara Berbasis Keadilan Restoratif (Restorative Justice)', 'Direktorat Jenderal Badan Peradilan Umum', '2025-03-18', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/197603112001121001/Penanganan_Perkara_Berbasis_Keadilan_Restoratif_Restorative_Justice_.pdf', 1, '2026-09-28 02:14:37'),
(423, 116, 'Pelatihan Singkat Pendalaman Substansi Dan Kebaruan Hukum Pidana Nasional (UU Nomor 1 Tahun 2023) bagi Hakim Pemeriksa Perkara Pidana Tingkat Pertama dan Tingkat Banding Peradilan Umum dan Peradilan A', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2025-09-01', NULL, '701/BSDK/PS.KUHP.G1/IX/2025', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/197603112001121001/Pelatihan_Singkat_Pendalaman_Substansi_Dan_Kebaruan_Hukum_Pidana_Nasional.pdf', 1, '2026-09-28 02:14:37'),
(424, 117, 'Pendidikan dan Pelatihan Prajabatan Golongan III (diklat Prajab III) Tahun 2010', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2010-03-08', NULL, '5.112/II.31/Diklat Prajab III/LAN/2010', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Pendidikan_dan_Pelatihan_Prajabatan_Golongan_III_diklat_Prajab_III_Tahun_2010.pdf', 1, '2026-09-28 02:14:37'),
(425, 117, 'AHLI PENGADAAN NASIONAL', 'LEMBAGA KEBIJAKAN PENGADAAN BARANG/JASA PEMERINTAH', '2011-05-20', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/AHLI_PENGADAAN_NASIONAL.pdf', 1, '2026-09-28 02:14:37'),
(426, 117, 'Barang dan Jasa', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2011-05-20', NULL, '041135710538339', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Barang_dan_Jasa.pdf', 1, '2026-09-28 02:14:37'),
(427, 117, 'BENDAHARA PENGELUARAN', 'KEMENTERIAN KEUANGAN RI', '2014-04-10', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/BENDAHARA_PENGELUARAN.pdf', 1, '2026-09-28 02:14:37'),
(428, 117, 'Bendaharawan', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2014-04-10', NULL, '2.3.1.2.8.0457', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Bendaharawan.pdf', 1, '2026-09-28 02:14:37'),
(429, 117, 'KOMPETENSI PEJABAT PEMBUAT KOMITMEN', 'KEMENTERIAN KEUANGAN RI', '2020-10-05', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/KOMPETENSI_PEJABAT_PEMBUAT_KOMITMEN.pdf', 1, '2026-09-28 02:14:37'),
(430, 117, 'SERAH TERIMA PEKERJAAN PPK & PPHP / PjPHP', 'DINAS PEKERJAAN UMUM BIDANG BINA JASA KONSTRUKSI PEMKAB. KUTAI TIMUR', '2020-11-04', NULL, '600/241/DPU-BJK/SERTIF/XI/2020', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/SERAH_TERIMA_PEKERJAAN_PPK_PPHP_atau_PjPHP.pdf', 1, '2026-09-28 02:14:37'),
(431, 117, 'Serah Terima Pekerjaan PPK & PPHP/PjHP', 'Dinas Pekerjaan Umum Bidang Bina Jasa Konstruksi Pemerintah Kabupaten Kutai Timur', '2020-11-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Serah_Terima_Pekerjaan_PPK_PPHPatauPjHP.pdf', 1, '2026-09-28 02:14:37'),
(432, 117, 'Peserta Sosialisasi SE No.22/SE/M/2020 Persyaratan Pemilihan dan Evaluasi Dokumen Penawaran Pengadaan Konstruksi', 'Lembaga Pengembangan dan Konsultasi Nasional', '2020-11-05', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Peserta_Sosialisasi_SE_Persyaratan_Pemilihan_dan_Evaluasi_Dokumen_Penawaran_Pengadaan_Konstruksi.pdf', 1, '2026-09-28 02:14:37'),
(433, 117, 'Peserta Rapat Persiapan Pelaksanaan Kontrak Pekerjaan Konstruksi', 'Forum Ahli Kontrak Pemerintah Indonesia', '2020-12-16', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Peserta_Rapat_Persiapan_Pelaksanaan_Kontrak_Pekerjaan_Konstruksi.pdf', 1, '2026-09-28 02:14:37'),
(434, 117, 'Hasil Uji Kompetensi Manajerial dan Sosial Kultura', 'Kementerian Keuangan RI', '2021-01-21', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Hasil_Uji_Kompetensi_Manajerial_dan_Sosial_Kultura.pdf', 1, '2026-09-28 02:14:37'),
(435, 117, 'E-Learning Jabatan Fungsional Pranata dan Analis Pengelolaan Keuangan APBN Angkatan I Tahun 2021', 'Kementerian Keuangan', '2021-06-18', NULL, 'SKP-00261/2.2.1.0.003.R/03/03/2021/00261', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/E-Learning_Jabatan_Fungsional_Pranata_dan_Analis_Pengelolaan_Keuangan_APBN_Angkatan_I_Tahun_2021.pdf', 1, '2026-09-28 02:14:37'),
(436, 117, 'E-Learning Pejabat Pembuat Komitmen Angkatan IV Tahun 2021', 'Kementerian Keuangan', '2021-07-02', NULL, 'SKP-00520/2.3.1.1.083.R/03/03/2021/00520', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/E-Learning_Pejabat_Pembuat_Komitmen_Angkatan_IV_Tahun_2021.pdf', 1, '2026-09-28 02:14:37'),
(437, 117, 'E-Learning Verifikasi Tagihan Belanja Barang Non Pegawai', 'Kementerian Keuangan', '2021-09-08', NULL, 'SKP-00859/2.3.1.2.044.R/03/03/2021/00859', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/E-Learning_Verifikasi_Tagihan_Belanja_Barang_Non_Pegawai.pdf', 1, '2026-09-28 02:14:37'),
(438, 117, 'E-Learning Verifikasi Tagihan Belanja Perjalanan Dinas Dalam Negeri', 'Kementerian Keuangan', '2021-09-08', NULL, 'SKP-00889/2.3.1.2.042.R/03/03/2021/00889', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/E-Learning_Verifikasi_Tagihan_Belanja_Perjalanan_Dinas_Dalam_Negeri.pdf', 1, '2026-09-28 02:14:37'),
(439, 117, 'E-Learning Verifikasi Tagihan Belanja Perjalanan Dinas Luar Negeri Periode 21 Juli s.d. 20 Agustus 2021 Tahun 2021', 'Kementerian Keuangan', '2021-09-17', NULL, 'SKP-00268/2.3.1.2.043.R/03/03/2021/00268', 'Pelatihan Fungsional', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/E-Learning_Verifikasi_Tagihan_Belanja_Perjalanan_Dinas_Luar_Negeri_Periode_21_Juli_s.d._20_Agustus_2021_Tahun_2021.pdf', 1, '2026-09-28 02:14:37'),
(440, 117, 'Sertifikasi Kompetensi PPSPM', 'Direktur Jenderal Perbendaharaan Kementerian Keuangan RI', '2021-12-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Sertifikasi_Kompetensi_PPSPM.pdf', 1, '2026-09-28 02:14:37'),
(441, 117, 'Diklat Teknis Manajemen ASN', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2023-03-20', NULL, '0014/ PT - MASN / III / 2023', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Diklat_Teknis_Manajemen_ASN.pdf', 1, '2026-09-28 02:14:37'),
(442, 117, 'Sertifikat Agen Perubahan Periode Tahun 2023', 'Pengadilan Negeri Wates', '2024-02-01', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Sertifikat_Agen_Perubahan_Periode_Tahun_2023.pdf', 1, '2026-09-28 02:14:37'),
(443, 117, 'PELATIHAN KOMPETENSI TEKNIS MANAJEMEN ASN JENJANG PENGAWAS/JABATAN SETARA', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2024-05-31', NULL, '0085/PT – MASN/P U S B A N G P E G A S N - B K N /V/2024', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/PELATIHAN_KOMPETENSI_TEKNIS_MANAJEMEN_ASN_JENJANG_PENGAWASatauJABATAN_SETARA.pdf', 1, '2026-09-28 02:14:37'),
(444, 117, 'Sertifikasi Kompetensi Teknis Manajemen ASN Jenjang Pengawas/Setara', 'Badan Kepegawaian Negara', '2024-07-30', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Sertifikasi_Kompetensi_Teknis_Manajemen_ASN_Jenjang_PengawasatauSetara.pdf', 1, '2026-09-28 02:14:37'),
(445, 117, 'PENGELOLAAN ARSIP PEJABAT NEGARA DAN ARSIP PERKARA', 'Badan Peradilan Umum MA RI', '2024-10-10', NULL, '1968/DJU/DL1.10/X/2024', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/PENGELOLAAN_ARSIP_PEJABAT_NEGARA_DAN_ARSIP_PERKARA.pdf', 1, '2026-09-28 02:14:37'),
(446, 117, 'BIMBINGAN TEKNIS DESAIN GRAFIS BERBASIS CANVA \'Meningkatkan Efektivitas Informasi Layanan Publik Serta Mendukung Dokumentasi dan Publikasi\'', 'Pengadilan Negeri Wates', '2025-06-13', NULL, '-', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/BIMBINGAN_TEKNIS_DESAIN_GRAFIS_BERBASIS_CANVA_Meningkatkan_Efektivitas_Informasi_Layanan_Publik_Serta_Mendukung_Dokument.pdf', 1, '2026-09-28 02:14:37'),
(447, 117, 'Sharing Session dengan tema ”Menjaga Integritas Status dan Kedudukan ASN di Era Dinamis”', 'Badan Kepegawaian Negara RI', '2025-06-19', NULL, '7437/KA.01.13/SS-SKKASN/VI/2025', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Sharing_Session_dengan_tema_U201dMenjaga_Integritas_Status_dan_Kedudukan_ASN_di_Era_Dinamis_U201d.pdf', 1, '2026-09-28 02:14:37'),
(448, 117, 'Sharing Session dengan tema \'Sharing Inovasi Berdampak : Dari Regional untuk ASN yang Melayani dan Menggerakkan\'', 'Badan Kepegawaian Negara RI', '2025-07-03', NULL, '2430/KA.01.15/SS-INOVKRX/VII/2025', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Sharing_Session_dengan_tema_Sharing_Inovasi_Berdampak_Dari_Regional_untuk_ASN_yang_Melayani_dan_Menggerakkan.pdf', 1, '2026-09-28 02:14:37'),
(449, 117, 'Sharing Session dengan tema “Best Practice Implementasi Sistem Merit”', 'Badan Kepegawaian Negara', '2025-07-24', NULL, '1767/KA.01.18/SS-BPISM/VII/2025', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Sharing_Session_dengan_tema_U201cBest_Practice_Implementasi_Sistem_Merit_U201d.pdf', 1, '2026-09-28 02:14:37');
INSERT INTO `sertifikat_pegawai` (`id_sertifikat`, `id_pegawai`, `nama_sertifikat`, `penyelenggara`, `tanggal_terbit`, `tanggal_kadaluarsa`, `nomor_sertifikat`, `jalur_pelatihan`, `status_validasi`, `jenis`, `file_sertifikat`, `dibuat_oleh`, `created_at`) VALUES
(450, 117, 'Sharing Session dengan tema “AI dalam Ekosistem Kerja ASN: Dari Administrasi Menuju Inovasi”', 'Badan Kepegawaian Negara', '2025-08-07', NULL, '2580/KA.01.20/SS-AIASNKOMDIGI/VIII/2025', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Sharing_Session_dengan_tema_AI_dalam_Ekosistem_Kerja_ASN_Dari_Administrasi_Menuju_Inovasi.pdf', 1, '2026-09-28 02:14:37'),
(451, 117, 'Sharing Session dengan tema “ASN Perekat Bangsa Melalui Aktualisasi Nilai-Nilai Pancasila”', 'Badan Kepegawaian Negara', '2025-08-14', NULL, '1404/KA.01.21/SS-ASNPENAPANCASILA/VIII/2025', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Sharing_Session_dengan_tema_ASN_Perekat_Bangsa_Melalui_Aktualisasi_Nilai-Nilai_Pancasila.pdf', 1, '2026-09-28 02:14:37'),
(452, 117, 'Sharing Session dengan tema “Smart Talent Management: Pemanfaatan AI untuk Membangun ASN Masa Depan\'', 'Badan Kepegawaian Negara', '2025-10-31', NULL, '9834/KA.01.25/SS-STM-AI-ASN/IX/2025', 'Pelatihan Manajerial', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Sharing_Session_dengan_tema_Smart_Talent_Management_Pemanfaatan_AI_untuk_Membangun_ASN_Masa_Depan.pdf', 1, '2026-09-28 02:14:37'),
(453, 117, 'Bimbingan Teknis Manajemen Pegawai Pemerintah dengan Perjanjian Kerja', 'Badan Urusan Administrasi', '2025-12-01', NULL, '10647/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Bimbingan_Teknis_Manajemen_Pegawai_Pemerintah_dengan_Perjanjian_Kerja.pdf', 1, '2026-09-28 02:14:37'),
(454, 117, 'SDM HANDAL DAN BERKOMPETEN DALAM RANGKA MEMBANGUN MANAJEMEN TALENTA', 'Badan Urusan Administrasi', '2025-12-19', NULL, '18208/BUA.2/KP3.4.2/XII/2025', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/SDM_HANDAL_DAN_BERKOMPETEN_DALAM_RANGKA_MEMBANGUN_MANAJEMEN_TALENTA.pdf', 1, '2026-09-28 02:14:37'),
(455, 117, 'Sertifikasi Kompetensi PPK (PNT)', 'KEMENTERIAN KEUANGAN REPUBLIK INDONESIA', '2025-12-31', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198702182009042004/Sertifikasi_Kompetensi_PPK_PNT_.pdf', 1, '2026-09-28 02:14:37'),
(456, 117, 'Jadi Paham Konflik Kepentingan', 'KPK', '2026-05-18', NULL, '487/64/05/2026', 'E-learning', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Jadi_Paham_Konflik_Kepentingan.pdf', 1, '2026-09-28 02:14:37'),
(457, 117, 'the Microsoft Elevate AI Training Session : GARUDA AI For Microsoft Elevate - AI For Public Impact', 'Microsoft Indonesia', '2026-05-20', NULL, '0520180/II/API/5956/2026', 'E-learning', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/the_Microsoft_Elevate_AI_Training_Session_GARUDA_AI_For_Microsoft_Elevate_-_AI_For_Public_Impact.pdf', 1, '2026-09-28 02:14:37'),
(458, 117, 'the Microsoft Elevate AI Training Session : GARUDA AI For Microsoft Elevate - AI Policy Lab', 'Microsoft Indonesia', '2026-05-25', NULL, '05251334/II/APL/7546/2026', 'E-learning', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/the_Microsoft_Elevate_AI_Training_Session_GARUDA_AI_For_Microsoft_Elevate_-_AI_Policy_Lab.pdf', 1, '2026-09-28 02:14:37'),
(459, 117, 'Pelatihan AI Fundamental', 'BKN', '2026-05-31', NULL, '0366/PAI.1.1/PUSBANGSDM/V/2026', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Pelatihan_AI_Fundamental.pdf', 1, '2026-09-28 02:14:37'),
(460, 117, 'Pelatihan AI untuk Automasi Proses and Data Analytics', 'BKN', '2026-05-31', NULL, '0288/PAI.1.2/PUSBANGSDM/V/2026', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Pelatihan_AI_untuk_Automasi_Proses_and_Data_Analytics.pdf', 1, '2026-09-28 02:14:37'),
(461, 117, 'Pelatihan AI untuk Monitoring, Anomaly Detection dan Risk Based-Audit', 'BKN', '2026-05-31', NULL, '0301/PAI.1.3/PUSBANGSDM/V/2026', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Pelatihan_AI_untuk_Monitoring_Anomaly_Detection_dan_Risk_Based-Audit.pdf', 1, '2026-09-28 02:14:37'),
(462, 117, 'Peningkatan Pemahaman Gratifikasi', 'KPK', '2026-06-24', NULL, '7186/245/06/2026', 'E-learning', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Peningkatan_Pemahaman_Gratifikasi.pdf', 1, '2026-09-28 02:14:37'),
(463, 117, 'Kegiatan Pendampingan dan Penjelasan terkait Layanan Status dan Pemberhentian Aparatur Sipil Negara (ASN)', 'Badan Urusan Administrasi', '2026-07-28', NULL, '6287/BUA.2/KP3.4.2/VII/2026', 'Bimbingan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198702182009042004/Kegiatan_Pendampingan_dan_Penjelasan_terkait_Layanan_Status_dan_Pemberhentian_Aparatur_Sipil_Negara_ASN_.pdf', 1, '2026-09-28 02:14:37'),
(464, 118, 'Penddikan dan Pelatihan Prajabatn Gol III Angk. XXII', 'Pusat Pendidikan dan Pelatihan Manajemen dan Kepemimpinan', '2011-03-25', NULL, '6.177/II.47/DIKLAT PRAJAB III/LAN/2011', 'Pelatihan Struktural Kepemimpinan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Penddikan_dan_Pelatihan_Prajabatn_Gol_III_Angk._XXII.pdf', 1, '2026-09-28 02:14:37'),
(465, 118, 'Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2011-04-08', NULL, '38/Bld/MA-RI/2011', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Calon_Hakim.pdf', 1, '2026-09-28 02:14:37'),
(466, 118, 'Pendidikan dan Pelatihan Kode Etik dan Pedoman Perilaku Hakim', 'Mahkamah Agung Republik Indonesia', '2011-10-03', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198602192009121003/Pendidikan_dan_Pelatihan_Kode_Etik_dan_Pedoman_Perilaku_Hakim.pdf', 1, '2026-09-28 02:14:37'),
(467, 118, 'Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2011-11-30', NULL, '04/Bld/MA-RI/2011', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Calon_Hakim_2.pdf', 1, '2026-09-28 02:14:37'),
(468, 118, 'Pelatihan Sertifikasi Mediator Calon Hakim Angkatan VI Peradilan Umum Seluruh Indonesia', 'Mahkamah Agung Republik Indonesia', '2012-08-11', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198602192009121003/Pelatihan_Sertifikasi_Mediator_Calon_Hakim_Angkatan_VI_Peradilan_Umum_Seluruh_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(469, 118, 'Calon Hakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2012-09-16', NULL, '14/Bld/MA-RI/2012', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Calon_Hakim_3.pdf', 1, '2026-09-28 02:14:37'),
(470, 118, 'Awareness and Documentation ICPE & ISO 9001 : 2015', 'PT. Proxi@MaxEarn Konsultan dan Pengadilan Negeri Gianyar', '2017-01-06', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198602192009121003/Awareness_and_Documentation_ICPE_ISO_9001_2015.pdf', 1, '2026-09-28 02:14:37'),
(471, 118, '\'Kode Etik dan Pedoman Perilaku Hakim\' bagi Hakim dengan Masa Kerja 0-8 Tahun', 'Komisi Yudisial Republik Indonesia', '2018-08-04', NULL, NULL, NULL, 'Tervalidasi', 'Sertifikasi', 'uploads/sertifikat/198602192009121003/Kode_Etik_dan_Pedoman_Perilaku_Hakim_bagi_Hakim_dengan_Masa_Kerja_0-8_Tahun.pdf', 1, '2026-09-28 02:14:37'),
(472, 118, 'Diklat Pembekalan Mentor Program Magang Cakim', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2018-08-10', NULL, '123/Bld/MA-RI/TOM I-PPC/2018', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Diklat_Pembekalan_Mentor_Program_Magang_Cakim.pdf', 1, '2026-09-28 02:14:37'),
(473, 118, 'Diklat Sertifikasi Hakim Lingkungan Hidup', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-07-09', NULL, '77/Bld/MA-RI/LH-XIII/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Diklat_Sertifikasi_Hakim_Lingkungan_Hidup.pdf', 1, '2026-09-28 02:14:37'),
(474, 118, 'Pelatihan Sertifikasi Hakim Pengadilan Hubungan Industrial (PHI)', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-10-08', NULL, '78/Bld/MA-RI/PHI/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Pelatihan_Sertifikasi_Hakim_Pengadilan_Hubungan_Industrial_PHI_.pdf', 1, '2026-09-28 02:14:37'),
(475, 118, 'Pelatihan Sertifikasi Hakim Tipikor', 'Pusat Pendidikan dan Pelatihan Tenaga Teknis Peradilan', '2021-12-02', NULL, '03/Bld/MA-RI/TIPIKOR-XXIII/2021', 'Pelatihan Teknis', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Pelatihan_Sertifikasi_Hakim_Tipikor.pdf', 1, '2026-09-28 02:14:37'),
(476, 118, '\'Membangun Komunikasi Kemitraan Pengadilan\' Competency Based Training Mahkamah Agung Republik Indonesia', 'London School Academy', NULL, NULL, '(tidak diset)', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Membangun_Komunikasi_Kemitraan_Pengadilan_Competency_Based_Training_Mahkamah_Agung_Republik_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(477, 118, '\'Membangun Tim Komunikasi Pengadilan\' Competency Based Training Mahkamah Agung Republik Indonesia', 'London School Academy', NULL, NULL, '(tidak diset)', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Membangun_Tim_Komunikasi_Pengadilan_Competency_Based_Training_Mahkamah_Agung_Republik_Indonesia.pdf', 1, '2026-09-28 02:14:37'),
(478, 118, 'Implementasi Kode Etik & Pedoman Perilaku Hakim', 'Kementerian Dalam Negeri Republik Indonesia, Konrad Adenauer Stiftung, dan Jimly School Law and Government', NULL, NULL, '(tidak diset)', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Implementasi_Kode_Etik_Pedoman_Perilaku_Hakim.pdf', 1, '2026-09-28 02:14:37'),
(479, 118, 'Intellectual Property Short Course', 'Supreme Court of the Republic of Indonesia and Japan International Cooperation Agency', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Intellectual_Property_Short_Course.pdf', 1, '2026-09-28 02:14:37'),
(480, 118, 'Live Training Webinar Indikator Kerja Utama Gelombang III', 'PPM Manajemen dan Badan Litbang Diklat Kumdil Mahkamah Agung Republik Indonesia', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Live_Training_Webinar_Indikator_Kerja_Utama_Gelombang_III.pdf', 1, '2026-09-28 02:14:37'),
(481, 118, 'Live Training Webinar Manajemen Risiko Gelombang I', 'PPM Manajemen dan Badan Litbang Diklat Kumdil Mahkamah Agung Republik Indonesia', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Live_Training_Webinar_Manajemen_Risiko_Gelombang_I.pdf', 1, '2026-09-28 02:14:37'),
(482, 118, 'Live Training Webinar Pemimpin Sebagai Pelatih Gelombang II', 'PPM Manajemen dan Badan Litbang Diklat Kumdil Mahkamah Agung Republik Indonesia', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Live_Training_Webinar_Pemimpin_Sebagai_Pelatih_Gelombang_II.pdf', 1, '2026-09-28 02:14:37'),
(483, 118, 'Lokakarya Hukum Persaingan Usaha Ke-38 untuk Hakim Pengadilan Negeri se-Provinsi Nusa Tenggara Barat', 'Komisi Pengawas Persaingan Usaha Republik Indonesia dan Mahkamah Agung Republik Indonesia', NULL, NULL, '(tidak diset)', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Lokakarya_Hukum_Persaingan_Usaha_Ke-38_untuk_Hakim_Pengadilan_Negeri_se-Provinsi_Nusa_Tenggara_Barat.pdf', 1, '2026-09-28 02:14:37'),
(484, 118, 'Pelatihan Peradilan Semu \'Membentuk Mahasiswa Fakultas Hukum Sebagai Tunas Yuris Yang Cerdas dan Berprestasi Dalam Proses Beracara Yang Ideal\'', 'Udayana Moot Court Community Fakultas Hukum Universitas Udayana', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Pelatihan_Peradilan_Semu_Membentuk_Mahasiswa_Fakultas_Hukum_Sebagai_Tunas_Yuris_Yang_Cerdas_dan_Berprestasi_Dalam_Proses.pdf', 1, '2026-09-28 02:14:37'),
(485, 118, 'Pembinaan Bagi Ketua/Wakil Ketua Pengadilan Negeri di Lingkungan Peradilan Umum Tahun Anggaran 2020', 'Direktorat Jenderal Badan Peradilan Umum Mahkamah Agung Republik Indonesia', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Pembinaan_Bagi_KetuaatauWakil_Ketua_Pengadilan_Negeri_di_Lingkungan_Peradilan_Umum_Tahun_Anggaran_2020.pdf', 1, '2026-09-28 02:14:37'),
(486, 118, 'Sekolah Pasar Modal Untuk Negeri', 'Otoritas Jasa Keuangan dan Ikatan Hakim Indonesia', NULL, NULL, '(tidak diset)', 'Workshop atau Lokakarya', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Sekolah_Pasar_Modal_Untuk_Negeri.pdf', 1, '2026-09-28 02:14:37'),
(487, 118, 'Sosialisasi Penanganan Dugaan Tindak Pidana Perbankan', 'Departemen Pemeriksaan Khusus dan Investigasi Perbankan Otoritas Jasa Keuangan', NULL, NULL, '(tidak diset)', 'Seminar/Konferensi/Sarasehan', 'Tervalidasi', 'Pelatihan', 'uploads/sertifikat/198602192009121003/Sosialisasi_Penanganan_Dugaan_Tindak_Pidana_Perbankan.pdf', 1, '2026-09-28 02:14:37');

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
(1, NULL, '12323', '$2y$10$9NtGq3urEkM56R8LK/6Uq.JfEwCHCSWYwJwVc5IAMlyagwxXJ7nPa', 'Admin', 1, '2026-10-20 13:29:02', '2026-09-13 13:20:29'),
(3, 20, '197303151992032001', '$2y$10$LmvO6Rw/oSMP.eP9cZQn7OSK8i3TelWTFsVmAngt0wZxsw4oilaBq', 'Penilai', 1, '2026-10-16 09:56:55', '2026-09-15 03:39:28'),
(4, 2, '196804141996031002', '$2y$10$Sqlz49AhHXPXvGi4fnj0X.wpyEIa9YDhWHcn0NAmkJXD2Dnb6hkF6', 'Penilai', 1, '2026-10-13 14:17:13', '2026-09-17 01:20:29'),
(5, 62, '197308161994031001', '$2y$10$HBPIMXcf3dMF2YWpWWn22.AP2H1iB/g6h/uNl6oNqBO95zF4/.Eai', 'Penilai', 1, '2026-10-20 13:28:10', '2026-09-22 03:53:03');

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
-- Indeks untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_audit_user` (`id_user`),
  ADD KEY `idx_audit_waktu` (`created_at`),
  ADD KEY `idx_audit_aksi` (`aksi`);

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
-- Indeks untuk tabel `dokumentasi_kuesioner`
--
ALTER TABLE `dokumentasi_kuesioner`
  ADD PRIMARY KEY (`id_dokumentasi`),
  ADD KEY `idx_dokumentasi_kuesioner_sesi` (`id_wawancara`);

--
-- Indeks untuk tabel `dokumentasi_wawancara`
--
ALTER TABLE `dokumentasi_wawancara`
  ADD PRIMARY KEY (`id_dokumentasi`),
  ADD KEY `idx_dokumentasi_wawancara_sesi` (`id_wawancara`);

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
-- Indeks untuk tabel `jenis_kegiatan_hakim`
--
ALTER TABLE `jenis_kegiatan_hakim`
  ADD PRIMARY KEY (`nama_jenis`);

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
-- Indeks untuk tabel `pemeriksaan_bukti_kuesioner`
--
ALTER TABLE `pemeriksaan_bukti_kuesioner`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD UNIQUE KEY `uq_pemeriksaan_bukti_kuesioner_penilai` (`id_wawancara`,`file_bukti`,`id_user_penilai`),
  ADD KEY `idx_pemeriksaan_bukti_kuesioner_penilai` (`id_user_penilai`);

--
-- Indeks untuk tabel `pemeriksaan_bukti_wawancara`
--
ALTER TABLE `pemeriksaan_bukti_wawancara`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD UNIQUE KEY `uq_pemeriksaan_bukti_penilai` (`id_wawancara`,`file_bukti`,`id_user_penilai`),
  ADD KEY `idx_pemeriksaan_bukti_penilai` (`id_user_penilai`);

--
-- Indeks untuk tabel `pemeriksaan_jawaban_kuesioner`
--
ALTER TABLE `pemeriksaan_jawaban_kuesioner`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD UNIQUE KEY `uq_pemeriksaan_jawaban_kuesioner_penilai` (`id_hasil`,`id_user_penilai`),
  ADD KEY `idx_pemeriksaan_jawaban_kuesioner_penilai` (`id_user_penilai`);

--
-- Indeks untuk tabel `pemeriksaan_jawaban_wawancara`
--
ALTER TABLE `pemeriksaan_jawaban_wawancara`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD UNIQUE KEY `uq_pemeriksaan_jawaban_penilai` (`id_hasil`,`id_user_penilai`),
  ADD KEY `idx_pemeriksaan_jawaban_penilai` (`id_user_penilai`);

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
-- Indeks untuk tabel `schema_migrations`
--
ALTER TABLE `schema_migrations`
  ADD PRIMARY KEY (`filename`);

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
  MODIFY `id_analisis_tugas` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id_log` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `bukti_dokumentasi`
--
ALTER TABLE `bukti_dokumentasi`
  MODIFY `id_bukti` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bukti_wawancara`
--
ALTER TABLE `bukti_wawancara`
  MODIFY `id_bukti` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `detail_wawancara`
--
ALTER TABLE `detail_wawancara`
  MODIFY `id_detail` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `diklat`
--
ALTER TABLE `diklat`
  MODIFY `id_diklat` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `dokumentasi_kuesioner`
--
ALTER TABLE `dokumentasi_kuesioner`
  MODIFY `id_dokumentasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `dokumentasi_wawancara`
--
ALTER TABLE `dokumentasi_wawancara`
  MODIFY `id_dokumentasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hasil_kuesioner`
--
ALTER TABLE `hasil_kuesioner`
  MODIFY `id_hasil` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `hasil_wawancara`
--
ALTER TABLE `hasil_wawancara`
  MODIFY `id_hasil` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id_jabatan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT untuk tabel `jawaban_wawancara`
--
ALTER TABLE `jawaban_wawancara`
  MODIFY `id_jawaban` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kebutuhan_diklat`
--
ALTER TABLE `kebutuhan_diklat`
  MODIFY `id_kebutuhan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `kegiatan_hakim`
--
ALTER TABLE `kegiatan_hakim`
  MODIFY `id_kegiatan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT untuk tabel `pemeriksaan_bukti_kuesioner`
--
ALTER TABLE `pemeriksaan_bukti_kuesioner`
  MODIFY `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pemeriksaan_bukti_wawancara`
--
ALTER TABLE `pemeriksaan_bukti_wawancara`
  MODIFY `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pemeriksaan_jawaban_kuesioner`
--
ALTER TABLE `pemeriksaan_jawaban_kuesioner`
  MODIFY `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pemeriksaan_jawaban_wawancara`
--
ALTER TABLE `pemeriksaan_jawaban_wawancara`
  MODIFY `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id_sertifikat` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=488;

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
-- Ketidakleluasaan untuk tabel `dokumentasi_kuesioner`
--
ALTER TABLE `dokumentasi_kuesioner`
  ADD CONSTRAINT `fk_dokumentasi_kuesioner_sesi` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokumentasi_wawancara`
--
ALTER TABLE `dokumentasi_wawancara`
  ADD CONSTRAINT `fk_dokumentasi_wawancara_sesi` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE;

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
-- Ketidakleluasaan untuk tabel `pemeriksaan_bukti_kuesioner`
--
ALTER TABLE `pemeriksaan_bukti_kuesioner`
  ADD CONSTRAINT `fk_pemeriksaan_bukti_kuesioner_penilai` FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemeriksaan_bukti_kuesioner_sesi` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemeriksaan_bukti_wawancara`
--
ALTER TABLE `pemeriksaan_bukti_wawancara`
  ADD CONSTRAINT `fk_pemeriksaan_bukti_penilai` FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemeriksaan_bukti_wawancara` FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemeriksaan_jawaban_kuesioner`
--
ALTER TABLE `pemeriksaan_jawaban_kuesioner`
  ADD CONSTRAINT `fk_pemeriksaan_jawaban_kuesioner_hasil` FOREIGN KEY (`id_hasil`) REFERENCES `hasil_kuesioner` (`id_hasil`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemeriksaan_jawaban_kuesioner_penilai` FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemeriksaan_jawaban_wawancara`
--
ALTER TABLE `pemeriksaan_jawaban_wawancara`
  ADD CONSTRAINT `fk_pemeriksaan_jawaban_hasil` FOREIGN KEY (`id_hasil`) REFERENCES `hasil_wawancara` (`id_hasil`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemeriksaan_jawaban_penilai` FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

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
