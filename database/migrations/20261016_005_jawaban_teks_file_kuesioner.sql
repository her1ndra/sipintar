ALTER TABLE `hasil_kuesioner`
  ADD COLUMN `daftar_jawaban` longtext DEFAULT NULL AFTER `daftar_nilai`,
  ADD COLUMN `file_jawaban` varchar(255) DEFAULT NULL AFTER `file_bukti`;
