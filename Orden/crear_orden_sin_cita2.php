<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $vehiculoID = $_POST['vehiculoID'];
    $servicio_solicitado = $_POST['servicioSolicitado'];
    $tipoServicio = "reparación"; // Cambio en el nombre del campo
    $costoManoObra = $_POST['costoManoObra'];
    $costoRefacciones = $_POST['costoRefacciones'];
    $empleadoID = $_POST['empleado'];
    $ubicacionID = $_POST['ubicacionID'];
    $anticipo = $_POST['anticipo'];
    $formaDePago = $_POST['formadepago'];

    // Fechas y estados predefinidos
    $fechaSolicitud = date('Y-m-d'); // Fecha actual
    $fechaCita = date('Y-m-d H:i:s'); // Fecha actual con hora para la cita
    $fechaOrden = $fechaCita;
    $urgencia = "si";
    $atencion = "muy urgente";

    if (
        empty($vehiculoID) || empty($tipoServicio) || empty($costoManoObra) ||
        empty($costoRefacciones) || empty($empleadoID) || empty($ubicacionID) ||
        empty($anticipo) || empty($formaDePago)
    ) {
        $_SESSION['error'] = "Todos los campos son requeridos.";
        header("Location: crear_orden_sin_cita.php");
        exit();
    }

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
        $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, costo_mano_obra, costo_refacciones, tipo_servicio, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
        $stmtCita = $pdo->prepare($sqlCita);
        $stmtCita->execute([$vehiculoID, $servicio_solicitado, $costoManoObra, $costoRefacciones, $tipoServicio, $fechaSolicitud, $fechaCita, $urgencia]);
        $citaID = $pdo->lastInsertId();
        
        // Insertar orden de trabajo
        $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, anticipo, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtOrden = $pdo->prepare($sqlOrden);
        $stmtOrden->execute([$fechaOrden, $anticipo, $atencion, $citaID, $empleadoID, $ubicacionID]);
        $ordenID = $pdo->lastInsertId();

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
            $_SESSION['error'] = "Error al realizar el pago: " . $e->getMessage();
            $pdo->rollBack();
            header("Location: crear_orden_sin_cita.php");
            exit();
        }

        $pdo->commit();
        $_SESSION['bien'] = "Cita y orden de trabajo creadas exitosamente.";
        header("Location: crear_orden_sin_cita.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al crear la cita y orden de trabajo: " . $e->getMessage();
        header("Location: crear_orden_sin_cita.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Método de solicitud no válido.";
    header("Location: crear_orden_sin_cita.php");
    exit();
}
?>
