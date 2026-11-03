<?php

function downloadBuktiPenilaianWord(PDO $pdo, int $idWawancara, string $jenis, ?array $jabatanIds): void
{
    $module = [
        'kuesioner' => [
            'uploads' => 'kuesioner',
            'documentation_table' => 'dokumentasi_kuesioner',
            'documentation_column' => 'file_dokumentasi',
            'title' => 'KUESIONER',
        ],
        'wawancara' => [
            'uploads' => 'wawancara',
            'documentation_table' => 'dokumentasi_wawancara',
            'documentation_column' => 'file_dokumentasi',
            'title' => 'WAWANCARA',
        ],
    ][$jenis] ?? null;
    if ($module === null) {
        http_response_code(400);
        exit('Jenis penilaian tidak valid.');
    }

    $params = [$idWawancara];
    $authorization = '';
    if ($jabatanIds !== null) {
        if (!$jabatanIds) {
            http_response_code(403);
            exit('Anda tidak memiliki kewenangan untuk mengunduh data ini.');
        }
        $placeholders = implode(',', array_fill(0, count($jabatanIds), '?'));
        $authorization = " AND (p.id_jabatan IN ($placeholders) OR k.id_jabatan_dinilai IN ($placeholders))";
        $params = array_merge($params, $jabatanIds, $jabatanIds);
    }
    $jenisWawancara = $jenis === 'kuesioner' ? 'IS NOT NULL' : 'IS NULL';
    $statement = $pdo->prepare(
        "SELECT w.id_wawancara, w.tanggal_wawancara, p.nama_lengkap, j.nama_jabatan,
                k.judul_kuesioner, k.kompetensi
         FROM wawancara w
         JOIN pegawai p ON p.id_pegawai = w.id_pegawai
         JOIN jabatan j ON j.id_jabatan = p.id_jabatan
         LEFT JOIN kuesioner k ON k.id_kuesioner = w.id_kuesioner
         WHERE w.id_wawancara = ? AND w.id_kuesioner $jenisWawancara$authorization"
    );
    $statement->execute($params);
    $session = $statement->fetch();
    if (!$session) {
        http_response_code($jabatanIds === null ? 404 : 403);
        exit('Data penilaian tidak ditemukan atau Anda tidak memiliki kewenangan.');
    }

    if ($jenis === 'kuesioner') {
        $proofStatement = $pdo->prepare(
            'SELECT file_bukti FROM hasil_kuesioner WHERE id_wawancara = ? AND file_bukti IS NOT NULL'
        );
        $proofStatement->execute([$idWawancara]);
        $proofFiles = array_map(static function (string $path): array {
            return ['path' => $path, 'label' => 'Bukti kompetensi kuesioner'];
        }, $proofStatement->fetchAll(PDO::FETCH_COLUMN));
    } else {
        $proofStatement = $pdo->prepare(
            'SELECT file_bukti FROM bukti_wawancara WHERE id_wawancara = ? ORDER BY id_bukti'
        );
        $proofStatement->execute([$idWawancara]);
        $sessionProofPaths = $proofStatement->fetchAll(PDO::FETCH_COLUMN);
        $proofFiles = array_map(static function (string $path, int $index): array {
            return ['path' => $path, 'label' => 'Bukti wawancara ' . ($index + 1)];
        }, $sessionProofPaths, array_keys($sessionProofPaths));

        $detailStatement = $pdo->prepare(
            'SELECT kompetensi, isi_penilaian, file_bukti
             FROM hasil_wawancara
             WHERE id_wawancara = ? AND file_bukti IS NOT NULL
             ORDER BY id_hasil'
        );
        $detailStatement->execute([$idWawancara]);
        foreach ($detailStatement->fetchAll() as $index => $detail) {
            $proofFiles[] = [
                'path' => $detail['file_bukti'],
                'label' => 'Bukti pertanyaan ' . ($index + 1) . ': ' . $detail['isi_penilaian'],
            ];
        }
    }

    $documentationStatement = $pdo->prepare(
        'SELECT ' . $module['documentation_column'] . ' FROM ' . $module['documentation_table']
        . ' WHERE id_wawancara = ? ORDER BY id_dokumentasi'
    );
    $documentationStatement->execute([$idWawancara]);
    $documentationPaths = $documentationStatement->fetchAll(PDO::FETCH_COLUMN);
    $documentationFiles = array_map(static function (string $path, int $index): array {
        return ['path' => $path, 'label' => 'Dokumentasi ' . ($index + 1)];
    }, $documentationPaths, array_keys($documentationPaths));

    createBuktiPenilaianWord($module['title'], $module['uploads'], $session, $proofFiles, $documentationFiles);
}

