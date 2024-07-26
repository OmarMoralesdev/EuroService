<?php
session_start();
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehiculoID = $_POST['vehiculoID'];
    $servicioSolicitado = $_POST['servicioSolicitado'];
    $fechaCita = date('Y-m-d');;
    $urgencia = "si";
    $fechaSolicitud = date('Y-m-d'); // Fecha actual
    $fechaOrden = $fechaCita;
    $costoManoObra = $_POST['costoManoObra'];
    $costoRefacciones = $_POST['costoRefacciones'];
    $empleadoID = $_POST['empleado'];
    $ubicacionID = $_POST['ubicacionID'];
    $atencion = "Muy Urgente";
    if ($costoManoObra < 0) {
        $_SESSION['error'] = "No puedes ingresar numeros negativos";
        header("Location: crear_orden_sin_cita.php");
        exit();
    } elseif ($costoRefacciones < 0) {
        $_SESSION['error'] = "No puedes ingresar numeros negativos";
        header("Location: crear_orden_sin_cita.php");
        exit();
    } else {
        require '../includes/db.php';
        $con = new Database();
        $pdo = $con->conectar();
        try {
            $pdo->beginTransaction();
            $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
            $stmtCita = $pdo->prepare($sqlCita);
            $stmtCita->execute([$vehiculoID, $servicioSolicitado, $fechaSolicitud, $fechaCita, $urgencia]);
            $citaID = $pdo->lastInsertId();
            $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones,atencion, citaID, empleadoID, ubicacionID ) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtOrden = $pdo->prepare($sqlOrden);
            $stmtOrden->execute([$fechaOrden, $costoManoObra, $costoRefacciones, $atencion, $citaID, $empleadoID, $ubicacionID]);
            $pdo->commit();
            $_SESSION['mensaje'] = "Cita y orden de trabajo creadas exitosamente.";
            header("Location: crear_orden_sin_cita.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = $e->getMessage();
            header("Location: crear_orden_sin_cita.php");
            exit();
        }
    }
}
