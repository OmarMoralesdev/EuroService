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

    // Obtener parámetros de paginación y ordenación
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $pagina = $pagina > 0 ? $pagina : 1;
    $limite = 10; // Número de resultados por página
    $offset = ($pagina - 1) * $limite;

    $orden_columna = isset($_GET['orden_columna']) ? $_GET['orden_columna'] : 'fecha_compra';
    $orden_direccion = isset($_GET['orden_direccion']) ? $_GET['orden_direccion'] : 'ASC';
    $orden_direccion = ($orden_direccion == 'ASC') ? 'ASC' : 'DESC'; // Validar dirección

    // Consulta para obtener los detalles de las compras de la semana seleccionada con paginación y ordenación
    $gastos_query = "
        SELECT C.fecha_compra, DC.cantidad, DC.precio_unitario, DC.subtotal, I.nombre AS insumo, P.nombre AS proveedor
        FROM COMPRAS C
        JOIN DETALLE_COMPRA DC ON C.compraID = DC.compraID
        JOIN INSUMO_PROVEEDOR IP ON DC.insumo_proveedorID = IP.insumo_proveedorID
        JOIN INSUMOS I ON IP.insumoID = I.insumoID
        JOIN PROVEEDORES P ON IP.proveedorID = P.proveedorID
        WHERE C.fecha_compra BETWEEN :inicio_semana AND :fin_semana
        ORDER BY $orden_columna $orden_direccion
        LIMIT :limite OFFSET :offset
    ";
    $stmt = $pdo->prepare($gastos_query);
    $stmt->bindParam(':inicio_semana', $inicio_semana);
    $stmt->bindParam(':fin_semana', $fin_semana);
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener el total de gastos de la semana seleccionada
    $total_gastos_query = "
        SELECT SUM(DC.subtotal) as total_gastos
        FROM DETALLE_COMPRA DC
        JOIN COMPRAS C ON DC.compraID = C.compraID
        WHERE C.fecha_compra BETWEEN :inicio_semana AND :fin_semana
    ";
    $stmt_total = $pdo->prepare($total_gastos_query);
    $stmt_total->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
    $total_gastos = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_gastos'];

    // Consulta para obtener el número total de registros para paginación
    $total_count_query = "
        SELECT COUNT(*) as total_count
        FROM COMPRAS C
        JOIN DETALLE_COMPRA DC ON C.compraID = DC.compraID
        WHERE C.fecha_compra BETWEEN :inicio_semana AND :fin_semana
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Gastos Semanal</title>
    <!-- Datepicker CSS -->
    <link rel="icon" type="image/x-icon" href="./img/incono.svg">
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

        th input {
            width: 100%;
            box-sizing: border-box;
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
                <h2>REPORTE DE GASTOS SEMANAL</h2>
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
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=fecha_compra&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Fecha de Compra <?php echo $orden_columna === 'fecha_compra' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=insumo&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Insumo <?php echo $orden_columna === 'insumo' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=proveedor&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Proveedor <?php echo $orden_columna === 'proveedor' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=cantidad&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Cantidad <?php echo $orden_columna === 'cantidad' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=precio_unitario&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Precio Unitario <?php echo $orden_columna === 'precio_unitario' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?semana=<?php echo urlencode($semana_seleccionada); ?>&pagina=<?php echo $pagina; ?>&orden_columna=subtotal&orden_direccion=<?php echo $orden_direccion === 'ASC' ? 'DESC' : 'ASC'; ?>">
                                            Subtotal <?php echo $orden_columna === 'subtotal' ? ($orden_direccion === 'ASC' ? '&uarr;' : '&darr;') : ''; ?>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gastos as $gasto) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($gasto['fecha_compra']); ?></td>
                                        <td><?php echo htmlspecialchars($gasto['insumo']); ?></td>
                                        <td><?php echo htmlspecialchars($gasto['proveedor']); ?></td>
                                        <td><?php echo htmlspecialchars($gasto['cantidad']); ?></td>
                                        <td>$<?php echo number_format($gasto['precio_unitario'], 2); ?></td>
                                        <td>$<?php echo number_format($gasto['subtotal'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="font-weight-bold">
                                    <td colspan="5" class="text-right">Total:</td>
                                    <td>$<?php echo number_format($total_gastos, 2); ?></td>
                                </tr>
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
        <!-- Datepicker JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script src="../assets/js/weekpicker.js"></script>
</body>
</html>
<?php
$pdo = null; 
?>