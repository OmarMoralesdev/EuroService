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

    // Preparar la consulta SQL para obtener los datos del rendimiento de los técnicos
    $sql = "SELECT ORDENES_TRABAJO.empleadoID, COUNT(*) AS num_ordenes, SUM(total_estimado) AS total_estimado, 
                   PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
            FROM ORDENES_TRABAJO
            INNER JOIN EMPLEADOS ON EMPLEADOS.empleadoID = ORDENES_TRABAJO.empleadoID 
            INNER JOIN PERSONAS ON PERSONAS.personaID = EMPLEADOS.personaID
            WHERE fecha_orden BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY ORDENES_TRABAJO.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno";

    // Preparar la declaración
    $stmt = $pdo->prepare($sql);

    // Enlazar los parámetros
    $stmt->bindParam(':fecha_inicio', $inicio_semana);
    $stmt->bindParam(':fecha_fin', $fin_semana);

    $stmt->execute();

    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $datos_reporte = $resultado ?: [];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$pdo = null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Rendimiento</title>
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

        .modal-dialog {
            max-width: 90%;
        }

        .modal-body {
            overflow-y: auto;
            height: auto;
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
                <h2>REPORTE DE RENDIMIENTO</h2>
                <div class="form-container">
                    <form method="GET" action="" class="mb-4">
                        <div class="row">
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
                        <hr>
                        <button type="submit" class="btn btn-dark d-grid gap-2 col-6 mx-auto">Ver Reporte</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre del Técnico</th>
                                    <th>Número de Órdenes</th>
                                    <th>Total Estimado</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($datos_reporte)) : ?>
                                    <?php foreach ($datos_reporte as $fila) : ?>
                                        <?php
                                        $nombreCompleto = "{$fila['nombre']} {$fila['apellido_paterno']} {$fila['apellido_materno']}";
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($nombreCompleto); ?></td>
                                            <td><?php echo htmlspecialchars($fila['num_ordenes']); ?></td>
                                            <td>$<?php echo number_format($fila['total_estimado'], 2); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detallesOrdenModal" data-empleado-id="<?php echo $fila['empleadoID']; ?>" onclick="cargarDetallesOrden(<?php echo $fila['empleadoID']; ?>)">
                                                    Ver Detalles
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4">No se encontraron órdenes para el periodo especificado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar los detalles de las órdenes -->
    <div class="modal fade" id="detallesOrdenModal" tabindex="-1" aria-labelledby="detallesOrdenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detallesOrdenModalLabel">Detalles de las Órdenes de Trabajo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detallesOrdenContent">
                        <!-- Los detalles de las órdenes se cargarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

    <!-- Script para cargar detalles de la orden -->
    <script>
        function cargarDetallesOrden(empleadoID) {
            // Realiza una petición AJAX para obtener los detalles de las órdenes del técnico seleccionado
            $.ajax({
                url: 'obtener_detalles_orden.php', // Este archivo PHP debe devolver los detalles de las órdenes en formato HTML
                method: 'GET',
                data: {
                    empleadoID: empleadoID
                },
                success: function(response) {
                    // Carga los detalles en el modal
                    $('#detallesOrdenContent').html(response);
                },
                error: function() {
                    $('#detallesOrdenContent').html('<p>Error al cargar los detalles de las órdenes.</p>');
                }
            });
        }
    </script>
</body>

</html>