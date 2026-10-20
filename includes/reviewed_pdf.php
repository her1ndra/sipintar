<?php

function createReviewedPdf(string $sourcePath): string
{
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!is_file($autoloadPath)) {
        throw new RuntimeException('Dependensi PDF belum terpasang. Jalankan composer install.');
    }
    require_once $autoloadPath;

    if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
        throw new RuntimeException('Dependensi FPDI tidak tersedia untuk memberi tanda pada PDF.');
    }

    $pdf = new \setasign\Fpdi\Fpdi();
    $pageCount = $pdf->setSourceFile($sourcePath);
    $pdf->SetAutoPageBreak(false);

    for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
        $templateId = $pdf->importPage($pageNumber);
        $pageSize = $pdf->getTemplateSize($templateId);
        $pdf->AddPage($pageSize['orientation'], [$pageSize['width'], $pageSize['height']]);
        $pdf->useTemplate($templateId);

        $label = 'Sudah diperiksa';
        $fontSize = 10;
        $checkboxSize = 4.5;
        $padding = 2.5;
        $height = 9;
        $pdf->SetFont('Arial', 'B', $fontSize);
        $width = $checkboxSize + $padding + $pdf->GetStringWidth($label) + (2 * $padding);
        $x = $pageSize['width'] - 12 - $width;
        $y = $pageSize['height'] - 14;

        $pdf->SetDrawColor(0, 100, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 100, 0);
        $pdf->SetLineWidth(0.35);
        $pdf->Rect($x, $y, $width, $height, 'DF');
        $checkboxX = $x + $padding;
        $checkboxY = $y + (($height - $checkboxSize) / 2);
        $pdf->Rect($checkboxX, $checkboxY, $checkboxSize, $checkboxSize);
        $pdf->SetLineWidth(0.6);
        $pdf->Line($checkboxX + 0.8, $checkboxY + 2.3, $checkboxX + 1.8, $checkboxY + 3.3);
        $pdf->Line($checkboxX + 1.8, $checkboxY + 3.3, $checkboxX + 3.9, $checkboxY + 0.9);
        $pdf->SetXY($checkboxX + $checkboxSize + $padding, $y + (($height - $fontSize * 0.35) / 2));
        $pdf->Cell($pdf->GetStringWidth($label), $fontSize * 0.35, $label);
    }

    return $pdf->Output('S');
}
