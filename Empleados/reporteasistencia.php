<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar(); // Conexión utilizando PDO

// Obtener el año y la semana del formulario o usar la semana y año actuales
if (isset($_GET['week'])) {
    $year = substr($_GET['week'], 0, 4);
    $week = substr($_GET['week'], 6, 2);
} else {
    $year = date('Y');
    $week = date('W');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asistencia Semanal</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2 class="my-4">Reporte de Asistencia Semanal</h2>
                <div class="form-container mb-4">
                    <!-- Formulario para seleccionar año y semana -->
                    <form method="GET" action="" class="form-inline">
                        <label for="week" class="mr-2">Semana:</label>
                        <input type="week" id="week" name="week" class="form-control mr-2" value="<?php echo htmlspecialchars($year . '-W' . $week); ?>">
                        <input type="submit" class="btn btn-primary" value="Generar Reporte">
                    </form>
                </div>

                <?php
                // Consulta SQL para obtener el reporte de asistencia por semana
                $sql = "
                SELECT 
                    CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS empleado,
                    YEAR(a.fecha) AS year,
                    WEEK(a.fecha, 1) AS week,
                    SUM(a.asistencia = 'asistencia') AS total_asistencias,
                    SUM(a.asistencia = 'falta') AS total_faltas,
                    SUM(a.asistencia = 'justificado') AS total_justificados
                FROM 
                    ASISTENCIA a
                JOIN 
                    EMPLEADOS e ON a.empleadoID = e.empleadoID
                JOIN 
                    PERSONAS p ON e.personaID = p.personaID
                WHERE 
                    YEAR(a.fecha) = :year AND WEEK(a.fecha, 1) = :week
                GROUP BY 
                    e.empleadoID, YEAR(a.fecha), WEEK(a.fecha, 1)
                ORDER BY 
                    p.nombre ASC, week ASC";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                $stmt->bindParam(':week', $week, PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Verificar si hay resultados
                if ($stmt->rowCount() > 0) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead class='thead-dark'>";
                    echo "<tr><th>Empleado</th><th>Año</th><th>Semana</th><th>Asistencias</th><th>Faltas</th><th>Justificados</th></tr>";
                    echo "</thead><tbody>";
                    // Mostrar resultados en la tabla
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['empleado']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['year']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['week']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['total_asistencias']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['total_faltas']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['total_justificados']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-warning'>No se encontraron registros de asistencia para la semana " . htmlspecialchars($week) . " del año " . htmlspecialchars($year) . ".</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
