CREATE TABLE IF NOT EXISTS `status_baca_admin` (
  `id_wawancara` int(10) UNSIGNED NOT NULL,
  `id_user_admin` int(10) UNSIGNED NOT NULL,
  `dibaca_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_wawancara`, `id_user_admin`),
  KEY `idx_status_baca_admin_user` (`id_user_admin`),
  CONSTRAINT `fk_status_baca_admin_wawancara`
    FOREIGN KEY (`id_wawancara`) REFERENCES `wawancara` (`id_wawancara`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_status_baca_admin_user`
    FOREIGN KEY (`id_user_admin`) REFERENCES `users` (`id_user`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
