<?php

namespace App\Libraries;

use TCPDF;

class PDFExporter extends TCPDF
{
    private $reportTitle = '';
    private $reportDate = '';
    private $companyName = 'Maintio - Wartungsmanagement System';

    public function __construct($title = 'Bericht', $orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format, true, 'UTF-8', false);

        $this->reportTitle = $title;
        $this->reportDate = date('d.m.Y H:i');

        // Set document information
        $this->SetCreator('Maintio');
        $this->SetAuthor('Maintio System');
        $this->SetTitle($title);
        $this->SetSubject($title);
        $this->SetKeywords('Wartung, Bericht, Maintio');

        // Set default header data
        $this->SetHeaderData('', 0, $this->companyName, $title . "\nErstellt am: " . $this->reportDate);

        // Set header and footer fonts
        $this->setHeaderFont(Array('helvetica', '', 10));
        $this->setFooterFont(Array('helvetica', '', 8));

        // Set default monospaced font
        $this->SetDefaultMonospacedFont('courier');

        // Set margins
        $this->SetMargins(15, 27, 15);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);

        // Set auto page breaks
        $this->SetAutoPageBreak(TRUE, 25);

        // Set image scale factor
        $this->setImageScale(1.25);

        // Set font
        $this->SetFont('helvetica', '', 10);
    }

    public function Header()
    {
        // Logo (falls vorhanden)
        // $this->Image('path/to/logo.png', 15, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Company name
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, $this->companyName, 0, 1, 'C');

        // Report title
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 8, $this->reportTitle, 0, 1, 'C');

        // Date
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 6, 'Erstellt am: ' . $this->reportDate, 0, 1, 'C');

        // Line break
        $this->Ln(5);

        // Draw line
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(5);
    }

    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);

        // Draw line
        $this->Line(15, $this->GetY(), 195, $this->GetY());

        // Set font
        $this->SetFont('helvetica', 'I', 8);

        // Page number
        $this->Cell(0, 10, 'Seite ' . $this->getAliasNumPage() . ' von ' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    public function addTitle($title, $size = 12)
    {
        $this->SetFont('helvetica', 'B', $size);
        $this->Cell(0, 8, $title, 0, 1, 'L');
        $this->Ln(3);
    }

    public function addSubtitle($subtitle, $size = 10)
    {
        $this->SetFont('helvetica', 'B', $size);
        $this->Cell(0, 6, $subtitle, 0, 1, 'L');
        $this->Ln(2);
    }

    public function addText($text, $size = 10)
    {
        $this->SetFont('helvetica', '', $size);
        $this->Cell(0, 5, $text, 0, 1, 'L');
        $this->Ln(1);
    }

    public function addTable($headers, $data, $widths = null)
    {
        if (empty($data)) {
            $this->addText('Keine Daten verfügbar.');
            return;
        }

        // Auto-calculate widths if not provided
        if ($widths === null) {
            $tableWidth = 165; // Total available width
            $colCount = count($headers);
            $widths = array_fill(0, $colCount, $tableWidth / $colCount);
        }

        // Header
        $this->SetFont('helvetica', 'B', 9);
        $this->SetFillColor(240, 240, 240);

        foreach ($headers as $i => $header) {
            $this->Cell($widths[$i], 7, $header, 1, 0, 'C', true);
        }
        $this->Ln();

        // Data
        $this->SetFont('helvetica', '', 8);
        $this->SetFillColor(255, 255, 255);

        $fill = false;
        foreach ($data as $row) {
            foreach ($row as $i => $cell) {
                // Truncate long text
                $cellText = strlen($cell) > 25 ? substr($cell, 0, 22) . '...' : $cell;
                $this->Cell($widths[$i], 6, $cellText, 1, 0, 'L', $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }

        $this->Ln(5);
    }

    public function addStatistics($stats)
    {
        $this->addSubtitle('Statistiken');

        $this->SetFont('helvetica', '', 9);

        foreach ($stats as $label => $value) {
            $this->Cell(60, 5, $label . ':', 0, 0, 'L');
            $this->Cell(40, 5, $value, 0, 1, 'R');
        }

        $this->Ln(5);
    }

    public function addFilterInfo($filters)
    {
        if (empty($filters)) {
            return;
        }

        $this->addSubtitle('Angewendete Filter');

        $this->SetFont('helvetica', '', 9);

        foreach ($filters as $label => $value) {
            if (!empty($value)) {
                $this->Cell(0, 5, $label . ': ' . $value, 0, 1, 'L');
            }
        }

        $this->Ln(5);
    }

    public function generatePDF($filename = 'bericht.pdf', $dest = 'I')
    {
        // Clean output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Output PDF
        return $this->Output($filename, $dest);
    }
}