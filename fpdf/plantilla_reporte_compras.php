<?php
require 'fpdf.php';

class PDF extends FPDF{
    private $fechaIni;
    private $fechaFin;

    public function __CONSTRUCT($orientacion,$medidas,$tamanio,$datos){
        parent::__CONSTRUCT($orientacion,$medidas,$tamanio);
        $this->fechaIni = $datos['fechaIni'];
        $this->fechaFin = $datos['fechaFin'];
    }

    public function Header() {
        // Logo
        $this->Image('imagenes/logoinvertido.png', 10, 10, 20);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 51, 102); // Azul oscuro

        // Título
        $this->Cell(30);
        $y = $this->GetY();
        $this->MultiCell(130, 10, 'Reporte de Compras', 0, 'C');
        
        // Subtítulo
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0, 0, 0); // Negro
        $this->Cell(30);
        $this->MultiCell(130, 7, 'Del ' . $this->fechaIni . ' al ' . $this->fechaFin, 0, 'C');

        // Fecha
        $this->SetXY(160, $y);
        $this->Cell(40, 10, 'Fecha: ' . date('d/m/Y'), 0, 1, 'L');

        // Línea decorativa
        $this->SetDrawColor(0, 51, 102); // Azul oscuro
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(8);

        // Encabezados de la tabla
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(0, 51, 102); // Azul oscuro
        $this->SetTextColor(255, 255, 255); // Blanco
        $this->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Estado', 1, 0, 'C', true);
        $this->Cell(60, 8, 'Cliente', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Total', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Medio de pago', 1, 1, 'C', true);
        $this->SetFont('Arial', '', 11);
    }

    public function Footer() {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(128, 128, 128); // Gris

        // Texto de pie de página
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo() . ' / {nb}', 0, 0, 'C');
        
        // Línea decorativa en pie de página
        $this->SetDrawColor(0, 51, 102); // Azul oscuro
        $this->SetLineWidth(0.2);
        $this->Line(10, 282, 200, 282);
    }

    // Método para generar filas con estilo alternado
    public function FancyRow($row, $fill) {
        $this->SetFillColor(230, 240, 255); // Azul claro
        $this->Cell(30, 6, $row['fecha_formateada'], 1, 0, 'C', $fill);
        $this->Cell(30, 6, $row['estado'], 1, 0, 'C', $fill);
        $this->Cell(60, 6, mb_convert_encoding($row['cliente'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
        $this->Cell(30, 6, $row['total'], 1, 0, 'C', $fill);
        $this->Cell(30, 6, $row['medio_pago'], 1, 1, 'C', $fill);
    }







}


?>