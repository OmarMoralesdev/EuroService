<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Financiero</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            cursor: default; /* Mantener el cursor como estándar */
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .main {
            margin: 20px 0;
        }
        .chart-container {
            display: flex;
            justify-content: center; /* Centrar el gráfico horizontalmente */
            margin-top: 20px;
        }
        .chart-wrapper {
            flex: 1;
            max-width: 1000px; /* Ajustar el ancho máximo del contenedor */
            height: 500px; /* Altura fija para el gráfico */
            position: relative;
        }
        canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="wrapper">
<?php include 'vabr.php'; ?>
    <?php require '../includes/db.php'; ?>

    <div class="main">
        <div class="container">
            <h2 style="text-align: center;">REPORTE FINANCIERO</h2>
            <div class="form-container">
                <?php
                // Conectar a la base de datos
                $con = new Database();
                $pdo = $con->conectar();
                
                // Consulta SQL
                $sql = "
                SELECT 
                    mes, 
                    SUM(total_ingresos_mensuales) AS total_ingresos_mensuales,
                    SUM(total_gastos_mensuales) AS total_gastos_mensuales,
                    SUM(total_ingresos_servicios) AS total_ingresos_servicios,
                    SUM(total_gasto_insumo) AS total_gasto_insumo,
                    SUM(total_ingresos_mensuales) - 
                    (SUM(total_gastos_mensuales) + SUM(total_gasto_insumo)) AS total_neto,
                    SUM(total_ingresos_mensuales) + SUM(total_ingresos_servicios) AS total_con_ingresos_servicios,
                    SUM(total_gastos_mensuales) + SUM(total_gasto_insumo) AS total_gastos_totales
                FROM (
                    SELECT DATE_FORMAT(p.fecha_pago, '%Y-%m') AS mes,
                           COALESCE(SUM(p.monto), 0) AS total_ingresos_mensuales,
                           0 AS total_gastos_mensuales,
                           0 AS total_ingresos_servicios,
                           0 AS total_gasto_insumo
                    FROM PAGOS p
                    WHERE p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                    GROUP BY DATE_FORMAT(p.fecha_pago, '%Y-%m')
            
                    UNION ALL
            
                    SELECT DATE_FORMAT(c.fecha_compra, '%Y-%m') AS mes,
                           0 AS total_ingresos_mensuales,
                           COALESCE(SUM(dc.subtotal), 0) AS total_gastos_mensuales,
                           0 AS total_ingresos_servicios,
                           0 AS total_gasto_insumo
                    FROM DETALLE_COMPRA dc
                    JOIN COMPRAS c ON dc.compraID = c.compraID
                    WHERE c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                    GROUP BY DATE_FORMAT(c.fecha_compra, '%Y-%m')
            
                    UNION ALL
            
                    SELECT DATE_FORMAT(o.fecha_orden, '%Y-%m') AS mes,
                           0 AS total_ingresos_mensuales,
                           0 AS total_gastos_mensuales,
                           COALESCE(SUM(o.costo_mano_obra + o.costo_refacciones), 0) AS total_ingresos_servicios,
                           0 AS total_gasto_insumo
                    FROM ORDENES_TRABAJO o
                    WHERE o.fecha_orden >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                    GROUP BY DATE_FORMAT(o.fecha_orden, '%Y-%m')
            
                    UNION ALL
            
                    SELECT DATE_FORMAT(c.fecha_compra, '%Y-%m') AS mes,
                           0 AS total_ingresos_mensuales,
                           0 AS total_gastos_mensuales,
                           0 AS total_ingresos_servicios,
                           COALESCE(SUM(dc.cantidad * dc.precio_unitario), 0) AS total_gasto_insumo
                    FROM DETALLE_COMPRA dc
                    JOIN COMPRAS c ON dc.compraID = c.compraID
                    JOIN INSUMO_PROVEEDOR ip ON dc.insumo_proveedorID = ip.insumo_proveedorID
                    JOIN INSUMOS i ON ip.insumoID = i.insumoID
                    WHERE c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                    GROUP BY DATE_FORMAT(c.fecha_compra, '%Y-%m')
                ) AS reporte
                GROUP BY mes
                ORDER BY mes DESC;
            ";
            

                // Ejecutar consulta
                $stmt = $pdo->query($sql);

                if ($stmt->rowCount() > 0) {
                    // Mostrar datos en una tabla
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-striped table-bordered'>";
                    echo "<tr>
                            <th>Mes</th>
                            <th>Total Ingresos Mensuales</th>
                            <th>Total Gastos Mensuales</th>
                            <th>Total Ingresos Servicios</th>
                            <th>Total Gasto Insumo</th>
                            <th>Total Neto</th>
                            <th>Total con Ingresos Servicios</th>
                            <th>Total Gastos Totales</th>
                          </tr>";

                    // Datos para gráficos
                    $data = [
                        'meses' => [],
                        'ingresos' => [],
                        'gastos' => [],
                        'ingresos_servicios' => [],
                        'gasto_insumo' => [],
                        'total_neto' => [],
                        'total_con_ingresos_servicios' => [],
                        'total_gastos_totales' => []
                    ];

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['mes']) . "</td>";
                        echo "<td>" . number_format($row['total_ingresos_mensuales'], 2) . "</td>";
                        echo "<td>" . number_format($row['total_gastos_mensuales'], 2) . "</td>";
                        echo "<td>" . number_format($row['total_ingresos_servicios'], 2) . "</td>";
                        echo "<td>" . number_format($row['total_gasto_insumo'], 2) . "</td>";
                        echo "<td>" . number_format($row['total_neto'], 2) . "</td>";
                        echo "<td>" . number_format($row['total_con_ingresos_servicios'], 2) . "</td>";
                        echo "<td>" . number_format($row['total_gastos_totales'], 2) . "</td>";
                        echo "</tr>";

                        // Recolectar datos para gráficos
                        $data['meses'][] = $row['mes'];
                        $data['ingresos'][] = (float)$row['total_ingresos_mensuales'];
                        $data['gastos'][] = (float)$row['total_gastos_mensuales'];
                        $data['ingresos_servicios'][] = (float)$row['total_ingresos_servicios'];
                        $data['gasto_insumo'][] = (float)$row['total_gasto_insumo'];
                        $data['total_neto'][] = (float)$row['total_neto'];
                        $data['total_con_ingresos_servicios'][] = (float)$row['total_con_ingresos_servicios'];
                        $data['total_gastos_totales'][] = (float)$row['total_gastos_totales'];
                    }
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No se encontraron resultados.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>