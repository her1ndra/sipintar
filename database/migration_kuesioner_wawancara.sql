-- Menghubungkan kuesioner lama dengan pegawai yang tersimpan di database.
-- Mapping berasal dari data saat ini:
-- kuesioner 1 -> pegawai 70, kuesioner 2 -> pegawai 67,
-- kuesioner 3 -> pegawai 58.
-- Jika belum ada Penilai yang berwenang, pembuat kuesioner dipakai
-- sebagai fallback agar sesi tetap dapat diisi.

START TRANSACTION;

INSERT INTO wawancara (
    id_pegawai,
    id_kuesioner,
    id_user_penilai,
    tanggal_wawancara,
    status
)
SELECT
    p.id_pegawai,
    k.id_kuesioner,
    COALESCE((
        SELECT u.id_user
        FROM users u
        JOIN pegawai penilai ON penilai.id_pegawai = u.id_pegawai
        JOIN wewenang_penilaian wp
            ON wp.id_jabatan_penilai = penilai.id_jabatan
           AND wp.id_jabatan_dinilai = p.id_jabatan
        WHERE u.role = 'Penilai'
          AND u.status_aktif = 1
        ORDER BY u.id_user
        LIMIT 1
    ), k.id_user_pembuat),
    CURDATE(),
    'Terjadwal'
FROM kuesioner k
JOIN pegawai p ON p.id_pegawai = k.id_jabatan_dinilai
WHERE k.id_kuesioner IN (1, 2, 3)
  AND NOT EXISTS (
      SELECT 1
      FROM wawancara w
      WHERE w.id_kuesioner = k.id_kuesioner
  );

COMMIT;
