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

$errors = [];
$activo = "si";
$currentYear = date('Y');

if ($anio < 1886 || $anio > $currentYear) {
    $errors[] = "El año debe estar entre 1886 y el año actual.";
}

if (empty($errors)) {
    $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
    $stmtVerificar = $pdo->prepare($verificar);
    $stmtVerificar->execute([$vin]);

    if ($stmtVerificar->rowCount() > 0) {
        $errors[] = "El vehículo ya está registrado.";
    } else {
        $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'si')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

        if ($stmt->rowCount() > 0) {
            $vehiculoID = $pdo->lastInsertId();
            
            // Insertar cita
            $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
            $stmtCita = $pdo->prepare($sqlCita);
            $stmtCita->execute([$vehiculoID, 'Inspección', date('Y-m-d'), date('Y-m-d'), 'si']);
            $citaID = $pdo->lastInsertId();

            // Insertar orden de trabajo
            $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtOrden = $pdo->prepare($sqlOrden);
            $stmtOrden->execute([date('d-m-y'), 800, 0, 'Muy Urgente', $citaID, $empleadoID, $ubicacionID]);
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

                $pdo->commit();
                $_SESSION['bien'] = "Ejecutado exitosamente";
                header("Location: inspeccion_view.php");
                exit();

            } catch (Exception $e) {
                $_SESSION['error'] = "Error al realizar el pago: " . $e->getMessage();
                header("Location: inspeccion_view.php");
                exit();
            }
        }
    }
} else {
    $_SESSION['error'] = implode(' ', $errors);
    header("Location: inspeccion_view.php");
    exit();
}
?>
