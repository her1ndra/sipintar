ALTER TABLE hasil_wawancara
    ADD COLUMN nilai TINYINT UNSIGNED NULL
        COMMENT 'Nilai 0-100'
        AFTER isi_penilaian;
