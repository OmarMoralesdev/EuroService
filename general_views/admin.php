<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CITAS PENDIENTES</title>
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
        }
        .main {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px;
            height: 10vh;
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
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2 style="text-align: center;">CITAS PENDIENTES</h2>
                <div class="form-container">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">VEHICULO</th>
                                    <th scope="col">SERVICIO SOLICITADO</th>
                                    <th scope="col">FECHA DE CITA</th>
                                    <th scope="col">TIEMPO RESTANTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();

                                $stmt = $pdo->prepare("
                                SELECT c.*, v.marca AS vehiculo, 
                                    DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
                                FROM CITAS c 
                                JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
                                WHERE c.estado = 'pendiente' 
                                ORDER BY c.fecha_cita ASC;
                                ");

                                $stmt->execute();
                                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($citas as $cita) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($cita['vehiculo']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['servicio_solicitado']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['fecha_cita']) . '</td>';
                                
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
