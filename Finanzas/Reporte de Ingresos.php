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

    // Consulta para obtener los detalles de los ingresos de la semana seleccionada
    $ingresos_query = "
        SELECT P.fecha_pago, P.monto, P.tipo_pago, P.forma_de_pago, OT.ordenID
        FROM PAGOS P
        JOIN ORDENES_TRABAJO OT ON P.ordenID = OT.ordenID
        WHERE P.fecha_pago BETWEEN :week_start AND :week_end
    ";
    $stmt = $pdo->prepare($ingresos_query);
    $stmt->execute(['week_start' => $week_start, 'week_end' => $week_end]);
    $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos Semanal</title>
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
        <h1>Reporte de Ingresos Semanal</h1>
        <form method="GET" action="">
            <label for="week">Selecciona la semana:</label>
            <input type="week" id="week" name="week" value="<?php echo htmlspecialchars($selected_week); ?>">
            <button type="submit">Ver Reporte</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Fecha de Pago</th>
                    <th>Monto</th>
                    <th>Tipo de Pago</th>
                    <th>Forma de Pago</th>
                    <th>Orden ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingresos as $ingreso): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ingreso['fecha_pago']); ?></td>
                        <td>$<?php echo number_format($ingreso['monto'], 2); ?></td>
                        <td><?php echo htmlspecialchars($ingreso['tipo_pago']); ?></td>
                        <td><?php echo htmlspecialchars($ingreso['forma_de_pago']); ?></td>
                        <td><?php echo htmlspecialchars($ingreso['ordenID']); ?></td>
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
