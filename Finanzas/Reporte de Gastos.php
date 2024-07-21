<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

try {
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

    // Consulta para obtener el total de gastos de la semana seleccionada
    $total_gastos_query = "
        SELECT SUM(DC.subtotal) as total_gastos
        FROM DETALLE_COMPRA DC
        JOIN COMPRAS C ON DC.compraID = C.compraID
        WHERE C.fecha_compra BETWEEN :week_start AND :week_end
    ";
    $stmt_total = $pdo->prepare($total_gastos_query);
    $stmt_total->execute(['week_start' => $week_start, 'week_end' => $week_end]);
    $total_gastos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_gastos'];
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Gastos Semanal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 20px;
        }
        .container {
            max-width: 900px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Reporte de Gastos Semanal</h1>
        <form method="GET" action="" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-6 offset-md-3">
                    <label for="week">Selecciona la semana:</label>
                    <input type="week" id="week" name="week" class="form-control" value="<?php echo htmlspecialchars($selected_week); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Ver Reporte</button>
        </form>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
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
                <tr class="font-weight-bold">
                    <td colspan="5" class="text-right">Total:</td>
                    <td>$<?php echo number_format($total_gastos, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$pdo = null; // Cerrar la conexión
?>