function createBuktiPenilaianWord(
    string $title,
    string $uploadFolder,
    array $session,
    array $proofFiles,
    array $documentationFiles
): void {
    if (!class_exists(ZipArchive::class)) {
        throw new RuntimeException('Ekstensi ZipArchive diperlukan untuk membuat dokumen Word.');
    }

    $xmlText = static function (string $value): string {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value);
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    };
    $paragraph = static function (string $value, bool $bold = false, int $size = 22) use ($xmlText): string {
        $properties = '<w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial"/><w:sz w:val="'
            . $size . '"/>' . ($bold ? '<w:b/>' : '') . '</w:rPr>';
        $runs = [];
        foreach (preg_split('/\r\n|\r|\n/', $value) as $index => $line) {
            if ($index > 0) {
                $runs[] = '<w:br/>';
            }
            $runs[] = '<w:t xml:space="preserve">' . $xmlText($line) . '</w:t>';
        }
        return '<w:p><w:pPr><w:spacing w:after="120"/></w:pPr><w:r>' . $properties
            . implode('', $runs) . '</w:r></w:p>';
    };

    $documentBody = $paragraph($title . ' - DOKUMENTASI DAN BUKTI', true, 30)
        . $paragraph('Nama: ' . $session['nama_lengkap'])
        . $paragraph('Jabatan: ' . $session['nama_jabatan'])
        . $paragraph('Tanggal: ' . date('d-m-Y', strtotime($session['tanggal_wawancara'])));
    if (!empty($session['judul_kuesioner'])) {
        $documentBody .= $paragraph('Judul kuesioner: ' . $session['judul_kuesioner']);
    }
    if (!empty($session['kompetensi'])) {
        $documentBody .= $paragraph('Kompetensi: ' . $session['kompetensi']);
    }

    $imageParts = [];
    $relationships = [];
    $contentTypes = [];
    $nextRelationshipId = 1;
    $drawingId = 1;
    $appendFiles = static function (string $heading, array $files) use (
        &$documentBody,
        &$imageParts,
        &$relationships,
        &$contentTypes,
        &$nextRelationshipId,
        &$drawingId,
        $paragraph,
        $xmlText,
        $uploadFolder
    ): void {
        $documentBody .= $paragraph($heading, true, 24);
        if (!$files) {
            $documentBody .= $paragraph('Tidak ada berkas.');
            return;
        }
        foreach ($files as $index => $file) {
            $path = (string) ($file['path'] ?? '');
            $label = (string) ($file['label'] ?? 'Berkas');
            $safePath = preg_match(
                '#^assets/uploads/' . preg_quote($uploadFolder, '#') . '/[A-Za-z0-9._-]+$#D',
                $path
            ) === 1;
            $root = realpath(__DIR__ . '/../assets/uploads/' . $uploadFolder);
            $absolutePath = $safePath ? realpath(__DIR__ . '/../' . $path) : false;
            if ($root === false || $absolutePath === false || dirname($absolutePath) !== $root) {
                $documentBody .= $paragraph($label . ' - berkas tidak tersedia.');
                continue;
            }

            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($absolutePath);
            if ($mime === 'application/pdf') {
                $entry = 'word/embeddings/bukti-' . ($index + 1) . '.pdf';
                $imageParts[$entry] = $absolutePath;
                $contentTypes['pdf'] = 'application/pdf';
                $documentBody .= $paragraph($label . ' - PDF disertakan dalam paket Word.');
                continue;
            }
            $imageInfo = @getimagesize($absolutePath);
            if (!in_array($mime, ['image/jpeg', 'image/png'], true) || $imageInfo === false
                || $imageInfo[0] < 1 || $imageInfo[1] < 1) {
                $documentBody .= $paragraph($label . ' - tipe berkas tidak didukung.');
                continue;
            }

            $extension = $mime === 'image/png' ? 'png' : 'jpg';
            $target = 'word/media/image-' . $drawingId . '.' . $extension;
            $relationshipId = 'rId' . $nextRelationshipId++;
            $relationships[] = '<Relationship Id="' . $relationshipId
                . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image-'
                . $drawingId . '.' . $extension . '"/>';
            $contentTypes[$extension] = $mime;
            $imageParts[$target] = $absolutePath;

            $maxWidth = 5943600;
            $maxHeight = 7315200;
            $width = $imageInfo[0] * 9525;
            $height = $imageInfo[1] * 9525;
            $scale = min(1, $maxWidth / $width, $maxHeight / $height);
            $width = (int) round($width * $scale);
            $height = (int) round($height * $scale);
            $escapedLabel = $xmlText($label);
            $documentBody .= $paragraph($label, true)
                . '<w:p><w:r><w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0">'
                . '<wp:extent cx="' . $width . '" cy="' . $height . '"/>'
                . '<wp:docPr id="' . $drawingId . '" name="' . $escapedLabel . '"/>'
                . '<wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1"/></wp:cNvGraphicFramePr>'
                . '<a:graphic><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
                . '<pic:pic><pic:nvPicPr><pic:cNvPr id="' . $drawingId . '" name="image-' . $drawingId . '.' . $extension . '"/>'
                . '<pic:cNvPicPr/></pic:nvPicPr><pic:blipFill><a:blip r:embed="' . $relationshipId . '"/>'
                . '<a:stretch><a:fillRect/></a:stretch></pic:blipFill><pic:spPr><a:xfrm>'
                . '<a:off x="0" y="0"/><a:ext cx="' . $width . '" cy="' . $height . '"/>'
                . '</a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>'
                . '</pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r></w:p>';
            $drawingId++;
        }
    };
    $appendFiles('BUKTI DAN DOKUMENTASI', array_merge($proofFiles, $documentationFiles));

    $documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
        . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'
        . ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"'
        . ' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"'
        . ' xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><w:body>'
        . $documentBody
        . '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="900" w:right="900" w:bottom="900" w:left="900"/></w:sectPr>'
        . '</w:body></w:document>';
    $types = '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>';
    foreach ($contentTypes as $extension => $mime) {
        $types .= '<Default Extension="' . $extension . '" ContentType="' . $mime . '"/>';
    }
    $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' . $types
        . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
        . '</Types>';
    $rootRelationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
        . '</Relationships>';
    $documentRelationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . implode('', $relationships) . '</Relationships>';

    $temporaryFile = tempnam(sys_get_temp_dir(), 'sipintar-bukti-');
    if ($temporaryFile === false) {
        throw new RuntimeException('Dokumen bukti penilaian gagal dibuat.');
    }
    $archive = new ZipArchive();
    if ($archive->open($temporaryFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        unlink($temporaryFile);
        throw new RuntimeException('Dokumen bukti penilaian gagal dibuat.');
    }
    $written = $archive->addFromString('[Content_Types].xml', $contentTypesXml);
    $written = $archive->addFromString('_rels/.rels', $rootRelationships) && $written;
    $written = $archive->addFromString('word/document.xml', $documentXml) && $written;
    $written = $archive->addFromString('word/_rels/document.xml.rels', $documentRelationships) && $written;
    foreach ($imageParts as $entry => $path) {
        $written = $archive->addFile($path, $entry) && $written;
    }
    $closed = $archive->close();
    if (!$written || !$closed) {
        unlink($temporaryFile);
        throw new RuntimeException('Dokumen bukti penilaian gagal dibuat.');
    }

    $prefix = strtolower($title) === 'kuesioner' ? 'kuesioner' : 'wawancara';
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $prefix . '-bukti-' . (int) $session['id_wawancara'] . '.docx"');
    header('Content-Length: ' . filesize($temporaryFile));
    readfile($temporaryFile);
    unlink($temporaryFile);
    exit;
}
