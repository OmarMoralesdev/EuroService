<?php

require '../includes/db.php'; 

$con = new Database();
$pdo = $con->conectar();

// Sanitización y validación de los inputs
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);
$servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
$fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);

if (!$vehiculoID || !$servicioSolicitado || !$fechaCita) {
    die("Error: Todos los campos son obligatorios.");
}

$fechaActual = date('Y-m-d H:i:s');
$fechaCitaTimestamp = strtotime($fechaCita);

if ($fechaCitaTimestamp <= strtotime($fechaActual)) {
    die("Error: La fecha de la cita debe ser posterior a la fecha actual.");
}

// Verificar que no haya citas programadas dentro de los próximos 30 minutos de la fecha solicitada
$fechaLimite = date('Y-m-d H:i:s', strtotime('+30 minutes', $fechaCitaTimestamp));
$sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE fecha_cita BETWEEN ? AND ?";
$query = $pdo->prepare($sql);
$query->execute([$fechaCita, $fechaLimite]);

$row = $query->fetch(PDO::FETCH_ASSOC);
$countCitasProximas = $row['countCitas'];

if ($countCitasProximas > 0) {
    die("Error: Hay una cita programada dentro de los próximos 30 minutos. Por favor, selecciona otra fecha.");
}

// Insertar la nueva cita en la base de datos
$sqlInsert = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, estado)
              VALUES (?, ?, ?, ?, 'pendiente')";
$queryInsert = $pdo->prepare($sqlInsert);
$resultInsert = $queryInsert->execute([$vehiculoID, $servicioSolicitado, $fechaActual, $fechaCita]);

if ($resultInsert) {
    echo "Cita registrada correctamente.";
} else {
    echo "Error al registrar la cita.";
}
?>
