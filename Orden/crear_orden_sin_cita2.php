<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $vehiculoID = $_POST['vehiculoID'];
    $servicioSolicitado = $_POST['servicioSolicitado'];
    $costoManoObra = $_POST['costoManoObra'];
    $costoRefacciones = $_POST['costoRefacciones'];
    $empleadoID = $_POST['empleado'];
    $ubicacionID = $_POST['ubicacionID'];
    $anticipo = $_POST['anticipo'];
    $formaDePago = $_POST['formadepago'];

    // Fechas y estados predefinidos
    $fechaSolicitud = date('Y-m-d'); // Fecha actual
    $fechaCita = date('Y-m-d'); // Fecha actual para la cita
    $fechaOrden = $fechaCita;
    $urgencia = "si";
    $atencion = "Muy Urgente";

    // Validación de datos
    if ($costoManoObra < 0 || $costoRefacciones < 0) {
        $_SESSION['error'] = "No puedes ingresar números negativos.";
        header("Location: crear_orden_sin_cita.php");
        exit();
    }

    require '../includes/db.php';
    $con = new Database();
    $pdo = $con->conectar();

    try {
        // Verificar si el vehículo ya tiene una cita pendiente o en proceso
        $sqlVerificarCita = "SELECT * FROM CITAS WHERE vehiculoID = ? AND estado IN ('pendiente', 'en proceso')";
        $stmtVerificarCita = $pdo->prepare($sqlVerificarCita);
        $stmtVerificarCita->execute([$vehiculoID]);

        if ($stmtVerificarCita->rowCount() > 0) {
            $_SESSION['error'] = "El vehículo ya tiene una cita pendiente o en proceso.";
            header("Location: crear_orden_sin_cita.php");
            exit();
        }

        $pdo->beginTransaction();

        // Insertar cita
        $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
        $stmtCita = $pdo->prepare($sqlCita);
        $stmtCita->execute([$vehiculoID, $servicioSolicitado, $fechaSolicitud, $fechaCita, $urgencia]);
        $citaID = $pdo->lastInsertId();

        // Verificar si ya existe una orden de trabajo para esta cita
        $sqlVerificarOrden = "SELECT * FROM ORDENES_TRABAJO WHERE citaID = ?";
        $stmtVerificarOrden = $pdo->prepare($sqlVerificarOrden);
        $stmtVerificarOrden->execute([$citaID]);

        if ($stmtVerificarOrden->rowCount() > 0) {
            $pdo->rollBack();
            $_SESSION['error'] = "Ya existe una orden de trabajo para esta cita.";
            header("Location: crear_orden_sin_cita.php");
            exit();
        }

        // Insertar orden de trabajo
        $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtOrden = $pdo->prepare($sqlOrden);
        $stmtOrden->execute([$fechaOrden, $costoManoObra, $costoRefacciones, $atencion, $citaID, $empleadoID, $ubicacionID]);
        $ordenID = $pdo->lastInsertId();

        // Insertar pago
        $sqlPago = "INSERT INTO PAGOS (ordenID, fecha_pago, monto, tipo_pago, forma_de_pago) VALUES (?, ?, ?, 'anticipo', ?)";
        $stmtPago = $pdo->prepare($sqlPago);
        $stmtPago->execute([$ordenID, $fechaOrden, $anticipo, $formaDePago]);

        // Verificar el límite de vehículos en la ubicación
        $sqlVerificarUbicacion = "SELECT vehiculos_maximos, vehiculos_actuales FROM UBICACIONES WHERE ubicacionID = ?";
        $stmtVerificarUbicacion = $pdo->prepare($sqlVerificarUbicacion);
        $stmtVerificarUbicacion->execute([$ubicacionID]);
        $ubicacion = $stmtVerificarUbicacion->fetch(PDO::FETCH_ASSOC);

        if (!$ubicacion) {
            $_SESSION['error'] = "Ubicación no encontrada.";
            header("Location: crear_orden_sin_cita.php");
            exit();
        }

        if ($ubicacion['vehiculos_actuales'] >= $ubicacion['vehiculos_maximos']) {
            $_SESSION['error'] = "La ubicación ya está llena.";
            header("Location: crear_orden_sin_cita.php");
            exit();
        }

        // Actualizar el conteo de vehículos en la ubicación
        $sqlActualizarUbicacion = "UPDATE UBICACIONES SET vehiculos_actuales = vehiculos_actuales + 1 WHERE ubicacionID = ?";
        $stmtActualizarUbicacion = $pdo->prepare($sqlActualizarUbicacion);
        $stmtActualizarUbicacion->execute([$ubicacionID]);

        $pdo->commit();
        $_SESSION['mensaje'] = "Cita y orden de trabajo creadas exitosamente.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al crear la cita y orden de trabajo: " . $e->getMessage();
    }

    header("Location: crear_orden_sin_cita.php");
    exit();
    actualizarEstadoCita($pdo, $citaID, 'en proceso');
} else {
    $_SESSION['error'] = "Método de solicitud no válido.";
    header("Location: crear_orden_sin_cita.php");
    exit();
}
