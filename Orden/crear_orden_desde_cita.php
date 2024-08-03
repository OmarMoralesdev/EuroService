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

            if ($costoManoObra < 0 || $costoRefacciones < 0) {
                $_SESSION['error'] = "No puedes ingresar números negativos.";
                header("Location: crear_orden_desde_cita.php");
                exit();
            }

            // Calcular el total estimado
            $total_estimado = $costoManoObra + $costoRefacciones;

            try {
                // Iniciar la transacción
                $pdo->beginTransaction();

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
                $sqlVerificarUbicacion = "
                SELECT 
                    u.capacidad AS vehiculos_maximos, 
                    COUNT(v.vehiculoID) AS vehiculos_actuales 
                FROM 
                    UBICACIONES u
                    LEFT JOIN ORDENES_TRABAJO ot ON u.ubicacionID = ot.ubicacionID
                    LEFT JOIN CITAS c ON ot.citaID = c.citaID
                    LEFT JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
                WHERE 
                    u.ubicacionID = ?
                GROUP BY 
                    u.ubicacionID, u.capacidad
                ";

                $stmtVerificarUbicacion = $pdo->prepare($sqlVerificarUbicacion);
                $stmtVerificarUbicacion->execute([$ubicacionID]);
                $ubicacion = $stmtVerificarUbicacion->fetch(PDO::FETCH_ASSOC);

                if (!$ubicacion) {
                    $pdo->rollBack();
                    $_SESSION['error'] = "Ubicación no encontrada.";
                    header("Location: crear_orden_sin_cita.php");
                    exit();
                }

                if ($ubicacion['vehiculos_actuales'] >= $ubicacion['vehiculos_maximos']) {
                    $pdo->rollBack();
                    $_SESSION['error'] = "La ubicación ya está llena.";
                    header("Location: crear_orden_sin_cita.php");
                    exit();
                }
                // Insertar orden de trabajo
                $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtOrden = $pdo->prepare($sqlOrden);
                $stmtOrden->execute([$fechaOrden, $costoManoObra, $costoRefacciones, $atencion, $citaID, $empleado, $ubicacionID]);
                $ordenID = $pdo->lastInsertId();

                $fechaPago = date('Y-m-d');
                $tipoPago = "anticipo";
                // Insertar pago
                realizarPago($pdo, $ordenID, $fechaPago, $anticipo, $tipoPago, $formaDePago);
                actualizarEstadoCita($pdo, $citaID, 'en proceso');

                // Hacer commit
                $pdo->commit();

                echo "Nueva orden de trabajo creada con ID: $ordenID";
            } catch (Exception $e) {
                // Rollback en caso de error
                $pdo->rollBack();
                echo "Error al crear la orden de trabajo: " . $e->getMessage();
            }
        } else {
            // Manejo para cuando no se han enviado detalles adicionales del formulario
        
?>

            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Completar Detalles de Orden de Trabajo</title>
            </head>

            <body>
                <div class="wrapper">
                    <?php include '../includes/vabr.php'; ?>
                    <div class="main p-3">
                        <div class="container">
                            <h2>Completar Detalles de Orden de Trabajo</h2>
                            <div class="form-container">
                                <?php
                                if (isset($_SESSION['error'])) {
                                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                                    unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                                }
                                if (isset($_SESSION['bien'])) {
                                    echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Usuario registrado!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='alert alert-success' role='alert'>{$_SESSION['bien']}</div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
                                    unset($_SESSION['bien']);
                                }
                                ?>
                                <form action="crear_orden_desde_cita.php" method="post">
                                    <input type="hidden" name="citaID" value="<?php echo $citaID; ?>">

                                    <label for="costoManoObra">Costo Mano de Obra:</label>
                                    <input type="number" step="0.01" id="costoManoObra" name="costoManoObra" class="form-control" required><br>

                                    <label for="costoRefacciones">Costo de Refacciones:</label>
                                    <input type="number" step="0.01" id="costoRefacciones" name="costoRefacciones" class="form-control" required><br>

                                    <label for="anticipo">Anticipo:</label>
                                    <input type="number" step="0.01" name="anticipo" class="form-control" required><br>

                                    <label for="formadepago" class="form-label">Forma de pago:</label>
                                    <select name="formadepago" class="form-control" required>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select><br>

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
                                    <div class="invalid-feedback">Debes seleccionar un empleado.</div><br>

                                    <label for="ubicacionID" class="form-label">Ubicación ID:</label>
                                    <select name="ubicacionID" class="form-control" required>
                                        <?php
                                        function obtenerUbicacionesActivas($pdo)
                                        {
                                            $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si';";
                                            $stmt = $pdo->query($sql);
                                            return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        }
                                        $ubicaciones = obtenerUbicacionesActivas($pdo);
                                        foreach ($ubicaciones as $ubicacion) {
                                            echo "<option value=\"{$ubicacion['ubicacionID']}\">{$ubicacion['lugar']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Debes seleccionar una ubicación.</div><br>

                                    <input type="hidden" name="detallesFormulario" value="true">

                                    <input type="submit" class="btn btn-dark w-100" value="Crear Orden de Trabajo">
                                </form>
                            </div>
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