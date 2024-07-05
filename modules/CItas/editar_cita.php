<?php
require 'database.php';

$con = new Database();
$pdo = $con->conectar();

$cita = null;
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        $sql = "SELECT * FROM CITAS WHERE citaID = ?";
        $query = $pdo->prepare($sql);
        $query->execute([$citaID]);
        $cita = $query->fetch(PDO::FETCH_ASSOC);

        if (!$cita) {
            $mensaje = "Cita no encontrada.";
        }
    } elseif (isset($_POST['actualizar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        $servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
        $fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);
        $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

        $fechaActual = date('Y-m-d H:i:s');
        $fechaCitaTimestamp = strtotime($fechaCita);

         if ($fechaCitaTimestamp >= strtotime($fechaActual)) {
            $mensaje = "Error: La fecha de la cita debe ser posterior a la fecha actual.";
        } else {
            $fechaLimite = date('Y-m-d H:i:s', strtotime('+30 minutes', $fechaCitaTimestamp));
            $sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE fecha_cita BETWEEN ? AND ? AND citaID != ?";
            $query = $pdo->prepare($sql);
            $query->execute([$fechaCita, $fechaLimite, $citaID]);

            $row = $query->fetch(PDO::FETCH_ASSOC);
            $countCitasProximas = $row['countCitas'];

            if ($countCitasProximas > 0) {
                $mensaje = "Error: Hay una cita programada dentro de los próximos 30 minutos. Por favor, selecciona otra fecha.";
            } else {
                $sqlUpdate = "UPDATE CITAS SET servicio_solicitado = ?, fecha_cita = ?, estado = ? WHERE citaID = ?";
                $queryUpdate = $pdo->prepare($sqlUpdate);
                $resultUpdate = $queryUpdate->execute([$servicioSolicitado, $fechaCita, $estado, $citaID]);

                if ($resultUpdate) {
                    $mensaje = "Cita actualizada correctamente.";
                    $sql = "SELECT * FROM CITAS WHERE citaID = ?";
                    $query = $pdo->prepare($sql);
                    $query->execute([$citaID]);
                    $cita = $query->fetch(PDO::FETCH_ASSOC);
                } else {
                    $mensaje = "Error al actualizar la cita.";
                }
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 50px auto;
        }
        h2 {
            text-align: center;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Cita</h2>
        <form method="post" action="editar_cita.php">
            <div class="mb-3">
                <label for="citaID" class="form-label">Buscar Cita por ID:</label>
                <input type="text" class="form-control" id="citaID" name="citaID" value="<?php echo isset($cita['citaID']) ? $cita['citaID'] : ''; ?>" required>
                <div class="invalid-feedback">Por favor, ingresa un ID de cita válido.</div>
            </div>
            <button type="submit" name="buscar" class="btn btn-dark w-100">Buscar Cita</button>
        </form>

        <?php if ($mensaje): ?>
            <div class="alert alert-info mt-3"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($cita): ?>
            <form method="post" action="editar_cita.php" class="mt-4">
                <div class="mb-3">
                    <label for="clienteID" class="form-label">Cliente ID:</label>
                    <input type="text" class="form-control" id="clienteID" name="clienteID" value="<?php echo $cita['clienteid']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="vehiculoID" class="form-label">Vehículo ID:</label>
                    <input type="text" class="form-control" id="vehiculoID" name="vehiculoID" value="<?php echo $cita['vehiculoID']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="servicioSolicitado" class="form-label">Servicio Solicitado:</label>
                    <input type="text" class="form-control" id="servicioSolicitado" name="servicioSolicitado" value="<?php echo $cita['servicio_solicitado']; ?>" required>
                    <div class="invalid-feedback">Debes ingresar el servicio solicitado.</div>
                </div>
                <div class="mb-3">
                    <label for="fecha_cita" class="form-label">Fecha de la Cita:</label>
                    <input type="datetime-local" class="form-control" id="fecha_cita" name="fecha_cita" value="<?php echo date('Y-m-d\TH:i', strtotime($cita['fecha_cita'])); ?>" required>
                    <div class="invalid-feedback">Debes seleccionar la fecha y hora de la cita.</div>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <select class="form-control" id="estado" name="estado" required>
                        <option value="pendiente" <?php echo $cita['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="en proceso" <?php echo $cita['estado'] == 'en proceso' ? 'selected' : ''; ?>>En Proceso</option>
                        <option value="completado" <?php echo $cita['estado'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                    </select>
                    <div class="invalid-feedback">Debes seleccionar el estado de la cita.</div>
                </div>
                <input type="hidden" name="citaID" value="<?php echo $cita['citaID']; ?>">
                <button type="submit" name="actualizar" class="btn btn-dark w-100">Guardar Cambios</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
