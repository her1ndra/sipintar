-- Menyimpan nama diklat yang belum tersedia pada katalog master.

ALTER TABLE kebutuhan_diklat
    ADD COLUMN IF NOT EXISTS diklat_lainnya VARCHAR(200) NULL AFTER id_diklat;
