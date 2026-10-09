<?php
function kegiatanHakimWordElement(DOMDocument $document, string $namespace, string $name, ?string $text = null): DOMElement
{
    $element = $document->createElementNS($namespace, $name);
    if ($text !== null) {
        $element->appendChild($document->createTextNode($text));
    }
    return $element;
}

function kegiatanHakimWordSetAttribute(DOMElement $element, string $namespace, string $name, string $value): void
{
    $prefix = $namespace === 'http://schemas.openxmlformats.org/wordprocessingml/2006/main' ? 'w:' : 'r:';
    $element->setAttributeNS($namespace, $prefix . $name, $value);
}

function buatDokumenWordKegiatanHakim(array $data, ?string $attachmentPath, ?string $attachmentUrl): string
{
    if (!class_exists(ZipArchive::class)) {
        throw new RuntimeException('Ekstensi ZipArchive diperlukan untuk membuat dokumen Word.');
    }

    $wordNs = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    $relationshipNs = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    $packageRelationshipNs = 'http://schemas.openxmlformats.org/package/2006/relationships';
    $contentTypeNs = 'http://schemas.openxmlformats.org/package/2006/content-types';
    $drawingNs = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';
    $drawingMainNs = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    $pictureNs = 'http://schemas.openxmlformats.org/drawingml/2006/picture';

    $image = null;
    if ($attachmentPath && is_file($attachmentPath)) {
        $imageInfo = getimagesize($attachmentPath);
        if ($imageInfo && in_array($imageInfo['mime'], ['image/jpeg', 'image/png'], true)) {
            $image = [
                'path' => $attachmentPath,
                'extension' => $imageInfo['mime'] === 'image/png' ? 'png' : 'jpg',
                'contentType' => $imageInfo['mime'],
                'width' => (int) $imageInfo[0],
                'height' => (int) $imageInfo[1],
            ];
        }
    }

    $document = new DOMDocument('1.0', 'UTF-8');
    $document->formatOutput = true;
    $documentRoot = kegiatanHakimWordElement($document, $wordNs, 'w:document');
    $documentRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:r', $relationshipNs);
    $documentRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:wp', $drawingNs);
    $documentRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:a', $drawingMainNs);
    $documentRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:pic', $pictureNs);
    $document->appendChild($documentRoot);
    $body = kegiatanHakimWordElement($document, $wordNs, 'w:body');
    $documentRoot->appendChild($body);

    $title = kegiatanHakimWordElement($document, $wordNs, 'w:p');
    $titleProperties = kegiatanHakimWordElement($document, $wordNs, 'w:pPr');
    $titleProperties->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:jc'));
    kegiatanHakimWordSetAttribute($titleProperties->lastChild, $wordNs, 'val', 'center');
    $title->appendChild($titleProperties);
    $titleRun = kegiatanHakimWordElement($document, $wordNs, 'w:r');
    $titleRunProperties = kegiatanHakimWordElement($document, $wordNs, 'w:rPr');
    $titleRunProperties->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:b'));
    $titleRunProperties->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:sz'));
    kegiatanHakimWordSetAttribute($titleRunProperties->lastChild, $wordNs, 'val', '32');
    $titleRun->appendChild($titleRunProperties);
    $titleRun->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:t', 'DATA KEGIATAN HAKIM'));
    $title->appendChild($titleRun);
    $body->appendChild($title);

    $table = kegiatanHakimWordElement($document, $wordNs, 'w:tbl');
    $tableProperties = kegiatanHakimWordElement($document, $wordNs, 'w:tblPr');
    $tableWidth = kegiatanHakimWordElement($document, $wordNs, 'w:tblW');
    kegiatanHakimWordSetAttribute($tableWidth, $wordNs, 'w', '9360');
    kegiatanHakimWordSetAttribute($tableWidth, $wordNs, 'type', 'dxa');
    $tableProperties->appendChild($tableWidth);
    $borders = kegiatanHakimWordElement($document, $wordNs, 'w:tblBorders');
    foreach (['top', 'left', 'bottom', 'right', 'insideH', 'insideV'] as $edge) {
        $border = kegiatanHakimWordElement($document, $wordNs, 'w:' . $edge);
        kegiatanHakimWordSetAttribute($border, $wordNs, 'val', 'single');
        kegiatanHakimWordSetAttribute($border, $wordNs, 'sz', '4');
        kegiatanHakimWordSetAttribute($border, $wordNs, 'space', '0');
        kegiatanHakimWordSetAttribute($border, $wordNs, 'color', '777777');
        $borders->appendChild($border);
    }
    $tableProperties->appendChild($borders);
    $table->appendChild($tableProperties);
    $tableGrid = kegiatanHakimWordElement($document, $wordNs, 'w:tblGrid');
    foreach (['2600', '6760'] as $columnWidth) {
        $gridColumn = kegiatanHakimWordElement($document, $wordNs, 'w:gridCol');
        kegiatanHakimWordSetAttribute($gridColumn, $wordNs, 'w', $columnWidth);
        $tableGrid->appendChild($gridColumn);
    }
    $table->appendChild($tableGrid);

    $details = [
        'Nama' => $data['nama_lengkap'],
        'Jenis kegiatan' => $data['jenis_kegiatan'],
        'Nama kegiatan' => $data['nama_kegiatan'],
        'Penyelenggara' => $data['penyelenggara'] ?: '-',
        'Tanggal' => $data['tanggal_mulai'] . ($data['tanggal_selesai'] ? ' s.d. ' . $data['tanggal_selesai'] : ''),
        'Tempat' => $data['lokasi'] ?: '-',
    ];
    foreach ($details as $label => $value) {
        $row = kegiatanHakimWordElement($document, $wordNs, 'w:tr');
        foreach ([$label, (string) $value] as $index => $text) {
            $cell = kegiatanHakimWordElement($document, $wordNs, 'w:tc');
            $cellProperties = kegiatanHakimWordElement($document, $wordNs, 'w:tcPr');
            $cellWidth = kegiatanHakimWordElement($document, $wordNs, 'w:tcW');
            kegiatanHakimWordSetAttribute($cellWidth, $wordNs, 'w', $index === 0 ? '2600' : '6760');
            kegiatanHakimWordSetAttribute($cellWidth, $wordNs, 'type', 'dxa');
            $cellProperties->appendChild($cellWidth);
            if ($index === 0) {
                $shading = kegiatanHakimWordElement($document, $wordNs, 'w:shd');
                kegiatanHakimWordSetAttribute($shading, $wordNs, 'val', 'clear');
                kegiatanHakimWordSetAttribute($shading, $wordNs, 'fill', 'EEEEEE');
                $cellProperties->appendChild($shading);
            }
            $cell->appendChild($cellProperties);
            $paragraph = kegiatanHakimWordElement($document, $wordNs, 'w:p');
            $run = kegiatanHakimWordElement($document, $wordNs, 'w:r');
            if ($index === 0) {
                $runProperties = kegiatanHakimWordElement($document, $wordNs, 'w:rPr');
                $runProperties->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:b'));
                $run->appendChild($runProperties);
            }
            $run->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:t', $text));
            $paragraph->appendChild($run);
            $cell->appendChild($paragraph);
            $row->appendChild($cell);
        }
        $table->appendChild($row);
    }
    $body->appendChild($table);

    $relationships = [[
        'id' => 'rId1',
        'type' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument',
        'target' => 'word/document.xml',
        'external' => false,
    ]];
    $attachmentHeading = kegiatanHakimWordElement($document, $wordNs, 'w:p');
    $attachmentHeadingRun = kegiatanHakimWordElement($document, $wordNs, 'w:r');
    $attachmentHeadingProperties = kegiatanHakimWordElement($document, $wordNs, 'w:rPr');
    $attachmentHeadingProperties->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:b'));
    $attachmentHeadingRun->appendChild($attachmentHeadingProperties);
    $attachmentHeadingRun->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:t', 'Lampiran surat tugas'));
    $attachmentHeading->appendChild($attachmentHeadingRun);
    $body->appendChild($attachmentHeading);

    if ($image) {
        $maxWidth = 5486400;
        $maxHeight = 7772400;
        $scale = min(1, $maxWidth / $image['width'], $maxHeight / $image['height']);
        $width = (int) round($image['width'] * $scale * 9525);
        $height = (int) round($image['height'] * $scale * 9525);
        $imageRelationshipId = 'rId2';
        $relationships[] = [
            'id' => $imageRelationshipId,
            'type' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image',
            'target' => 'media/lampiran.' . $image['extension'],
            'external' => false,
        ];

        $imageParagraph = kegiatanHakimWordElement($document, $wordNs, 'w:p');
        $imageRun = kegiatanHakimWordElement($document, $wordNs, 'w:r');
        $drawing = kegiatanHakimWordElement($document, $wordNs, 'w:drawing');
        $inline = kegiatanHakimWordElement($document, $drawingNs, 'wp:inline');
        foreach (['distT', 'distB', 'distL', 'distR'] as $distance) {
            $inline->setAttribute($distance, '0');
        }
        $extent = kegiatanHakimWordElement($document, $drawingNs, 'wp:extent');
        $extent->setAttribute('cx', (string) $width);
        $extent->setAttribute('cy', (string) $height);
        $inline->appendChild($extent);
        $effectExtent = kegiatanHakimWordElement($document, $drawingNs, 'wp:effectExtent');
        foreach (['l', 't', 'r', 'b'] as $edge) {
            $effectExtent->setAttribute($edge, '0');
        }
        $inline->appendChild($effectExtent);
        $documentProperties = kegiatanHakimWordElement($document, $drawingNs, 'wp:docPr');
        $documentProperties->setAttribute('id', '1');
        $documentProperties->setAttribute('name', 'Lampiran surat tugas');
        $inline->appendChild($documentProperties);
        $frameProperties = kegiatanHakimWordElement($document, $drawingNs, 'wp:cNvGraphicFramePr');
        $frameLock = kegiatanHakimWordElement($document, $drawingMainNs, 'a:graphicFrameLocks');
        $frameLock->setAttribute('noChangeAspect', '1');
        $frameProperties->appendChild($frameLock);
        $inline->appendChild($frameProperties);
        $graphic = kegiatanHakimWordElement($document, $drawingMainNs, 'a:graphic');
        $graphicData = kegiatanHakimWordElement($document, $drawingMainNs, 'a:graphicData');
        $graphicData->setAttribute('uri', $pictureNs);
        $picture = kegiatanHakimWordElement($document, $pictureNs, 'pic:pic');
        $nonVisualProperties = kegiatanHakimWordElement($document, $pictureNs, 'pic:nvPicPr');
        $nonVisualProperties->appendChild(kegiatanHakimWordElement($document, $pictureNs, 'pic:cNvPr'));
        $nonVisualProperties->lastChild->setAttribute('id', '0');
        $nonVisualProperties->lastChild->setAttribute('name', 'lampiran.' . $image['extension']);
        $pictureProperties = kegiatanHakimWordElement($document, $pictureNs, 'pic:cNvPicPr');
        $pictureLock = kegiatanHakimWordElement($document, $drawingMainNs, 'a:picLocks');
        $pictureLock->setAttribute('noChangeAspect', '1');
        $pictureProperties->appendChild($pictureLock);
        $nonVisualProperties->appendChild($pictureProperties);
        $picture->appendChild($nonVisualProperties);
        $fill = kegiatanHakimWordElement($document, $pictureNs, 'pic:blipFill');
        $blip = kegiatanHakimWordElement($document, $drawingMainNs, 'a:blip');
        $blip->setAttributeNS($relationshipNs, 'r:embed', $imageRelationshipId);
        $fill->appendChild($blip);
        $stretch = kegiatanHakimWordElement($document, $drawingMainNs, 'a:stretch');
        $stretch->appendChild(kegiatanHakimWordElement($document, $drawingMainNs, 'a:fillRect'));
        $fill->appendChild($stretch);
        $picture->appendChild($fill);
        $shapeProperties = kegiatanHakimWordElement($document, $pictureNs, 'pic:spPr');
        $transform = kegiatanHakimWordElement($document, $drawingMainNs, 'a:xfrm');
        $transform->appendChild(kegiatanHakimWordElement($document, $drawingMainNs, 'a:off'));
        $transform->lastChild->setAttribute('x', '0');
        $transform->lastChild->setAttribute('y', '0');
        $transform->appendChild(kegiatanHakimWordElement($document, $drawingMainNs, 'a:ext'));
        $transform->lastChild->setAttribute('cx', (string) $width);
        $transform->lastChild->setAttribute('cy', (string) $height);
        $shapeProperties->appendChild($transform);
        $geometry = kegiatanHakimWordElement($document, $drawingMainNs, 'a:prstGeom');
        $geometry->setAttribute('prst', 'rect');
        $geometry->appendChild(kegiatanHakimWordElement($document, $drawingMainNs, 'a:avLst'));
        $shapeProperties->appendChild($geometry);
        $picture->appendChild($shapeProperties);
        $graphicData->appendChild($picture);
        $graphic->appendChild($graphicData);
        $inline->appendChild($graphic);
        $drawing->appendChild($inline);
        $imageRun->appendChild($drawing);
        $imageParagraph->appendChild($imageRun);
        $body->appendChild($imageParagraph);
    } elseif ($attachmentUrl) {
        $hyperlinkId = 'rId2';
        $relationships[] = [
            'id' => $hyperlinkId,
            'type' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink',
            'target' => $attachmentUrl,
            'external' => true,
        ];
        $paragraph = kegiatanHakimWordElement($document, $wordNs, 'w:p');
        $hyperlink = kegiatanHakimWordElement($document, $wordNs, 'w:hyperlink');
        $hyperlink->setAttributeNS($relationshipNs, 'r:id', $hyperlinkId);
        $run = kegiatanHakimWordElement($document, $wordNs, 'w:r');
        $runProperties = kegiatanHakimWordElement($document, $wordNs, 'w:rPr');
        $runProperties->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:color'));
        kegiatanHakimWordSetAttribute($runProperties->lastChild, $wordNs, 'val', '0563C1');
        $runProperties->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:u'));
        kegiatanHakimWordSetAttribute($runProperties->lastChild, $wordNs, 'val', 'single');
        $run->appendChild($runProperties);
        $run->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:t', 'Buka lampiran surat tugas'));
        $hyperlink->appendChild($run);
        $paragraph->appendChild($hyperlink);
        $body->appendChild($paragraph);
    } else {
        $body->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:p'));
        $body->lastChild->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:r'));
        $body->lastChild->lastChild->appendChild(kegiatanHakimWordElement($document, $wordNs, 'w:t', 'Tidak ada lampiran.'));
    }

    $section = kegiatanHakimWordElement($document, $wordNs, 'w:sectPr');
    $pageSize = kegiatanHakimWordElement($document, $wordNs, 'w:pgSz');
    kegiatanHakimWordSetAttribute($pageSize, $wordNs, 'w', '12240');
    kegiatanHakimWordSetAttribute($pageSize, $wordNs, 'h', '15840');
    $section->appendChild($pageSize);
    $pageMargins = kegiatanHakimWordElement($document, $wordNs, 'w:pgMar');
    foreach (['top' => '1440', 'right' => '1440', 'bottom' => '1440', 'left' => '1440', 'header' => '720', 'footer' => '720', 'gutter' => '0'] as $name => $value) {
        kegiatanHakimWordSetAttribute($pageMargins, $wordNs, $name, $value);
    }
    $section->appendChild($pageMargins);
    $body->appendChild($section);

    $relationshipDocument = new DOMDocument('1.0', 'UTF-8');
    $relationshipRoot = $relationshipDocument->createElementNS($packageRelationshipNs, 'Relationships');
    $relationshipDocument->appendChild($relationshipRoot);
    foreach ($relationships as $relationship) {
        $element = $relationshipDocument->createElementNS($packageRelationshipNs, 'Relationship');
        $element->setAttribute('Id', $relationship['id']);
        $element->setAttribute('Type', $relationship['type']);
        $element->setAttribute('Target', $relationship['target']);
        if ($relationship['external']) {
            $element->setAttribute('TargetMode', 'External');
        }
        $relationshipRoot->appendChild($element);
    }

    $contentTypes = new DOMDocument('1.0', 'UTF-8');
    $contentTypesRoot = $contentTypes->createElementNS($contentTypeNs, 'Types');
    $contentTypes->appendChild($contentTypesRoot);
    foreach ([
        ['Extension' => 'rels', 'ContentType' => 'application/vnd.openxmlformats-package.relationships+xml'],
        ['Extension' => 'xml', 'ContentType' => 'application/xml'],
    ] as $defaultType) {
        $default = $contentTypes->createElementNS($contentTypeNs, 'Default');
        foreach ($defaultType as $name => $value) {
            $default->setAttribute($name, $value);
        }
        $contentTypesRoot->appendChild($default);
    }
    if ($image) {
        $default = $contentTypes->createElementNS($contentTypeNs, 'Default');
        $default->setAttribute('Extension', $image['extension']);
        $default->setAttribute('ContentType', $image['contentType']);
        $contentTypesRoot->appendChild($default);
    }
    $documentType = $contentTypes->createElementNS($contentTypeNs, 'Override');
    $documentType->setAttribute('PartName', '/word/document.xml');
    $documentType->setAttribute('ContentType', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml');
    $contentTypesRoot->appendChild($documentType);

    $rootRelationships = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="' . $packageRelationshipNs . '">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
        . '</Relationships>';
    $temporaryFile = tempnam(sys_get_temp_dir(), 'kegiatan-hakim-word-');
    if ($temporaryFile === false) {
        throw new RuntimeException('File sementara Word tidak dapat dibuat.');
    }
    $zip = new ZipArchive();
    if ($zip->open($temporaryFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        unlink($temporaryFile);
        throw new RuntimeException('Dokumen Word tidak dapat dibuat.');
    }
    $zip->addFromString('[Content_Types].xml', $contentTypes->saveXML());
    $zip->addFromString('_rels/.rels', $rootRelationships);
    $zip->addFromString('word/document.xml', $document->saveXML());
    $zip->addFromString('word/_rels/document.xml.rels', $relationshipDocument->saveXML());
    if ($image) {
        $zip->addFile($image['path'], 'word/media/lampiran.' . $image['extension']);
    }
    $zip->close();
    $wordDocument = file_get_contents($temporaryFile);
    unlink($temporaryFile);
    if ($wordDocument === false) {
        throw new RuntimeException('Dokumen Word tidak dapat dibaca setelah dibuat.');
    }

    return $wordDocument;
}