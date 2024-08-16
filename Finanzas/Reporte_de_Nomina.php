<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

try {
    // Obtener la semana seleccionada
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime('monday this week', strtotime($semana_seleccionada)));
    $fin_semana = date('Y-m-d', strtotime('sunday this week', strtotime($semana_seleccionada)));

    // Consulta para obtener los detalles de la nómina de la semana seleccionada
    $nomina_query = "
        SELECT N.fecha_de_pago, E.alias, N.faltas, N.rebajas, N.bonos, N.rebajas_adicionales, N.total
        FROM NOMINAS N
        JOIN EMPLEADOS E ON N.empleadoID = E.empleadoID
        WHERE N.fecha_inicio = :inicio_semana AND N.fecha_fin = :fin_semana
    ";
    $stmt = $pdo->prepare($nomina_query);
    $stmt->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
    $nomina = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el total de la nómina de la semana seleccionada
    $total_nomina_query = "
        SELECT SUM(N.total) as total_nomina
        FROM NOMINAS N
        WHERE N.fecha_inicio = :inicio_semana AND N.fecha_fin = :fin_semana
    ";
    $stmt_total = $pdo->prepare($total_nomina_query);
    $stmt_total->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
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
                                    <input type="hidden" id="semana" name="semana" value="<?php echo htmlspecialchars($semana_seleccionada); ?>">
                                    <div id="week-picker" class="input-group">
                                        <div class="form-control"><?php echo date('Y-m-d', strtotime($inicio_semana)) . ' - ' . date('Y-m-d', strtotime($fin_semana)); ?></div>
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
                                    <th>Bonos</th>
                                    <th>Rebajas Adicionales</th>
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
                                        <td>$<?php echo number_format($n['bonos'], 2); ?></td>
                                        <td>$<?php echo number_format($n['rebajas_adicionales'], 2); ?></td>
                                        <td>$<?php echo number_format($n['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right">Total:</td>
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
    </div>
</body>

</html>
