<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

// Inicializa las variables
$datos_reporte = [];
$total_registros = 0;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina = $pagina > 0 ? $pagina : 1;
$limite = 10; // Número de resultados por página
$offset = ($pagina - 1) * $limite;

$orden_columna = isset($_GET['orden_columna']) ? $_GET['orden_columna'] : 'num_ordenes';
$orden_direccion = isset($_GET['orden_direccion']) ? $_GET['orden_direccion'] : 'ASC';
$orden_direccion = ($orden_direccion == 'ASC') ? 'ASC' : 'DESC'; // Validar dirección

try {
    // Obtener la semana seleccionada
    $semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');

    // Calcular las fechas de inicio y fin de la semana seleccionada
    $inicio_semana = date('Y-m-d', strtotime('monday this week', strtotime($semana_seleccionada)));
    $fin_semana = date('Y-m-d', strtotime('sunday this week', strtotime($semana_seleccionada)));

    // Preparar la consulta SQL para obtener los datos del rendimiento de los técnicos con paginación y ordenación
    $sql = "
        SELECT 
            OT.empleadoID, 
            COUNT(*) AS num_ordenes, 
            SUM(C.costo_mano_obra + C.costo_refacciones) AS total_estimado, 
            P.nombre, 
            P.apellido_paterno, 
            P.apellido_materno
        FROM 
            ORDENES_TRABAJO OT
        INNER JOIN 
            EMPLEADOS E ON E.empleadoID = OT.empleadoID
        INNER JOIN 
            PERSONAS P ON P.personaID = E.personaID
        INNER JOIN 
            CITAS C ON C.citaID = OT.citaID
        WHERE 
            OT.fecha_orden BETWEEN :fecha_inicio AND :fecha_fin
        GROUP BY 
            OT.empleadoID, 
            P.nombre, 
            P.apellido_paterno, 
            P.apellido_materno
        ORDER BY $orden_columna $orden_direccion
        LIMIT :limite OFFSET :offset
    ";

    // Preparar la declaración
    $stmt = $pdo->prepare($sql);

    // Enlazar los parámetros
    $stmt->bindParam(':fecha_inicio', $inicio_semana);
    $stmt->bindParam(':fecha_fin', $fin_semana);
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $datos_reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el número total de registros para paginación
    $total_count_query = "
        SELECT COUNT(*) as total_count
        FROM 
            ORDENES_TRABAJO OT
        INNER JOIN 
            EMPLEADOS E ON E.empleadoID = OT.empleadoID
        INNER JOIN 
            PERSONAS P ON P.personaID = E.personaID
        INNER JOIN 
            CITAS C ON C.citaID = OT.citaID
        WHERE 
            OT.fecha_orden BETWEEN :fecha_inicio AND :fecha_fin
    ";
    $stmt_count = $pdo->prepare($total_count_query);
    $stmt_count->bindParam(':fecha_inicio', $inicio_semana);
    $stmt_count->bindParam(':fecha_fin', $fin_semana);
    $stmt_count->execute();
    $total_registros = $stmt_count->fetch(PDO::FETCH_ASSOC)['total_count'];
    $total_paginas = ceil($total_registros / $limite);

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

        th {
            position: relative;
        }

        th a {
            text-decoration: none;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination .active {
            font-weight: bold;
            text-decoration: underline;
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
                                    <div class="form-control"><?php echo date('Y-m-d', strtotime($inicio_semana)) . ' - ' . date('Y-m-d', strtotime($fin_semana)); ?></div>
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
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=nombre&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Nombre del Técnico <?php echo $orden_columna === 'nombre' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=num_ordenes&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Número de Órdenes <?php echo $orden_columna === 'num_ordenes' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=total_estimado&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Total Estimado <?php echo $orden_columna === 'total_estimado' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
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
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#detallesOrdenModal" data-empleado-id="<?php echo $fila['empleadoID']; ?>" onclick="cargarDetallesOrden(<?php echo $fila['empleadoID']; ?>)">
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

                    <!-- Paginación -->
                    <div class="pagination">
                        <?php if ($pagina > 1) : ?>
                            <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina - 1; ?>&orden_columna=<?php echo $orden_columna; ?>&orden_direccion=<?php echo $orden_direccion; ?>">&laquo; Anterior</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_paginas; $i++) : ?>
                            <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $i; ?>&orden_columna=<?php echo $orden_columna; ?>&orden_direccion=<?php echo $orden_direccion; ?>" class="<?php echo $i === $pagina ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($pagina < $total_paginas) : ?>
                            <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina + 1; ?>&orden_columna=<?php echo $orden_columna; ?>&orden_direccion=<?php echo $orden_direccion; ?>">Siguiente &raquo;</a>
                        <?php endif; ?>
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