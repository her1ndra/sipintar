CREATE TABLE IF NOT EXISTS `pemeriksaan_bukti_wawancara` (
  `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `file_bukti` varchar(255) NOT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `diperiksa_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pemeriksaan`),
  UNIQUE KEY `uq_pemeriksaan_bukti_penilai` (`id_wawancara`, `file_bukti`, `id_user_penilai`),
  KEY `idx_pemeriksaan_bukti_penilai` (`id_user_penilai`),
  CONSTRAINT `fk_pemeriksaan_bukti_wawancara`
    FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_pemeriksaan_bukti_penilai`
    FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
