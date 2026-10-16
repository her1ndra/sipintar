CREATE TABLE IF NOT EXISTS `pemeriksaan_jawaban_kuesioner` (
  `id_pemeriksaan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_hasil` int(10) UNSIGNED NOT NULL,
  `id_user_penilai` int(10) UNSIGNED NOT NULL,
  `jawaban_hash` char(64) NOT NULL,
  `diperiksa_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pemeriksaan`),
  UNIQUE KEY `uq_pemeriksaan_jawaban_kuesioner_penilai` (`id_hasil`, `id_user_penilai`),
  KEY `idx_pemeriksaan_jawaban_kuesioner_penilai` (`id_user_penilai`),
  CONSTRAINT `fk_pemeriksaan_jawaban_kuesioner_hasil`
    FOREIGN KEY (`id_hasil`) REFERENCES `hasil_kuesioner` (`id_hasil`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_pemeriksaan_jawaban_kuesioner_penilai`
    FOREIGN KEY (`id_user_penilai`) REFERENCES `users` (`id_user`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
