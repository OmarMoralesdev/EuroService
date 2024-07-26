<?php
require_once '../vendor/autoload.php'; // Ajusta la ruta según donde tengas instalado TCPDF

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['empleadoID'])) {
    header('Location: login.php');
    exit();
}

$cliente_nombre = $_GET['cliente_nombre'] ?? '';
$cantidad_pagada_recibo = $_GET['cantidad_pagada_recibo'] ?? '';
$fecha_recibo = $_GET['fecha_recibo'] ?? '';
$receptor = $_GET['receptor'] ?? '';

if (empty($cliente_nombre) || empty($cantidad_pagada_recibo) || empty($fecha_recibo) || empty($receptor)) {
    die("Error: datos incompletos para generar el recibo.");
}

// Crear nuevo PDF
$pdf = new TCPDF();
$pdf->AddPage();

// Título
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 15, 'Recibo de Pago', 0, 1, 'C');

// Detalles del recibo
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(50, 10, 'Fecha:', 0, 0);
$pdf->Cell(0, 10, $fecha_recibo, 0, 1);

$pdf->Cell(50, 10, 'Cliente:', 0, 0);
$pdf->Cell(0, 10, $cliente_nombre, 0, 1);

$pdf->Cell(50, 10, 'Cantidad Pagada:', 0, 0);
$pdf->Cell(0, 10, $cantidad_pagada_recibo, 0, 1);

$pdf->Cell(50, 10, 'Receptor:', 0, 0);
$pdf->Cell(0, 10, $receptor, 0, 1);

// Firma
$pdf->Ln(20);
$pdf->Cell(50, 10, 'Firma:', 0, 0);
$pdf->Cell(0, 10, $receptor, 0, 1);

// Output
$pdf->Output('recibo_pago.pdf', 'D');
?>
