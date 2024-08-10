<?php
require '../includes/db.php';
session_start();
$con = new Database();
$pdo = $con->conectar();


$empleadoID = isset($_POST['empleadoID']) ? intval($_POST['empleadoID']) : null;
$ubicacionID = isset($_POST['ubicacionID']) ? intval($_POST['ubicacionID']) : null;
$formaDePago = isset($_POST['formaDePago']) ? trim($_POST['formaDePago']) : '';
$vehiculoID = filter_input(INPUT_POST, 'vehiculoID', FILTER_SANITIZE_NUMBER_INT);






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

    $fechaPago = date('Y-m-d');
    $tipoPago = "anticipo";


    try {
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
    } catch (PDOException $e) {
        $_SESSION['error'] = ("Error al realizar el pago: " . $e->getMessage());
        header("Location: inspeccion_view.php");
        exit();
    }
    // Consultar el pago asociado a la orden
    $stmt = $pdo->prepare("SELECT pagoID FROM PAGOS WHERE ordenID = ?");
    $stmt->execute([$ordenID]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pago) {
        $_SESSION['error'] = "No se encontró un pago asociado a esta orden.";
        header("Location: entregar.php");
        exit();
    }

    $pagoID = $pago['pagoID'];

    // Registrar la entrega usando el procedimiento almacenado
    $stmt = $pdo->prepare("CALL registrar_entrega(?)");
    $stmt->execute([$pagoID]);

    $nuevaUbicacionID = 4;
    // Actualizar la orden de trabajo con la nueva ubicación
    $sqlActualizarOrden = "UPDATE ORDENES_TRABAJO SET ubicacionID = ? WHERE ordenID = ?";
    $stmtActualizarOrden = $pdo->prepare($sqlActualizarOrden);
    $stmtActualizarOrden->execute([$nuevaUbicacionID, $ordenID]);
    $_SESSION['bien'] = "Ejecutado exitosamente";
    header("Location: inspeccion_view.php");
    exit();


    $_SESSION['bien'] = "Ejecutado exitosamente.";
    header("Location: inspeccion_view.php");
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    header("Location: inspeccion_view.php");
    exit();
}
