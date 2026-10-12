CREATE TABLE IF NOT EXISTS jenis_kegiatan_hakim (
    nama_jenis VARCHAR(100) NOT NULL PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO jenis_kegiatan_hakim (nama_jenis) VALUES
    ('Narasumber'),
    ('Bimtek/Pelatihan'),
    ('Pengajar');

INSERT IGNORE INTO jenis_kegiatan_hakim (nama_jenis)
SELECT DISTINCT jenis_kegiatan
FROM kegiatan_hakim
WHERE jenis_kegiatan IS NOT NULL AND TRIM(jenis_kegiatan) <> '';

ALTER TABLE kegiatan_hakim
    MODIFY jenis_kegiatan VARCHAR(100) NOT NULL;