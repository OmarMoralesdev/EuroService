<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar(); // Conexión utilizando PDO

// Obtener el año y la semana del formulario o usar la semana y año actuales
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$week = isset($_GET['week']) ? $_GET['week'] : date('W');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Asistencia Semanal</title>
</head>
<body>
    <h1>Reporte de Asistencia Semanal</h1>

    <!-- Formulario para seleccionar año y semana -->
    <form method="GET" action="">
        Año: <input type="number" name="year" value="<?php echo htmlspecialchars($year); ?>">
        Semana: <input type="number" name="week" value="<?php echo htmlspecialchars($week); ?>">
        <input type="submit" value="Generar Reporte">
    </form>

    <?php
    // Consulta SQL para obtener el reporte de asistencia por semana
    $sql = "
        SELECT 
            e.nombre AS empleado,
            YEAR(a.fecha) AS year,
            WEEK(a.fecha, 1) AS week,
            SUM(a.asistencia = 'asistencia') AS total_asistencias,
            SUM(a.asistencia = 'falta') AS total_faltas,
            SUM(a.asistencia = 'justificado') AS total_justificados
        FROM 
            ASISTENCIA a
        JOIN 
            EMPLEADOS e ON a.empleadoID = e.empleadoID
        WHERE 
            YEAR(a.fecha) = :year AND WEEK(a.fecha, 1) = :week
        GROUP BY 
            e.empleadoID, YEAR(a.fecha), WEEK(a.fecha, 1)
        ORDER BY 
            e.nombre ASC, week ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->bindParam(':week', $week, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay resultados
    if ($stmt->rowCount() > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Empleado</th><th>Año</th><th>Semana</th><th>Asistencias</th><th>Faltas</th><th>Justificados</th></tr>";
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
        echo "</table>";
    } else {
        echo "No se encontraron registros de asistencia para la semana $week del año $year.";
    }
    ?>
</body>
</html>
