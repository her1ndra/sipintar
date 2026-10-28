<?php

function dashboardPenilaianRingkasan(PDO $pdo, ?array $jabatanIds = null, ?int $idUserAdmin = null): array
{
    $scope = '';
    $scopeParams = [];
    if ($jabatanIds !== null) {
        if (!$jabatanIds) {
            return [
                'selesai' => 0,
                'perlu_ditindaklanjuti' => 0,
                'total' => 0,
                'wawancara_selesai' => 0,
                'wawancara_total' => 0,
                'kuesioner_selesai' => 0,
                'kuesioner_total' => 0,
                'belum_dibaca_wawancara' => 0,
                'belum_dibaca_kuesioner' => 0,
                'tren' => dashboardPenilaianTrenKosong(),
            ];
        }
        $placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
        $scope = " AND p.id_jabatan IN ($placeholders)";
        $scopeParams = array_map('intval', $jabatanIds);
    }

    $interviews = $pdo->prepare(
        "SELECT w.id_wawancara, COUNT(h.id_hasil) AS total_pertanyaan,
                COUNT(h.nilai) AS pertanyaan_dinilai
         FROM wawancara w
         JOIN pegawai p ON p.id_pegawai = w.id_pegawai
         LEFT JOIN hasil_wawancara h ON h.id_wawancara = w.id_wawancara
         WHERE w.id_kuesioner IS NULL $scope
         GROUP BY w.id_wawancara"
    );
    $interviews->execute($scopeParams);
    $interviewRows = $interviews->fetchAll(PDO::FETCH_ASSOC);
    $interviewTotal = count($interviewRows);
    $interviewComplete = 0;
    foreach ($interviewRows as $row) {
        if ((int) $row['total_pertanyaan'] > 0
            && (int) $row['total_pertanyaan'] === (int) $row['pertanyaan_dinilai']) {
            $interviewComplete++;
        }
    }

    $questionnaireScope = '';
    $questionnaireParams = [];
    if ($jabatanIds !== null) {
        $questionnaireScope = " AND (p.id_jabatan IN ($placeholders)
             OR k.id_jabatan_dinilai IN ($placeholders))";
        $questionnaireParams = array_merge($scopeParams, $scopeParams);
    }
    $questionnaires = $pdo->prepare(
        "SELECT w.id_wawancara, q.id_pertanyaan, q.nomor_urut,
                hk.daftar_nilai, hk.daftar_jawaban
         FROM wawancara w
         JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
         JOIN pertanyaan_kuesioner q ON q.id_kuesioner = k.id_kuesioner
         LEFT JOIN pegawai p ON p.id_pegawai = w.id_pegawai
         LEFT JOIN hasil_kuesioner hk ON hk.id_wawancara = w.id_wawancara
         WHERE w.id_kuesioner IS NOT NULL $questionnaireScope
         ORDER BY w.id_wawancara, q.nomor_urut"
    );
    $questionnaires->execute($questionnaireParams);
    $questionnaireSessions = [];
    foreach ($questionnaires->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $sessionId = (int) $row['id_wawancara'];
        if (!isset($questionnaireSessions[$sessionId])) {
            $scores = json_decode((string) ($row['daftar_nilai'] ?? ''), true);
            $answers = json_decode((string) ($row['daftar_jawaban'] ?? ''), true);
            $questionnaireSessions[$sessionId] = [
                'jumlah_pertanyaan' => 0,
                'pertanyaan_dijawab' => 0,
                'scores' => is_array($scores) ? $scores : [],
                'answers' => is_array($answers) ? $answers : [],
            ];
        }
        $session = &$questionnaireSessions[$sessionId];
        $session['jumlah_pertanyaan']++;
        $questionId = (string) $row['id_pertanyaan'];
        $scoreIndex = (int) $row['nomor_urut'] - 1;
        $score = $session['scores'][$scoreIndex] ?? null;
        $answer = $session['answers'][$questionId] ?? '';
        if (($score !== null && $score !== '')
            || (is_string($answer) && trim($answer) !== '')) {
            $session['pertanyaan_dijawab']++;
        }
        unset($session);
    }
    $questionnaireTotal = count($questionnaireSessions);
    $questionnaireComplete = 0;
    foreach ($questionnaireSessions as $session) {
        if ($session['jumlah_pertanyaan'] > 0
            && $session['jumlah_pertanyaan'] === $session['pertanyaan_dijawab']) {
            $questionnaireComplete++;
        }
    }

    $unread = ['wawancara' => 0, 'kuesioner' => 0];
    if ($idUserAdmin !== null) {
        foreach ([
            'wawancara' => 'w.id_kuesioner IS NULL',
            'kuesioner' => 'w.id_kuesioner IS NOT NULL',
        ] as $type => $typeCondition) {
            $unreadQuery = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM wawancara w
                 JOIN pegawai p ON p.id_pegawai = w.id_pegawai
                 LEFT JOIN status_baca_admin s
                   ON s.id_wawancara = w.id_wawancara AND s.id_user_admin = ?
                 WHERE $typeCondition AND s.id_wawancara IS NULL $scope"
            );
            $unreadQuery->execute(array_merge([$idUserAdmin], $scopeParams));
            $unread[$type] = (int) $unreadQuery->fetchColumn();
        }
    }

    $trendScope = '';
    $trendParams = [];
    if ($jabatanIds !== null) {
        $trendScope = " AND (p.id_jabatan IN ($placeholders)
             OR (w.id_kuesioner IS NOT NULL AND k.id_jabatan_dinilai IN ($placeholders)))";
        $trendParams = array_merge($scopeParams, $scopeParams);
    }
    $firstMonth = new DateTimeImmutable('first day of this month');
    $firstMonth = $firstMonth->modify('-5 months');
    $endMonth = (new DateTimeImmutable('first day of this month'))->modify('+1 month');
    $trendQuery = $pdo->prepare(
        "SELECT DATE_FORMAT(w.created_at, '%Y-%m') AS bulan,
                SUM(w.id_kuesioner IS NULL) AS wawancara,
                SUM(w.id_kuesioner IS NOT NULL) AS kuesioner
         FROM wawancara w
         JOIN pegawai p ON p.id_pegawai = w.id_pegawai
         LEFT JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
         WHERE w.created_at >= ? AND w.created_at < ? $trendScope
         GROUP BY DATE_FORMAT(w.created_at, '%Y-%m')"
    );
    $trendQuery->execute(array_merge(
        [$firstMonth->format('Y-m-d H:i:s'), $endMonth->format('Y-m-d H:i:s')],
        $trendParams
    ));
    $trendByMonth = [];
    foreach ($trendQuery->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $trendByMonth[$row['bulan']] = [
            'wawancara' => (int) $row['wawancara'],
            'kuesioner' => (int) $row['kuesioner'],
        ];
    }

    $labels = [];
    $interviewTrend = [];
    $questionnaireTrend = [];
    $monthNames = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];
    for ($monthOffset = 0; $monthOffset < 6; $monthOffset++) {
        $month = $firstMonth->modify('+' . $monthOffset . ' months');
        $key = $month->format('Y-m');
        $labels[] = $monthNames[(int) $month->format('n')] . ' ' . $month->format('Y');
        $interviewTrend[] = $trendByMonth[$key]['wawancara'] ?? 0;
        $questionnaireTrend[] = $trendByMonth[$key]['kuesioner'] ?? 0;
    }

    $complete = $interviewComplete + $questionnaireComplete;
    $total = $interviewTotal + $questionnaireTotal;
    return [
        'selesai' => $complete,
        'perlu_ditindaklanjuti' => $total - $complete,
        'total' => $total,
        'wawancara_selesai' => $interviewComplete,
        'wawancara_total' => $interviewTotal,
        'kuesioner_selesai' => $questionnaireComplete,
        'kuesioner_total' => $questionnaireTotal,
        'belum_dibaca_wawancara' => $unread['wawancara'],
        'belum_dibaca_kuesioner' => $unread['kuesioner'],
        'tren' => [
            'labels' => $labels,
            'wawancara' => $interviewTrend,
            'kuesioner' => $questionnaireTrend,
        ],
    ];
}

function dashboardPenilaianTrenKosong(): array
{
    $month = new DateTimeImmutable('first day of this month');
    $month = $month->modify('-5 months');
    $monthNames = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];
    $labels = [];
    for ($monthOffset = 0; $monthOffset < 6; $monthOffset++) {
        $currentMonth = $month->modify('+' . $monthOffset . ' months');
        $labels[] = $monthNames[(int) $currentMonth->format('n')] . ' ' . $currentMonth->format('Y');
    }
    return ['labels' => $labels, 'wawancara' => [0, 0, 0, 0, 0, 0], 'kuesioner' => [0, 0, 0, 0, 0, 0]];
}
