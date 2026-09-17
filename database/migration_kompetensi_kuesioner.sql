ALTER TABLE kuesioner
    ADD COLUMN kompetensi VARCHAR(200) NOT NULL DEFAULT '' AFTER id_jabatan_dinilai;

UPDATE kuesioner k
JOIN jabatan j ON j.id_jabatan = k.id_jabatan_dinilai
SET k.kompetensi = j.nama_jabatan
WHERE k.kompetensi = '';
