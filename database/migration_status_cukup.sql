ALTER TABLE detail_wawancara
    MODIFY status_kompetensi ENUM('Kompeten', 'Cukup', 'Tidak Kompeten')
    NOT NULL DEFAULT 'Tidak Kompeten';
