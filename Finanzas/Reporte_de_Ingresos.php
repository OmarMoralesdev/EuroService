<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

// Inicializa las variables
$ingresos = [];
$total_ingresos = 0;

try {
    // Obtener la semana seleccionada
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime($semana_seleccionada));
    $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));

    // Consulta mejorada para obtener los detalles de los ingresos de la semana seleccionada
    $ingresos_query = "
    SELECT 
        P.fecha_pago, 
        P.monto, 
        P.tipo_pago, 
        P.forma_de_pago, 
        OT.ordenID,
        CONCAT(PER.nombre, ' ', PER.apellido_paterno, ' ', PER.apellido_materno) AS nombre_completo_cliente,
        CONCAT(V.marca, ' ', V.modelo, ' ', V.anio) AS modelos,
        S.servicio_solicitado,
        S.tipo_servicio
    FROM 
        PAGOS P
    JOIN 
        ORDENES_TRABAJO OT ON P.ordenID = OT.ordenID
    JOIN 
        CITAS S ON OT.citaID = S.citaID
    JOIN 
        VEHICULOS V ON S.vehiculoID = V.vehiculoID
    JOIN 
        CLIENTES C ON V.clienteID = C.clienteID
    JOIN 
        PERSONAS PER ON C.personaID = PER.personaID
    WHERE 
        P.fecha_pago BETWEEN :inicio_semana AND :fin_semana
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
    $total_ingresos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_ingresos'] ?? 0;
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ingresos Semanal</title>
    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
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
                <h2>REPORTE DE INGRESOS SEMANAL</h2>
                <div class="form-container">
                    <form method="GET" action="" class="mb-4">
                        <div class="form-row">
                            <div class="col-md-6 offset-md-3">
                                <label for="semana">Selecciona la semana:</label>
                                <div id="week-picker" class="input-group">
                                    <input type="hidden" id="semana" name="semana" value="<?php echo htmlspecialchars($semana_seleccionada); ?>">
                                    <div id="week-picker" class="input-group">
                                        <div class="form-control"><?php echo date('Y-m-d', strtotime($inicio_semana)) . ' - ' . date('Y-m-d', strtotime($fin_semana)); ?></div>
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
                                    <th>Nombre del Cliente</th>
                                    <th>Vehículo</th>
                                    <th>Servicio Solicitado</th>
                                    <th>Tipo de Servicio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ingresos)) : ?>
                                    <?php foreach ($ingresos as $ingreso) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ingreso['fecha_pago']); ?></td>
                                            <td>$<?php echo number_format($ingreso['monto'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($ingreso['tipo_pago']); ?></td>
                                            <td><?php echo htmlspecialchars($ingreso['forma_de_pago']); ?></td>
                                            <td><?php echo htmlspecialchars($ingreso['ordenID']); ?></td>
                                            <td><?php echo htmlspecialchars($ingreso['nombre_completo_cliente']); ?></td>
                                            <td><?php echo htmlspecialchars($ingreso['modelos']); ?></td>
                                            <td><?php echo htmlspecialchars($ingreso['servicio_solicitado']); ?></td>
                                            <td><?php echo htmlspecialchars($ingreso['tipo_servicio']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="font-weight-bold">
                                        <td colspan="8" class="text-right">Total:</td>
                                        <td>$<?php echo number_format($total_ingresos, 2); ?></td>
                                    </tr>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No se encontraron ingresos para la semana seleccionada.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="../assets/js/weekpicker.js"></script>
</body>

</html>