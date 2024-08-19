<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

// Inicializa las variables
$nomina = [];
$total_nomina = 0;

// Obtener la semana seleccionada
$semana_seleccionada = isset($_GET['semana']) ? $_GET['semana'] : date('Y-m-d');

// Calcular las fechas de inicio y fin de la semana seleccionada
$inicio_semana = date('Y-m-d', strtotime('monday this week', strtotime($semana_seleccionada)));
$fin_semana = date('Y-m-d', strtotime('sunday this week', strtotime($semana_seleccionada)));

// Obtener parámetros de paginación y ordenación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$pagina = $pagina > 0 ? $pagina : 1;
$limite = 10; // Número de resultados por página
$offset = ($pagina - 1) * $limite;

$orden_columna = isset($_GET['orden_columna']) ? $_GET['orden_columna'] : 'fecha_de_pago';
$orden_direccion = isset($_GET['orden_direccion']) ? $_GET['orden_direccion'] : 'ASC';
$orden_direccion = ($orden_direccion == 'ASC') ? 'ASC' : 'DESC'; // Validar dirección

try {
    // Consulta para obtener los detalles de la nómina de la semana seleccionada con paginación y ordenación
    $nomina_query = "
        SELECT N.fecha_de_pago, E.alias, N.faltas, N.rebajas, N.bonos, N.rebajas_adicionales, N.total
        FROM NOMINAS N
        JOIN EMPLEADOS E ON N.empleadoID = E.empleadoID
        WHERE N.fecha_inicio = :inicio_semana AND N.fecha_fin = :fin_semana
        ORDER BY $orden_columna $orden_direccion
        LIMIT :limite OFFSET :offset
    ";
    $stmt = $pdo->prepare($nomina_query);
    $stmt->bindParam(':inicio_semana', $inicio_semana);
    $stmt->bindParam(':fin_semana', $fin_semana);
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $nomina = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el total de la nómina de la semana seleccionada
    $total_nomina_query = "
        SELECT SUM(N.total) as total_nomina
        FROM NOMINAS N
        WHERE N.fecha_inicio = :inicio_semana AND N.fecha_fin = :fin_semana
    ";
    $stmt_total = $pdo->prepare($total_nomina_query);
    $stmt_total->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
    $total_nomina = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_nomina'] ?? 0;

    // Consulta para obtener el número total de registros para paginación
    $total_count_query = "
        SELECT COUNT(*) as total_count
        FROM NOMINAS N
        WHERE N.fecha_inicio = :inicio_semana AND N.fecha_fin = :fin_semana
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
                <h2>REPORTE DE NÓMINA SEMANAL</h2>
                <div class="form-container">
                    <form method="GET" action="" class="mb-4">
                        <div class="col-md-6 offset-md-3">
                            <label for="semana">Selecciona la semana:</label>
                            <div id="week-picker" class="input-group">
                                <input type="hidden" id="semana" name="semana" value="<?php echo htmlspecialchars($semana_seleccionada); ?>">
                                <div class="form-control"><?php echo date('Y-m-d', strtotime($inicio_semana)) . ' - ' . date('Y-m-d', strtotime($fin_semana)); ?></div>
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
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=fecha_de_pago&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Fecha de Pago <?php echo $orden_columna === 'fecha_de_pago' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=alias&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Empleado <?php echo $orden_columna === 'alias' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=faltas&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Faltas <?php echo $orden_columna === 'faltas' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=rebajas&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Rebajas <?php echo $orden_columna === 'rebajas' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=total&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Total <?php echo $orden_columna === 'total' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($nomina)) : ?>
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
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No se encontraron registros para la semana seleccionada.</td>
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
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Datepicker JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <!-- Tu script personalizado -->
        <script src="../assets/js/weekpicker.js"></script>
    </div>
</body>

</html>
<?php
$pdo = null;
?>