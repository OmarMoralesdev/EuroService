<?php
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

function obtenerEmpleadosDisponibles($pdo)
{
    $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
            FROM EMPLEADOS 
            JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function obtenerUbicacionesActivas($pdo)
{
    $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si';";
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
        $citaID = $_POST['citaID'];
        $fechaOrden = date('Y-m-d H:i:s');

        // Verificar si se han enviado los detalles adicionales del formulario
        if (isset($_POST['detallesFormulario'])) {
            $costoManoObra = $_POST['costoManoObra'];
            $costoRefacciones = $_POST['costoRefacciones'];
            $empleado = $_POST['empleado'];
            $ubicacionID = $_POST['ubicacionID'];
            $anticipo = $_POST['anticipo'];
            $tipoPago = "anticipo";
            $atencion = 'no urgente'; // Puedes ajustar esto según los detalles del formulario
            $formaDePago = $_POST['formadepago'];

            // Calcular el total estimado
            $total_estimado = $costoManoObra + $costoRefacciones;

            try {
                // Crear la orden de trabajo
                $nuevaOrdenID = crearOrdenTrabajo($pdo, $fechaOrden, $costoManoObra, $costoRefacciones, $total_estimado, $atencion, $citaID, $empleado, $ubicacionID);

                realizarPago($pdo, $ordenID, $fechaPago, $anticipo, $tipoPago, $formaDePago);
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
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
                <link rel="stylesheet" href="../assets/css/styles.css">
                <title>Completar Detalles de Orden de Trabajo</title>
            </head>

            <body>
                <div class="wrapper">
                    <?php include '../includes/vabr.html'; ?>
                    <div class="main p-3">
                        <div class="container">
                            <h1>Completar Detalles de Orden de Trabajo</h1>
                            <form action="crear_orden_desde_cita.php" method="post">
                                <input type="hidden" name="citaID" value="<?php echo $citaID; ?>">

                                <label for="costoManoObra">Costo Mano de Obra:</label>
                                <input type="number" step="0.01" id="costoManoObra" name="costoManoObra" class="form-control" required><br><br>

                                <label for="costoRefacciones">Costo de Refacciones:</label>
                                <input type="number" step="0.01" id="costoRefacciones" name="costoRefacciones" class="form-control" required><br><br>

                                <label for="anticipo">Anticipo:</label>
                                <input type="number" step="0.01" name="anticipo" class="form-control" required><br><br>

                                <label for="formadepago" class="form-label">Forma de pago:</label>
                                <select name="formadepago" class="form-control" required>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                </select>

                                <label for="empleado" class="form-label">Empleado ID:</label>
                                <select name="empleado" class="form-control" required>
                                    <?php
                                    $empleados = obtenerEmpleadosDisponibles($pdo);
                                    foreach ($empleados as $empleado) {
                                        $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                                        echo "<option value=\"{$empleado['empleadoID']}\">{$nombreCompleto}</option>";
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Debes seleccionar un empleado.</div>

                                <label for="ubicacionID" class="form-label">Ubicación ID:</label>
                                <select name="ubicacionID" class="form-control" required>
                                    <?php
                                    $ubicaciones = obtenerUbicacionesActivas($pdo);
                                    if (empty($ubicaciones)) {
                                        echo "<option value=''>No hay ubicaciones disponibles</option>";
                                    } else {
                                        foreach ($ubicaciones as $ubicacion) {
                                            echo "<option value=\"{$ubicacion['ubicacionID']}\">{$ubicacion['lugar']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">Debes seleccionar una ubicación.</div>

                                <input type="hidden" name="detallesFormulario" value="true">

                                <input type="submit" value="Crear Orden de Trabajo">
                            </form>
                        </div>
                    </div>
                </div>
            </body>

            </html>
<?php
        }
    } else {
        echo "Cita no encontrada.";
    }
}
?>