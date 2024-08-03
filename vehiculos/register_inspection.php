<?php
require '../includes/db.php';
session_start();
$con = new Database();
$pdo = $con->conectar();


$empleadoID = isset($_POST['empleadoID']) ? intval($_POST['empleadoID']) : null;
$ubicacionID = isset($_POST['ubicacionID']) ? intval($_POST['ubicacionID']) : null;
$formaDePago = isset($_POST['formaDePago']) ? trim($_POST['formaDePago']) : '';
$clienteID = isset($_POST['clienteID']) ? $_POST['clienteID'] : '';
$marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
$modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
$anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';
$color = isset($_POST['color']) ? trim($_POST['color']) : '';
$kilometraje = isset($_POST['kilometraje']) ? trim($_POST['kilometraje']) : '';
$placas = isset($_POST['placas']) ? trim($_POST['placas']) : '';
$vin = isset($_POST['vin']) ? trim($_POST['vin']) : '';

$currentYear = date('Y');

if ($anio < 1886 || $anio > $currentYear) {
    $_SESSION['error'] = "El año debe estar entre 1886 y el año actual.";
}

if (empty($errors)) {
    $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
    $stmtVerificar = $pdo->prepare($verificar);
    $stmtVerificar->execute([$vin]);

    if ($stmtVerificar->rowCount() > 0) {
        $_SESSION['error'] = "El vehículo ya está registrado.";
    } else {
        $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin,continuidad,activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,'si')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin, $continuidad2]);

        if ($stmt->rowCount() > 0) {

        }
    }
}
if ($empleadoID === null || $ubicacionID === null || empty($formaDePago)) {
      $_SESSION['error'] = 'Error: Faltan datos necesarios.';
      header("Location: autos_view.php");
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