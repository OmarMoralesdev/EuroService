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
$concepto = $_GET['concepto'] ?? '';
$orden = $_GET['ordenID'] ?? '';
if (empty($cliente_nombre) || empty($cantidad_pagada_recibo) || empty($fecha_recibo) || empty($receptor)) {
    die("Error: datos incompletos para generar el recibo.");
}

// Crear nuevo PDF
$pdf = new TCPDF();
// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Servicio Especializado Berlanga Angel');
$pdf->SetTitle('Recibo de Pago');
$pdf->SetSubject('Recibo de Pago');

// Añadir una página
$pdf->AddPage();

// Contenido HTML
$html = <<<EOD
<h1 style="text-align: center;">RECIBO DE PAGO</h1>
<table border="0" cellpadding="5">
    <tr>
        <td><strong>Fecha:</strong></td>
        <td>{$fecha_recibo}</td>
    </tr>
    <tr>
        <td><strong>No.{$orden}:</strong></td>
        <td>8</td>
    </tr>
    <tr>
        <td><strong>Recibí de:</strong></td>
        <td>{$cliente_nombre}</td>
    </tr>
    <tr>
        <td><strong>Cantidad:</strong></td>
        <td>{$cantidad_pagada_recibo}</td>
    </tr>
    <tr>
        <td><strong>Concepto:</strong></td>
        <td>{$concepto}</td>
    </tr>
    <tr>
        <td><strong>Firma:</strong></td>
        <td>___________________________</td>
    </tr>
    <tr>
        <td><strong>Recibido por:</strong></td>
        <td>{$receptor}</td>
    </tr>
</table>
<br>
<p style="text-align: center;">SERVICIO ESPECIALIZADO BERLANGA ANGEL</p>
EOD;

// Escribir el HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output
$pdf->Output('recibo_pago.pdf', 'D');
?>
