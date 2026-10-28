<?php

const LAPORAN_WORD_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
const LAPORAN_DRAWING_NS = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';
const LAPORAN_PICTURE_NS = 'http://schemas.openxmlformats.org/drawingml/2006/picture';
const LAPORAN_DRAWING_MAIN_NS = 'http://schemas.openxmlformats.org/drawingml/2006/main';
const LAPORAN_REL_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

function laporanWordAppendBodyNode(DOMDocument $document, DOMElement $node): void
{
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('w', LAPORAN_WORD_NS);
    $body = $xpath->query('//w:body')->item(0);
    if (!$body instanceof DOMElement) {
        throw new RuntimeException('Bagian isi dokumen TNA tidak ditemukan.');
    }
    $section = $xpath->query('./w:sectPr', $body)->item(0);
    if ($section) {
        $body->insertBefore($node, $section);
    } else {
        $body->appendChild($node);
    }
}

function laporanWordParagraph(
    DOMDocument $document,
    string $value,
    string $alignment = 'left',
    bool $bold = false,
    int $size = 10,
    int $after = 100
): DOMElement {
    $paragraph = $document->createElementNS(LAPORAN_WORD_NS, 'w:p');
    $properties = $document->createElementNS(LAPORAN_WORD_NS, 'w:pPr');
    $spacing = $document->createElementNS(LAPORAN_WORD_NS, 'w:spacing');
    $spacing->setAttributeNS(LAPORAN_WORD_NS, 'w:after', (string) $after);
    $properties->appendChild($spacing);
    if ($alignment !== 'left') {
        $justify = $document->createElementNS(LAPORAN_WORD_NS, 'w:jc');
        $justify->setAttributeNS(LAPORAN_WORD_NS, 'w:val', $alignment);
        $properties->appendChild($justify);
    }
    $paragraph->appendChild($properties);

    foreach (preg_split('/\r\n|\r|\n/', $value) as $index => $line) {
        $run = $document->createElementNS(LAPORAN_WORD_NS, 'w:r');
        $runProperties = $document->createElementNS(LAPORAN_WORD_NS, 'w:rPr');
        $font = $document->createElementNS(LAPORAN_WORD_NS, 'w:rFonts');
        $font->setAttributeNS(LAPORAN_WORD_NS, 'w:ascii', 'Century Gothic');
        $font->setAttributeNS(LAPORAN_WORD_NS, 'w:hAnsi', 'Century Gothic');
        $runProperties->appendChild($font);
        if ($bold) {
            $runProperties->appendChild($document->createElementNS(LAPORAN_WORD_NS, 'w:b'));
        }
        $fontSize = $document->createElementNS(LAPORAN_WORD_NS, 'w:sz');
        $fontSize->setAttributeNS(LAPORAN_WORD_NS, 'w:val', (string) ($size * 2));
        $runProperties->appendChild($fontSize);
        $run->appendChild($runProperties);
        if ($index > 0) {
            $run->appendChild($document->createElementNS(LAPORAN_WORD_NS, 'w:br'));
        }
        $text = $document->createElementNS(LAPORAN_WORD_NS, 'w:t');
        $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
        $text->appendChild($document->createTextNode($line));
        $run->appendChild($text);
        $paragraph->appendChild($run);
    }
    return $paragraph;
}

function laporanWordTabularParagraph(
    DOMDocument $document,
    string $label,
    string $value,
    int $tabPosition = 1800
): DOMElement {
    $paragraph = laporanWordParagraph($document, '', 'left', false, 10, 30);
    $properties = $paragraph->firstChild;
    $tabs = $document->createElementNS(LAPORAN_WORD_NS, 'w:tabs');
    $tabStop = $document->createElementNS(LAPORAN_WORD_NS, 'w:tab');
    $tabStop->setAttributeNS(LAPORAN_WORD_NS, 'w:val', 'left');
    $tabStop->setAttributeNS(LAPORAN_WORD_NS, 'w:pos', (string) $tabPosition);
    $tabs->appendChild($tabStop);
    $properties->insertBefore($tabs, $properties->firstChild);
    while ($paragraph->childNodes->length > 1) {
        $paragraph->removeChild($paragraph->lastChild);
    }

    $createRun = static function () use ($document): DOMElement {
        $run = $document->createElementNS(LAPORAN_WORD_NS, 'w:r');
        $runProperties = $document->createElementNS(LAPORAN_WORD_NS, 'w:rPr');
        $font = $document->createElementNS(LAPORAN_WORD_NS, 'w:rFonts');
        $font->setAttributeNS(LAPORAN_WORD_NS, 'w:ascii', 'Century Gothic');
        $font->setAttributeNS(LAPORAN_WORD_NS, 'w:hAnsi', 'Century Gothic');
        $runProperties->appendChild($font);
        $fontSize = $document->createElementNS(LAPORAN_WORD_NS, 'w:sz');
        $fontSize->setAttributeNS(LAPORAN_WORD_NS, 'w:val', '20');
        $runProperties->appendChild($fontSize);
        $run->appendChild($runProperties);
        return $run;
    };

    $labelRun = $createRun();
    $labelText = $document->createElementNS(LAPORAN_WORD_NS, 'w:t');
    $labelText->appendChild($document->createTextNode($label));
    $labelRun->appendChild($labelText);
    $paragraph->appendChild($labelRun);

    $tabRun = $createRun();
    $tabRun->appendChild($document->createElementNS(LAPORAN_WORD_NS, 'w:tab'));
    $paragraph->appendChild($tabRun);

    $valueRun = $createRun();
    $valueText = $document->createElementNS(LAPORAN_WORD_NS, 'w:t');
    $valueText->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
    $valueText->appendChild($document->createTextNode(': ' . $value));
    $valueRun->appendChild($valueText);
    $paragraph->appendChild($valueRun);

    return $paragraph;
}

