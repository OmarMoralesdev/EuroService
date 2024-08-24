<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Financiero</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .table-wrapper {
            overflow-x: auto; /* Permite hacer scroll en pantallas pequeñas */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
            min-width: 600px; /* Establece un ancho mínimo para evitar que se haga demasiado pequeña */
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .chart-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .chart-wrapper {
            flex: 1;
            max-width: 100%;
            height: 500px;
            position: relative;
        }
        canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
        @media (max-width: 768px) {
            h2 {
                font-size: 20px;
            }
            th, td {
                font-size: 14px;
                padding: 8px;
            }
            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2>CITAS PENDIENTES</h2>
                <div class="form-container">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead align="center">
                                <tr>
                                    <th scope="col">VEHÍCULO</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">SERVICIO SOLICITADO</th>
                                    <th scope="col">FECHA DE CITA</th>
                                    <th scope="col">REFACCIONES</th>
                                    <th scope="col">MANO DE OBRA</th>
                                    <th scope="col">TOTAL</th>
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
