<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

function obtenerCostoMinimoPorCliente($pdo, $clienteID) {
    $sql = "SELECT ROUND(SUM(CITAS.costo_mano_obra + CITAS.costo_refacciones) / 2, 2) as costo_minimo
            FROM CITAS 
            JOIN VEHICULOS ON CITAS.vehiculoID = VEHICULOS.vehiculoID
            WHERE VEHICULOS.clienteID = :clienteID
            AND CITAS.estado = 'pendiente'
            AND (DATE(CITAS.fecha_cita) <= CURDATE())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':clienteID', $clienteID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['costo_minimo'] : 0; // Retorna 0 si no hay resultados
}

function obtenerEmpleadosDisponibles($pdo) {
    $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
            FROM EMPLEADOS 
            JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID
            WHERE EMPLEADOS.tipo != 'administrativo'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerUbicacionesActivas($pdo) {
    $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['citaID'])) {
    $_SESSION['citaID'] = $_POST['citaID'];
}

$citaID = isset($_SESSION['citaID']) ? $_SESSION['citaID'] : null;

if (!$citaID) {
    $_SESSION['error'] = 'No se ha seleccionado una cita válida.';
    header('Location: seleccionar_cita.php');
    exit;
}

// Obtener el clienteID basado en la citaID
$sql = "SELECT VEHICULOS.clienteID
        FROM CITAS
        JOIN VEHICULOS ON CITAS.vehiculoID = VEHICULOS.vehiculoID
        WHERE CITAS.citaID = :citaID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':citaID', $citaID, PDO::PARAM_INT);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);
$clienteID = $cliente ? $cliente['clienteID'] : null;

$costoMinimo = $clienteID ? obtenerCostoMinimoPorCliente($pdo, $clienteID) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_cita_session'])) {
    unset($_SESSION['citaID']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <title>Completar Detalles de Orden de Trabajo</title>
</head>
<body>
<div class="wrapper">
    <?php include '../includes/vabr.php'; ?>
    <div class="main p-3">
        <div class="container">
            <h2>CREAR Orden de Trabajo</h2>
            <div class="form-container">
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['bien'])) {
                    echo "
                    <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h1 class='modal-title fs-5' id='staticBackdropLabel'>Orden registrada!</h1>
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
                <form action="crear_orden_desde_cita_back.php" method="post">
                    <input type="hidden" name="citaID" value="<?php echo htmlspecialchars($citaID, ENT_QUOTES, 'UTF-8'); ?>">

                    <label for="anticipo">Anticipo:</label>
                    <input type="number" step="0.01" min="0" name="anticipo" class="form-control" value="<?php echo htmlspecialchars($costoMinimo, ENT_QUOTES, 'UTF-8'); ?>" required>
                    <small class="form-text text-muted">cantidad minima de anticipo. <strong><?php echo htmlspecialchars($costoMinimo, ENT_QUOTES, 'UTF-8'); ?></strong></small><br>
                    <br>

                    <label for="atencion" class="form-label">Atencion:</label>
                    <select name="atencion" class="form-control" required>
                        <option value="no urgente">No urgente</option>
                        <option value="urgente">Urgente</option>
                        <option value="muy urgente">Muy urgente</option>
                    </select><br>

                    <label for="formadepago" class="form-label">Forma de pago:</label>
                    <select name="formadepago" class="form-control" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select><br>

                    <label for="empleado" class="form-label">Empleado:</label>
                    <select name="empleado" class="form-control" required>
                        <?php
                        $empleados = obtenerEmpleadosDisponibles($pdo);
                        foreach ($empleados as $empleado) {
                            $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                            echo "<option value=\"{$empleado['empleadoID']}\">{$nombreCompleto}</option>";
                        }
                        ?>
                    </select><br>

                    <label for="ubicacionID" class="form-label">Ubicación:</label>
                    <select name="ubicacionID" class="form-control" required>
                        <?php
                        $ubicaciones = obtenerUbicacionesActivas($pdo);
                        foreach ($ubicaciones as $ubicacion) {
                            echo "<option value=\"{$ubicacion['ubicacionID']}\">{$ubicacion['lugar']}</option>";
                        }
                        ?>
                    </select><br>
                    <input type="submit" class="btn btn-dark w-100" value="Crear Orden de Trabajo">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function validateNonNegative(event) {
        var value = parseFloat(event.target.value);
        if (isNaN(value) || value < 0) {
            event.target.value = '';
        }
    }

    document.querySelectorAll('input[type="number"]').forEach(function(input) {
        input.addEventListener('blur', validateNonNegative);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gD9nA2j6keFtn5L1mF7r2F06J5yVmnY7HAs+ptW4FwwkFJEJ9s" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-9gTjAs8a20dmtQ5k0z5b8LE5J4OoWohUE5lO6k29p5dPm5X0P9V19Vsc8SwGm9IFz" crossorigin="anonymous"></script>
</body>
</html>