function laporanWordPageBreak(DOMDocument $document): DOMElement
{
    $paragraph = $document->createElementNS(LAPORAN_WORD_NS, 'w:p');
    $run = $document->createElementNS(LAPORAN_WORD_NS, 'w:r');
    $break = $document->createElementNS(LAPORAN_WORD_NS, 'w:br');
    $break->setAttributeNS(LAPORAN_WORD_NS, 'w:type', 'page');
    $run->appendChild($break);
    $paragraph->appendChild($run);
    return $paragraph;
}

function laporanWordImageRun(
    DOMDocument $document,
    string $relationshipId,
    string $name,
    int $width,
    int $height,
    int $drawingId
): DOMElement {
    $run = $document->createElementNS(LAPORAN_WORD_NS, 'w:r');
    $drawing = $document->createElementNS(LAPORAN_WORD_NS, 'w:drawing');
    $inline = $document->createElementNS(LAPORAN_DRAWING_NS, 'wp:inline');
    $inline->setAttribute('distT', '0');
    $inline->setAttribute('distB', '0');
    $inline->setAttribute('distL', '0');
    $inline->setAttribute('distR', '0');
    $extent = $document->createElementNS(LAPORAN_DRAWING_NS, 'wp:extent');
    $extent->setAttribute('cx', (string) $width);
    $extent->setAttribute('cy', (string) $height);
    $inline->appendChild($extent);
    $properties = $document->createElementNS(LAPORAN_DRAWING_NS, 'wp:docPr');
    $properties->setAttribute('id', (string) $drawingId);
    $properties->setAttribute('name', $name);
    $inline->appendChild($properties);
    $frame = $document->createElementNS(LAPORAN_DRAWING_NS, 'wp:cNvGraphicFramePr');
    $frameLock = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:graphicFrameLocks');
    $frameLock->setAttribute('noChangeAspect', '1');
    $frame->appendChild($frameLock);
    $inline->appendChild($frame);

    $graphic = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:graphic');
    $graphicData = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:graphicData');
    $graphicData->setAttribute('uri', LAPORAN_PICTURE_NS);
    $picture = $document->createElementNS(LAPORAN_PICTURE_NS, 'pic:pic');
    $nonVisual = $document->createElementNS(LAPORAN_PICTURE_NS, 'pic:nvPicPr');
    $nonVisual->appendChild($document->createElementNS(LAPORAN_PICTURE_NS, 'pic:cNvPr'));
    $nonVisual->lastChild->setAttribute('id', (string) $drawingId);
    $nonVisual->lastChild->setAttribute('name', $name);
    $nonVisual->appendChild($document->createElementNS(LAPORAN_PICTURE_NS, 'pic:cNvPicPr'));
    $picture->appendChild($nonVisual);

    $fill = $document->createElementNS(LAPORAN_PICTURE_NS, 'pic:blipFill');
    $blip = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:blip');
    $blip->setAttributeNS(LAPORAN_REL_NS, 'r:embed', $relationshipId);
    $fill->appendChild($blip);
    $stretch = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:stretch');
    $stretch->appendChild($document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:fillRect'));
    $fill->appendChild($stretch);
    $picture->appendChild($fill);

    $shape = $document->createElementNS(LAPORAN_PICTURE_NS, 'pic:spPr');
    $transform = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:xfrm');
    $offset = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:off');
    $offset->setAttribute('x', '0');
    $offset->setAttribute('y', '0');
    $transform->appendChild($offset);
    $dimensions = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:ext');
    $dimensions->setAttribute('cx', (string) $width);
    $dimensions->setAttribute('cy', (string) $height);
    $transform->appendChild($dimensions);
    $shape->appendChild($transform);
    $geometry = $document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:prstGeom');
    $geometry->setAttribute('prst', 'rect');
    $geometry->appendChild($document->createElementNS(LAPORAN_DRAWING_MAIN_NS, 'a:avLst'));
    $shape->appendChild($geometry);
    $picture->appendChild($shape);

    $graphicData->appendChild($picture);
    $graphic->appendChild($graphicData);
    $inline->appendChild($graphic);
    $drawing->appendChild($inline);
    $run->appendChild($drawing);
    return $run;
}

function laporanWordAppendixTable(
    DOMDocument $document,
    array $headings,
    array $rows,
    array $columnWidths,
    bool $plain = false
): DOMElement {
    $table = $document->createElementNS(LAPORAN_WORD_NS, 'w:tbl');
    $properties = $document->createElementNS(LAPORAN_WORD_NS, 'w:tblPr');
    $width = $document->createElementNS(LAPORAN_WORD_NS, 'w:tblW');
    $width->setAttributeNS(LAPORAN_WORD_NS, 'w:w', (string) array_sum($columnWidths));
    $width->setAttributeNS(LAPORAN_WORD_NS, 'w:type', 'dxa');
    $properties->appendChild($width);
    $layout = $document->createElementNS(LAPORAN_WORD_NS, 'w:tblLayout');
    $layout->setAttributeNS(LAPORAN_WORD_NS, 'w:type', 'fixed');
    $properties->appendChild($layout);
    $borders = $document->createElementNS(LAPORAN_WORD_NS, 'w:tblBorders');
    foreach (['top', 'left', 'bottom', 'right', 'insideH', 'insideV'] as $edge) {
        $border = $document->createElementNS(LAPORAN_WORD_NS, 'w:' . $edge);
        $border->setAttributeNS(LAPORAN_WORD_NS, 'w:val', $plain ? 'nil' : 'single');
        if (!$plain) {
            $border->setAttributeNS(LAPORAN_WORD_NS, 'w:sz', '4');
            $border->setAttributeNS(LAPORAN_WORD_NS, 'w:color', '666666');
        }
        $borders->appendChild($border);
    }
    $properties->appendChild($borders);
    $table->appendChild($properties);

    $grid = $document->createElementNS(LAPORAN_WORD_NS, 'w:tblGrid');
    foreach ($columnWidths as $columnWidth) {
        $column = $document->createElementNS(LAPORAN_WORD_NS, 'w:gridCol');
        $column->setAttributeNS(LAPORAN_WORD_NS, 'w:w', (string) $columnWidth);
        $grid->appendChild($column);
    }
    $table->appendChild($grid);

    $appendRow = static function (array $values, bool $header = false) use ($document, $table, $columnWidths, $plain): void {
        $row = $document->createElementNS(LAPORAN_WORD_NS, 'w:tr');
        $height = $document->createElementNS(LAPORAN_WORD_NS, 'w:trPr');
        $rowHeight = $document->createElementNS(LAPORAN_WORD_NS, 'w:trHeight');
        $rowHeight->setAttributeNS(LAPORAN_WORD_NS, 'w:val', $header ? '360' : '420');
        $rowHeight->setAttributeNS(LAPORAN_WORD_NS, 'w:hRule', 'atLeast');
        $height->appendChild($rowHeight);
        $row->appendChild($height);
        foreach ($columnWidths as $index => $columnWidth) {
            $cell = $document->createElementNS(LAPORAN_WORD_NS, 'w:tc');
            $cellProperties = $document->createElementNS(LAPORAN_WORD_NS, 'w:tcPr');
            $cellWidth = $document->createElementNS(LAPORAN_WORD_NS, 'w:tcW');
            $cellWidth->setAttributeNS(LAPORAN_WORD_NS, 'w:w', (string) $columnWidth);
            $cellWidth->setAttributeNS(LAPORAN_WORD_NS, 'w:type', 'dxa');
            $cellProperties->appendChild($cellWidth);
            if ($header) {
                $shade = $document->createElementNS(LAPORAN_WORD_NS, 'w:shd');
                $shade->setAttributeNS(LAPORAN_WORD_NS, 'w:fill', 'D9EAF7');
                $cellProperties->appendChild($shade);
            }
            $cell->appendChild($cellProperties);
            $cell->appendChild(laporanWordParagraph(
                $document,
                (string) ($values[$index] ?? ''),
                $header || $index === 0 ? 'center' : 'left',
                $header,
                9,
                0
            ));
            $row->appendChild($cell);
        }
        $table->appendChild($row);
    };

    $appendRow($headings, true);
    foreach ($rows as $row) {
        $appendRow($row);
    }
    return $table;
}

function laporanWordAddRelationship(ZipArchive $zip, DOMDocument $relationships, string $filePath, string $target): string
{
    $root = $relationships->documentElement;
    if (!$root) {
        throw new RuntimeException('Relasi gambar dokumen TNA tidak valid.');
    }
    $nextId = 1;
    foreach ($root->childNodes as $relationship) {
        if ($relationship instanceof DOMElement && preg_match('/^rId(\d+)$/', $relationship->getAttribute('Id'), $match)) {
            $nextId = max($nextId, (int) $match[1] + 1);
        }
    }
    $relationshipId = 'rId' . $nextId;
    $relationship = $relationships->createElementNS(
        'http://schemas.openxmlformats.org/package/2006/relationships',
        'Relationship'
    );
    $relationship->setAttribute('Id', $relationshipId);
    $relationship->setAttribute('Type', LAPORAN_REL_NS . '/image');
    $relationship->setAttribute('Target', 'media/' . $target);
    if ($zip->addFromString('word/media/' . $target, $filePath) !== true) {
        throw new RuntimeException('Gambar lampiran laporan TNA gagal ditambahkan.');
    }
    $root->appendChild($relationship);
    return $relationshipId;
}

function laporanWordEnsureImageType(DOMDocument $contentTypes, string $extension, string $mime): void
{
    $xpath = new DOMXPath($contentTypes);
    $xpath->registerNamespace('ct', 'http://schemas.openxmlformats.org/package/2006/content-types');
    if ($xpath->query('/ct:Types/ct:Default[@Extension="' . $extension . '"]')->length > 0) {
        return;
    }
    $default = $contentTypes->createElementNS(
        'http://schemas.openxmlformats.org/package/2006/content-types',
        'Default'
    );
    $default->setAttribute('Extension', $extension);
    $default->setAttribute('ContentType', $mime);
    $contentTypes->documentElement->appendChild($default);
}

function laporanWordAddPageNumberFooter(
    DOMDocument $document,
    ZipArchive $zip,
    DOMDocument $relationships,
    DOMDocument $contentTypes
): void {
    $relationshipsXpath = new DOMXPath($relationships);
    $relationshipsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');
    $nextId = 1;
    foreach ($relationshipsXpath->query('/rel:Relationships/rel:Relationship/@Id') as $id) {
        if (preg_match('/^rId(\d+)$/', $id->nodeValue, $match)) {
            $nextId = max($nextId, (int) $match[1] + 1);
        }
    }
    $relationshipId = 'rId' . $nextId;
    $relationship = $relationships->createElementNS(
        'http://schemas.openxmlformats.org/package/2006/relationships',
        'Relationship'
    );
    $relationship->setAttribute('Id', $relationshipId);
    $relationship->setAttribute('Type', LAPORAN_REL_NS . '/footer');
    $relationship->setAttribute('Target', 'footer1.xml');
    $relationships->documentElement->appendChild($relationship);

    $footer = new DOMDocument('1.0', 'UTF-8');
    $footer->formatOutput = false;
    $root = $footer->createElementNS(LAPORAN_WORD_NS, 'w:ftr');
    $root->setAttributeNS(
        'http://www.w3.org/2000/xmlns/',
        'xmlns:r',
        LAPORAN_REL_NS
    );
    $footer->appendChild($root);
    $paragraph = $footer->createElementNS(LAPORAN_WORD_NS, 'w:p');
    $paragraphProperties = $footer->createElementNS(LAPORAN_WORD_NS, 'w:pPr');
    $alignment = $footer->createElementNS(LAPORAN_WORD_NS, 'w:jc');
    $alignment->setAttributeNS(LAPORAN_WORD_NS, 'w:val', 'center');
    $paragraphProperties->appendChild($alignment);
    $paragraph->appendChild($paragraphProperties);
    $run = $footer->createElementNS(LAPORAN_WORD_NS, 'w:r');
    $runProperties = $footer->createElementNS(LAPORAN_WORD_NS, 'w:rPr');
    $font = $footer->createElementNS(LAPORAN_WORD_NS, 'w:rFonts');
    $font->setAttributeNS(LAPORAN_WORD_NS, 'w:ascii', 'Century Gothic');
    $font->setAttributeNS(LAPORAN_WORD_NS, 'w:hAnsi', 'Century Gothic');
    $runProperties->appendChild($font);
    $fontSize = $footer->createElementNS(LAPORAN_WORD_NS, 'w:sz');
    $fontSize->setAttributeNS(LAPORAN_WORD_NS, 'w:val', '18');
    $runProperties->appendChild($fontSize);
    $run->appendChild($runProperties);

    foreach ([
        ['begin', null],
        ['instruction', ' PAGE '],
        ['separate', null],
        ['result', '1'],
        ['end', null],
    ] as [$type, $value]) {
        if ($type === 'instruction') {
            $instruction = $footer->createElementNS(LAPORAN_WORD_NS, 'w:instrText');
            $instruction->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
            $instruction->appendChild($footer->createTextNode($value));
            $run->appendChild($instruction);
        } elseif ($type === 'result') {
            $text = $footer->createElementNS(LAPORAN_WORD_NS, 'w:t');
            $text->appendChild($footer->createTextNode($value));
            $run->appendChild($text);
        } else {
            $field = $footer->createElementNS(LAPORAN_WORD_NS, 'w:fldChar');
            $field->setAttributeNS(LAPORAN_WORD_NS, 'w:fldCharType', $type);
            $run->appendChild($field);
        }
    }
    $paragraph->appendChild($run);
    $root->appendChild($paragraph);
    $footerXml = $footer->saveXML();
    if ($footerXml === false || $zip->addFromString('word/footer1.xml', $footerXml) !== true) {
        throw new RuntimeException('Footer nomor halaman laporan TNA gagal dibuat.');
    }

    $typesXpath = new DOMXPath($contentTypes);
    $typesXpath->registerNamespace('ct', 'http://schemas.openxmlformats.org/package/2006/content-types');
    if ($typesXpath->query('/ct:Types/ct:Override[@PartName="/word/footer1.xml"]')->length === 0) {
        $override = $contentTypes->createElementNS(
            'http://schemas.openxmlformats.org/package/2006/content-types',
            'Override'
        );
        $override->setAttribute('PartName', '/word/footer1.xml');
        $override->setAttribute('ContentType', 'application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml');
        $contentTypes->documentElement->appendChild($override);
    }

    $documentXpath = new DOMXPath($document);
    $documentXpath->registerNamespace('w', LAPORAN_WORD_NS);
    $section = $documentXpath->query('//w:body/w:sectPr')->item(0);
    if (!$section instanceof DOMElement) {
        throw new RuntimeException('Pengaturan halaman dokumen TNA tidak ditemukan.');
    }
    $footerReference = $document->createElementNS(LAPORAN_WORD_NS, 'w:footerReference');
    $footerReference->setAttributeNS(LAPORAN_WORD_NS, 'w:type', 'default');
    $footerReference->setAttributeNS(LAPORAN_REL_NS, 'r:id', $relationshipId);
    $pageSize = $documentXpath->query('./w:pgSz', $section)->item(0);
    if ($pageSize) {
        $section->insertBefore($footerReference, $pageSize);
    } else {
        $section->insertBefore($footerReference, $section->firstChild);
    }
}

function laporanWordSetParagraphStyle(DOMElement $paragraph, string $styleId): void
{
    $document = $paragraph->ownerDocument;
    if (!$document instanceof DOMDocument) {
        throw new RuntimeException('Paragraf daftar isi tidak valid.');
    }
    $properties = null;
    foreach ($paragraph->childNodes as $child) {
        if ($child instanceof DOMElement && $child->localName === 'pPr') {
            $properties = $child;
            break;
        }
    }
    if (!$properties) {
        $properties = $document->createElementNS(LAPORAN_WORD_NS, 'w:pPr');
        $paragraph->insertBefore($properties, $paragraph->firstChild);
    }
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('w', LAPORAN_WORD_NS);
    $style = $xpath->query('./w:pStyle', $properties)->item(0);
    $existingStyleId = $style instanceof DOMElement
        ? $style->getAttributeNS(LAPORAN_WORD_NS, 'val')
        : '';
    if (!$style instanceof DOMElement) {
        $style = $document->createElementNS(LAPORAN_WORD_NS, 'w:pStyle');
        $properties->insertBefore($style, $properties->firstChild);
    }
    $style->setAttributeNS(LAPORAN_WORD_NS, 'w:val', $styleId);
    if ($existingStyleId !== $styleId) {
        $numbering = $xpath->query('./w:numPr', $properties)->item(0);
        if ($numbering) {
            $properties->removeChild($numbering);
        }
    }
}

function laporanWordForceBlackHeadingStyles(ZipArchive $zip): void
{
    $stylesXml = $zip->getFromName('word/styles.xml');
    if ($stylesXml === false) {
        throw new RuntimeException('Gaya dokumen laporan TNA tidak ditemukan.');
    }
    $styles = new DOMDocument();
    $styles->preserveWhiteSpace = false;
    if (!$styles->loadXML($stylesXml)) {
        throw new RuntimeException('Gaya dokumen laporan TNA tidak dapat dibuka.');
    }

    $xpath = new DOMXPath($styles);
    $xpath->registerNamespace('w', LAPORAN_WORD_NS);
    $headingStyles = $xpath->query('//w:style');
    if (!$headingStyles) {
        throw new RuntimeException('Gaya judul laporan TNA tidak dapat diperiksa.');
    }
    foreach ($headingStyles as $style) {
        if (!$style instanceof DOMElement
            || !preg_match('/^(?:Heading[1-9](?:Char)?|Title|Subtitle)$/', $style->getAttributeNS(LAPORAN_WORD_NS, 'styleId'))) {
            continue;
        }
        $runProperties = $xpath->query('./w:rPr', $style)->item(0);
        if (!$runProperties instanceof DOMElement) {
            $runProperties = $styles->createElementNS(LAPORAN_WORD_NS, 'w:rPr');
            $style->appendChild($runProperties);
        }
        $color = $xpath->query('./w:color', $runProperties)->item(0);
        if (!$color instanceof DOMElement) {
            $color = $styles->createElementNS(LAPORAN_WORD_NS, 'w:color');
            $runProperties->appendChild($color);
        }
        $color->setAttributeNS(LAPORAN_WORD_NS, 'w:val', '000000');
        foreach (['themeColor', 'themeTint', 'themeShade'] as $attribute) {
            $color->removeAttributeNS(LAPORAN_WORD_NS, $attribute);
        }
    }
    $updatedStyles = $styles->saveXML();
    if ($updatedStyles === false || $zip->addFromString('word/styles.xml', $updatedStyles) !== true) {
        throw new RuntimeException('Gaya judul laporan TNA gagal disimpan.');
    }
}

function laporanWordAddContents(DOMDocument $document, ZipArchive $zip): void
{
    $titles = [
        'PENDAHULUAN',
        'TAHAP PELAKSANAAN ANALISIS GAP',
        'ANALISIS TUGAS',
        'WAWANCARA KOMPETENSI',
        'KUESIONER KOMPETENSI',
        'ANALISIS KESENJANGAN (GAP) KOMPETENSI',
        'PENENTUAN KEBUTUHAN DIKLAT',
        'LAMPIRAN',
        'SARAN',
        'PENUTUP',
    ];
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('w', LAPORAN_WORD_NS);
    $foundTitles = [];
    foreach ($xpath->query('//w:body/w:p') as $paragraph) {
        $text = '';
        $textNodes = $xpath->query('.//w:t', $paragraph);
        foreach ($textNodes as $part) {
            $text .= $part->nodeValue;
        }
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        if ($text === 'DAFTARN ISI') {
            $textNodes->item(0)->nodeValue = 'DAFTAR ISI';
            $text = 'DAFTAR ISI';
        }
        if (in_array($text, $titles, true)) {
            laporanWordSetParagraphStyle($paragraph, 'Heading1');
            $foundTitles[] = $text;
        }
    }
    foreach ($titles as $title) {
        if (!in_array($title, $foundTitles, true)) {
            throw new RuntimeException('Bagian "' . $title . '" tidak ditemukan untuk daftar isi TNA.');
        }
    }
    $tocInstruction = $xpath->query('//w:instrText[contains(., "TOC ")]')->item(0);
    if (!$tocInstruction instanceof DOMElement) {
        throw new RuntimeException('Field daftar isi otomatis tidak ditemukan pada template TNA.');
    }
    $instructionRun = $tocInstruction->parentNode;
    $fieldRun = $instructionRun ? $instructionRun->previousSibling : null;
    while ($fieldRun && !($fieldRun instanceof DOMElement && $fieldRun->localName === 'r'
        && $xpath->query('./w:fldChar[@w:fldCharType="begin"]', $fieldRun)->length > 0)) {
        $fieldRun = $fieldRun->previousSibling;
    }
    if (!$fieldRun instanceof DOMElement) {
        throw new RuntimeException('Penanda field daftar isi TNA tidak valid.');
    }
    $fieldBegin = $xpath->query('./w:fldChar[@w:fldCharType="begin"]', $fieldRun)->item(0);
    $fieldBegin->setAttributeNS(LAPORAN_WORD_NS, 'w:dirty', 'true');

    $settingsXml = $zip->getFromName('word/settings.xml');
    $settings = new DOMDocument();
    $settings->preserveWhiteSpace = false;
    if ($settingsXml === false || !$settings->loadXML($settingsXml)) {
        throw new RuntimeException('Pengaturan dokumen TNA tidak dapat dibuka.');
    }
    $settingsXpath = new DOMXPath($settings);
    $settingsXpath->registerNamespace('w', LAPORAN_WORD_NS);
    $updateFields = $settingsXpath->query('/w:settings/w:updateFields')->item(0);
    if (!$updateFields instanceof DOMElement) {
        $updateFields = $settings->createElementNS(LAPORAN_WORD_NS, 'w:updateFields');
        $shapeDefaults = $settingsXpath->query('/w:settings/w:shapeDefaults')->item(0);
        if ($shapeDefaults) {
            $settings->documentElement->insertBefore($updateFields, $shapeDefaults);
        } else {
            $settings->documentElement->appendChild($updateFields);
        }
    }
    $updateFields->setAttributeNS(LAPORAN_WORD_NS, 'w:val', 'true');
    $settingsOutput = $settings->saveXML();
    if ($settingsOutput === false || $zip->addFromString('word/settings.xml', $settingsOutput) !== true) {
        throw new RuntimeException('Pengaturan pembaruan nomor halaman daftar isi gagal disimpan.');
    }
}

function laporanWordAppendixLetterhead(
    DOMDocument $document,
    string $logoRelationship,
    int $drawingId
): void {
    $table = laporanWordAppendixTable($document, [], [], [1300, 7700], true);
    $xpath = new DOMXPath($document);
    $xpath->registerNamespace('w', LAPORAN_WORD_NS);
    $emptyRow = $xpath->query('./w:tr', $table)->item(0);
    if ($emptyRow) {
        $table->removeChild($emptyRow);
    }
    $row = $document->createElementNS(LAPORAN_WORD_NS, 'w:tr');
    foreach ([1300, 7700] as $index => $columnWidth) {
        $cell = $document->createElementNS(LAPORAN_WORD_NS, 'w:tc');
        $cellProperties = $document->createElementNS(LAPORAN_WORD_NS, 'w:tcPr');
        $width = $document->createElementNS(LAPORAN_WORD_NS, 'w:tcW');
        $width->setAttributeNS(LAPORAN_WORD_NS, 'w:w', (string) $columnWidth);
        $width->setAttributeNS(LAPORAN_WORD_NS, 'w:type', 'dxa');
        $cellProperties->appendChild($width);
        $cell->appendChild($cellProperties);
        if ($index === 0) {
            $paragraph = $document->createElementNS(LAPORAN_WORD_NS, 'w:p');
            $paragraph->appendChild(laporanWordImageRun($document, $logoRelationship, 'Logo PN Yogyakarta', 777240, 777240, $drawingId));
            $cell->appendChild($paragraph);
        } else {
            foreach ([
                ['MAHKAMAH AGUNG REPUBLIK INDONESIA', 10, true],
                ['DIREKTORAT JENDERAL BADAN PERADILAN UMUM', 9, true],
                ['PENGADILAN TINGGI YOGYAKARTA', 11, true],
                ['PENGADILAN NEGERI YOGYAKARTA', 13, true],
                ['Jalan Kapas Nomor 10, Semaki, Kecamatan Umbulharjo', 7, false],
                ['Kota Yogyakarta, D.I. Yogyakarta 55166. www.pn-yogyakarta.go.id, pn.yogya@gmail.com', 7, false],
            ] as [$text, $size, $bold]) {
                $cell->appendChild(laporanWordParagraph($document, $text, 'center', $bold, $size, 0));
            }
        }
        $row->appendChild($cell);
    }
    $table->appendChild($row);
    laporanWordAppendBodyNode($document, $table);

    $rule = $document->createElementNS(LAPORAN_WORD_NS, 'w:p');
    $properties = $document->createElementNS(LAPORAN_WORD_NS, 'w:pPr');
    $borders = $document->createElementNS(LAPORAN_WORD_NS, 'w:pBdr');
    $bottom = $document->createElementNS(LAPORAN_WORD_NS, 'w:bottom');
    $bottom->setAttributeNS(LAPORAN_WORD_NS, 'w:val', 'double');
    $bottom->setAttributeNS(LAPORAN_WORD_NS, 'w:sz', '6');
    $bottom->setAttributeNS(LAPORAN_WORD_NS, 'w:space', '1');
    $bottom->setAttributeNS(LAPORAN_WORD_NS, 'w:color', '000000');
    $borders->appendChild($bottom);
    $properties->appendChild($borders);
    $rule->appendChild($properties);
    laporanWordAppendBodyNode($document, $rule);
}

function laporanWordAddAppendices(
    DOMDocument $document,
    ZipArchive $zip,
    PDO $pdo,
    DateTimeImmutable $start,
    DateTimeImmutable $end,
    string $chairName
): void {
    $logoPath = dirname(__DIR__) . '/assets/img/logo.png';
    $logo = file_get_contents($logoPath);
    if ($logo === false || getimagesizefromstring($logo) === false) {
        throw new RuntimeException('Logo kop surat tidak dapat dibaca.');
    }

    $relationships = new DOMDocument();
    $relationships->preserveWhiteSpace = false;
    $relationshipXml = $zip->getFromName('word/_rels/document.xml.rels');
    if ($relationshipXml === false || !$relationships->loadXML($relationshipXml)) {
        throw new RuntimeException('Relasi dokumen TNA tidak dapat dibuka.');
    }
    $contentTypes = new DOMDocument();
    $contentTypes->preserveWhiteSpace = false;
    $contentTypesXml = $zip->getFromName('[Content_Types].xml');
    if ($contentTypesXml === false || !$contentTypes->loadXML($contentTypesXml)) {
        throw new RuntimeException('Tipe konten dokumen TNA tidak dapat dibuka.');
    }
    laporanWordEnsureImageType($contentTypes, 'png', 'image/png');
    $logoRelationship = laporanWordAddRelationship($zip, $relationships, $logo, 'tna-appendix-logo.png');
    $drawingId = 500;

    $dateLabel = laporanTanggalDownload();
    $today = substr($dateLabel, strlen('Yogyakarta, '));
    $appendPageHeader = static function (string $title) use ($document, $logoRelationship, &$drawingId): void {
        laporanWordAppendBodyNode($document, laporanWordPageBreak($document));
        laporanWordAppendixLetterhead($document, $logoRelationship, $drawingId++);
        $heading = laporanWordParagraph($document, $title, 'center', true, 13, 180);
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('w', LAPORAN_WORD_NS);
        $headingRuns = $xpath->query('.//w:rPr', $heading);
        if ($headingRuns) {
            foreach ($headingRuns as $runProperties) {
                if (!$runProperties instanceof DOMElement) {
                    continue;
                }
                $color = $document->createElementNS(LAPORAN_WORD_NS, 'w:color');
                $color->setAttributeNS(LAPORAN_WORD_NS, 'w:val', '000000');
                $runProperties->appendChild($color);
            }
        }
        laporanWordAppendBodyNode($document, $heading);
    };

    laporanWordAppendBodyNode($document, laporanWordPageBreak($document));
    $appendixDivider = laporanWordParagraph($document, 'LAMPIRAN', 'center', true, 16, 0);
    $dividerProperties = $appendixDivider->firstChild;
    $dividerSpacing = $document->createElementNS(LAPORAN_WORD_NS, 'w:spacing');
    $dividerSpacing->setAttributeNS(LAPORAN_WORD_NS, 'w:before', '6000');
    $dividerSpacing->setAttributeNS(LAPORAN_WORD_NS, 'w:after', '0');
    $dividerProperties->replaceChild($dividerSpacing, $dividerProperties->firstChild);
    laporanWordAppendBodyNode($document, $appendixDivider);

    $appendPageHeader('UNDANGAN');
    foreach ([
        ['Nomor', '................................................'],
        ['Lampiran', '-'],
        ['Perihal', 'Undangan Rapat Penyusunan Training Need Analysis (TNA)'],
    ] as [$label, $value]) {
        laporanWordAppendBodyNode($document, laporanWordTabularParagraph($document, $label, $value));
    }
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Yogyakarta, ' . $today, 'right', false, 10, 160));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Yth. Bapak/Ibu:', 'left', false, 10, 50));
    foreach ([
        '1. Hakim Pengawas Bidang Kepegawaian',
        '2. Panitera',
        '3. Sekretaris',
        '4. Kepala Subbagian Kepegawaian, Organisasi dan Tata Laksana',
        '5. Pegawai terkait',
    ] as $recipient) {
        laporanWordAppendBodyNode($document, laporanWordParagraph($document, $recipient, 'left', false, 10, 20));
    }
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Di tempat', 'left', false, 10, 130));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Dengan hormat,', 'left', false, 10, 80));
    laporanWordAppendBodyNode($document, laporanWordParagraph(
        $document,
        'Dengan ini kami mengundang Bapak/Ibu untuk menghadiri rapat penyusunan Training Need Analysis (TNA) Pengadilan Negeri Yogyakarta, yang akan dilaksanakan pada:',
        'both',
        false,
        10,
        90
    ));
    foreach ([
        ['Hari/Tanggal', '........................................................'],
        ['Waktu', '........................................................'],
        ['Tempat', 'Pengadilan Negeri Yogyakarta'],
        ['Agenda', 'Rapat penyusunan Training Need Analysis (TNA)'],
    ] as $detail) {
        laporanWordAppendBodyNode($document, laporanWordTabularParagraph($document, $detail[0], $detail[1]));
    }
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, '', 'left', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Mengingat pentingnya kegiatan tersebut, kami mengharapkan kehadiran Bapak/Ibu tepat waktu. Demikian undangan ini disampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.', 'both', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, '', 'left', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Hormat kami,', 'right', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Ketua Pengadilan Negeri Yogyakarta', 'right', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, '', 'right', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, '', 'right', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, '', 'right', false, 10, 30));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, $chairName, 'right', true, 10, 0));

    $appendPageHeader('DAFTAR HADIR');
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'RAPAT PENYUSUNAN TRAINING NEED ANALYSIS (TNA)', 'center', true, 10, 120));
    laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Hari/Tanggal : ........................................................     Tempat : Pengadilan Negeri Yogyakarta', 'left', false, 9, 100));
    $attendanceRows = array_map(
        static fn(int $number): array => [(string) $number, '', '', ''],
        range(1, 18)
    );
    laporanWordAppendBodyNode($document, laporanWordAppendixTable(
        $document,
        ['No.', 'Nama', 'Jabatan/Instansi', 'Tanda Tangan'],
        $attendanceRows,
        [600, 3300, 2700, 2400]
    ));

    $statement = $pdo->prepare(
        "SELECT sumber, nama_lengkap, nama_jabatan, file_dokumentasi
         FROM (
             SELECT 'Dokumentasi wawancara' AS sumber, p.nama_lengkap, j.nama_jabatan,
                    d.file_dokumentasi, d.id_dokumentasi AS urutan
             FROM dokumentasi_wawancara d
             JOIN wawancara w ON w.id_wawancara = d.id_wawancara
             JOIN pegawai p ON p.id_pegawai = w.id_pegawai
             JOIN jabatan j ON j.id_jabatan = p.id_jabatan
             WHERE w.created_at >= ? AND w.created_at < ?
             UNION ALL
             SELECT 'Dokumentasi kuesioner' AS sumber, p.nama_lengkap, j.nama_jabatan,
                    d.file_dokumentasi, d.id_dokumentasi AS urutan
             FROM dokumentasi_kuesioner d
             JOIN wawancara w ON w.id_wawancara = d.id_wawancara
             JOIN pegawai p ON p.id_pegawai = w.id_pegawai
             JOIN jabatan j ON j.id_jabatan = p.id_jabatan
             WHERE w.created_at >= ? AND w.created_at < ?
         ) AS dokumentasi
         ORDER BY sumber, nama_lengkap, urutan"
    );
    $statement->execute([
        $start->format('Y-m-d H:i:s'),
        $end->format('Y-m-d H:i:s'),
        $start->format('Y-m-d H:i:s'),
        $end->format('Y-m-d H:i:s'),
    ]);
    $photos = $statement->fetchAll(PDO::FETCH_ASSOC);
    if (!$photos) {
        $appendPageHeader('DOKUMENTASI WAWANCARA DAN KUESIONER');
        laporanWordAppendBodyNode($document, laporanWordParagraph($document, 'Belum ada foto dokumentasi wawancara atau kuesioner pada periode laporan ini.', 'center', false, 10, 0));
    }
    foreach ($photos as $index => $photo) {
        $relativePath = (string) $photo['file_dokumentasi'];
        if (!preg_match('~^assets/uploads/(?:wawancara|kuesioner)/[a-zA-Z0-9._-]+$~', $relativePath)) {
            throw new RuntimeException('Lokasi file dokumentasi laporan TNA tidak valid.');
        }
        $filePath = dirname(__DIR__) . '/' . $relativePath;
        $realPath = realpath($filePath);
        $uploadRoot = realpath(dirname(__DIR__) . '/assets/uploads');
        if ($realPath === false || $uploadRoot === false
            || !str_starts_with($realPath, $uploadRoot . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('File dokumentasi laporan TNA tidak ditemukan.');
        }
        $image = file_get_contents($realPath);
        $imageInfo = $image === false ? false : getimagesizefromstring($image);
        if ($image === false || !is_array($imageInfo) || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png'], true)) {
            throw new RuntimeException('File dokumentasi laporan TNA bukan gambar JPG/PNG yang valid.');
        }
        $extension = $imageInfo['mime'] === 'image/png' ? 'png' : 'jpeg';
        laporanWordEnsureImageType($contentTypes, $extension, $imageInfo['mime']);
        $fileName = 'tna-documentation-' . ($index + 1) . '.' . $extension;
        $photoRelationship = laporanWordAddRelationship($zip, $relationships, $image, $fileName);

        $appendPageHeader('DOKUMENTASI WAWANCARA DAN KUESIONER');
        $paragraph = $document->createElementNS(LAPORAN_WORD_NS, 'w:p');
        $paragraphProperties = $document->createElementNS(LAPORAN_WORD_NS, 'w:pPr');
        $justify = $document->createElementNS(LAPORAN_WORD_NS, 'w:jc');
        $justify->setAttributeNS(LAPORAN_WORD_NS, 'w:val', 'center');
        $paragraphProperties->appendChild($justify);
        $paragraph->appendChild($paragraphProperties);
        $maxWidth = 5600000;
        $maxHeight = 4300000;
        $scale = min($maxWidth / $imageInfo[0], $maxHeight / $imageInfo[1]);
        $paragraph->appendChild(laporanWordImageRun(
            $document,
            $photoRelationship,
            'Dokumentasi ' . ($index + 1),
            (int) round($imageInfo[0] * $scale),
            (int) round($imageInfo[1] * $scale),
            $drawingId++
        ));
        laporanWordAppendBodyNode($document, $paragraph);
        laporanWordAppendBodyNode($document, laporanWordParagraph(
            $document,
            $photo['sumber'] . ' - ' . $photo['nama_jabatan'] . ' (' . $photo['nama_lengkap'] . ')',
            'center',
            false,
            9,
            0
        ));
    }

    $appendPageHeader('SARAN');
    $appendPageHeader('PENUTUP');

    laporanWordAddContents($document, $zip);
    laporanWordAddPageNumberFooter($document, $zip, $relationships, $contentTypes);
    if ($zip->addFromString('word/_rels/document.xml.rels', $relationships->saveXML()) !== true
        || $zip->addFromString('[Content_Types].xml', $contentTypes->saveXML()) !== true) {
        throw new RuntimeException('Lampiran dokumen TNA gagal disimpan.');
    }
}
