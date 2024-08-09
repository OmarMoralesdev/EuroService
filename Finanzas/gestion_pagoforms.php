<?php
// Datos de conexión a la base de datos
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();
$resultado = []; // Inicializar como array vacío
$mensaje_error = ''; // Variable para almacenar el mensaje de error
$semana_seleccionada = ''; // Inicializar variable de la semana seleccionada


try {
    // Obtener la semana desde el formulario
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime($semana_seleccionada . ' 0 days'));
    $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));

    // Preparar la consulta SQL para llamar al procedimiento almacenado
    $sql = "CALL gestionPagosSemanal(:fecha_inicio, :fecha_fin)";

    // Preparar la sentencia
    $stmt = $pdo->prepare($sql);

    // Bind de los parámetros
    $stmt->bindParam(':fecha_inicio', $inicio_semana);
    $stmt->bindParam(':fecha_fin', $fin_semana);

    // Ejecutar la sentencia
    $stmt->execute();

    // Obtener los resultados
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje_error = "Error: " . $e->getMessage();
} finally {
    // Cerrar la conexión
    $pdo = null;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>Gestión de Pagos Semanal</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REPORTE DE GASTOS SEMANAL</h2>
                <div class="form-container">
                    <form method="GET" action="">
                        <div class="form-group">
                            <div class="form-group col-md-6 offset-md-3">
                                <label for="semana">Selecciona la semana:</label>
                                <div id="week-picker" class="input-group date">
                                    <div class="form-control"><?php echo date('Y-m-d', strtotime($inicio_semana)) . ' - ' . date('Y-m-d', strtotime($fin_semana)); ?></div>
                                    <input type="hidden" id="semana" name="semana" value="<?php echo htmlspecialchars($semana_seleccionada); ?>">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Ver reporte</button>
                    </form>

                    <?php if (!empty($resultado)) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Recibo ID</th>
                                        <th>Fecha Recibo</th>
                                        <th>Cliente</th>
                                        <th>Cantidad Pagada</th>
                                        <th>Estado Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultado as $fila) : ?>
                                        <?php
                                        $estado_pago = $fila['estado_pago'];
                                        $color_celda_estado = "";

                                        if ($estado_pago == 'verde') {
                                            $color_celda_estado = "#d4edda"; // Verde claro
                                        } elseif ($estado_pago == 'naranja') {
                                            $color_celda_estado = "#fff3cd"; // Naranja claro
                                        } elseif ($estado_pago == 'rojo') {
                                            $color_celda_estado = "#f8d7da"; // Rojo claro
                                        }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($fila['reciboID']); ?></td>
                                            <td><?= htmlspecialchars($fila['fecha_recibo']); ?></td>
                                            <td><?= htmlspecialchars($fila['cliente']); ?></td>
                                            <td><?= htmlspecialchars($fila['cantidad_pagada']); ?></td>
                                            <td style="background-color: <?= htmlspecialchars($color_celda_estado); ?>;">
                                                <?= htmlspecialchars($fila['estado_pago']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif ($mensaje_error) : ?>
                        <div class="alert alert-danger mt-4"><?= htmlspecialchars($mensaje_error); ?></div>
                    <?php else : ?>
                        <p class="mt-4">No se encontraron resultados.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="../assets/js/weekpicker.js"></script>
</body>

</html>