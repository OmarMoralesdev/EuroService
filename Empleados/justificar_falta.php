<?php
session_start();
require '../includes/db.php';

// Inicializar variables
$empleadoID = isset($_POST['empleadox']) ? (int)$_POST['empleadox'] : null;
$empleado = null;
$faltas = [];

// Conectar a la base de datos
$con = new Database();
$pdo = $con->conectar();

if ($empleadoID) {
    // Obtener la información del empleado
    $sqlEmpleado = "SELECT PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
                     FROM EMPLEADOS 
                     JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID 
                     WHERE EMPLEADOS.empleadoID = :empleadoID";
    $stmtEmpleado = $pdo->prepare($sqlEmpleado);
    $stmtEmpleado->bindParam(':empleadoID', $empleadoID, PDO::PARAM_INT);
    $stmtEmpleado->execute();
    $empleado = $stmtEmpleado->fetch(PDO::FETCH_ASSOC);

    if ($empleado) {
        // Obtener las faltas del empleado
        $sqlFaltas = "SELECT fecha, asistenciaID FROM ASISTENCIA
                      WHERE empleadoID = :empleadoID AND ASISTENCIA = 'falta'";
        $stmtFaltas = $pdo->prepare($sqlFaltas);
        $stmtFaltas->bindParam(':empleadoID', $empleadoID, PDO::PARAM_INT);
        $stmtFaltas->execute();
        $faltas = $stmtFaltas->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // No se encontró el empleado
        $empleadoID = null;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Justificar falta</title>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>JUSTIFICAR FALTA</h2>
                <?php
// Mostrar mensajes de éxito si están presentes
if (isset($_SESSION['bien'])) {
    echo "<div class='alert alert-success' role='alert'>{$_SESSION['bien']}</div>";
    unset($_SESSION['bien']);
}

// Mostrar mensajes de error si están presentes
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-success' role='alert'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}
?>

                <!-- Formulario para seleccionar al empleado -->
                <div class="mb-4">
                    <form action="" method="post">
                        <select name="empleadox" class="form-control" required onchange="this.form.submit()">
                            <option value="">Seleccione un empleado</option>
                            <?php
                            // Obtener empleados disponibles
                            function obtenerEmpleadosDisponibles($pdo)
                            {
                                $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
                                        FROM EMPLEADOS 
                                        JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID";
                                $stmt = $pdo->query($sql);
                                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }
                            
                            $empleados = obtenerEmpleadosDisponibles($pdo);
                            foreach ($empleados as $empleadoOption) {
                                $nombreCompleto = htmlspecialchars("{$empleadoOption['nombre']} {$empleadoOption['apellido_paterno']} {$empleadoOption['apellido_materno']}");
                                $selected = $empleadoID == $empleadoOption['empleadoID'] ? 'selected' : '';
                                echo "<option value=\"{$empleadoOption['empleadoID']}\" $selected>$nombreCompleto</option>";
                            }
                            ?>
                        </select>
                    </form>
                </div>

                <!-- Mostrar la información del empleado y sus faltas -->
                <?php if ($empleadoID && $empleado): ?>
                    <div>
                        <?php if (empty($faltas)): ?>
                            <!-- Modal para mostrar mensaje si no hay faltas -->
                            <div class="modal fade" id="nofaltas" tabindex="-1" aria-labelledby="nofaltasLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="nofaltasLabel">Empleado sin faltas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>El empleado <?php echo htmlspecialchars("{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}"); ?> no tiene faltas.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var noFaltasModal = new bootstrap.Modal(document.getElementById('nofaltas'));
                                    noFaltasModal.show();
                                });
                            </script>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($faltas as $falta): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card" style="width: 100%;">
                                            <div class="card-body">
                                                <h5 class="card-title">DÍA: <?php echo htmlspecialchars($falta['fecha']); ?></h5>
                                            </div>
                                            <button type="button" class="btn btn-dark btn-md" data-bs-toggle="modal" data-bs-target="#justificarModal<?php echo $falta['asistenciaID']; ?>">Justificar</button>
                                        </div>
                                    </div>

                                    <!-- Modal para justificar -->
                                    <div class="modal fade" id="justificarModal<?php echo $falta['asistenciaID']; ?>" tabindex="-1" aria-labelledby="justificarModalLabel<?php echo $falta['asistenciaID']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="justificarModalLabel<?php echo $falta['asistenciaID']; ?>">Confirmar Justificación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de que deseas justificar la falta del día <strong><?php echo htmlspecialchars($falta['fecha']); ?></strong>?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="registrar_justificacion.php" method="post">
                                                        <input type="hidden" name="empleadox" value="<?php echo htmlspecialchars($empleadoID); ?>">
                                                        <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($falta['fecha']); ?>">
                                                        <button type="submit" class="btn btn-dark">Justificar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
