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
$tipoServicio = "inspección"; // Cambio en el nombre del campo

// Consultar el nombre del empleado
$sqlEmpleado = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
FROM EMPLEADOS 
JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID
WHERE empleadoID = ?";
$stmtEmpleado = $pdo->prepare($sqlEmpleado);
$stmtEmpleado->execute([$empleadoID]);
$empleado = $stmtEmpleado->fetch(PDO::FETCH_ASSOC);
$empleadoNombre = $empleado ? $empleado['nombre'] : 'Desconocido';

// Consultar el nombre de la ubicación
$sqlUbicacion = "SELECT lugar FROM UBICACIONES WHERE ubicacionID = ?";
$stmtUbicacion = $pdo->prepare($sqlUbicacion);
$stmtUbicacion->execute([$ubicacionID]);
$ubicacion = $stmtUbicacion->fetch(PDO::FETCH_ASSOC);
$ubicacionNombre = $ubicacion ? $ubicacion['nombre'] : 'Desconocido';

// Consultar el vehículo
$sqlVehiculo = "SELECT marca, modelo, anio FROM VEHICULOS WHERE vehiculoID = ?";
$stmtVehiculo = $pdo->prepare($sqlVehiculo);
$stmtVehiculo->execute([$vehiculoID]);
$vehiculo = $stmtVehiculo->fetch(PDO::FETCH_ASSOC);
if (!$vehiculo) {
    $_SESSION['error'] = 'Error: El vehículo no está registrado en la base de datos.';
    header("Location: inspeccion_view.php");
    exit();
}

// Guardar datos en sesión para repoblar el formulario en caso de error
$_SESSION['formData'] = [
    'vehiculoID' => $vehiculoID,
    'diagnostico' => $diagnostico,
    'empleadoID' => $empleadoID,
    'ubicacionID' => $ubicacionID,
    'formadepago' => $formaDePago,
    'vehiculoMarca' => $vehiculo['marca'],
    'vehiculoModelo' => $vehiculo['modelo'],
    'vehiculoAnio' => $vehiculo['anio'],
    'empleadoNombre' => $empleadoNombre,
    'ubicacionNombre' => $ubicacionNombre
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
        $fechaSolicitud = date('Y-m-d'); // Fecha actual
        $fechaCita = date('Y-m-d H:i:s'); // Fecha actual con hora para la cita
        $fechaOrden = $fechaCita;
        $urgencia = "si";
        $atencion = "muy urgente";
        // Insertar cita
        $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, costo_mano_obra, costo_refacciones, tipo_servicio, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
        $stmtCita = $pdo->prepare($sqlCita);
        $stmtCita->execute([$vehiculoID, $diagnostico, 800, 0, $tipoServicio, $fechaSolicitud, $fechaCita, $urgencia]);
        $citaID = $pdo->lastInsertId();
        
        // Insertar orden de trabajo
        $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, anticipo, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtOrden = $pdo->prepare($sqlOrden);
        $stmtOrden->execute([$fechaOrden, $anticipo, $atencion, $citaID, $empleadoID, $ubicacionID]);
        $ordenID = $pdo->lastInsertId();
        
        $fechaPago = date('Y-m-d');
        $tipoPago = "anticipo";
        $anticipo = 800 * 0.5;
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
        header("Location: inspeccion_view.php");
        exit();
    }

   
   
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
unset($_SESSION['formData']);
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
