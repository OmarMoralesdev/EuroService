<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

try {

    // Obtener la semana seleccionada
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');
    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime($semana_seleccionada));
    $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));

    // Consulta para obtener los detalles de los ingresos de la semana seleccionada
    $ingresos_query = "
        SELECT P.fecha_pago, P.monto, P.tipo_pago, P.forma_de_pago, OT.ordenID
        FROM PAGOS P
        JOIN ORDENES_TRABAJO OT ON P.ordenID = OT.ordenID
        WHERE P.fecha_pago BETWEEN :inicio_semana AND :fin_semana
    ";
    $stmt = $pdo->prepare($ingresos_query);
    $stmt->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
    $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el total de ingresos de la semana seleccionada
    $total_ingresos_query = "
        SELECT SUM(P.monto) as total_ingresos
        FROM PAGOS P
        WHERE P.fecha_pago BETWEEN :inicio_semana AND :fin_semana
    ";
    $stmt_total = $pdo->prepare($total_ingresos_query);
    $stmt_total->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
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
        <!-- Datepicker CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
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
                            <div id="week-picker" class="input-group date">
                                    <input type="text" class="form-control" readonly value="<?php echo date('Y-m-d', strtotime($inicio_semana)) . ' - ' . date('Y-m-d', strtotime($fin_semana)); ?>">
                                    <input type="hidden" id="semana" name="semana" value="<?php echo htmlspecialchars($semana_seleccionada); ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="glyphicon glyphicon-calendar"><i class="bi bi-calendar"></i></i></span>
                                    </div>
                                </div>
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
          <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="../assets/js/weekpicker.js"></script>  
</body>

</html>

<?php
$pdo = null; // Cerrar la conexión
?>