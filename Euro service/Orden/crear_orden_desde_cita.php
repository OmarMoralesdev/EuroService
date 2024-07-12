<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
// Función para obtener la lista de empleados disponibles
function obtenerEmpleadosDisponibles($pdo) {
    $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
            FROM EMPLEADOS 
            JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID";
           
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citaID = $_POST['citaID'];

    // Obtener los detalles de la cita seleccionada
    $cita = obtenerDetallesCita($pdo, $citaID);
    $vehiculo = obtenerDetallesVehiculo($pdo, $cita['vehiculoID']);
    $cliente = obtenerDetallesCliente($pdo, $vehiculo['clienteID']);

    if ($cita) {
        // Crear la orden de trabajo basada en los detalles de la cita
        $vehiculoID = $cita['vehiculoID'];
        $fechaOrden = date('Y-m-d H:i:s');
        $detallesTrabajo = $cita['servicio_solicitado'];
        $estado = 'pendiente';
        
        // Verificar si se han enviado los detalles adicionales del formulario
        if (isset($_POST['detallesFormulario'])) {
            $costoManoObra = $_POST['costoManoObra'];
            $costoRefacciones = $_POST['costoRefacciones'];
            $empleado = $_POST['empleado'];
            $ubicacionID = 1;  // Asignar una ubicación por defecto, esto puede ajustarse
            $atencion = 'no urgente';

            try {
                // Crear la orden de trabajo
                $nuevaOrdenID = crearOrdenTrabajo($pdo, $vehiculoID, $fechaOrden, $detallesTrabajo, $costoManoObra, $costoRefacciones, $estado, $empleado, $ubicacionID, $atencion);

                // Actualizar el estado de la cita a 'completado'
                actualizarEstadoCita($pdo, $citaID, 'completado');

                echo "Nueva orden de trabajo creada con ID: $nuevaOrdenID";
            } catch (Exception $e) {
                echo "Error al crear la orden de trabajo: " . $e->getMessage();
            }
        } else {
            // Mostrar formulario para ingresar detalles adicionales de la orden de trabajo
            // y seleccionar empleado
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Completar Detalles de Orden de Trabajo</title>
</head>
<body>
    <h1>Completar Detalles de Orden de Trabajo</h1>
    <form action="crear_orden_desde_cita.php" method="post">
        <input type="hidden" name="citaID" value="<?php echo $citaID; ?>">

        <label for="costoManoObra">Costo Mano de Obra:</label>
        <input type="number" step="0.01" id="costoManoObra" name="costoManoObra" required><br><br>

        <label for="costoRefacciones">Costo de Refacciones:</label>
        <input type="number" step="0.01" id="costoRefacciones" name="costoRefacciones" required><br><br>

        <label for="empleado">Seleccionar Empleado:</label>
        <select id="empleado" name="empleado" required>
            <?php
            // Obtener la lista de empleados disponibles
            $empleados = obtenerEmpleadosDisponibles($pdo);
            foreach ($empleados as $empleado) {
                $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                echo "<option value=\"{$empleado['empleadoID']}\">{$nombreCompleto}</option>";
            }
            ?>
        </select><br><br>

        <input type="hidden" name="detallesFormulario" value="true">

        <input type="submit" value="Crear Orden de Trabajo">
    </form>
</body>
</html>
<?php
        }
    } else {
        echo "Cita no encontrada.";
    }
}
?>
