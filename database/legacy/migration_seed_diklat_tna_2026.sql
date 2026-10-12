-- Data kebutuhan diklat berdasarkan Dokumen Training Need Analysis 2026.

ALTER TABLE kebutuhan_diklat
    MODIFY prioritas ENUM('Sangat Tinggi', 'Tinggi', 'Sedang', 'Rendah') NOT NULL DEFAULT 'Sedang';

DELETE FROM kebutuhan_diklat;

INSERT INTO diklat (nama_diklat)
SELECT 'Diklat Administrasi Perkara Perdata'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Diklat Administrasi Perkara Perdata');

INSERT INTO diklat (nama_diklat)
SELECT 'Pelatihan SIPP dan e-Berpadu'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Pelatihan SIPP dan e-Berpadu');

INSERT INTO diklat (nama_diklat)
SELECT 'Diklat Administrasi Perkara Tipikor'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Diklat Administrasi Perkara Tipikor');

INSERT INTO diklat (nama_diklat)
SELECT 'Pelatihan Teknis Perkara PHI'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Pelatihan Teknis Perkara PHI');

INSERT INTO diklat (nama_diklat)
SELECT 'Pelatihan Dokumentasi dan Pelaporan'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Pelatihan Dokumentasi dan Pelaporan');

INSERT INTO diklat (nama_diklat)
SELECT 'Diklat Administrasi Kepegawaian'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Diklat Administrasi Kepegawaian');

INSERT INTO diklat (nama_diklat)
SELECT 'Pelatihan Monitoring Evaluasi dan TI'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Pelatihan Monitoring Evaluasi dan TI');

INSERT INTO diklat (nama_diklat)
SELECT 'Diklat Pengelolaan BMN dan Keuangan'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Diklat Pengelolaan BMN dan Keuangan');

INSERT INTO diklat (nama_diklat)
SELECT 'Pelatihan Service Excellent, Pelatihan PTSP'
WHERE NOT EXISTS (SELECT 1 FROM diklat WHERE nama_diklat = 'Pelatihan Service Excellent, Pelatihan PTSP');

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', 2026,
       'Perlu peningkatan administrasi perkara dan ketelitian kerja'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Diklat Administrasi Perkara Perdata'
WHERE j.nama_jabatan = 'Panitera Muda Perdata'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', 2026,
       'Perlu pemahaman aplikasi pidana'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Pelatihan SIPP dan e-Berpadu'
WHERE j.nama_jabatan = 'Panitera Muda Pidana'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Diklat teknis kepaniteraan', 'Tinggi', 'Diusulkan', 2026,
       'Perlu peningkatan administrasi perkara dan ketelitian kerja'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Diklat Administrasi Perkara Tipikor'
WHERE j.nama_jabatan LIKE 'Panitera Muda%Tipikor'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Diklat teknis kepaniteraan', 'Sedang', 'Diusulkan', 2026,
       'Perlu peningkatan administrasi perkara PHI dan ketelitian kerja'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Pelatihan Teknis Perkara PHI'
WHERE j.nama_jabatan LIKE 'Panitera Muda%PHI'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Diklat teknis kepaniteraan', 'Sedang', 'Diusulkan', 2026,
       'Perlu arsiparis perkara'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Pelatihan Dokumentasi dan Pelaporan'
WHERE j.nama_jabatan = 'Panitera Muda Hukum'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Diklat + coaching', 'Tinggi', 'Diusulkan', 2026,
       'Administrasi ASN, Penguasaan Aplikasi SIKEP, SI ASN, KOMDANAS, E KINERJA'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Diklat Administrasi Kepegawaian'
WHERE j.nama_jabatan LIKE 'Kepala Sub Bagian%Kepegawaian%'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Bimtek Kepegawaian', 'Sedang', 'Diusulkan', 2026,
       'Monitoring Perencanaan, TI dan Pelaporan'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Pelatihan Monitoring Evaluasi dan TI'
WHERE j.nama_jabatan LIKE 'Kepala Sub Bagian%Perencanaan%'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'Bimtek Kepegawaian', 'Tinggi', 'Diusulkan', 2026,
       'Perlu penambahan personil dan pelatihan untuk Aplikasi di Keuangan'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Diklat Pengelolaan BMN dan Keuangan'
WHERE j.nama_jabatan LIKE 'Kepala Sub Bagian%Umum%Keuangan%'
LIMIT 1;

INSERT INTO kebutuhan_diklat
    (id_pegawai, id_diklat, metode_pengembangan, prioritas, status, tahun_rencana, catatan)
SELECT p.id_pegawai, d.id_diklat, 'In House Training', 'Sangat Tinggi', 'Diusulkan', 2026,
       'Perlu service excellent dan pengaduan publik'
FROM pegawai p
JOIN jabatan j ON j.id_jabatan = p.id_jabatan
JOIN diklat d ON d.nama_diklat = 'Pelatihan Service Excellent, Pelatihan PTSP'
WHERE j.nama_jabatan = 'Petugas PTSP'
LIMIT 1;
