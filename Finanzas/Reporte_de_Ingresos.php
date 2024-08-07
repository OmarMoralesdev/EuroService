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

    // Consulta para obtener el total de ingresos de la semana seleccionada
    $total_ingresos_query = "
        SELECT SUM(P.monto) as total_ingresos
        FROM PAGOS P
        WHERE P.fecha_pago BETWEEN :week_start AND :week_end
    ";
    $stmt_total = $pdo->prepare($total_ingresos_query);
    $stmt_total->execute(['week_start' => $week_start, 'week_end' => $week_end]);
    $total_ingresos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_ingresos'];
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ingresos Semanal</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REPORTE DE INGRESOS SEMANAL</h2>
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
                                    <th>Fecha de Pago</th>
                                    <th>Monto</th>
                                    <th>Tipo de Pago</th>
                                    <th>Forma de Pago</th>
                                    <th>Folio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ingresos as $ingreso) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ingreso['fecha_pago']); ?></td>
                                        <td>$<?php echo number_format($ingreso['monto'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($ingreso['tipo_pago']); ?></td>
                                        <td><?php echo htmlspecialchars($ingreso['forma_de_pago']); ?></td>
                                        <td><?php echo htmlspecialchars($ingreso['ordenID']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Total:</td>
                                    <td>$<?php echo number_format($total_ingresos, 2); ?></td>
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