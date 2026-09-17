-- =====================================================================
-- SI PINTAR - Sistem Informasi Penilaian dan Pengembangan Kompetensi
--             Aparatur
-- Database  : si_pintar
-- Engine    : InnoDB | Charset: utf8mb4
--
-- CATATAN PERUBAHAN:
-- - Tabel pegawai disederhanakan: hanya Nama, NIP, Jabatan (+status aktif)
-- - Role aplikasi hanya 2: Admin & Penilai (user)
-- - Otorisasi "siapa menilai siapa" diatur lewat tabel wewenang_penilaian
--   (Ketua/Wakil Ketua -> Hakim, Panitera -> Panmud/PP/Jurusita/JSP/Staf
--    Kepaniteraan, Sekretaris -> Kasubbag/Staf Kesekretariatan)
-- - Kuesioner dibuat sendiri oleh masing-masing user Penilai
-- =====================================================================

CREATE DATABASE IF NOT EXISTS si_pintar
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE si_pintar;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- 1. MASTER JABATAN & OTORISASI PENILAIAN
-- =====================================================================

-- 1.1 Master seluruh jabatan (baik yang berperan sebagai penilai
--     maupun yang dinilai)
CREATE TABLE jabatan (
    id_jabatan      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_jabatan    VARCHAR(20)  NOT NULL UNIQUE,
    nama_jabatan    VARCHAR(100) NOT NULL,
    is_penilai      TINYINT(1)   NOT NULL DEFAULT 0
        COMMENT '1 = jabatan ini berpotensi menjadi user Penilai (Ketua/Wakil/Panitera/Sekretaris)',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1.2 Wewenang Penilaian: memetakan jabatan penilai -> jabatan yang
--     berhak dinilai olehnya
CREATE TABLE wewenang_penilaian (
    id_wewenang         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_jabatan_penilai  INT UNSIGNED NOT NULL,
    id_jabatan_dinilai  INT UNSIGNED NOT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wewenang (id_jabatan_penilai, id_jabatan_dinilai),
    CONSTRAINT fk_wewenang_penilai
        FOREIGN KEY (id_jabatan_penilai) REFERENCES jabatan(id_jabatan)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_wewenang_dinilai
        FOREIGN KEY (id_jabatan_dinilai) REFERENCES jabatan(id_jabatan)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================================
-- 2. DATA PEGAWAI (disederhanakan) & SERTIFIKAT
-- =====================================================================

-- 2.1 Data Pegawai: Nama, NIP, Jabatan saja (inti)
--     dibuat_oleh WAJIB diisi id_user dengan role Admin (dicek di PHP,
--     bukan constraint SQL, karena MySQL tidak bisa cross-check ENUM
--     role pada tabel lain via FK biasa)
CREATE TABLE pegawai (
    id_pegawai      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nip             VARCHAR(30)  NOT NULL UNIQUE,
    nama_lengkap    VARCHAR(150) NOT NULL,
    id_jabatan      INT UNSIGNED NOT NULL,
    status_aktif    ENUM('Aktif','Non-Aktif') NOT NULL DEFAULT 'Aktif',
    dibuat_oleh     INT UNSIGNED NULL COMMENT 'users.id_user (role Admin) yang menginput data ini',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pegawai_jabatan
        FOREIGN KEY (id_jabatan) REFERENCES jabatan(id_jabatan)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_pegawai_dibuat_oleh
        FOREIGN KEY (dibuat_oleh) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2.2 Analisis tugas per jabatan/pegawai
CREATE TABLE analisis_tugas (
    id_analisis_tugas              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_jabatan                     INT UNSIGNED NOT NULL,
    tugas                          TEXT NOT NULL,
    kegiatan                       TEXT NOT NULL,
    kompetensi_sementara_jabatan   TEXT NULL,
    kompetensi_jabatan             TEXT NOT NULL,
    created_at                     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_analisis_tugas_jabatan
        FOREIGN KEY (id_jabatan) REFERENCES jabatan(id_jabatan)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2.2 Sertifikat Pegawai (1 pegawai bisa punya banyak sertifikat)
--     dibuat_oleh juga WAJIB Admin (dicek di PHP)
CREATE TABLE sertifikat_pegawai (
    id_sertifikat       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pegawai          INT UNSIGNED NOT NULL,
    nama_sertifikat     VARCHAR(200) NOT NULL,
    penyelenggara       VARCHAR(150) NULL,
    tanggal_terbit      DATE NULL,
    tanggal_kadaluarsa  DATE NULL,
    file_sertifikat     VARCHAR(255) NULL COMMENT 'Path file upload sertifikat',
    dibuat_oleh         INT UNSIGNED NULL COMMENT 'users.id_user (role Admin) yang menginput data ini',
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sertifikat_pegawai
        FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_sertifikat_dibuat_oleh
        FOREIGN KEY (dibuat_oleh) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================================
-- 3. AKUN APLIKASI (ROLE: ADMIN & PENILAI)
-- =====================================================================

-- 3.1 Users - hanya 2 role. Untuk role Penilai, WAJIB terhubung ke
--     pegawai (identitas & jabatannya menentukan siapa yang boleh
--     dia nilai, lihat wewenang_penilaian)
CREATE TABLE users (
    id_user         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pegawai      INT UNSIGNED NULL UNIQUE
        COMMENT 'Wajib diisi jika role = Penilai; boleh NULL untuk Admin murni',
    nip             VARCHAR(30)  NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('Admin','Penilai') NOT NULL DEFAULT 'Penilai',
    status_aktif    TINYINT(1) NOT NULL DEFAULT 1,
    last_login      DATETIME NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_pegawai
        FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================================
-- 4. KOMPETENSI, KUESIONER (DIBUAT SENDIRI OLEH PENILAI) & WAWANCARA
-- =====================================================================

-- 4.1 Master Kompetensi (opsional ditautkan ke pertanyaan, dipakai
--     untuk keperluan analisis gap)
CREATE TABLE kompetensi (
    id_kompetensi   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_kompetensi VARCHAR(20)  NOT NULL UNIQUE,
    nama_kompetensi VARCHAR(150) NOT NULL,
    jenis_kompetensi ENUM('Manajerial','Teknis','Sosiokultural','Pemerintahan') NOT NULL,
    deskripsi       TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.2 Standar Kompetensi per Jabatan (level minimal, dasar hitung gap)
CREATE TABLE standar_kompetensi_jabatan (
    id_standar      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_jabatan      INT UNSIGNED NOT NULL COMMENT 'Jabatan pihak yang DINILAI',
    id_kompetensi   INT UNSIGNED NOT NULL,
    level_minimal   TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Skala 1-5',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_jabatan_kompetensi (id_jabatan, id_kompetensi),
    CONSTRAINT fk_standar_jabatan
        FOREIGN KEY (id_jabatan) REFERENCES jabatan(id_jabatan)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_standar_kompetensi
        FOREIGN KEY (id_kompetensi) REFERENCES kompetensi(id_kompetensi)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.3 Kuesioner: dibuat dan dimiliki oleh masing-masing user Penilai,
--     ditujukan untuk satu jabatan target (yang menjadi wewenangnya)
CREATE TABLE kuesioner (
    id_kuesioner    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user_pembuat INT UNSIGNED NOT NULL COMMENT 'users.id_user dengan role Penilai',
    id_jabatan_dinilai INT UNSIGNED NOT NULL
        COMMENT 'Harus salah satu jabatan yang ada di wewenang_penilaian milik pembuat',
    judul_kuesioner VARCHAR(200) NOT NULL,
    tahun_periode   YEAR NOT NULL,
    status          ENUM('Draft','Aktif','Non-Aktif') NOT NULL DEFAULT 'Draft',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kuesioner_user
        FOREIGN KEY (id_user_pembuat) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_kuesioner_jabatan_dinilai
        FOREIGN KEY (id_jabatan_dinilai) REFERENCES jabatan(id_jabatan)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.4 Daftar Pertanyaan dalam Kuesioner (dibuat bebas oleh pemilik
--     kuesioner, opsional ditautkan ke satu kompetensi)
CREATE TABLE pertanyaan_kuesioner (
    id_pertanyaan   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_kuesioner    INT UNSIGNED NULL COMMENT 'Opsional; wawancara dapat dibuat tanpa kuesioner',
    id_kompetensi   INT UNSIGNED NULL,
    nomor_urut      INT UNSIGNED NOT NULL DEFAULT 1,
    teks_pertanyaan TEXT NOT NULL,
    bobot           DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pertanyaan_kuesioner
        FOREIGN KEY (id_kuesioner) REFERENCES kuesioner(id_kuesioner)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_pertanyaan_kompetensi
        FOREIGN KEY (id_kompetensi) REFERENCES kompetensi(id_kompetensi)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE detail_wawancara (
    id_detail       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL,
    kompetensi      VARCHAR(200) NOT NULL,
    isi_penilaian   TEXT NOT NULL,
    foto_bukti      VARCHAR(255) NULL,
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_detail_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE hasil_wawancara (
    id_hasil        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL,
    kompetensi      VARCHAR(200) NOT NULL,
    isi_penilaian   TEXT NOT NULL,
    nilai           TINYINT UNSIGNED NULL COMMENT 'Nilai 0-100',
    file_bukti      VARCHAR(255) NULL,
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_hasil_wawancara (id_wawancara),
    CONSTRAINT fk_hasil_wawancara_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*
CREATE TABLE hasil_wawancara_legacy (
    id_hasil        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL UNIQUE,
    file_bukti      VARCHAR(255) NULL,
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hasil_wawancara_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

-- 4.5 Sesi Wawancara / Pengisian Kuesioner terhadap seorang pegawai
CREATE TABLE wawancara (
    id_wawancara    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pegawai      INT UNSIGNED NOT NULL COMMENT 'Pegawai yang diwawancara/dinilai',
    id_kuesioner    INT UNSIGNED NOT NULL,
    id_user_penilai INT UNSIGNED NOT NULL COMMENT 'users.id_user yang melakukan wawancara',
    tanggal_wawancara DATE NOT NULL,
    status          ENUM('Terjadwal','Berlangsung','Selesai','Batal') NOT NULL DEFAULT 'Terjadwal',
    catatan         TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wawancara_pegawai
        FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_wawancara_kuesioner
        FOREIGN KEY (id_kuesioner) REFERENCES kuesioner(id_kuesioner)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_wawancara_penilai
        FOREIGN KEY (id_user_penilai) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE bukti_wawancara (
    id_bukti        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL,
    file_bukti      VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_bukti_wawancara (id_wawancara),
    CONSTRAINT fk_bukti_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4.6 Jawaban per Pertanyaan dalam satu sesi Wawancara
CREATE TABLE jawaban_wawancara (
    id_jawaban      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL,
    id_pertanyaan   INT UNSIGNED NOT NULL,
    jawaban_teks    TEXT NULL,
    file_jawaban    VARCHAR(255) NULL COMMENT 'File jawaban kuesioner',
    foto_bukti      VARCHAR(255) NULL COMMENT 'Foto bukti kompetensi wawancara',
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
    skor            DECIMAL(5,2) NULL COMMENT 'Skor 1-5 hasil penilaian jawaban',
    catatan         TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wawancara_pertanyaan (id_wawancara, id_pertanyaan),
    CONSTRAINT fk_jawaban_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_jawaban_pertanyaan
        FOREIGN KEY (id_pertanyaan) REFERENCES pertanyaan_kuesioner(id_pertanyaan)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE hasil_kuesioner (
    id_hasil        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL UNIQUE,
    file_bukti      VARCHAR(255) NULL,
    daftar_nilai    TEXT NULL COMMENT 'JSON nilai per pertanyaan',
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hasil_kuesioner_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================================
-- 5. PENILAIAN KOMPETENSI & BUKTI DOKUMENTASI
-- =====================================================================

-- 5.1 Hasil Penilaian Kompetensi (per pegawai, per kompetensi, per periode)
CREATE TABLE penilaian_kompetensi (
    id_penilaian    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pegawai      INT UNSIGNED NOT NULL,
    id_kompetensi   INT UNSIGNED NOT NULL,
    id_wawancara    INT UNSIGNED NULL COMMENT 'Sumber data wawancara (jika ada)',
    id_user_penilai INT UNSIGNED NOT NULL COMMENT 'users.id_user yang menetapkan status',
    periode_penilaian YEAR NOT NULL,
    skor_akhir      DECIMAL(5,2) NULL,
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL,
    tanggal_penilaian DATE NOT NULL,
    catatan         TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pegawai_kompetensi_periode (id_pegawai, id_kompetensi, periode_penilaian),
    CONSTRAINT fk_penilaian_pegawai
        FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_penilaian_kompetensi
        FOREIGN KEY (id_kompetensi) REFERENCES kompetensi(id_kompetensi)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_penilaian_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_penilaian_user_penilai
        FOREIGN KEY (id_user_penilai) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5.2 Bukti Dokumentasi Pendukung Penilaian (upload file)
CREATE TABLE bukti_dokumentasi (
    id_bukti        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_penilaian    INT UNSIGNED NOT NULL,
    nama_file       VARCHAR(255) NOT NULL,
    path_file       VARCHAR(255) NOT NULL,
    jenis_bukti     ENUM('Foto','Dokumen','Sertifikat','Surat Tugas','Lainnya') NOT NULL DEFAULT 'Dokumen',
    uploaded_by     INT UNSIGNED NULL COMMENT 'users.id_user',
    tanggal_upload  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    keterangan      TEXT NULL,
    CONSTRAINT fk_bukti_penilaian
        FOREIGN KEY (id_penilaian) REFERENCES penilaian_kompetensi(id_penilaian)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_bukti_uploader
        FOREIGN KEY (uploaded_by) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================================
-- 6. ANALISIS GAP KOMPETENSI & KEBUTUHAN DIKLAT
-- =====================================================================

-- 6.1 Analisis Kesenjangan (Gap) Kompetensi
CREATE TABLE analisis_kesenjangan_kompetensi (
    id_kesenjangan                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_jabatan                     INT UNSIGNED NOT NULL,
    id_pegawai                     INT UNSIGNED NOT NULL,
    kompetensi_jabatan             TEXT NOT NULL,
    kompetensi_pegawai_saat_ini    TEXT NOT NULL,
    gap_kompetensi                 TEXT NOT NULL,
    dampak                         TEXT NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_gap_jabatan
        FOREIGN KEY (id_jabatan) REFERENCES jabatan(id_jabatan)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_gap_pegawai
        FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6.2 Katalog Diklat/Pelatihan
CREATE TABLE diklat (
    id_diklat       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_kompetensi   INT UNSIGNED NULL,
    nama_diklat     VARCHAR(200) NOT NULL,
    penyelenggara   VARCHAR(150) NULL,
    deskripsi       TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_diklat_kompetensi
        FOREIGN KEY (id_kompetensi) REFERENCES kompetensi(id_kompetensi)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6.3 Kebutuhan Diklat (rekomendasi tindak lanjut dari hasil gap)
CREATE TABLE kebutuhan_diklat (
    id_kebutuhan    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pegawai      INT UNSIGNED NOT NULL,
    id_gap          INT UNSIGNED NULL,
    id_diklat       INT UNSIGNED NULL,
    prioritas       ENUM('Tinggi','Sedang','Rendah') NOT NULL DEFAULT 'Sedang',
    status          ENUM('Diusulkan','Disetujui','Terlaksana','Ditolak') NOT NULL DEFAULT 'Diusulkan',
    tahun_rencana   YEAR NOT NULL,
    catatan         TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_kebutuhan_pegawai
        FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_kebutuhan_gap
        FOREIGN KEY (id_gap) REFERENCES analisis_gap(id_gap)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_kebutuhan_diklat
        FOREIGN KEY (id_diklat) REFERENCES diklat(id_diklat)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================================
-- 7. TRACING KEGIATAN HAKIM
--    (Narasumber, Bimtek/Pelatihan, Pengajar)
-- =====================================================================

CREATE TABLE kegiatan_hakim (
    id_kegiatan     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pegawai      INT UNSIGNED NOT NULL COMMENT 'Harus pegawai dengan jabatan Hakim (divalidasi di aplikasi)',
    jenis_kegiatan  ENUM('Narasumber','Bimtek/Pelatihan','Pengajar') NOT NULL,
    nama_kegiatan   VARCHAR(200) NOT NULL,
    penyelenggara   VARCHAR(150) NULL,
    peran_topik     VARCHAR(200) NULL COMMENT 'Topik/materi/peran spesifik dalam kegiatan',
    tanggal_mulai   DATE NOT NULL,
    tanggal_selesai DATE NULL,
    lokasi          VARCHAR(150) NULL,
    file_bukti      VARCHAR(255) NULL,
    keterangan      TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kegiatan_pegawai
        FOREIGN KEY (id_pegawai) REFERENCES pegawai(id_pegawai)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- 8. SEED DATA: MASTER JABATAN & WEWENANG PENILAIAN
-- =====================================================================

INSERT INTO jabatan (kode_jabatan, nama_jabatan, is_penilai) VALUES
    ('KTU', 'Ketua',                    1),
    ('WKL', 'Wakil Ketua',              1),
    ('PAN', 'Panitera',                 1),
    ('SEK', 'Sekretaris',               1),
    ('HKM', 'Hakim',                    0),
    ('PM',  'Panitera Muda',            0),
    ('PP',  'Panitera Pengganti',       0),
    ('JS',  'Jurusita',                 0),
    ('JSP', 'Jurusita Pengganti',       0),
    ('SKP', 'Staf Kepaniteraan',        0),
    ('KSB', 'Kepala Sub Bagian',        0),
    ('SKS', 'Staf Kesekretariatan',     0);

-- Ketua & Wakil Ketua -> menilai Hakim
INSERT INTO wewenang_penilaian (id_jabatan_penilai, id_jabatan_dinilai)
SELECT p.id_jabatan, d.id_jabatan
FROM jabatan p, jabatan d
WHERE p.kode_jabatan IN ('KTU','WKL') AND d.kode_jabatan = 'HKM';

-- Panitera -> menilai Panitera Muda, Panitera Pengganti, Jurusita,
--             Jurusita Pengganti, Staf Kepaniteraan
INSERT INTO wewenang_penilaian (id_jabatan_penilai, id_jabatan_dinilai)
SELECT p.id_jabatan, d.id_jabatan
FROM jabatan p, jabatan d
WHERE p.kode_jabatan = 'PAN' AND d.kode_jabatan IN ('PM','PP','JS','JSP','SKP');

-- Sekretaris -> menilai Kasubbag, Staf Kesekretariatan
INSERT INTO wewenang_penilaian (id_jabatan_penilai, id_jabatan_dinilai)
SELECT p.id_jabatan, d.id_jabatan
FROM jabatan p, jabatan d
WHERE p.kode_jabatan = 'SEK' AND d.kode_jabatan IN ('KSB','SKS');
