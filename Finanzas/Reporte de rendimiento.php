<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

try {
    // Obtener la semana seleccionada
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-\WW');

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime($semana_seleccionada . '1 Monday this week'));
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
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Rendimiento</title>
    <style>
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REPORTE DE RENDIMIENTO</h2>
                <div class="form-container">
                    <form method="GET" action="" class="mb-4">
                        <div class="form-row">
                            <div class="form-group col-md-6 offset-md-3">
                                <label for="semana">Selecciona la semana:</label>
                                <input type="week" id="semana" name="semana" class="form-control" value="<?php echo htmlspecialchars($semana_seleccionada); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Ver Reporte</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
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
</body>

</html>