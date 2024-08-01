<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $anio = trim($_POST['anio']);
    $color = trim($_POST['color']);
    $kilometraje = trim($_POST['kilometraje']);
    $placas = trim($_POST['placas']);
    $vin = trim($_POST['vin']);

    $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
    $stmtVerificar = $pdo->prepare($verificar);
    $stmtVerificar->execute([$vin]);

        if ($stmtVerificar->rowCount() > 0) {
            $_SESSION['error'] = "El vehículo ya está registrado.";
        } else {
            $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['mensaje'] = "Vehículo registrado exitosamente.";
                $vehiculoID = $pdo->lastInsertId();

                if ($continuidad) {
                    // Registrar la cita e inspección si se ha confirmado la continuidad
                    $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
                    $stmtCita = $pdo->prepare($sqlCita);
                    $stmtCita->execute([$vehiculoID, 'Inspección', date('Y-m-d'), date('Y-m-d'), 'Muy Urgente']);
                    $citaID = $pdo->lastInsertId();

                    // Insertar orden de trabajo
                    $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmtOrden = $pdo->prepare($sqlOrden);
                    $stmtOrden->execute([date('Y-m-d'), 800, 0, 'Muy Urgente', $citaID, $_POST['empleado'], $_POST['ubicacionID']]);
                    $ordenID = $pdo->lastInsertId();

                    // Insertar pago
                    $sqlPago = "INSERT INTO PAGOS (ordenID, fecha_pago, monto, tipo_pago, forma_de_pago) VALUES (?, ?, ?, 'anticipo', ?)";
                    $stmtPago = $pdo->prepare($sqlPago);
                    $stmtPago->execute([$ordenID, date('Y-m-d'), 0, $_POST['formadepago']]);

                    // Actualizar conteo de vehículos en la ubicación
                    $sqlActualizarUbicacion = "UPDATE UBICACIONES SET vehiculos_actuales = vehiculos_actuales + 1 WHERE ubicacionID = ?";
                    $stmtActualizarUbicacion = $pdo->prepare($sqlActualizarUbicacion);
                    $stmtActualizarUbicacion->execute([$_POST['ubicacionID']]);

                    $_SESSION['mensaje'] = " Cita e inspección registradas exitosamente.";
                } else {
                    $showModal = true;
                }
            } else {
                $_SESSION['error'] = "Error: " . $pdo->errorInfo()[2];
            }
        }
    }
}
?>