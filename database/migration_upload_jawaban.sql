ALTER TABLE jawaban_wawancara
    ADD COLUMN file_jawaban VARCHAR(255) NULL AFTER jawaban_teks,
    ADD COLUMN foto_bukti VARCHAR(255) NULL AFTER file_jawaban;
