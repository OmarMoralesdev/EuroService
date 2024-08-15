<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras</title>
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #B2B2B2;
            margin: 0;
            padding: 0;
        }
        .table {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
            width: 100%;
            text-align: left;
        }
        .main {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 20px;
            height: 100vh;
            box-sizing: border-box;
        }
        .table-wrapper {
            max-height: calc(75vh - 60px);
            overflow-y: auto;
            width: 100%;
            padding-right: 20px;
            margin-right: 50px; 
        }
        .atrasado {
            color: red;
        }
        .help-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #b3b3b3; 
            color: black;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .help-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .help-modal-content {
            background-color: #222;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
        .filter-form {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .filter-form select {
            margin-left: 10px;
            padding: 5px;
        }
        .filter-form button {
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2 style="text-align: center;">REGISTRO DE COMPRAS</h2>
                <div class="form-container">
                    <form class="filter-form" method="GET" action="">
                        <label for="month">Mes:</label>
                        <select name="month" id="month">
                            <option value="">Seleccionar Mes</option>
                            <?php 
                            $selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
                            for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo $m == $selectedMonth ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $m, 10)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <label for="year">Año:</label>
                        <select name="year" id="year">
                            <option value="">Seleccionar Año</option>
                            <?php 
                            $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
                            for ($y = date('Y'); $y >= 2000; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <button type="submit">Filtrar</button>
                    </form>
                    
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">FECHA DE COMPRA</th>
                                    <th scope="col">INSUMO</th>
                                    <th scope="col">CATEGORIA</th>
                                    <th scope="col">PROVEEDOR</th>
                                    <th scope="col">TIPO DE COMPRA</th>
                                    <th scope="col">PRECIO UNITARIO</th>
                                    <th scope="col">CANTIDAD COMPRADA</th>
                                    <th scope="col">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();

                                $month = isset($_GET['month']) ? $_GET['month'] : '';
                                $year = isset($_GET['year']) ? $_GET['year'] : '';

                                $query = "
                                    SELECT c.fecha_compra, i.nombre AS nombre_insumo, ca.nombre AS categoria, p.nombre AS nombre_proveedor, c.tipo_compraID, dc.precio_unitario, dc.cantidad, c.total
                                    FROM COMPRAS c
                                    INNER JOIN DETALLE_COMPRA dc ON c.compraID = dc.compraID
                                    INNER JOIN INSUMO_PROVEEDOR ip ON dc.insumo_proveedorID = ip.insumo_proveedorID
                                    INNER JOIN INSUMOS i ON ip.insumoID = i.insumoID
                                    INNER JOIN CATEGORIAS ca ON i.categoriaID = ca.categoriaID
                                    INNER JOIN PROVEEDORES p ON ip.proveedorID = p.proveedorID
                                ";

                                $conditions = [];
                                if ($month) {
                                    $conditions[] = "MONTH(c.fecha_compra) = :month";
                                }
                                if ($year) {
                                    $conditions[] = "YEAR(c.fecha_compra) = :year";
                                }

                                if ($conditions) {
                                    $query .= " WHERE " . implode(" AND ", $conditions);
                                }

                                $query .= " ORDER BY c.fecha_compra DESC";

                                $stmt = $pdo->prepare($query);

                                if ($month) {
                                    $stmt->bindParam(':month', $month, PDO::PARAM_INT);
                                }
                                if ($year) {
                                    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
                                }

                                $stmt->execute();
                                $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if ($compras) {
                                    foreach ($compras as $compra) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($compra['fecha_compra']) . '</td>';
                                        echo '<td>' . htmlspecialchars($compra['nombre_insumo']) . '</td>';
                                        echo '<td>' . htmlspecialchars($compra['categoria']) . '</td>';
                                        echo '<td>' . htmlspecialchars($compra['nombre_proveedor']) . '</td>';
                                        echo '<td>' . htmlspecialchars($compra['tipo_compraID']) . '</td>';
                                        echo '<td>' . htmlspecialchars($compra['precio_unitario']) . '</td>';
                                        echo '<td>' . htmlspecialchars($compra['cantidad']) . '</td>';
                                        echo '<td>' . htmlspecialchars($compra['total']) . '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="8" style="text-align:center;">No se encontraron compras para el mes y año seleccionados.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
