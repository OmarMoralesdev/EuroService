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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Gastos Semanal</title>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REPORTE DE GASTOS SEMANAL</h2>
                <div class="form-container">
                    <form method="GET" action="" class="mb-4">
                        <div class="form-row">
                            <div class="form-group col-md-6 offset-md-3">
                                <label for="week">Selecciona la semana:</label>
                                <input type="week" id="week" name="week" class="form-control" value="<?php echo htmlspecialchars($selected_week); ?>">
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Ver Reporte</button>
                        <br>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
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
                                <?php foreach ($gastos as $gasto) : ?>
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
                </div>
            </div>
        </div>
</body>

</html>

<?php
$pdo = null; // Cerrar la conexión
?>