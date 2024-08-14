<?php
require '../includes/db.php'; 
session_start();
$con = new Database();
$pdo = $con->conectar();

// Sanitización y validación de los inputs
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);
$servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
$costo_mano_obra = filter_input(INPUT_POST, 'costoManoObra', FILTER_SANITIZE_STRING);
$costo_refacciones = filter_input(INPUT_POST, 'costoRefacciones', FILTER_SANITIZE_STRING);
$fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);

if (!$vehiculoID || !$servicioSolicitado || !$fechaCita || !$costo_mano_obra || !$costo_refacciones) {
    $_SESSION['error'] = "Error: Todos los campos son obligatorios.";
    header("Location: seleccionar_cita_view.php");
    exit();
}

$fechaActual = new DateTime();
$fechaCita = new DateTime($fechaCita);

// Validación: La fecha de la cita no debe estar en el pasado
if ($fechaCita < $fechaActual) {
    $_SESSION['error'] = "Error: La fecha de la cita no puede estar en el pasado.";
    header("Location: seleccionar_cita_view.php");
    exit();
}

// Validación: La hora de la cita debe estar dentro del horario laboral permitido
$horaCita = $fechaCita->format('H:i:s');
$horaInicioLaboral = "09:00:00";
$horaFinLaboral = "17:00:00";

if ($horaCita < $horaInicioLaboral || $horaCita > $horaFinLaboral) {
    $_SESSION['error'] = "Error: La cita debe programarse dentro del horario laboral (09:00 - 17:00).";
    header("Location: seleccionar_cita_view.php");
    exit();
}

// Validación: Verificar que la nueva cita no se solape con otras citas en el sistema
$fechaInicioIntervalo = (clone $fechaCita)->modify('-30 minutes')->format('Y-m-d H:i:s');
$fechaFinIntervalo = (clone $fechaCita)->modify('+30 minutes')->format('Y-m-d H:i:s');

// Verificar si hay citas existentes en el intervalo de 30 minutos
$sqlGlobal = "SELECT COUNT(*) AS countCitasGlobal FROM CITAS WHERE fecha_cita BETWEEN ? AND ?";
$queryGlobal = $pdo->prepare($sqlGlobal);
$queryGlobal->execute([$fechaInicioIntervalo, $fechaFinIntervalo]);

$rowGlobal = $queryGlobal->fetch(PDO::FETCH_ASSOC);
$countCitasGlobal = $rowGlobal['countCitasGlobal'];

if ($countCitasGlobal > 0) {
    $_SESSION['error'] = "Error: Ya hay una cita programada dentro del intervalo de 30 minutos. Por favor, selecciona otra fecha.";
    header("Location: seleccionar_cita_view.php");
    exit();
}

// Validación: Verificar que el vehículo no tenga citas pendientes
$sqlVehiculoPendiente = "SELECT COUNT(*) AS countCitasPendientes FROM CITAS WHERE vehiculoID = ? AND estado = 'pendiente'";
$queryVehiculoPendiente = $pdo->prepare($sqlVehiculoPendiente);
$queryVehiculoPendiente->execute([$vehiculoID]);

$rowVehiculoPendiente = $queryVehiculoPendiente->fetch(PDO::FETCH_ASSOC);
$countCitasPendientes = $rowVehiculoPendiente['countCitasPendientes'];

if ($countCitasPendientes > 0) {
    $_SESSION['error'] = "Error: El vehículo ya tiene una cita pendiente. No se puede programar una nueva cita hasta que se libere la cita actual.";
    header("Location: seleccionar_cita_view.php");
    exit();
}

// Validación: Verificar que no haya citas para el mismo vehículo en el intervalo de 30 minutos
$sqlVehiculo = "SELECT COUNT(*) AS countCitasVehiculo FROM CITAS WHERE vehiculoID = ? AND fecha_cita BETWEEN ? AND ?";
$queryVehiculo = $pdo->prepare($sqlVehiculo);
$queryVehiculo->execute([$vehiculoID, $fechaInicioIntervalo, $fechaFinIntervalo]);

$rowVehiculo = $queryVehiculo->fetch(PDO::FETCH_ASSOC);
$countCitasVehiculo = $rowVehiculo['countCitasVehiculo'];

if ($countCitasVehiculo > 0) {
    $_SESSION['error'] = "Error: El vehículo ya tiene una cita programada dentro del intervalo de 30 minutos.";
    header("Location: seleccionar_cita_view.php");
    exit();
}

   // Validación inicial para evitar números negativos
   if ($costoManoObra < 0 || $costoRefacciones < 0) {
    $_SESSION['error'] = "No puedes ingresar números negativos.";
    header("Location: crear_orden_desde_cita.php?citaID=$citaID");
    exit();
}


// Insertar la nueva cita en la base de datos
$sqlInsert = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, costo_mano_obra, costo_refacciones, fecha_cita, urgencia, estado)
              VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')";
$queryInsert = $pdo->prepare($sqlInsert);
$resultInsert = $queryInsert->execute([$vehiculoID, $servicioSolicitado, $costo_mano_obra, $costo_refacciones, $fechaActual->format('Y-m-d H:i:s'), $fechaCita->format('Y-m-d H:i:s'), 'no']);

if ($resultInsert) {
    $_SESSION['bien'] = "Cita registrada correctamente.";
    header("Location: seleccionar_cita_view.php");
    exit();
} else {
    $_SESSION['error'] = "Error al registrar la cita.";
    header("Location: seleccionar_cita_view.php");
    exit();
}
?>
