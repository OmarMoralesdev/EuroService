<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

function obtenerEmpleadosDisponibles($pdo) {
    $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
            FROM EMPLEADOS 
            JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerUbicacionesActivas($pdo) {
    $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['citaID'])) {
    // Almacenar la citaID en la sesión cuando se recibe por POST
    $_SESSION['citaID'] = $_POST['citaID'];
}

$citaID = isset($_SESSION['citaID']) ? $_SESSION['citaID'] : null;

if (!$citaID) {
    // Si no hay citaID en la sesión, mostrar un mensaje de error o redirigir
    $_SESSION['error'] = 'No se ha seleccionado una cita válida.';
    header('Location: seleccionar_cita.php'); // Cambia esto a la página de selección de cita
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_cita_session'])) {
    // Eliminar la variable de sesión 'citaID'
    unset($_SESSION['citaID']);
    exit(); // Terminar la ejecución para no procesar el resto del script
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
            <h2>Completar Detalles de Orden de Trabajo</h2>
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

                    <label for="costoManoObra">Costo Mano de Obra:</label>
                    <input type="number" step="0.01" id="costoManoObra" name="costoManoObra" min="0" class="form-control" required>

                    <label for="costoRefacciones">Costo de Refacciones:</label>
                    <input type="number" step="0.01" id="costoRefacciones" name="costoRefacciones" min="0" class="form-control" required>

                    <label for="anticipo">Anticipo:</label>
                    <input type="number" step="0.01" min="0" name="anticipo" class="form-control" required>
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
        if (value < 0) {
            event.target.value = '';
            alert('El valor no puede ser negativo.');
        }
    }

    document.getElementById('costoManoObra').addEventListener('input', validateNonNegative);
    document.getElementById('costoRefacciones').addEventListener('input', validateNonNegative);
    document.getElementById('anticipo').addEventListener('input', validateNonNegative);
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ($('#staticBackdrop').length) {
            $('#staticBackdrop').modal('show');
        }
    });
</script>

</body>
</html>
