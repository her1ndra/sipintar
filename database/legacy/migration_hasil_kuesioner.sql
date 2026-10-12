CREATE TABLE hasil_kuesioner (
    id_hasil        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL UNIQUE,
    file_bukti      VARCHAR(255) NULL,
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_hasil_kuesioner_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
