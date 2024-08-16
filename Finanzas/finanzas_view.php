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

                // Consulta SQL actualizada para datos semanales
                $sql = "
                    SELECT 
                        DATE_FORMAT(fecha_pago, '%Y-%u') AS semana,
                        SUM(CASE WHEN tipo_pago = 'anticipo' THEN monto ELSE 0 END) AS ingresos_totales,
                        0 AS gastos_totales,
                        0 AS ingresos_semanales,
                        0 AS gastos_semanales,
                        0 AS compras_semanales
                    FROM PAGOS
                    WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    GROUP BY YEARWEEK(fecha_pago)

                    UNION ALL

                    SELECT 
                        DATE_FORMAT(c.fecha_compra, '%Y-%u') AS semana,
                        0 AS ingresos_totales,
                        COALESCE(SUM(dc.cantidad * dc.precio_unitario), 0) AS gastos_totales,
                        0 AS ingresos_semanales,
                        COALESCE(SUM(dc.cantidad * dc.precio_unitario), 0) AS gastos_semanales,
                        COALESCE(SUM(dc.cantidad * dc.precio_unitario), 0) AS compras_semanales
                    FROM DETALLE_COMPRA dc
                    JOIN COMPRAS c ON dc.compraID = c.compraID
                    WHERE c.fecha_compra >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    GROUP BY YEARWEEK(c.fecha_compra)

                    UNION ALL

                    SELECT 
                        DATE_FORMAT(c.fecha_cita, '%Y-%u') AS semana,
                        COALESCE(SUM(c.total_estimado), 0) AS ingresos_semanales,
                        0 AS gastos_totales,
                        COALESCE(SUM(c.total_estimado), 0) AS ingresos_semanales,
                        0 AS gastos_semanales,
                        0 AS compras_semanales
                    FROM CITAS c
                    WHERE c.fecha_cita >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    GROUP BY YEARWEEK(c.fecha_cita)

                    UNION ALL

                    SELECT 
                        DATE_FORMAT(fecha_pago, '%Y-%u') AS semana,
                        0 AS ingresos_totales,
                        SUM(CASE WHEN tipo_pago = 'pago_empleado' THEN monto ELSE 0 END) AS gastos_totales,
                        0 AS ingresos_semanales,
                        SUM(CASE WHEN tipo_pago = 'pago_empleado' THEN monto ELSE 0 END) AS gastos_semanales,
                        0 AS compras_semanales
                    FROM PAGOS
                    WHERE fecha_pago >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    GROUP BY YEARWEEK(fecha_pago)
                ";

                // Ejecutar consulta
                $stmt = $pdo->query($sql);

                // Inicializar datos para gráficos
                $data = [
                    'semanas' => [],
                    'ingresos_totales' => [],
                    'gastos_totales' => [],
                    'ingresos_semanales' => [],
                    'gastos_semanales' => [],
                    'compras_semanales' => []
                ];

                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Recolectar datos para gráficos
                        $data['semanas'][] = $row['semana'];
                        $data['ingresos_totales'][] = (float)$row['ingresos_totales'];
                        $data['gastos_totales'][] = (float)$row['gastos_totales'];
                        $data['ingresos_semanales'][] = (float)$row['ingresos_semanales'];
                        $data['gastos_semanales'][] = (float)$row['gastos_semanales'];
                        $data['compras_semanales'][] = (float)$row['compras_semanales'];
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
                        var semanas = <?php echo json_encode($data['semanas']); ?>;
                        var ingresos_totales = <?php echo json_encode($data['ingresos_totales']); ?>;
                        var gastos_totales = <?php echo json_encode($data['gastos_totales']); ?>;
                        var ingresos_semanales = <?php echo json_encode($data['ingresos_semanales']); ?>;
                        var gastos_semanales = <?php echo json_encode($data['gastos_semanales']); ?>;
                        var compras_semanales = <?php echo json_encode($data['compras_semanales']); ?>;

                        // Crear gráfico
                        var ctx = document.getElementById('financialChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar', // Tipo de gráfico
                            data: {
                                labels: semanas,
                                datasets: [
                                    {
                                        label: 'Ingresos Totales',
                                        data: ingresos_totales,
                                        backgroundColor: 'rgba(0, 128, 0, 0.7)',
                                        borderColor: 'rgba(0, 128, 0, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Ingresos Semanales',
                                        data: ingresos_semanales,
                                        backgroundColor: 'rgba(153, 102, 255, 0.7)',
                                        borderColor: 'rgba(153, 102, 255, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Gastos Semanales',
                                        data: gastos_semanales,
                                        backgroundColor: 'rgba(255, 206, 86, 0.7)',
                                        borderColor: 'rgba(255, 206, 86, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Compras Semanales',
                                        data: compras_semanales,
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
