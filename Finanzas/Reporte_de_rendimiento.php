<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

try {
    // Obtener la semana seleccionada
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime($semana_seleccionada . ' 0 days')); 
    $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));

    // Preparar la consulta SQL para obtener los datos del rendimiento de los técnicos
    $sql = "SELECT ORDENES_TRABAJO.empleadoID, COUNT(*) AS num_ordenes, SUM(total_estimado) AS total_estimado, 
                   PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
            FROM ORDENES_TRABAJO
            INNER JOIN EMPLEADOS ON EMPLEADOS.empleadoID = ORDENES_TRABAJO.empleadoID 
            INNER JOIN PERSONAS ON PERSONAS.personaID = EMPLEADOS.personaID
            WHERE fecha_orden BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY ORDENES_TRABAJO.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno";

    // Preparar la declaración
    $stmt = $pdo->prepare($sql);

    // Enlazar los parámetros
    $stmt->bindParam(':fecha_inicio', $inicio_semana);
    $stmt->bindParam(':fecha_fin', $fin_semana);

    $stmt->execute();

    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $datos_reporte = $resultado ?: [];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Rendimiento</title>

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
    .datepicker {
        background-color: #f7f7f7;
        border-radius: 5px;
        padding: 15px;
    }

</style>

</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REPORTE DE RENDIMIENTO</h2>
                <div class="form-container">
                    <form method="GET" action="" class="mb-4">
                        <div class="form-row">
                            <div class="form-group col-md-6 offset-md-3">
                                <label for="semana">Selecciona la semana:</label>
                                <div id="week-picker" class="input-group date">
                                    <div class="form-control"><?php echo date('Y-m-d', strtotime($inicio_semana)) . ' - ' . date('Y-m-d', strtotime($fin_semana)); ?></div>
                                    <input type="hidden" id="semana" name="semana" value="<?php echo htmlspecialchars($semana_seleccionada); ?>">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Ver Reporte</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                        <thead>
                                <tr>
                                    <th>Nombre del Técnico</th>
                                    <th>Número de Órdenes</th>
                                    <th>Total Estimado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($datos_reporte)) : ?>
                                    <?php foreach ($datos_reporte as $fila) : ?>
                                        <?php
                                        $nombreCompleto = "{$fila['nombre']} {$fila['apellido_paterno']} {$fila['apellido_materno']}";
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($nombreCompleto); ?></td>
                                            <td><?php echo htmlspecialchars($fila['num_ordenes']); ?></td>
                                            <td>$<?php echo number_format($fila['total_estimado'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="3">No se encontraron órdenes para el periodo especificado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="../assets/js/weekpicker.js"></script>
</body>
</html>
