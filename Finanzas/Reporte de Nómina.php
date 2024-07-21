<?php

try {
    require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

    $con = new Database();
    $pdo = $con->conectar();
    // Obtener la semana seleccionada
    $selected_week = isset($_GET['week']) ? $_GET['week'] : date('Y-\WW');

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $week_start = date('Y-m-d', strtotime($selected_week));
    $week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));

    // Consulta para obtener los detalles de la nómina de la semana seleccionada
    $nomina_query = "
        SELECT N.fecha_de_pago, E.alias, N.faltas, N.rebajas, N.total
        FROM NOMINAS N
        JOIN EMPLEADOS E ON N.empleadoID = E.empleadoID
        WHERE N.fecha_de_pago BETWEEN :week_start AND :week_end
    ";
    $stmt = $pdo->prepare($nomina_query);
    $stmt->execute(['week_start' => $week_start, 'week_end' => $week_end]);
    $nomina = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Nómina Semanal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reporte de Nómina Semanal</h1>
        <form method="GET" action="">
            <label for="week">Selecciona la semana:</label>
            <input type="week" id="week" name="week" value="<?php echo htmlspecialchars($selected_week); ?>">
            <button type="submit">Ver Reporte</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Fecha de Pago</th>
                    <th>Empleado</th>
                    <th>Faltas</th>
                    <th>Rebajas</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nomina as $n): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($n['fecha_de_pago']); ?></td>
                        <td><?php echo htmlspecialchars($n['alias']); ?></td>
                        <td><?php echo htmlspecialchars($n['faltas']); ?></td>
                        <td>$<?php echo number_format($n['rebajas'], 2); ?></td>
                        <td>$<?php echo number_format($n['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$pdo = null; // Cerrar la conexión
?>
