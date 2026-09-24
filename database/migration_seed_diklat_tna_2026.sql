-- Data diklat berdasarkan Dokumen Training Need Analysis 2026.

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
