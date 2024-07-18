<?php
require '../includes/db.php'; 
session_start();
$con = new Database();
$pdo = $con->conectar();

// Sanitización y validación de los inputs
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);
$servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
$fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);

if (!$vehiculoID || !$servicioSolicitado || !$fechaCita) {
    $_SESSION['error'] = "Error: Todos los campos son obligatorios.";
    header("Location: index.php");
    exit();
}

$fechaActual = date('Y-m-d H:i:s');
$fechaCitaTimestamp = strtotime($fechaCita);

if ($fechaCitaTimestamp <= strtotime($fechaActual)) {
    $_SESSION['error'] = "Error: La fecha de la cita debe ser posterior a la fecha actual.";
    header("Location: index.php");
    exit();
}

// Verificar que no haya citas programadas dentro de los próximos 30 minutos de la fecha solicitada
$fechaLimite = date('Y-m-d H:i:s', strtotime('+30 minutes', $fechaCitaTimestamp));
$sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE fecha_cita BETWEEN ? AND ?";
$query = $pdo->prepare($sql);
$query->execute([$fechaCita, $fechaLimite]);

$row = $query->fetch(PDO::FETCH_ASSOC);
$countCitasProximas = $row['countCitas'];

if ($countCitasProximas > 0) {
    $_SESSION['error'] = "Error: Hay una cita programada dentro de los próximos 30 minutos. Por favor, selecciona otra fecha.";
    header("Location: index.php");
    exit();
}

// Insertar la nueva cita en la base de datos
$sqlInsert = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, estado)
              VALUES (?, ?, ?, ?, 'pendiente')";
$queryInsert = $pdo->prepare($sqlInsert);
$resultInsert = $queryInsert->execute([$vehiculoID, $servicioSolicitado, $fechaActual, $fechaCita]);

if ($resultInsert) {
    $_SESSION['bien'] = "Cita registrada correctamente.";
    header("Location: index.php");
    exit();
} else {
    $_SESSION['error'] = "Error al registrar la cita.";
    header("Location: index.php");
    exit();
}
?>
