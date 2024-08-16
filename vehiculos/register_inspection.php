<?php
require '../includes/db.php';
session_start();

$con = new Database();
$pdo = $con->conectar();

$empleadoID = isset($_POST['empleadoID']) ? intval($_POST['empleadoID']) : null;
$ubicacionID = isset($_POST['ubicacionID']) ? intval($_POST['ubicacionID']) : null;
$formaDePago = isset($_POST['formadepago']) ? trim($_POST['formadepago']) : '';
$clienteID = isset($_POST['clienteID']) ? $_POST['clienteID'] : '';
$marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
$modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
$anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';
$color = isset($_POST['color']) ? trim($_POST['color']) : '';
$kilometraje = isset($_POST['kilometraje']) ? trim($_POST['kilometraje']) : '';
$placas = isset($_POST['placas']) ? trim($_POST['placas']) : '';
$vin = isset($_POST['vin']) ? trim($_POST['vin']) : '';

$errors = [];
$activo = "si";
$currentYear = date('Y');

if ($anio < 1886 || $anio > $currentYear) {
    $_SESSION['error'] = "El año debe estar entre 1886 y el año actual.";
    header("Location: inspeccion_view.php");
    exit();
}
$sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'si')";
$stmt = $pdo->prepare($sql);
$stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

if ($stmt->rowCount() > 0) {
    $vehiculoID = $pdo->lastInsertId();

    // Insertar cita
    $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, tipo_servicio, fecha_solicitud, costo_mano_obra, costo_refacciones, total_estimado, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtCita = $pdo->prepare($sqlCita);
    $stmtCita->execute([$vehiculoID, 'Inspección', 'inspeccion', date('Y-m-d'), 800, 0, 800, date('Y-m-d'), 'si', 'pendiente']);
    $citaID = $pdo->lastInsertId();

    // Insertar orden de trabajo
    $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?)";
    $stmtOrden = $pdo->prepare($sqlOrden);
    $stmtOrden->execute([date('d-m-y'), 'Muy Urgente', $citaID, $empleadoID, $ubicacionID]);
    $ordenID = $pdo->lastInsertId();
    $anticipo = 800 * 0.5;
    $fechaPago = date('Y-m-d');
    $tipoPago = "anticipo";

    
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
   
}