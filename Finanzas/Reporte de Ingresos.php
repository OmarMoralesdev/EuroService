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
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos Semanal</title>
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
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h1 class="text-center mb-4">Reporte de Ingresos Semanal</h1>
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
                            <th>Fecha de Pago</th>
                            <th>Monto</th>
                            <th>Tipo de Pago</th>
                            <th>Forma de Pago</th>
                            <th>ID de Orden</th>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$pdo = null; // Cerrar la conexión
?>