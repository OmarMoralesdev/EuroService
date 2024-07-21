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

    // Consulta para obtener los detalles de las compras de la semana seleccionada
    $gastos_query = "
        SELECT C.fecha_compra, DC.cantidad, DC.precio_unitario, DC.subtotal, I.nombre AS insumo, P.nombre AS proveedor
        FROM COMPRAS C
        JOIN DETALLE_COMPRA DC ON C.compraID = DC.compraID
        JOIN INSUMO_PROVEEDOR IP ON DC.insumo_proveedorID = IP.insumo_proveedorID
        JOIN INSUMOS I ON IP.insumoID = I.insumoID
        JOIN PROVEEDORES P ON IP.proveedorID = P.proveedorID
        WHERE C.fecha_compra BETWEEN :week_start AND :week_end
    ";
    $stmt = $pdo->prepare($gastos_query);
    $stmt->execute(['week_start' => $week_start, 'week_end' => $week_end]);
    $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Gastos Semanal</title>
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
        <h1>Reporte de Gastos Semanal</h1>
        <form method="GET" action="">
            <label for="week">Selecciona la semana:</label>
            <input type="week" id="week" name="week" value="<?php echo htmlspecialchars($selected_week); ?>">
            <button type="submit">Ver Reporte</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Fecha de Compra</th>
                    <th>Insumo</th>
                    <th>Proveedor</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $gasto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($gasto['fecha_compra']); ?></td>
                        <td><?php echo htmlspecialchars($gasto['insumo']); ?></td>
                        <td><?php echo htmlspecialchars($gasto['proveedor']); ?></td>
                        <td><?php echo htmlspecialchars($gasto['cantidad']); ?></td>
                        <td>$<?php echo number_format($gasto['precio_unitario'], 2); ?></td>
                        <td>$<?php echo number_format($gasto['subtotal'], 2); ?></td>
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
