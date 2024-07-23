<?php
require '../includes/db.php'; 
session_start();
$con = new Database();
$pdo = $con->conectar();

// Sanitización y validación de los inputs
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);
$servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
$fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);
$urgencia = 'no';
if (!$vehiculoID || !$servicioSolicitado || !$fechaCita) {
    $_SESSION['error'] = "Error: Todos los campos son obligatorios.";
    header("Location: index.php");
    exit();
}

$fechaActual = date('Y-m-d H:i:s');
$fechaCitaTimestamp = strtotime($fechaCita);

// Validación: La fecha de la cita no debe estar en el pasado
if ($fechaCitaTimestamp < strtotime($fechaActual)) {
    $_SESSION['error'] = "Error: La fecha de la cita no puede estar en el pasado.";
    header("Location: index.php");
    exit();
}

// Validación: La hora de la cita debe estar dentro del horario laboral permitido
$horaCita = date('H:i:s', $fechaCitaTimestamp);
$horaInicioLaboral = "09:00:00";
$horaFinLaboral = "17:00:00";

if ($horaCita < $horaInicioLaboral || $horaCita > $horaFinLaboral) {
    $_SESSION['error'] = "Error: La cita debe programarse dentro del horario laboral (09:00 - 17:00).";
    header("Location: index.php");
    exit();
}

// Validación: Verificar que la nueva cita no se solape con otras citas en el sistema
$fechaInicioIntervalo = date('Y-m-d H:i:s', strtotime('-30 minutes', $fechaCitaTimestamp));
$fechaFinIntervalo = date('Y-m-d H:i:s', strtotime('+30 minutes', $fechaCitaTimestamp));

// Verificar si hay citas existentes en el intervalo de 30 minutos
$sqlGlobal = "SELECT COUNT(*) AS countCitasGlobal FROM CITAS WHERE fecha_cita BETWEEN ? AND ?";
$queryGlobal = $pdo->prepare($sqlGlobal);
$queryGlobal->execute([$fechaInicioIntervalo, $fechaFinIntervalo]);

$rowGlobal = $queryGlobal->fetch(PDO::FETCH_ASSOC);
$countCitasGlobal = $rowGlobal['countCitasGlobal'];

if ($countCitasGlobal > 0) {
    $_SESSION['error'] = "Error: Ya hay una cita programada dentro del intervalo de 30 minutos. Por favor, selecciona otra fecha.";
    header("Location: index.php");
    exit();
}

// Validación: Verificar que no haya citas para el mismo vehículo en el intervalo de 30 minutos
$sqlVehiculo = "SELECT COUNT(*) AS countCitasVehiculo FROM CITAS WHERE vehiculoID = ? and estado = 'pendiente' ";
$queryVehiculo = $pdo->prepare($sqlVehiculo);
$queryVehiculo->execute([$vehiculoID]);

$rowVehiculo = $queryVehiculo->fetch(PDO::FETCH_ASSOC);
$countCitasVehiculo = $rowVehiculo['countCitasVehiculo'];

if ($countCitasVehiculo > 0) {
    $_SESSION['error'] = "Error: El vehículo ya tiene una cita programada dentro de los 30 minutos antes o después de la franja horaria solicitada.";
    header("Location: index.php");
    exit();
}

// Insertar la nueva cita en la base de datos
$sqlInsert = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado)
              VALUES (?, ?, ?, ?, ?, 'pendiente')";
$queryInsert = $pdo->prepare($sqlInsert);
$resultInsert = $queryInsert->execute([$vehiculoID, $servicioSolicitado, $fechaActual, $fechaCita, $urgencia]);

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
