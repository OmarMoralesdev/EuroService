<?php
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

$cita = null;
$mensaje = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        if ($citaID) {
            $sql = "SELECT * FROM CITAS WHERE citaID = ?";
            $query = $pdo->prepare($sql);
            $query->execute([$citaID]);
            $cita = $query->fetch(PDO::FETCH_ASSOC);

            if (!$cita) {
                $mensaje = "Cita no encontrada.";
            } else {
                $vehiculoID = $cita['vehiculoID'];
                $detalles = obtenerDetallesVehiculoyCliente($pdo, $vehiculoID);
            }
        } else {
            $mensaje = "ID de cita inválido.";
        }
    } elseif (isset($_POST['actualizar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        $servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
        $fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);
        $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

        $fechaActual = date('Y-m-d H:i:s');
        $fechaCitaTimestamp = strtotime($fechaCita);

        if ($fechaCitaTimestamp > strtotime($fechaActual)) {
            $mensaje = "Error: La fecha de la cita debe ser posterior a la fecha actual.";
        } else {
            $fechaLimite = date('Y-m-d H:i:s', strtotime('+30 minutes', $fechaCitaTimestamp));
            $sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE fecha_cita BETWEEN ? AND ? AND citaID != ?";
            $query = $pdo->prepare($sql);
            $query->execute([$fechaCita, $fechaLimite, $citaID]);

            $row = $query->fetch(PDO::FETCH_ASSOC);
            $countCitasProximas = $row['countCitas'];

    
                $sqlUpdate = "UPDATE CITAS SET servicio_solicitado = ?, fecha_cita = ?, estado = ? WHERE citaID = ?";
                $queryUpdate = $pdo->prepare($sqlUpdate);
                $resultUpdate = $queryUpdate->execute([$servicioSolicitado, $fechaCita, $estado, $citaID]);

                if ($resultUpdate) {
                    $mensaje = "Cita actualizada correctamente.";
                    $sql = "SELECT * FROM CITAS WHERE citaID = ?";
                    $query = $pdo->prepare($sql);
                    $query->execute([$citaID]);
                    $cita = $query->fetch(PDO::FETCH_ASSOC);
                    $vehiculoID = $cita['vehiculoID'];
                    $detalles = obtenerDetallesVehiculoyCliente($pdo, $vehiculoID);
                } else {
                    $mensaje = "Error al actualizar la cita.";
                }
            
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include '../includes/vabr.html'; ?>
    <div class="main p-2">
        <div class="container">
            <h2>EDITAR CITA</h2>
            <div class="form-container">
                <form method="post" action="#">
                    <div class="mb-3">
                        <label for="citaID" class="form-label">Seleccionar Cita:</label>
                        <select id="citaID" name="citaID" class="form-select" required>
                            <?php
                            $citas = listarCitasPendientes($pdo);
                            foreach ($citas as $citaOption) {
                                echo "<option value=\"{$citaOption['citaID']}\">Cita ID: {$citaOption['citaID']} - Vehículo: {$citaOption['marca']} {$citaOption['modelo']} {$citaOption['anio']} - Cliente: {$citaOption['nombre']} {$citaOption['apellido_paterno']} {$citaOption['apellido_materno']} - Servicio: {$citaOption['servicio_solicitado']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="buscar" class="btn btn-dark w-100">Buscar Cita</button>
                </form>
                <?php if ($mensaje) : ?>
                    <div class="alert alert-info mt-3"><?php echo $mensaje; ?></div>
                <?php endif; ?>

                <?php if ($cita) : ?>
                    <?php $detalles = obtenerDetallesVehiculoyCliente($pdo, $cita['vehiculoID']); ?>
                    <form method="post" action="#" class="mt-4">
                        <div class="mb-3">
                            <label for="clienteID" class="form-label">Cliente:</label>
                            <input type="text" class="form-control" id="clienteID" name="clienteID" value="<?php echo $detalles['nombre'] . ' ' . $detalles['apellido_paterno']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="vehiculoID" class="form-label">Vehículo:</label>
                            <input type="text" class="form-control" id="vehiculoID" name="vehiculoID" value="<?php echo $detalles['marca'] . ' ' . $detalles['modelo'] . ' ' . $detalles['anio']; ?>" readonly>
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
                        <input type="hidden" name="citaID" value="<?php echo $cita['citaID']; ?>">
                        <button type="submit" name="actualizar" class="btn btn-dark w-100">Guardar Cambios</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
