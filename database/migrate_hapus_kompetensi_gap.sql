USE si_pintar;

ALTER TABLE analisis_kesenjangan_kompetensi
    DROP FOREIGN KEY fk_kesenjangan_kompetensi,
    DROP COLUMN id_kompetensi;
