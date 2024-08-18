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
            cursor: default;
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
            justify-content: center;
            margin-top: 20px;
        }
        .chart-wrapper {
            flex: 1;
            max-width: 1000px;
            height: 500px;
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
    <?php include '../includes/vabr.php'; ?>
    <?php require '../includes/db.php'; ?>

    <div class="main">
        <div class="container">
            <h2 style="text-align: center;">GRÁFICA FINANCIERA</h2>
            <div class="form-container">
                <?php
                // Conectar a la base de datos
                $con = new Database();
                $pdo = $con->conectar();
                
                // Consulta SQL actualizada
                $sql = "       
                    SELECT 
                        DATE_FORMAT(p.fecha_pago, '%Y-%m-%d') AS dia,
                        SUM(p.monto) AS total_ingresos_mensuales,
                        0 AS total_gastos_mensuales,
                        0 AS total_ingresos_servicios,
                        0 AS total_gasto_insumo,
                        SUM(p.monto) AS total_neto,
                        SUM(p.monto) AS total_con_ingresos_servicios,
                        0 AS total_gastos_totales
                    FROM PAGOS p
                    WHERE p.fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY p.fecha_pago

                    UNION ALL     

                    SELECT 
                        DATE_FORMAT(c.fecha_compra, '%Y-%m-%d') AS dia,
                        0 AS total_ingresos_mensuales,
                        COALESCE(SUM(dc.subtotal), 0) AS total_gastos_mensuales,
                        0 AS total_ingresos_servicios,
                        COALESCE(SUM(dc.subtotal), 0) AS total_gasto_insumo,
                        0 AS total_neto,
                        0 AS total_con_ingresos_servicios,
                        COALESCE(SUM(dc.subtotal), 0) AS total_gastos_totales
                    FROM DETALLE_COMPRA dc
                    JOIN COMPRAS c ON dc.compraID = c.compraID
                    WHERE c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY c.fecha_compra

                    UNION ALL

                    SELECT 
                        DATE_FORMAT(c.fecha_cita, '%Y-%m-%d') AS dia,
                        0 AS total_ingresos_mensuales,
                        0 AS total_gastos_mensuales,
                        COALESCE(SUM(c.total_estimado), 0) AS total_ingresos_servicios,
                        0 AS total_gasto_insumo,
                        COALESCE(SUM(c.total_estimado), 0) AS total_neto,
                        COALESCE(SUM(c.total_estimado), 0) AS total_con_ingresos_servicios,
                        0 AS total_gastos_totales
                    FROM CITAS c
                    WHERE c.fecha_cita >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY c.fecha_cita

                    UNION ALL

                    SELECT 
                        DATE_FORMAT(c.fecha_compra, '%Y-%m-%d') AS dia,
                        0 AS total_ingresos_mensuales,
                        0 AS total_gastos_mensuales,
                        0 AS total_ingresos_servicios,
                        COALESCE(SUM(dc.cantidad * dc.precio_unitario), 0) AS total_gasto_insumo,
                        0 AS total_neto,
                        0 AS total_con_ingresos_servicios,
                        COALESCE(SUM(dc.cantidad * dc.precio_unitario), 0) AS total_gastos_totales
                    FROM DETALLE_COMPRA dc
                    JOIN COMPRAS c ON dc.compraID = c.compraID
                    WHERE c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY c.fecha_compra
                ";

                // Ejecutar consulta
                $stmt = $pdo->query($sql);

                if ($stmt->rowCount() > 0) {
                    // Datos para gráficos
                    $data = [
                        'dias' => [],
                        'ingresos' => [],
                        'gastos' => [],
                        'ingresos_servicios' => [],
                        'gasto_insumo' => [],
                        'total_neto' => [],
                        'total_gastos_totales' => []
                    ];

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Recolectar datos para gráficos
                        $data['dias'][] = $row['dia'];
                        $data['ingresos'][] = (float)$row['total_ingresos_mensuales'];
                        $data['gastos'][] = (float)$row['total_gastos_mensuales'];
                        $data['ingresos_servicios'][] = (float)$row['total_ingresos_servicios'];
                        $data['gasto_insumo'][] = (float)$row['total_gasto_insumo'];
                        $data['total_neto'][] = (float)$row['total_neto'];
                        $data['total_gastos_totales'][] = (float)$row['total_gastos_totales'];
                    }
                } else {
                    echo "<p>No se encontraron resultados.</p>";
                }
                ?>

                <!-- Contenedor para el gráfico -->
                <div class="chart-container">
                    <div class="chart-wrapper">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Datos provenientes de PHP
                        var dias = <?php echo json_encode($data['dias']); ?>;
                        var ingresos = <?php echo json_encode($data['ingresos']); ?>;
                        var gastos = <?php echo json_encode($data['gastos']); ?>;
                        var ingresos_servicios = <?php echo json_encode($data['ingresos_servicios']); ?>;
                        var gasto_insumo = <?php echo json_encode($data['gasto_insumo']); ?>;
                        var total_neto = <?php echo json_encode($data['total_neto']); ?>;
                        var total_gastos_totales = <?php echo json_encode($data['total_gastos_totales']); ?>;

                        // Crear gráfico
                        var ctx = document.getElementById('financialChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar', // Tipo de gráfico
                            data: {
                                labels: dias,
                                datasets: [
                                    {
                                        label: 'Ingresos Mensuales',
                                        data: ingresos,
                                        backgroundColor: 'rgba(0, 128, 0, 0.7)',
                                        borderColor: 'rgba(0, 128, 0, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Gastos Mensuales',
                                        data: gastos,
                                        backgroundColor: 'rgba(255, 0, 0, 0.7)',
                                        borderColor: 'rgba(255, 0, 0, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Ingresos Servicios',
                                        data: ingresos_servicios,
                                        backgroundColor: 'rgba(153, 102, 255, 0.7)',
                                        borderColor: 'rgba(153, 102, 255, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Gasto Insumo',
                                        data: gasto_insumo,
                                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                                        borderColor: 'rgba(255, 159, 64, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Total Neto',
                                        data: total_neto,
                                        backgroundColor: 'rgba(255, 206, 86, 0.7)',
                                        borderColor: 'rgba(255, 206, 86, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Total Gastos Totales',
                                        data: total_gastos_totales,
                                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                responsive: true,
                                maintainAspectRatio: false,
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>
</body>
</html>
