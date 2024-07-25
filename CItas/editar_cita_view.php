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
    <?php include '../includes/vabr.html'; ?>
    <div class="main p-2">
        <div class="container">
            <h2>EDITAR CITA</h2>
            <div class="form-container">
                <?php if ($mensaje) : ?>
                    <div class="alert alert-info mt-3"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

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
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado:</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="pendiente" <?php echo $cita['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="en proceso" <?php echo $cita['estado'] == 'en proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                <option value="completado" <?php echo $cita['estado'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                            </select>
                        </div>
                        <button type="submit" name="actualizar" class="btn btn-dark w-100">Guardar Cambios</button>
                    </form>
                <?php else : ?>
                    <div class="alert alert-warning mt-3">No se encontraron detalles de la cita.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('fecha_cita');
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const minDate = `${year}-${month}-${day}T09:00`;
        const maxDate = `${year + 1}-${month}-${day}T17:00`;

        // Establecer el valor mínimo y máximo del Date Picker
        dateInput.min = minDate;
        dateInput.max = maxDate;

        dateInput.addEventListener('input', function() {
            const selectedDate = new Date(dateInput.value);
            const selectedHour = selectedDate.getHours();
            const selectedMinutes = selectedDate.getMinutes();

            if (selectedHour < 9 || (selectedHour >= 17 && selectedMinutes > 0)) {
                dateInput.setCustomValidity('La hora debe estar dentro del horario laboral (09:00 - 17:00).');
            } else {
                dateInput.setCustomValidity('');
            }
        });
    });
</script>
</body>
</html>
