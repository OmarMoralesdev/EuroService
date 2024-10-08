<?php
require '../includes/db.php';
session_start();

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_cita_session'])) {
    // Eliminar la variable de sesión 'cita'
    unset($_SESSION['cita']);
    exit(); // Terminar la ejecución para no procesar el resto del script
}

$cita = isset($_SESSION['cita']) ? $_SESSION['cita'] : null;
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : "";
unset($_SESSION['mensaje']); // Limpiar el mensaje después de mostrarlo

if ($cita) {
    $vehiculoID = $cita['vehiculoID'];
    $detalles = obtenerDetallesVehiculoyCliente($pdo, $vehiculoID);
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-2">
            <div class="container">
                <h2>CANCELAR CITA</h2>
                <div class="form-container">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error']) . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    
                    ?>

                    <?php if ($cita) : ?>
                        <form method="post" action="Cancelarcita_back.php" id="formCita" class="mt-4">
                            <input type="hidden" name="citaID" value="<?php echo htmlspecialchars($cita['citaID']); ?>">
                            <input type="hidden" name="estado" id="estadoField" value="">
                            <div class="mb-3">
                                <label for="clienteID" class="form-label">Cliente:</label>
                                <input type="text" class="form-control" id="clienteID" value="<?php echo htmlspecialchars($detalles['nombre'] . ' ' . $detalles['apellido_paterno']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="vehiculoID" class="form-label">Vehículo:</label>
                                <input type="text" class="form-control" id="vehiculoID" value="<?php echo htmlspecialchars($detalles['marca'] . ' ' . $detalles['modelo'] . ' ' . $detalles['anio']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="servicioSolicitado" class="form-label">Servicio Solicitado:</label>
                                <input type="text" class="form-control" id="servicioSolicitado" value="<?php echo htmlspecialchars($cita['servicio_solicitado']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_cita" class="form-label">Fecha de la Cita:</label>
                                <input type="datetime-local" class="form-control" id="fecha_cita" value="<?php echo date('Y-m-d\TH:i', strtotime($cita['fecha_cita'])); ?>" readonly>
                            </div>

                            <button type="button" class="btn btn-danger d-grid gap-2 col-6 mx-auto" id="btnEliminar">Cancelar</button>
                        </form>
                    <?php else : ?>
                        <div class="alert alert-warning mt-3">No se encontraron detalles de la cita.</div>
                    <?php endif; ?>
                </div>

                <!-- Modal de Confirmación -->
                <div class="modal fade" id="confirmacionModal" tabindex="-1" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmacionModalLabel">Confirmar Eliminación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>¿Estás seguro de que deseas eliminar la cita?<br></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-danger" id="confirmarEliminar">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>

     
                <script>
                    // Muestra el modal de confirmación cuando se hace clic en el botón "Eliminar"
                    document.getElementById('btnEliminar').addEventListener('click', function() {
                        const confirmacionModal = new bootstrap.Modal(document.getElementById('confirmacionModal'));
                        confirmacionModal.show();
                    });

                    // Configura el valor del campo oculto y envía el formulario cuando se confirma la eliminación
                    document.getElementById('confirmarEliminar').addEventListener('click', function() {
                        document.getElementById('estadoField').value = 'cancelado'; // Configura el estado a 'cancelado'
                        document.getElementById('formCita').submit(); // Envía el formulario
                    });

                    // Muestra el modal de éxito si está presente
                    $(document).ready(function() {
                        if ($('#staticBackdrop').length) {
                            $('#staticBackdrop').modal('show');
                        }
                    });
                </script>
            </div>
        </div>
    </div>
    <script>
    // Al salir de la página, borrar la cita de la sesión
    window.addEventListener('beforeunload', function(event) {
        // Si el usuario está recargando la página, evitar la eliminación de la cita
        if (event.persisted || (window.performance && window.performance.navigation.type == 1)) {
            // Recarga de página detectada, no hacer nada
        } else {
            // Salida de la página detectada, eliminar la sesión del cita
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'eliminar_cita_session=true'
            });
        }
    });
</script>

</body>

</html>
