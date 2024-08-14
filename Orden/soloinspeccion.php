<?php
require '../includes/db.php';
session_start();
$con = new Database();
$pdo = $con->conectar();

// Obtener y sanitizar datos
$empleadoID = isset($_POST['empleadoID']) ? intval($_POST['empleadoID']) : null;
$ubicacionID = isset($_POST['ubicacionID']) ? intval($_POST['ubicacionID']) : null;
$formaDePago = isset($_POST['formadepago']) ? trim($_POST['formadepago']) : '';
$diagnostico = isset($_POST['diagnostico']) ? trim($_POST['diagnostico']) : '';
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);
$_SESSION['formData'] = [
    'vehiculoID' => '1',
    'diagnostico' => $diagnostico,
    'empleadoID' => '2',
    'ubicacionID' => '3',
    'formadepago' => 'tarjeta'
];

if (!$empleadoID || !$ubicacionID || !$formaDePago || !$diagnostico || !$vehiculoID) {
    $_SESSION['error'] = 'Error: Faltan datos requeridos.';
    $_SESSION['formData'];
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
        $_SESSION['formData'];
        header("Location: inspeccion_view.php");
        exit();
    }

    // Insertar cita
// Insertar cita
$sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, tipo_servicio, fecha_solicitud, fecha_cita, urgencia, estado) 
            VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
$stmtCita = $pdo->prepare($sqlCita);
$stmtCita->execute([
    $vehiculoID,                 
    $diagnostico,                // Servicio solicitado
    'inspección',                // Tipo de servicio (inspección, reparación, mantenimiento)
    date('Y-m-d'),               // Fecha de solicitud (solo la fecha actual)
    date('Y-m-d H:i:s'),         // Fecha de la cita (fecha y hora actuales)
    'si'                         // Urgencia
]);
$citaID = $pdo->lastInsertId();  // Obtener el ID de la cita recién insertada

    // Insertar orden de trabajo
    $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtOrden = $pdo->prepare($sqlOrden);
    $stmtOrden->execute([date('Y-m-d'), 800, 0, 'Muy Urgente', $citaID, $empleadoID, $ubicacionID]);
    $ordenID = $pdo->lastInsertId();
    $anticipo = 800 * 0.5;

    $fechaPago = date('Y-m-d');
    $tipoPago = "anticipo";

    // Llamar al procedimiento almacenado para realizar el pago
    $sql = "CALL realizarPago(:ordenID, :fechaPago, :monto, :tipoPago, :formaDePago)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ordenID' => $ordenID,
        ':fechaPago' => $fechaPago,
        ':monto' => $anticipo,
        ':tipoPago' => $tipoPago,
        ':formaDePago' => $formaDePago,
    ]);

    // Consultar el pago asociado a la orden
    $stmt = $pdo->prepare("SELECT pagoID FROM PAGOS WHERE ordenID = ?");
    $stmt->execute([$ordenID]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pago) {
        $_SESSION['error'] = "No se encontró un pago asociado a esta orden.";
        $_SESSION['datos'];
        header("Location: inspeccion_view.php");
        exit();
    }

    $pagoID = $pago['pagoID'];

    // Registrar la entrega usando el procedimiento almacenado
    $sqlRegistrarEntrega = "CALL registrar_entrega(:pagoID, :formaDePago)";
    $stmtRegistrarEntrega = $pdo->prepare($sqlRegistrarEntrega);
    $stmtRegistrarEntrega->execute([
        ':pagoID' => $ordenID,
        ':formaDePago' => $formaDePago
    ]);

    // Actualizar la orden de trabajo con la nueva ubicación
    $nuevaUbicacionID = 1; // Ajusta esta ID según sea necesario
    $sqlActualizarOrden = "UPDATE ORDENES_TRABAJO SET ubicacionID = ? WHERE ordenID = ?";
    $stmtActualizarOrden = $pdo->prepare($sqlActualizarOrden);
    $stmtActualizarOrden->execute([$nuevaUbicacionID, $ordenID]);

    $_SESSION['bien'] = "Ejecutado exitosamente.";
    header("Location: inspeccion_view.php");
    exit();
} catch (PDOException $e) {
    // Manejo de errores
    $_SESSION['error'] = "Error: " . $e->getMessage();
    $_SESSION['datos'];
    header("Location: inspeccion_view.php");
    exit();
}
?>
