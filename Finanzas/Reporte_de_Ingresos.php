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

    // Obtener parámetros de paginación y ordenación
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $pagina = $pagina > 0 ? $pagina : 1;
    $limite = 10; // Número de resultados por página
    $offset = ($pagina - 1) * $limite;

    $orden_columna = isset($_GET['orden_columna']) ? $_GET['orden_columna'] : 'fecha_pago';
    $orden_direccion = isset($_GET['orden_direccion']) ? $_GET['orden_direccion'] : 'ASC';
    $orden_direccion = ($orden_direccion == 'ASC') ? 'ASC' : 'DESC'; // Validar dirección

    // Consulta para obtener los detalles de los ingresos de la semana seleccionada con paginación y ordenación
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
    ORDER BY $orden_columna $orden_direccion
    LIMIT :limite OFFSET :offset
    ";

    $stmt = $pdo->prepare($ingresos_query);
    $stmt->bindParam(':inicio_semana', $inicio_semana);
    $stmt->bindParam(':fin_semana', $fin_semana);
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
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

    // Consulta para obtener el número total de registros para paginación
    $total_count_query = "
        SELECT COUNT(*) as total_count
        FROM PAGOS P
        WHERE P.fecha_pago BETWEEN :inicio_semana AND :fin_semana
    ";
    $stmt_count = $pdo->prepare($total_count_query);
    $stmt_count->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
    $total_count = $stmt_count->fetch(PDO::FETCH_ASSOC)['total_count'];
    $total_paginas = ceil($total_count / $limite);

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
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=fecha_pago&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Fecha de Pago <?php echo $orden_columna === 'fecha_pago' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=monto&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Monto <?php echo $orden_columna === 'monto' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=tipo_pago&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Tipo de Pago <?php echo $orden_columna === 'tipo_pago' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=forma_de_pago&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Forma de Pago <?php echo $orden_columna === 'forma_de_pago' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=ordenID&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Folio de Orden <?php echo $orden_columna === 'ordenID' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=nombre_completo_cliente&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Nombre del Cliente <?php echo $orden_columna === 'nombre_completo_cliente' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=modelos&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Vehículo <?php echo $orden_columna === 'modelos' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=servicio_solicitado&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Servicio Solicitado <?php echo $orden_columna === 'servicio_solicitado' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=tipo_servicio&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Tipo de Servicio <?php echo $orden_columna === 'tipo_servicio' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
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
    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="../assets/js/weekpicker.js"></script>
</body>

</html>
<?php
$pdo = null;
?>
