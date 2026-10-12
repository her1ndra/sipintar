ALTER TABLE jawaban_wawancara
    ADD COLUMN status_kompetensi ENUM('Kompeten', 'Cukup', 'Tidak Kompeten')
    NOT NULL DEFAULT 'Tidak Kompeten' AFTER foto_bukti;
