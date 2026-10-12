-- Menyelaraskan tabel kebutuhan_diklat dengan menu penentuan diklat.
-- id_pegawai, id_kesenjangan, dan id_diklat menjadi sumber:
-- nama pegawai, gap kompetensi, dan diklat yang dibutuhkan.

ALTER TABLE kebutuhan_diklat
    ADD COLUMN IF NOT EXISTS metode_pengembangan VARCHAR(100) NULL AFTER id_diklat;
