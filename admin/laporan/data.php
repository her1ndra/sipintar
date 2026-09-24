<?php
function laporanPeriode(array $input): array
{
    $mode = ($input['periode'] ?? 'tahunan') === 'bulanan' ? 'bulanan' : 'tahunan';
    $year = (int) ($input['tahun'] ?? date('Y'));
    $year = max(2000, min(2100, $year));

    if ($mode === 'bulanan') {
        $month = (int) ($input['bulan'] ?? date('n'));
        $month = max(1, min(12, $month));
        $start = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
        $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $label = $monthNames[$month] . ' ' . $year;
        return [$mode, $year, $month, $start, $start->modify('+1 month'), $label];
    }

    $start = new DateTimeImmutable(sprintf('%04d-01-01', $year));
    return [$mode, $year, null, $start, $start->modify('+1 year'), 'Tahun ' . $year];
}

function laporanAmbilData(PDO $pdo, DateTimeImmutable $start, DateTimeImmutable $end): array
{
    $from = $start->format('Y-m-d H:i:s');
    $to = $end->format('Y-m-d H:i:s');
    $params = [$from, $to];
    $ambil = static function (PDO $pdo, string $sql, array $params) {
        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    };
    $filter = static function (string $alias): string {
        return " WHERE {$alias}.created_at >= ? AND {$alias}.created_at < ? ";
    };

    return [
        'analisis_tugas' => $ambil($pdo, "SELECT j.nama_jabatan, p.nama_lengkap, x.tugas, x.kegiatan,
            x.kompetensi_sementara_jabatan, x.kompetensi_jabatan, x.created_at
            FROM analisis_tugas x JOIN jabatan j ON j.id_jabatan = x.id_jabatan
            LEFT JOIN pegawai p ON p.id_pegawai = x.id_pegawai" . $filter('x') . ' ORDER BY j.nama_jabatan, x.created_at', $params),
        'gap' => $ambil($pdo, "SELECT j.nama_jabatan, p.nama_lengkap, x.kompetensi_jabatan,
            x.kompetensi_pegawai_saat_ini, x.gap_kompetensi, x.dampak, x.created_at
            FROM analisis_kesenjangan_kompetensi x JOIN jabatan j ON j.id_jabatan = x.id_jabatan
            JOIN pegawai p ON p.id_pegawai = x.id_pegawai" . $filter('x') . ' ORDER BY j.nama_jabatan, p.nama_lengkap', $params),
        'diklat' => $ambil($pdo, "SELECT p.nama_lengkap, j.nama_jabatan,
            COALESCE(NULLIF(x.diklat_lainnya, ''), d.nama_diklat, '-') AS nama_diklat,
            x.metode_pengembangan, x.prioritas, x.status, x.tahun_rencana, x.catatan, x.created_at
            FROM kebutuhan_diklat x JOIN pegawai p ON p.id_pegawai = x.id_pegawai
            JOIN jabatan j ON j.id_jabatan = p.id_jabatan LEFT JOIN diklat d ON d.id_diklat = x.id_diklat" . $filter('x') . ' ORDER BY x.prioritas, p.nama_lengkap', $params),
        'kuesioner' => $ambil($pdo, "SELECT x.judul_kuesioner, x.kompetensi, x.tahun_periode,
            x.status, j.nama_jabatan, x.created_at
            FROM kuesioner x JOIN jabatan j ON j.id_jabatan = x.id_jabatan_dinilai" . $filter('x') . ' ORDER BY x.created_at DESC', $params),
        'wawancara' => $ambil($pdo, "SELECT p.nama_lengkap, j.nama_jabatan, x.tanggal_wawancara,
            x.status, x.catatan, x.created_at
            FROM wawancara x JOIN pegawai p ON p.id_pegawai = x.id_pegawai
            JOIN jabatan j ON j.id_jabatan = p.id_jabatan" . $filter('x') . ' ORDER BY x.tanggal_wawancara, p.nama_lengkap', $params),
        'hasil_wawancara' => $ambil($pdo, "SELECT p.nama_lengkap, h.kompetensi, h.nilai,
            h.status_kompetensi, h.isi_penilaian, h.created_at
            FROM hasil_wawancara h JOIN wawancara w ON w.id_wawancara = h.id_wawancara
            JOIN pegawai p ON p.id_pegawai = w.id_pegawai" . $filter('h') . ' ORDER BY p.nama_lengkap, h.created_at', $params),
        'sertifikat' => $ambil($pdo, "SELECT p.nama_lengkap, s.nama_sertifikat, s.penyelenggara,
            s.tanggal_terbit, s.tanggal_kadaluarsa, s.created_at
            FROM sertifikat_pegawai s JOIN pegawai p ON p.id_pegawai = s.id_pegawai" . $filter('s') . ' ORDER BY s.tanggal_terbit DESC', $params),
        'kegiatan_hakim' => $ambil($pdo, "SELECT p.nama_lengkap, k.jenis_kegiatan, k.nama_kegiatan,
            k.penyelenggara, k.peran_topik, k.tanggal_mulai, k.tanggal_selesai, k.lokasi, k.created_at
            FROM kegiatan_hakim k JOIN pegawai p ON p.id_pegawai = k.id_pegawai" . $filter('k') . ' ORDER BY k.tanggal_mulai DESC', $params),
    ];
}

function laporanLabel(string $key): string
{
    return [
        'analisis_tugas' => 'Analisis Tugas',
        'gap' => 'Analisis Kesenjangan Kompetensi',
        'diklat' => 'Kebutuhan Diklat',
        'kuesioner' => 'Kuesioner Kompetensi',
        'wawancara' => 'Wawancara',
        'hasil_wawancara' => 'Hasil Wawancara',
        'sertifikat' => 'Sertifikat Pegawai',
        'kegiatan_hakim' => 'Kegiatan Hakim',
    ][$key] ?? $key;
}

function laporanNilai($value): string
{
    $value = trim((string) ($value ?? ''));
    return $value === '' ? '-' : $value;
}