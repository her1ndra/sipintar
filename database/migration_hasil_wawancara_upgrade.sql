ALTER TABLE hasil_wawancara
    ADD COLUMN kompetensi VARCHAR(200) NOT NULL AFTER id_wawancara,
    ADD COLUMN isi_penilaian TEXT NOT NULL AFTER kompetensi,
    DROP INDEX id_wawancara,
    ADD INDEX idx_hasil_wawancara (id_wawancara);
