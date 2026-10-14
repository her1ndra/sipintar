CREATE TABLE IF NOT EXISTS `dokumentasi_kuesioner` (
  `id_dokumentasi` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_dokumentasi` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_dokumentasi`),
  KEY `idx_dokumentasi_kuesioner_sesi` (`id_wawancara`),
  CONSTRAINT `fk_dokumentasi_kuesioner_sesi`
    FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
