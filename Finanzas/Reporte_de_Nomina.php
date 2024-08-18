<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

try {
    // Obtener la semana seleccionada
    $selected_week = isset($_GET['week']) ? $_GET['week'] : date('Y-\WW');
    $date = new DateTime();
    $date->setISODate((int)substr($selected_week, 0, 4), (int)substr($selected_week, 6, 2));

    // Calcular la fecha del primer y último día de la semana seleccionada
    $week_start = $date->format('Y-m-d');
    $date->modify('+6 days');
    $week_end = $date->format('Y-m-d');

    // Consulta para obtener los detalles de la nómina de la semana seleccionada
    $nomina_query = "
        SELECT N.fecha_de_pago, E.alias, N.faltas, N.rebajas, N.total
        FROM NOMINAS N
        JOIN EMPLEADOS E ON N.empleadoID = E.empleadoID
        WHERE N.fecha_inicio = :week_start AND N.fecha_fin = :week_end
    ";
    $stmt = $pdo->prepare($nomina_query);
    $stmt->execute(['week_start' => $week_start, 'week_end' => $week_end]);
    $nomina = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el total de la nómina de la semana seleccionada
    $total_nomina_query = "
        SELECT SUM(N.total) as total_nomina
        FROM NOMINAS N
        WHERE N.fecha_inicio = :week_start AND N.fecha_fin = :week_end
    ";
    $stmt_total = $pdo->prepare($total_nomina_query);
    $stmt_total->execute(['week_start' => $week_start, 'week_end' => $week_end]);
    $total_nomina = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_nomina'];
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>Reporte de Nómina Semanal</title>
    <style>
        .main {
            align-items: center;
        }

        .datepicker {
            background-color: #f7f7f7;
            border-radius: 5px;
            padding: 15px;
        }

        .input-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REPORTE DE NÓMINA SEMANAL</h2>
                <div class="form-container">
                    <form method="GET" action="" class="mb-4">
                        <div class="col-md-6 offset-md-3">
                            <label for="semana">Selecciona la semana:</label>
                            <div id="week-picker" class="input-group">
                                <input type="hidden" id="semana" name="week" value="<?php echo htmlspecialchars($selected_week); ?>">
                                <div id="week-picker" class="input-group">
                                    <div class="form-control"><?php echo date('Y-m-d', strtotime($week_start)) . ' - ' . date('Y-m-d', strtotime($week_end)); ?></div>
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
                                    <th>Empleado</th>
                                    <th>Faltas</th>
                                    <th>Rebajas</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($nomina as $n) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($n['fecha_de_pago']); ?></td>
                                        <td><?php echo htmlspecialchars($n['alias']); ?></td>
                                        <td><?php echo htmlspecialchars($n['faltas']); ?></td>
                                        <td>$<?php echo number_format($n['rebajas'], 2); ?></td>
                                        <td>$<?php echo number_format($n['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">Total:</td>
                                    <td>$<?php echo number_format($total_nomina, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Datepicker JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <!-- Tu script personalizado -->
        <script src="../assets/js/weekpicker.js"></script>
</body>

</html>