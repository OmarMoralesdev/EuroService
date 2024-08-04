<?php
require '../includes/db.php';
session_start();
$con = new Database();
$pdo = $con->conectar();


$empleadoID = isset($_POST['empleadoID']) ? intval($_POST['empleadoID']) : null;
$ubicacionID = isset($_POST['ubicacionID']) ? intval($_POST['ubicacionID']) : null;
$formaDePago = isset($_POST['formaDePago']) ? trim($_POST['formaDePago']) : '';
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);


$currentYear = date('Y');

if ($anio < 1886 || $anio > $currentYear) {
    $_SESSION['error'] = "El año debe estar entre 1886 y el año actual.";
    header("Location: inspeccion_view.php");
    exit();
}


if ($empleadoID === null || $ubicacionID === null || empty($formaDePago)) {
      $_SESSION['error'] = 'Error: Faltan datos necesarios.';
      header("Location: inspeccion_view.php");
      exit();
}


try {
    // Verificar si el vehiculoID existe
    $sqlVerificarVehiculo = "SELECT COUNT(*) FROM VEHICULOS WHERE vehiculoID = ?";
    $stmtVerificarVehiculo = $pdo->prepare($sqlVerificarVehiculo);
    $stmtVerificarVehiculo->execute([$vehiculoID]);
    $vehiculoCount = $stmtVerificarVehiculo->fetchColumn();

    if ($vehiculoCount == 0) {
          $_SESSION['error'] = 'Error: El vehículo no está registrado en la base de datos.';
          header("Location: inspeccion_view.php");
          exit();
    }

    // Insertar cita
    $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
    $stmtCita = $pdo->prepare($sqlCita);
    $stmtCita->execute([$vehiculoID, 'Inspección', date('Y-m-d'), date('Y-m-d'), 'si']);
    $citaID = $pdo->lastInsertId();

    // Insertar orden de trabajo
    $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtOrden = $pdo->prepare($sqlOrden);
    $stmtOrden->execute([date('Y-m-d'), 800, 0, 'Muy Urgente', $citaID, $empleadoID, $ubicacionID]);
    $ordenID = $pdo->lastInsertId();
    $anticipo = 800 * 0.5;

    // Insertar pago
    realizarPago($pdo, $ordenID, date('Y-m-d'), $anticipo, "anticipo", $formaDePago);

    $_SESSION['bien'] = "Cita y orden de trabajo registradas exitosamente.";
    header("Location: inspeccion_view.php");
    exit();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    header("Location: inspeccion_view.php");
    exit();
}

?>