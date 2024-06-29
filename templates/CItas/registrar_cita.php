<?php

require 'database.php';

$con = new Database();
$pdo = $con->conectar();

$clienteID = filter_input(INPUT_POST, 'clienteID', FILTER_SANITIZE_NUMBER_INT);
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);
$servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
$fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);

$fechaActual = date('Y-m-d H:i:s');
$fechaCitaTimestamp = strtotime($fechaCita);

if ($fechaCitaTimestamp <= strtotime($fechaActual)) {
    die("Error: La fecha de la cita debe ser posterior a la fecha actual.");
}

$sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE vehiculoID = ? AND fecha_cita BETWEEN DATE_SUB(?, INTERVAL 1 YEAR) AND ?";
$query = $pdo->prepare($sql);
$query->execute([$vehiculoID, $fechaCita, $fechaCita]);

$row = $query->fetch(PDO::FETCH_ASSOC);



$fechaLimite = date('Y-m-d H:i:s', strtotime('+30 minutes', $fechaCitaTimestamp));
$sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE fecha_cita BETWEEN ? AND ?";
$query = $pdo->prepare($sql);
$query->execute([$fechaCita, $fechaLimite]);

$row = $query->fetch(PDO::FETCH_ASSOC);
$countCitasProximas = $row['countCitas'];

if ($countCitasProximas > 0) {
    die("Error: Hay una cita programada dentro de los próximos 30 minutos. Por favor, selecciona otra fecha.");
}


$sqlInsert = "INSERT INTO CITAS (clienteid, vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, estado)
              VALUES (?, ?, ?, ?, ?, 'pendiente')";
$queryInsert = $pdo->prepare($sqlInsert);
$resultInsert = $queryInsert->execute([$clienteID, $vehiculoID, $servicioSolicitado, $fechaActual, $fechaCita]);

if ($resultInsert) {
    echo "Cita registrada correctamente.";
} else {
    echo "Error al registrar la cita.";
}
?>
