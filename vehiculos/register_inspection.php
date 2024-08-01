<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['vehiculo'])) {
       $_SESSION['error'] = 'Error: ID del vehículo no está definido en la sesión.';
       header("Location: autos_view.php");
       exit();
}

$vehiculoID = $_POST['vehiculoID'];
$empleadoID = isset($_POST['empleadoID']) ? intval($_POST['empleadoID']) : null;
$ubicacionID = isset($_POST['ubicacionID']) ? intval($_POST['ubicacionID']) : null;
$formaDePago = isset($_POST['formaDePago']) ? trim($_POST['formaDePago']) : '';

if ($empleadoID === null || $ubicacionID === null || empty($formaDePago)) {
      $_SESSION['error'] = 'Error: Faltan datos necesarios.';
      header("Location: autos_view.php");
      exit();
}

$con = new Database();
$pdo = $con->conectar();

try {
    // Verificar si el vehiculoID existe
    $sqlVerificarVehiculo = "SELECT COUNT(*) FROM VEHICULOS WHERE vehiculoID = ?";
    $stmtVerificarVehiculo = $pdo->prepare($sqlVerificarVehiculo);
    $stmtVerificarVehiculo->execute([$vehiculoID]);
    $vehiculoCount = $stmtVerificarVehiculo->fetchColumn();

    if ($vehiculoCount == 0) {
          $_SESSION['error'] = 'Error: El vehículo no está registrado en la base de datos.';
          header("Location: autos_view.php");
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
    header("Location: autos_view.php");
    exit();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    header("Location: autos_view.php");
    exit();
}

?>
