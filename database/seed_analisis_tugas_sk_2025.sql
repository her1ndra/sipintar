-- Seed Analisis Tugas berdasarkan SK Ketua Pengadilan Negeri Yogyakarta
-- Nomor 70/KPN.W13.U1/SK.OT1.2/XI/2025.
--
-- Jalankan pada database si_pintar. Tugas dan kegiatan diambil/diringkas dari
-- uraian dalam SK. Kompetensi jabatan merupakan rumusan pendukung karena SK
-- tidak menetapkan kolom kompetensi secara tersendiri.
--
-- Jabatan spesifik yang belum tersedia akan ditambahkan ke tabel jabatan.
-- ID jabatan dibuat otomatis dan kode dibuat tetap agar skrip aman diulang.
--
-- Query aman dijalankan ulang: baris dengan jabatan dan isi tugas yang sama
-- tidak akan ditambahkan kembali.

START TRANSACTION;

CREATE TEMPORARY TABLE tmp_jabatan_sk (
    kode_jabatan VARCHAR(20) NOT NULL,
    nama_jabatan VARCHAR(100) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

INSERT INTO tmp_jabatan_sk (kode_jabatan, nama_jabatan) VALUES
('KSR-PDT', 'Kasir Kepaniteraan Perdata'),
('JSP-PDT', 'Jurusita Pengganti Kepaniteraan Perdata'),
('APP-PDT', 'Klerek Analis Perkara Peradilan Kepaniteraan Perdata'),
('JSP-PID', 'Jurusita Pengganti Kepaniteraan Pidana'),
('PGP-PID', 'Klerek Pengelolaan Perkara Kepaniteraan Pidana'),
('APP-PID', 'Klerek Analis Perkara Peradilan Kepaniteraan Pidana'),
('APP-PHI', 'Klerek Analis Perkara Peradilan PHI'),
('PGP-PHI', 'Klerek Pengelola Perkara Kepaniteraan PHI'),
('OLO-PHI-SK', 'Operator Layanan Operasional Kepaniteraan PHI'),
('APP-TPK', 'Klerek Analis Perkara Peradilan Kepaniteraan TIPIKOR'),
('DOK-TPK', 'Klerek Dokumentasi Hukum Kepaniteraan TIPIKOR'),
('APP-HKM', 'Klerek Analis Perkara Peradilan Kepaniteraan Muda Hukum'),
('PAK-HKM', 'Pengadministrasi Perkantoran Kepaniteraan Muda Hukum'),
('PLO-PTIP-SK', 'Operator Penata Layanan Operasional Subbagian Perencanaan, Teknologi Informasi, dan Pelaporan'),
('PLO-KOT', 'Operator Penata Layanan Operasional Subbagian Kepegawaian, Organisasi, dan Tata Laksana'),
('OLO-UK-SK', 'Operator Penata Layanan Operasional Subbagian Umum dan Keuangan'),
('APKEU-UK', 'Analis Pengelolaan Keuangan APBN Subbagian Umum dan Keuangan'),
('PDI-UK', 'Klerek Pengolah Data dan Informasi Subbagian Umum dan Keuangan');

INSERT INTO jabatan (kode_jabatan, nama_jabatan, is_penilai)
SELECT s.kode_jabatan, s.nama_jabatan, 0
FROM tmp_jabatan_sk AS s
WHERE NOT EXISTS (
    SELECT 1
    FROM jabatan AS j
    WHERE j.kode_jabatan = s.kode_jabatan
);

DROP TEMPORARY TABLE tmp_jabatan_sk;

CREATE TEMPORARY TABLE tmp_analisis_tugas_sk (
    kode_jabatan VARCHAR(20) NOT NULL,
    tugas TEXT NOT NULL,
    kegiatan TEXT NOT NULL,
    kompetensi_jabatan TEXT NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

INSERT INTO tmp_analisis_tugas_sk (kode_jabatan, tugas, kegiatan, kompetensi_jabatan) VALUES
('KTU',
'Bertanggung jawab sepenuhnya terhadap jalannya peradilan, baik ke dalam maupun ke luar, serta memimpin pelaksanaan tugas pokok dan fungsi Pengadilan Negeri Yogyakarta.',
'Memimpin dan bertanggung jawab atas pelaksanaan tugas pokok dan fungsi pengadilan berdasarkan undang-undang, kebijakan Mahkamah Agung dan pemerintah.
Membuat kebijakan umum bidang kepaniteraan dan kesekretariatan melalui keputusan, penetapan, surat dinas atau surat tugas, serta membagi tugas aparatur.
Menyusun program kerja berkala bersama hakim, panitera, sekretaris, dan pejabat kesekretariatan/kepaniteraan terkait DIPA dan kebutuhan kantor.
Mengawasi dan mengevaluasi realisasi rencana fisik penjabaran DIPA setiap triwulan.
Melakukan pembinaan, pengawasan, dan tanggung jawab umum atas pelaksanaan anggaran DIPA.
Mengawasi keuangan negara dan barang milik negara.
Membina dan mengawasi hakim, panitera, sekretaris, serta seluruh pejabat kepaniteraan dan kesekretariatan.
Melakukan pengawasan melekat terhadap perilaku pegawai dan menjadi bahan penilaian prestasi kerja/SKP.
Menyusun laporan tahunan pelaksanaan tugas kepada Pengadilan Tinggi Yogyakarta dan Mahkamah Agung.
Menetapkan majelis hakim yang menangani perkara dan hakim pengawas bidang.
Merencanakan dan memimpin rapat koordinasi dengan Panitera dan Sekretaris serta pertemuan lain yang diperlukan.
Menerima dan menindaklanjuti hasil pengawasan hakim pengawas bidang.
Menilai SKP hakim, Panitera, dan Sekretaris.
Mengusulkan pengangkatan atau penambahan pegawai/jabatan kepada Mahkamah Agung melalui Pengadilan Tinggi Yogyakarta.
Melakukan penyumpahan dan pelantikan hakim, Panitera, Sekretaris, pejabat, dan PNS.
Membina dan mengawasi kegiatan IKAHI, PTWP, dan Dharma Yukti Karini.
Menetapkan panjar biaya perkara, biaya panggilan, biaya eksekusi, dan biaya proses peradilan lainnya.
Menerima dan meneliti berkas perkara dari Panitera serta menetapkan majelis hakim.
Dalam keadaan mendesak dapat mengganti Ketua Majelis dengan hakim lain melalui penetapan.
Memimpin jalannya eksekusi serta memerintahkan pemanggilan termohon dan pelaksanaan eksekusi.
Memerintahkan penangguhan eksekusi untuk jangka waktu tertentu apabila terdapat gugatan perlawanan pihak ketiga.
Menetapkan pelaksanaan dan tempat lelang atau kantor lelang negara.
Memeriksa pemegang kas perkara dan keuangan rutin DIPA secara berkala atau insidentil.
Menandatangani izin kuasa khusus/insidentil, penutupan buku register, serta laporan bulanan dan tahunan bersama Panitera dan Sekretaris.
Memberi izin membawa daftar, catatan, risalah, berita acara, atau berkas perkara keluar dari ruang kepaniteraan.
Memberikan penilaian prestasi dan SKP sesuai kewenangan.
Berkoordinasi dengan instansi penegak hukum dan instansi pemerintah lainnya.
Menunjuk hakim untuk memberikan bantuan pembuatan gugatan/permohonan lisan bagi pihak yang buta huruf.
Membuat uraian tugas bagi seluruh pejabat dan pelaksana.
Melaksanakan tugas pimpinan pengadilan dan tugas lain berdasarkan peraturan atau perintah atasan.',
'Kepemimpinan peradilan, hukum acara, tata kelola pengadilan, pengawasan, manajemen SDM, pengelolaan anggaran dan BMN'),
('WKL',
'Membantu Ketua dalam memimpin Pengadilan Negeri, perencanaan program kerja, pengawasan, dan koordinasi.',
'Membantu Ketua menyusun perencanaan program kerja jangka pendek dan jangka panjang.
Mewakili Ketua apabila berhalangan.
Melaksanakan delegasi dari Ketua.
Melakukan pengawasan intern terhadap pelaksanaan tugas agar sesuai rencana kerja dan ketentuan yang berlaku serta melaporkan hasilnya kepada Ketua.
Mengevaluasi hasil pengawasan dan memberikan penilaian.
Melakukan koordinasi dan kerja sama dengan instansi pemerintah dan swasta.',
'Kepemimpinan, perencanaan kerja, pengawasan intern, evaluasi, koordinasi, pemahaman peraturan peradilan'),
('HKM-UM',
'Memeriksa, memutus, dan menyelesaikan perkara pidana dan perdata pada tingkat pertama.',
'Menerima, memeriksa, mengadili, dan menyelesaikan perkara yang diajukan.
Melaksanakan pengawasan yang ditugaskan Ketua dan melaporkan pelaksanaannya kepada pimpinan pengadilan.
Melaksanakan pengawasan dan pengamatan (Kimwasmat) terhadap pelaksanaan putusan pidana di lembaga pemasyarakatan/rumah tahanan serta melaporkannya kepada Mahkamah Agung.
Melaksanakan tugas kedinasan lain yang diperintahkan atasan.',
'Hukum acara perdata dan pidana, pemeriksaan dan penilaian alat bukti, penyusunan putusan, etika hakim, pengawasan'),
('HKM-MU',
'Memeriksa, memutus, dan menyelesaikan perkara pidana dan perdata pada tingkat pertama.',
'Menerima, memeriksa, mengadili, dan menyelesaikan perkara yang diajukan.
Melaksanakan pengawasan yang ditugaskan Ketua dan melaporkan pelaksanaannya kepada pimpinan pengadilan.
Melaksanakan pengawasan dan pengamatan (Kimwasmat) terhadap pelaksanaan putusan pidana di lembaga pemasyarakatan/rumah tahanan serta melaporkannya kepada Mahkamah Agung.
Melaksanakan tugas kedinasan lain yang diperintahkan atasan.',
'Hukum acara perdata dan pidana, pemeriksaan dan penilaian alat bukti, penyusunan putusan, etika hakim, pengawasan'),
('HKM-MM',
'Memeriksa, memutus, dan menyelesaikan perkara pidana dan perdata pada tingkat pertama.',
'Menerima, memeriksa, mengadili, dan menyelesaikan perkara yang diajukan.
Melaksanakan pengawasan yang ditugaskan Ketua dan melaporkan pelaksanaannya kepada pimpinan pengadilan.
Melaksanakan pengawasan dan pengamatan (Kimwasmat) terhadap pelaksanaan putusan pidana di lembaga pemasyarakatan/rumah tahanan serta melaporkannya kepada Mahkamah Agung.
Melaksanakan tugas kedinasan lain yang diperintahkan atasan.',
'Hukum acara perdata dan pidana, pemeriksaan dan penilaian alat bukti, penyusunan putusan, etika hakim, pengawasan'),
('HAP',
'Memeriksa, memutus, dan menyelesaikan perkara perselisihan hubungan industrial pada tingkat pertama.',
'Menerima, memeriksa, mengadili, dan menyelesaikan perkara PHI yang diajukan.
Melaksanakan tugas persidangan dan tugas kedinasan lain sesuai ketentuan serta penugasan pimpinan.',
'Hukum acara perselisihan hubungan industrial, pemeriksaan dan penilaian alat bukti, penyusunan putusan, etika hakim'),
('HAT',
'Memeriksa, memutus, dan menyelesaikan perkara tindak pidana korupsi pada tingkat pertama.',
'Menerima, memeriksa, mengadili, dan menyelesaikan perkara TIPIKOR yang diajukan.
Melaksanakan tugas persidangan dan tugas kedinasan lain sesuai ketentuan serta penugasan pimpinan.',
'Hukum acara pidana dan tindak pidana korupsi, pemeriksaan alat bukti, penyusunan putusan, etika hakim'),
('PAN',
'Melaksanakan dukungan teknis dan administrasi perkara serta menyelesaikan surat-surat kepaniteraan.',
'Mengkoordinasikan, membina, dan mengawasi pemberian dukungan teknis kepaniteraan.
Mengelola administrasi perkara perdata.
Mengelola administrasi perkara pidana.
Mengelola administrasi perkara khusus.
Mengelola administrasi perkara, penyajian data perkara, dan transparansi perkara.
Mengelola administrasi keuangan program teknis dan keuangan perkara, minutasi, evaluasi, dan administrasi kepaniteraan.
Mendukung pelaksanaan mediasi.
Melaksanakan pembinaan teknis kepaniteraan dan kejurusitaan.
Melaksanakan fungsi lain yang diberikan Ketua Pengadilan Negeri.',
'Administrasi perkara, hukum acara, kepemimpinan kepaniteraan, keuangan perkara, pengelolaan data, pembinaan dan pengawasan'),
('PM-PDT',
'Melaksanakan administrasi perkara di bidang perdata.',
'Memeriksa dan menelaah kelengkapan berkas perkara perdata.
Meregistrasi perkara gugatan dan permohonan.
Mendistribusikan perkara terdaftar kepada Ketua Majelis berdasarkan penetapan penunjukan majelis hakim.
Menerima kembali berkas perkara yang telah diputus dan diminutasi.
Memberitahukan isi putusan tingkat pertama kepada para pihak yang tidak hadir.
Menyampaikan pemberitahuan putusan banding, kasasi, dan peninjauan kembali kepada para pihak.
Menerima dan mengirim berkas perkara yang dimohonkan banding, kasasi, dan peninjauan kembali.
Mengawasi pemberitahuan isi putusan upaya hukum dan menyampaikan relaas penyerahan isi putusan kepada Pengadilan Tinggi dan Mahkamah Agung.
Menerima konsinyasi dan permohonan eksekusi.
Menyimpan berkas perkara yang belum berkekuatan hukum tetap dan menyerahkan berkas berkekuatan hukum tetap kepada Panitera Muda Hukum.
Melaksanakan urusan tata usaha kepaniteraan dan fungsi lain dari Panitera.',
'Administrasi perkara perdata, hukum acara perdata, registrasi dan minutasi, SIPP/e-Court, pengelolaan berkas, pelayanan publik'),
('KSR-PDT',
'Mengelola kas dan administrasi keuangan perkara pada Kepaniteraan Perdata.',
'Menerima dan membukukan panjar biaya perkara sesuai SKUM pada jurnal keuangan perkara.
Mengeluarkan dan membukukan uang untuk penyelenggaraan peradilan sesuai SK biaya panjar.
Menyerahkan uang hak-hak kepaniteraan kepada Bendahara Penerima untuk disetorkan ke Kas Negara.
Menginput transaksi biaya panjar ke dalam SIPP.
Membuat laporan bulanan keuangan perkara.
Melaksanakan tugas sebagai Bendahara Proses.
Melaksanakan tugas kedinasan lain dari pimpinan.',
'Administrasi keuangan perkara, pencatatan jurnal, pengelolaan panjar, penguasaan SIPP, ketelitian dan akuntabilitas'),
('JSP-PDT',
'Melaksanakan tugas Jurusita Pengganti pada Kepaniteraan Perdata.',
'Melaksanakan tugas sebagai Jurusita Pengganti.
Menginput perkara permohonan, gugatan, gugatan sederhana, dan eksekusi pada SIPP.
Memproses penerimaan berkas permohonan, gugatan, dan gugatan sederhana beserta kelengkapannya untuk diserahkan kepada Panitera Pengganti.
Meregistrasi buku induk keuangan dan keuangan eksekusi.
Melaksanakan tugas Pojok E-Court dan PTSP.
Membantu tugas Panitera Muda Perdata.
Melaksanakan tugas kedinasan lain dari pimpinan.',
'Administrasi perkara perdata, tugas kejurusitaan, SIPP/e-Court, pengelolaan berkas dan keuangan perkara, pelayanan PTSP'),
('APP-PDT',
'Menangani administrasi perkara dan upaya hukum pada Kepaniteraan Perdata.',
'Membuat akta terkait upaya hukum, termasuk akta banding, kasasi, dan peninjauan kembali.
Menerima dan memeriksa memori/kontra memori banding dan kasasi serta berkas peninjauan kembali.
Menginput data upaya hukum ke SIPP, e-Court, dan e-Court Banding.
Memproses dan mengirim berkas upaya hukum ke Pengadilan Tinggi atau Mahkamah Agung secara elektronik.
Memberitahukan isi putusan banding, kasasi, dan peninjauan kembali.
Menyusun laporan keperdataan, laporan pengawasan bidang, dan laporan perkara upaya hukum.
Menginput data pada aplikasi perdata lainnya.
Mengirim berkas inaktif berkekuatan hukum tetap ke Kepaniteraan Hukum sesuai prosedur arsip.
Memberikan layanan PTSP Kepaniteraan Perdata dan melaksanakan tugas kedinasan dari pimpinan.',
'Administrasi perkara perdata, upaya hukum, SIPP/e-Court, penyusunan akta dan laporan, kearsipan, pelayanan PTSP'),
('PM-PID',
'Melaksanakan administrasi perkara di bidang pidana.',
'Memeriksa dan menelaah kelengkapan berkas perkara pidana serta melakukan registrasi perkara.
Menerima permohonan praperadilan dan memberitahukannya kepada termohon.
Mendistribusikan perkara terdaftar kepada Ketua Majelis berdasarkan penetapan Ketua Pengadilan.
Menghitung, menyiapkan, dan mengirim penetapan penahanan, perpanjangan penahanan, dan penangguhan penahanan.
Menerima permohonan izin penggeledahan dan penyitaan dari penyidik.
Menerima kembali berkas perkara yang telah diputus dan diminutasi.
Memberitahukan isi putusan tingkat pertama kepada pihak yang tidak hadir serta menyampaikan pemberitahuan putusan banding, kasasi, dan peninjauan kembali.
Menerima dan mengirim berkas perkara banding, kasasi, dan peninjauan kembali.
Mengawasi pemberitahuan putusan upaya hukum dan menyampaikan relaas kepada Pengadilan Tinggi dan Mahkamah Agung.
Memberitahukan putusan upaya hukum kepada Jaksa Penuntut Umum dan terdakwa.
Menerima permohonan eksekusi, menyimpan berkas yang belum berkekuatan hukum tetap, serta menyerahkan berkas berkekuatan hukum tetap kepada Panitera Muda Hukum.
Melaksanakan urusan tata usaha kepaniteraan dan fungsi lain dari Panitera.',
'Administrasi perkara pidana, hukum acara pidana, SIPP/e-Berpadu, penahanan dan upaya hukum, pengelolaan berkas'),
('JSP-PID',
'Melaksanakan tugas Jurusita Pengganti pada Kepaniteraan Pidana.',
'Menjalankan tugas-tugas Jurusita Pengganti.
Memproses permohonan sita, penggeledahan, diversi, dan tahanan melalui e-Berpadu.
Mengirim salinan putusan dan petikan putusan kepada terdakwa/penasihat hukum dan penuntut umum.
Membantu Panitera Muda Pidana melaksanakan tugas lainnya.
Melaksanakan tugas kedinasan lain dari pimpinan.',
'Administrasi perkara pidana, tugas kejurusitaan, penguasaan e-Berpadu, pengiriman dokumen, ketelitian'),
('PGP-PID',
'Mengelola penerimaan, pendaftaran, dan input perkara pidana pada Kepaniteraan Pidana.',
'Memberikan pelayanan pada Pelayanan Terpadu Satu Pintu.
Menerima berkas perkara pidana biasa, pidana anak, pidana singkat, pidana cepat, lalu lintas, dan praperadilan.
Mendaftarkan perkara pidana biasa, pidana anak, pidana singkat, pidana cepat, lalu lintas, dan praperadilan.
Menginput data perkara dari e-Berpadu ke SIPP.
Memproses dan memvalidasi surat izin besuk melalui e-Berpadu.
Membantu Panitera Muda Pidana dan melaksanakan tugas kedinasan dari pimpinan.',
'Administrasi perkara pidana, registrasi perkara, SIPP/e-Berpadu, verifikasi dokumen, pelayanan PTSP'),
('APP-PID',
'Menangani pelayanan dan administrasi upaya hukum pada Kepaniteraan Pidana.',
'Memberikan pelayanan pada Pelayanan Terpadu Satu Pintu.
Menerima permohonan banding dan peninjauan kembali dari terdakwa/keluarga, penuntut umum, atau penasihat hukum melalui e-Berpadu.
Membuat akta permohonan banding dan peninjauan kembali.
Membuat surat laporan pemberitahuan banding dan peninjauan kembali kepada Pengadilan Tinggi/Mahkamah Agung.
Menerima serta membuat tanda terima memori/kontra memori banding.
Melengkapi berkas Bundel A dan Bundel B dalam e-Berpadu dan SIPP untuk dikirim kepada Pengadilan Tinggi/Mahkamah Agung.
Mengirim tembusan surat pengiriman banding dan peninjauan kembali kepada para pihak melalui Bagian Umum.
Meregistrasi perkara banding dan peninjauan kembali pada buku register/buku bantu perkara.
Menyerahkan berkas berkekuatan hukum tetap kepada Kepaniteraan Hukum dan mencatatnya di SIPP.
Membantu Panitera Muda Pidana dan melaksanakan tugas kedinasan dari pimpinan.',
'Administrasi upaya hukum pidana, e-Berpadu/SIPP, penyusunan akta dan surat, pengelolaan bundel perkara, pelayanan PTSP'),
('PM-PHI',
'Melaksanakan administrasi perkara khusus penyelesaian perselisihan hubungan industrial.',
'Membantu Hakim dalam persidangan dengan mengikuti dan mencatat jalannya persidangan.
Memonitor pendaftaran gugatan secara elektronik, panjar biaya gugatan, dan register perkara PHI.
Memeriksa berkas perkara yang diajukan upaya hukum kasasi.
Menerima dan memeriksa berkas perkara yang telah diminutasi.
Memonitor penyerahan berkas inaktif berkekuatan hukum tetap kepada Kepaniteraan Hukum.
Memeriksa permohonan eksekusi dan membuat resume permohonan eksekusi.
Memonitor dan mendistribusikan surat masuk dan surat kembali serta membuat surat balasan.',
'Administrasi perkara PHI, hukum acara hubungan industrial, e-Court, upaya hukum, eksekusi, monitoring dan pelaporan'),
('APP-PHI',
'Mendaftarkan dan memproses perkara perdata khusus/PHI serta mendukung pelayanan Kepaniteraan PHI.',
'Mendaftarkan perkara perdata khusus/PHI melalui e-Court.
Menyiapkan dan memproses berkas pendaftaran perkara untuk didistribusikan kepada Panitera Pengganti.
Memeriksa checklist berkas yang diserahkan Panitera Pengganti sebelum diarsipkan di Kepaniteraan Hukum.
Membuat berita acara penyerahan berkas inaktif kepada Kepaniteraan Hukum.
Membuat laporan pengawasan bidang dan laporan TLHP Kepaniteraan PHI.
Melaksanakan tugas sebagai petugas PTSP.
Melaksanakan tugas kedinasan dari atasan.',
'Administrasi perkara PHI, e-Court, pemeriksaan kelengkapan berkas, pengarsipan dan pelaporan, pelayanan PTSP'),
('PGP-PHI',
'Mengelola administrasi perkara PHI dan pendaftaran perjanjian bersama.',
'Mendaftarkan upaya hukum kasasi perkara PHI secara elektronik.
Menyiapkan dan memproses berkas upaya hukum untuk dikirim secara elektronik.
Memproses permohonan pendaftaran akta Perjanjian Bersama (Bipartit/Mediasi).
Mencatat pendaftaran Perjanjian Bersama pada buku register.
Membuat monitoring, evaluasi, dan laporan Kepaniteraan PHI.
Melaksanakan tugas sebagai petugas PTSP.
Membantu tugas Panitera Muda PHI serta melaksanakan tugas kedinasan dari atasan.',
'Administrasi perkara PHI, pendaftaran elektronik, pengelolaan kasasi dan Perjanjian Bersama, pelaporan, pelayanan PTSP'),
('OLO-PHI-SK',
'Mendukung layanan operasional dan administrasi perkara pada Kepaniteraan PHI.',
'Mendaftarkan upaya hukum kasasi perkara PHI secara elektronik.
Menyiapkan dan memproses berkas perkara upaya hukum untuk dikirim secara elektronik.
Memproses permohonan pendaftaran akta Perjanjian Bersama (Bipartit/Mediasi).
Mencatat pendaftaran Perjanjian Bersama pada buku register.
Membuat monitoring, evaluasi, dan laporan Kepaniteraan PHI.
Melaksanakan tugas PTSP.
Membantu tugas Panitera Muda PHI dan melaksanakan tugas kedinasan dari atasan.',
'Administrasi perkara PHI, aplikasi perkara elektronik, pencatatan register, penyusunan laporan, pelayanan PTSP'),
('PM-TPK',
'Melaksanakan administrasi perkara khusus tindak pidana korupsi.',
'Menjadi koordinator pelayanan PTSP.
Melakukan monitoring dan evaluasi administrasi Kepaniteraan TIPIKOR.
Melakukan monitoring dan evaluasi pelimpahan perkara TIPIKOR melalui e-Berpadu.
Melakukan monitoring dan evaluasi pemrosesan izin besuk, izin penyitaan, izin penggeledahan, dan penahanan melalui e-Berpadu.
Melakukan monitoring dan evaluasi pemrosesan upaya hukum perkara TIPIKOR secara elektronik.',
'Administrasi perkara TIPIKOR, e-Berpadu, monitoring dan evaluasi, koordinasi pelayanan, integritas'),
('APP-TPK',
'Melaksanakan pelayanan dan administrasi upaya hukum Kepaniteraan TIPIKOR.',
'Melaksanakan pelayanan PTSP TIPIKOR.
Memproses permohonan izin/persetujuan penyitaan dan penggeledahan melalui e-Berpadu.
Memproses permohonan upaya hukum secara elektronik.
Membuat monitoring, evaluasi, dan laporan Kepaniteraan TIPIKOR.
Melaksanakan tugas kedinasan dari atasan.',
'Administrasi perkara TIPIKOR, e-Berpadu, upaya hukum elektronik, penyusunan laporan, pelayanan PTSP'),
('DOK-TPK',
'Melaksanakan pelayanan dokumentasi hukum dan administrasi berkas Kepaniteraan TIPIKOR.',
'Melaksanakan pelayanan PTSP TIPIKOR.
Memproses permohonan izin/persetujuan besuk melalui e-Berpadu.
Memproses berkas perkara inaktif ke Kepaniteraan Hukum.
Membuat laporan pengawasan bidang dan TLHP Kepaniteraan TIPIKOR.
Melaksanakan tugas kedinasan dari atasan.',
'Administrasi dokumentasi hukum, e-Berpadu, kearsipan perkara, penyusunan laporan, pelayanan PTSP'),
('PM-HKM',
'Mengumpulkan, mengolah, dan menyajikan data perkara, kehumasan, arsip perkara, serta pelaporan.',
'Mengumpulkan, mengelola, dan menyajikan data perkara.
Menyajikan statistik perkara.
Menyusun dan mengirim pelaporan perkara.
Menata, menyimpan, dan memelihara arsip perkara.
Melaksanakan kerja sama dengan arsip daerah untuk penitipan berkas perkara.
Menyiapkan, mengelola, dan menyajikan bahan transparansi perkara.
Menghimpun pengaduan masyarakat.
Melaksanakan fungsi lain yang diberikan Panitera.',
'Pengelolaan data dan statistik perkara, pelaporan, kearsipan, transparansi informasi, pelayanan pengaduan'),
('APP-HKM',
'Memberikan layanan dan mengelola administrasi Kepaniteraan Muda Hukum.',
'Melaksanakan tugas sebagai petugas PTSP Kepaniteraan Muda Hukum.
Memberikan pelayanan Eraterang Badilum dan akta di bawah tangan (waarmerking).
Memproses permintaan surat keterangan inkracht.
Melayani pendaftaran surat kuasa khusus dan pembuatan permohonan surat kuasa insidentil.
Meregistrasi berkas perkara pidana, perdata, TIPIKOR, dan PHI yang telah berkekuatan hukum tetap.
Mencatat peminjaman berkas perkara/arsip.
Membuat dan mengarsipkan jawaban surat masuk.
Menyusun monev SPKP/SPAK, Eraterang, SIPP, PTSP, uraian tugas, dan Posbakum.
Membuat statistik layanan hukum, laporan Hawasbid/TLHP, laporan bulanan ke Pengadilan Tinggi, dan laporan survei harian.
Membuat notulen rapat berjenjang kepaniteraan.
Melaksanakan perintah atasan sehubungan dengan kedinasan.',
'Pelayanan hukum dan PTSP, pengelolaan arsip dan register, statistik, monitoring dan evaluasi, penyusunan laporan'),
('PAK-HKM',
'Menangani pengaduan dan administrasi pelayanan pada Kepaniteraan Muda Hukum.',
'Menangani pengaduan dan menjadi operator SIWAS.
Menangani pengaduan pada meja pengaduan.
Membuat laporan bulanan, tahunan, serta laporan triwulan SPKP dan SPAK.
Memberikan pelayanan waarmerking dan melaksanakan tugas PTSP.
Mendaftarkan surat kuasa khusus dan mengoperasikan layanan elektronik.
Menata arsip dan menginput arsip ke SIPP.
Membuat laporan semester terkait gratifikasi.
Melaksanakan perintah atasan sehubungan dengan kedinasan.',
'Administrasi perkantoran, penanganan pengaduan, pelayanan PTSP, pengelolaan arsip dan SIPP, penyusunan laporan'),
('JS',
'Melaksanakan perintah Ketua atau Hakim Ketua Majelis untuk menyampaikan pengumuman, teguran, pemberitahuan, dan tindakan pengadilan sesuai ketentuan perundang-undangan.',
'Membuat relaas panggilan sidang.
Membuat relaas pemberitahuan putusan Pengadilan Negeri, Pengadilan Tinggi, dan Mahkamah Agung.
Membuat relaas pemberitahuan terkait banding, kasasi, peninjauan kembali, dan eksekusi.
Membuat berita acara penyitaan dan menyerahkan salinannya kepada pihak yang berkepentingan.
Membuat penetapan dan berita acara terkait pelaksanaan eksekusi.',
'Hukum acara, tata cara pemanggilan dan pemberitahuan, penyusunan relaas, penyitaan, pelaksanaan eksekusi'),
('PP',
'Membantu Hakim dalam persidangan perkara perdata dan pidana serta melaporkan kegiatan persidangan.',
'Membantu Hakim dalam persidangan dan melaporkan kegiatan persidangan kepada bagian terkait.
Membantu membuat penetapan hari sidang dan berita acara persidangan.
Membantu membuat penetapan terdakwa tetap ditahan, dikeluarkan dari tahanan, atau diubah jenis tahanannya.
Melaporkan barang bukti.
Melaporkan perkara yang telah diputus beserta amar putusan kepada Panitera Muda Pidana atau Perdata.
Menyerahkan berkas yang telah diminutasi.',
'Administrasi persidangan, hukum acara perdata dan pidana, penyusunan berita acara, pengelolaan barang bukti dan berkas'),
('SEK',
'Memberikan dukungan administrasi, organisasi, keuangan, sumber daya manusia, sarana, dan prasarana di lingkungan Pengadilan Negeri Kelas IA.',
'Menyiapkan bahan pelaksanaan perencanaan program dan anggaran.
Melaksanakan urusan kepegawaian dan keuangan.
Menyiapkan penataan organisasi dan tata laksana.
Mengelola teknologi informasi dan statistik.
Mengelola surat-menyurat, arsip, perlengkapan, rumah tangga, keamanan, keprotokolan, hubungan masyarakat, dan perpustakaan.
Menyiapkan pemantauan, evaluasi, dokumentasi, dan pelaporan di lingkungan kesekretariatan.',
'Manajemen administrasi, perencanaan dan anggaran, pengelolaan SDM dan keuangan, organisasi, sarana prasarana, pelaporan'),
('KSB-PTIP',
'Menyiapkan program dan anggaran, mengelola teknologi informasi dan statistik, serta melaksanakan pemantauan, evaluasi, dokumentasi, dan pelaporan.',
'Menyiapkan bahan pelaksanaan program dan anggaran.
Menyusun perencanaan program/kegiatan/RKAKL serta TOR, RAB, dan data dukung pengajuan anggaran.
Melaksanakan revisi DIPA dan DIPA Halaman III serta revisi POK sesuai rencana kegiatan dan dana tersedia.
Merawat dan memelihara jaringan, CCTV, server, sistem aplikasi, database, dan sarana TI.
Menjaga keamanan website dan aplikasi terintegrasi serta mendukung keterbukaan informasi publik dan publikasi.
Mengkoordinasikan dukungan jaringan listrik dan genset bagi sarana TI.
Mengumpulkan data statistik dan mengelola dokumen SK dalam JDIH.
Memelihara dan menyinkronkan sistem SIPP, e-Court, Direktori Putusan, SPPT-TI, media sosial, PTSP, dan sidang online.
Menyusun dan mengirim laporan Badilum selain perkara, SMART, laporan bulanan/tahunan, SAKIP pada Komdanas, dan evaluasi kinerja SEMAR.
Menindaklanjuti hasil AMPUH Badilum, pemeriksaan Pengadilan Tinggi, SMAP, Bawas, dan pengawasan bidang.
Mengelola dokumentasi persuratan, melakukan monitoring dan evaluasi berjenjang, serta mendukung pengembangan SIPP, e-Court, e-Berpadu, dan SIAP.',
'Perencanaan dan penganggaran, pengelolaan jaringan/server/aplikasi, keamanan informasi, statistik, monitoring dan evaluasi, pelaporan'),
('PKAP',
'Mempersiapkan sistem, sarana, dan prasarana teknologi informasi serta melaksanakan pemantauan, evaluasi, dokumentasi, dan pelaporan.',
'Mempersiapkan sistem dan sarana prasarana teknologi informasi.
Melaksanakan pemantauan, evaluasi, dokumentasi, dan pelaporan.
Mengedit dan mengunggah berita serta kegiatan pengadilan di media sosial dan website.
Menyusun dan membuat video.
Memelihara CCTV.
Menginstalasi atau meningkatkan sistem operasi dan aplikasi pendukung.
Menangani keluhan dan gangguan serta melakukan troubleshooting perangkat, jaringan, sistem operasi, dan aplikasi.
Mengelola perangkat keras.
Membuat laporan pengawasan dan tindak lanjut Hawasbid.
Menyusun dokumen laporan.
Melaksanakan tugas kedinasan dari atasan.',
'Teknologi informasi, perangkat keras dan lunak, jaringan, troubleshooting, keamanan sistem, pengelolaan konten dan pelaporan'),
('PLO-PTIP-SK',
'Mengelola perangkat pendukung teknologi informasi dan melaksanakan administrasi penerimaan serta pelaporan.',
'Mengelola perangkat pendukung teknologi informasi dan perangkat lunak.
Membuat dokumen potongan gaji.
Membuat laporan Bendahara Penerimaan.
Membuat dan mengajukan dokumen uang makan.
Membuat kode billing PNBP dan laporan penerimaan.
Membuat laporan pengawasan dan tindak lanjut Hawasbid.
Mengumpulkan dan menyusun laporan tahunan.
Melaksanakan tugas kedinasan dari atasan.',
'Pengelolaan perangkat TI, administrasi keuangan, PNBP, penyusunan dokumen dan laporan, ketelitian'),
('KSB-KOT',
'Menyiapkan urusan kepegawaian, penataan organisasi, dan tata laksana.',
'Merencanakan dan memimpin pelaksanaan tugas kepegawaian, organisasi, dan tata laksana.
Menetapkan sasaran kegiatan tahunan.
Menyusun dan menjadwalkan rencana kegiatan.
Membagi tugas kepada bawahan dan menentukan penanggung jawab kegiatan.
Menggerakkan dan mengarahkan pelaksanaan kegiatan subbagian.
Berkonsultasi dengan atasan apabila diperlukan dan melaksanakan tugas khusus pimpinan.
Mengevaluasi dan memantau pelaksanaan tugas bawahan.
Melaksanakan tugas kedinasan dari atasan.',
'Administrasi kepegawaian, manajemen SDM, perencanaan kerja, organisasi dan tata laksana, kepemimpinan dan evaluasi'),
('PLO-KOT',
'Melaksanakan administrasi dan layanan operasional pada Subbagian Kepegawaian, Organisasi, dan Tata Laksana.',
'Menjawab surat masuk.
Membuat surat tugas.
Menginput presensi pada KOMDANAS dan data PCKB pada KOMDANAS.
Membuat laporan kedisiplinan, TLHP pengawasan, mutasi internal, dan pengembangan kompetensi.
Memutakhirkan data pada SIKEP Mahkamah Agung.
Mendistribusikan surat keputusan Ketua, surat tugas, dan surat lainnya.
Menginput surat cuti pada SIKEP.
Memutakhirkan data pegawai, kenaikan pangkat, KGB, dan pensiun pada monitor.
Melaksanakan tugas kedinasan dari atasan.',
'Administrasi kepegawaian, tata naskah dinas, penguasaan SIKEP dan KOMDANAS, pengelolaan data, penyusunan laporan'),
('ART',
'Mengelola arsip dinamis dan statis serta melaksanakan pembinaan kearsipan.',
'Mengelola arsip dinamis.
Mengelola arsip statis.
Melaksanakan pembinaan kearsipan.
Melaksanakan tugas kedinasan dari atasan.',
'Manajemen kearsipan, klasifikasi dan retensi arsip, pemeliharaan arsip, pembinaan kearsipan'),
('KSB-UK',
'Menyiapkan urusan surat-menyurat, arsip, perlengkapan, rumah tangga, keamanan, keprotokolan, hubungan masyarakat, perpustakaan, serta pengelolaan keuangan.',
'Merencanakan pencairan anggaran satu minggu sebelum pencairan, menginput pada AFS, dan mengirimkan ke KPPN.
Mengajukan uang persediaan dan mengantarkan SPM ke KPPN.
Membuat serta mengajukan tambahan uang persediaan mendesak dan mengantarkan SPM ke KPPN.
Mengajukan ganti uang persediaan dan mengantarkan SPM ke KPPN.
Membuat SPM langsung dan mengantarkannya ke KPPN.
Mencatat transaksi pada buku kas umum, buku pembantu kas tunai, bank, pajak, uang persediaan, dan kas LS.
Membuat pertanggungjawaban bendahara pengeluaran serta laporan realisasi dan penyerapan anggaran.
Menerima dan menyetorkan pajak melalui kantor pos/bank.
Membuat daftar dan pertanggungjawaban remunerasi serta mengirimkannya kepada Korwil.',
'Pengelolaan keuangan negara, perbendaharaan, AFS dan SPM, pencatatan transaksi, pajak, penyusunan laporan dan pertanggungjawaban'),
('OLO-UK-SK',
'Menyiapkan informasi dan pemberitaan, layanan kehumasan, serta dokumen pembayaran dan pengajuan keuangan.',
'Menyiapkan penyelenggaraan informasi dan pemberitaan.
Menyiapkan laporan analisis dan evaluasi pelaksanaan kehumasan.
Menyiapkan rumusan pelayanan dan pemberitaan.
Mengelola data pemohon informasi kehumasan.
Memeriksa dan menyediakan dokumen pembayaran gaji induk, gaji susulan, gaji ke-13, THR, uang penghargaan Hakim Ad Hoc, dan PPNPN.
Memeriksa data pegawai dan menyediakan dokumen pengajuan tunjangan kinerja ASN serta transport hakim.
Memeriksa bukti pendukung, menguji SPP, memvalidasi, memproses SPM ke KPPN melalui SAKTI, dan mengarsipkan dokumen SPM.',
'Kehumasan dan layanan informasi, administrasi gaji dan tunjangan, verifikasi SPP/SPM, penguasaan SAKTI, pengarsipan'),
('APKEU-UK',
'Melaksanakan pelaporan dan rekonsiliasi keuangan APBN serta menyiapkan dokumen pembayaran.',
'Melaksanakan laporan manual realisasi anggaran DIPA 01 dan DIPA 03.
Melaksanakan rekonsiliasi keuangan DIPA 01 dan DIPA 03.
Menyampaikan neraca, laporan realisasi anggaran, laporan operasional, dan laporan perubahan ekuitas.
Menyampaikan berita acara rekonsiliasi keuangan DIPA 01 dan DIPA 03.
Menerima bukti pembayaran sebagai bahan pembuatan SPBy.
Membuat SPP sebagai dasar pembuatan SPM ke KPPN melalui SAKTI untuk belanja 52.
Mengarsipkan SPM beserta lampirannya sesuai bulan.
Memeriksa berkas-berkas relaas DIPA 03.',
'Akuntansi pemerintahan, pengelolaan APBN/DIPA, rekonsiliasi, penyusunan laporan keuangan, SAKTI, verifikasi dokumen'),
('PDI-UK',
'Mengelola data dan informasi Barang Milik Negara serta administrasi pelaporannya.',
'Memeriksa dokumen dan menginput data transaksi BMN.
Memproses usulan penetapan status penggunaan, asuransi, hibah masuk, sewa, rencana kebutuhan, dan penghapusan BMN.
Menyusun laporan pengawasan dan pengendalian BMN semesteran dan tahunan.
Menyusun laporan BMN semesteran dan tahunan.
Membuat daftar barang ruangan/barang lainnya, Kartu Identitas Barang, labelisasi, dan SK penggunaan BMN.
Melakukan inventarisasi BMN.
Melakukan rekonsiliasi internal modul GLP dengan modul aset tetap dan persediaan pada SAKTI setiap bulan, semester, dan tahun.',
'Administrasi BMN, inventarisasi, aplikasi SAKTI, rekonsiliasi aset dan persediaan, penyusunan laporan');

-- Pastikan seluruh kode yang dipakai oleh seed tersedia sebelum memasukkan data.
SELECT DISTINCT s.kode_jabatan AS kode_jabatan_tidak_ditemukan
FROM tmp_analisis_tugas_sk AS s
LEFT JOIN jabatan AS j ON j.kode_jabatan = s.kode_jabatan
WHERE j.id_jabatan IS NULL;

INSERT INTO analisis_tugas
    (id_jabatan, id_pegawai, tugas, kegiatan,
     kompetensi_sementara_jabatan, kompetensi_jabatan)
SELECT
    j.id_jabatan,
    NULL,
    s.tugas,
    s.kegiatan,
    NULL,
    s.kompetensi_jabatan
FROM tmp_analisis_tugas_sk AS s
JOIN jabatan AS j ON j.kode_jabatan = s.kode_jabatan
WHERE NOT EXISTS (
    SELECT 1
    FROM analisis_tugas AS a
    WHERE a.id_jabatan = j.id_jabatan
      AND a.id_pegawai IS NULL
      AND a.tugas = s.tugas
);

DROP TEMPORARY TABLE tmp_analisis_tugas_sk;

COMMIT;
