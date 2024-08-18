<?php
// Datos de conexión a la base de datos
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

// Inicializar variables
$resultado = []; // Inicializar como array vacío
$mensaje_error = ''; // Variable para almacenar el mensaje de error
$semana_seleccionada = ''; // Inicializar variable de la semana seleccionada

try {
    // Crear una nueva instancia de la clase Database
    $con = new Database();
    $pdo = $con->conectar();

    // Obtener la semana desde el formulario
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');

    // Validar que la fecha sea válida
    $fecha_valida = DateTime::createFromFormat('Y-m-d', $semana_seleccionada);
    if (!$fecha_valida) {
        throw new Exception('Fecha inválida.');
    }

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime($semana_seleccionada . ' -' . (date('N', strtotime($semana_seleccionada)) - 1) . ' days'));
    $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));

    // Preparar la consulta SQL para llamar al procedimiento almacenado
    $sql = "CALL ObtenerPagosSemanal(:fecha_inicio, :fecha_fin)";

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
    $mensaje_error = "Error en la base de datos: " . $e->getMessage();
} catch (Exception $e) {
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
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>Gestión de Pagos Semanal</title>
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

        .table td, .table th {
            text-align: center;
        }
    </style>
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
                            <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Ver reporte</button>
                        </div>
                    </form>

                    <?php if (!empty($resultado)) : ?>
                        <div class="table-responsive mt-4">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Folio Pago</th>
                                        <th>Folio Orden</th>
                                        <th>Fecha Orden</th>
                                        <th>Fecha Pago</th>
                                        <th>Monto</th>
                                        <th>Tipo de Pago</th>
                                        <th>Forma de Pago</th>
                                        <th>Vehículo</th>                                  
                                        <th>Persona Nombre</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultado as $fila) : ?>
                                        <?php
                                        $estado_pago = $fila['estado'];
                                        $color_celda_estado = "";

                                        if ($estado_pago == 'verde') {
                                            $color_celda_estado = "#28a745"; // Verde intenso
                                        } elseif ($estado_pago == 'naranja') {
                                            $color_celda_estado = "#ffc107"; // Naranja intenso
                                        } elseif ($estado_pago == 'rojo') {
                                            $color_celda_estado = "#dc3545"; // Rojo intenso
                                        }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($fila['pagoID']); ?></td>
                                            <td><?= htmlspecialchars($fila['ordenID']); ?></td>
                                            <td><?= htmlspecialchars($fila['fecha_orden']); ?></td>
                                            <td><?= htmlspecialchars($fila['fecha_pago']); ?></td>
                                            <td><?= htmlspecialchars($fila['monto']); ?></td>
                                            <td><?= htmlspecialchars($fila['tipo_pago']); ?></td>
                                            <td><?= htmlspecialchars($fila['forma_de_pago']); ?></td>
                                            <td><?= htmlspecialchars($fila['modelos']); ?></td>                                   
                                            <td><?= htmlspecialchars($fila['nombre_completo_cliente']); ?></td>
                                   
                                            <td style="background-color: <?= htmlspecialchars($color_celda_estado); ?>;">
                                                <?= htmlspecialchars($fila['estado']); ?>
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
