<?php
require '../includes/db.php';
session_start();

$con = new Database();
$pdo = $con->conectar();

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
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-2">
            <div class="container">
                <h2>EDITAR CITA</h2>
                <div class="form-container">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if ($mensaje) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Usuario registrado!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='alert alert-success' role='alert'>{$mensaje}</div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>                                       
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                    ?>

                    <?php if ($cita) : ?>
                        <form method="post" action="editar_cita_back.php" class="mt-4">
                            <input type="hidden" name="citaID" value="<?php echo htmlspecialchars($cita['citaID']); ?>">
                            <div class="mb-3">
                                <label for="clienteID" class="form-label">Cliente:</label>
                                <input type="text" class="form-control" id="clienteID" name="clienteID" value="<?php echo htmlspecialchars($detalles['nombre'] . ' ' . $detalles['apellido_paterno']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="vehiculoID" class="form-label">Vehículo:</label>
                                <input type="text" class="form-control" id="vehiculoID" name="vehiculoID" value="<?php echo htmlspecialchars($detalles['marca'] . ' ' . $detalles['modelo'] . ' ' . $detalles['anio']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="servicioSolicitado" class="form-label">Servicio Solicitado:</label>
                                <input type="text" class="form-control" id="servicioSolicitado" name="servicioSolicitado" value="<?php echo htmlspecialchars($cita['servicio_solicitado']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_cita" class="form-label">Fecha de la Cita:</label>
                                <input type="datetime-local" class="form-control" id="fecha_cita" name="fecha_cita" value="<?php echo date('Y-m-d\TH:i', strtotime($cita['fecha_cita'])); ?>" required>
                            </div>
                            <button type="submit" name="actualizar" class="btn btn-dark w-100" onclick="return confirmCancel();">Guardar Cambios</button>
                        </form>
                    <?php else : ?>
                        <div class="alert alert-warning mt-3">No se encontraron detalles de la cita.</div>
                    <?php endif; ?>
                </div>

                <!-- Modal de Confirmación -->
                <div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelConfirmationModalLabel">Confirmar Cancelación</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                ¿Estás seguro de que deseas cancelar esta cita?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="cancelNo" data-bs-dismiss="modal">No, mantener cita</button>
                                <button type="button" class="btn btn-danger" id="confirmCancel">Sí, cancelar cita</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const dateInput = document.getElementById('fecha_cita');
                        const today = new Date();
                        const tomorrow = new Date(today);
                        tomorrow.setDate(today.getDate() + 1); // Día siguiente

                        const year = tomorrow.getFullYear();
                        const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
                        const day = String(tomorrow.getDate()).padStart(2, '0');
                        const minDate = `${year}-${month}-${day}T09:00`;
                        const maxDate = `${year + 1}-${month}-${day}T17:00`;

                        // Establecer el valor mínimo y máximo del Date Picker
                        dateInput.min = minDate;
                        dateInput.max = maxDate;

                        dateInput.addEventListener('input', function() {
                            const selectedDate = new Date(dateInput.value);
                            const selectedHour = selectedDate.getHours();
                            const selectedMinutes = selectedDate.getMinutes();

                            if (selectedDate < tomorrow) {
                                dateInput.setCustomValidity('La fecha debe ser al menos para el día siguiente.');
                            } else if (selectedHour < 9 || (selectedHour >= 17 && selectedMinutes > 0)) {
                                dateInput.setCustomValidity('La hora debe estar dentro del horario laboral (09:00 - 17:00).');
                            } else {
                                dateInput.setCustomValidity('');
                            }
                        });
                    });
                    document.getElementById('confirmCancel').addEventListener('click', function() {
                        // Marca el checkbox como checked
                        document.getElementById('estado').checked = true;
                        // Cierra el modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('cancelConfirmationModal'));
                        modal.hide();
                    });

                    document.getElementById('cancelNo').addEventListener('click', function() {
                        // Desmarca el checkbox si el usuario elige no cancelar
                        document.getElementById('estado').checked = false;
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        if ($('#staticBackdrop').length) {
                            $('#staticBackdrop').modal('show');
                        }
                    });
                </script>
</body>

</html>