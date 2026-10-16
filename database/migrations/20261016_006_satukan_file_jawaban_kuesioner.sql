UPDATE `hasil_kuesioner`
SET `file_bukti` = COALESCE(`file_bukti`, `file_jawaban`)
WHERE `file_jawaban` IS NOT NULL;

ALTER TABLE `hasil_kuesioner`
  DROP COLUMN `file_jawaban`;
