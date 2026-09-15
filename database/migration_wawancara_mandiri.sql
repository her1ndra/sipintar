ALTER TABLE wawancara
    MODIFY id_kuesioner INT UNSIGNED NULL;

CREATE TABLE detail_wawancara (
    id_detail       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL,
    kompetensi      VARCHAR(200) NOT NULL,
    isi_penilaian   TEXT NOT NULL,
    foto_bukti      VARCHAR(255) NULL,
    status_kompetensi ENUM('Kompeten','Cukup','Tidak Kompeten') NOT NULL DEFAULT 'Tidak Kompeten',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_detail_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
