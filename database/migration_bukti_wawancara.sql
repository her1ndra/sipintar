CREATE TABLE bukti_wawancara (
    id_bukti        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_wawancara    INT UNSIGNED NOT NULL,
    file_bukti      VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_bukti_wawancara (id_wawancara),
    CONSTRAINT fk_bukti_wawancara
        FOREIGN KEY (id_wawancara) REFERENCES wawancara(id_wawancara)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
