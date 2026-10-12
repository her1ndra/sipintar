-- Migrasi 001: tabel pendukung keamanan (audit log).
-- Jalankan lewat:  php database/migrate.php

CREATE TABLE IF NOT EXISTS `audit_log` (
  `id_log` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` int(10) UNSIGNED DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `aksi` varchar(50) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `idx_audit_user` (`id_user`),
  KEY `idx_audit_waktu` (`created_at`),
  KEY `idx_audit_aksi` (`aksi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
