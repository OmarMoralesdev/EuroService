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
</head>
<body>
    <div class="wrapper">
    <?php include 'vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2 style="text-align: center;">CITAS PENDIENTES</h2>
                <div class="form-container">
                    <div class="table-wrapper">
                        <table class="table">
                        <thead align="center">
                                <tr>
                                    <th scope="col">VEHÍCULO</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">SERVICIO SOLICITADO</th>
                                    <th scope="col">FECHA DE CITA</th>
                                    <th scope="col">COSTO DE REFACCIONES</th>
                                    <th scope="col">COSTO DE MANO DE OBRA</th>
                                    <th scope="col">TOTAL 
                                        (costo inspección incluido)</th>
                                    <th scope="col">ANTICIPO MÍNIMO</th>
                                    <th scope="col">TIEMPO RESTANTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();

                                $stmt = $pdo->prepare("
                                SELECT c.*, CONCAT(v.marca, ' ', v.modelo, ' ', v.anio, ' - ', v.color) AS VEHICULO, 
                                CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS PROPIETARIO,
                                (c.total_estimado/2) AS anticipo_minimo,
                                DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
                                FROM CITAS c 
                                JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
                                JOIN CLIENTES cl ON v.clienteID = cl.clienteID
                                JOIN PERSONAS p ON cl.personaID = p.personaID
                                WHERE c.estado = 'pendiente' 
                                ORDER BY c.fecha_cita ASC;
                                ");

                                $stmt->execute();
                                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($citas as $cita) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($cita['VEHICULO']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['PROPIETARIO']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['servicio_solicitado']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['fecha_cita']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['costo_refacciones']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['costo_mano_obra']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['total_estimado']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['anticipo_minimo']) . '</td>';
                                
                                    // Asignar tiempo restante basado en los días restantes
                                    $diasRestantes = $cita['dias_restantes'];
                                    $tiempoRestante = ($diasRestantes < 0) ? 'Atrasado' : (($diasRestantes == 0) ? 'Hoy' : $diasRestantes . ' días');
                                    $claseTiempoRestante = ($diasRestantes < 0) ? 'atrasado' : '';
                                
                                    echo '<td class="' . $claseTiempoRestante . '">' . $tiempoRestante . '</td>';
                                
                                    echo '</tr>';
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
