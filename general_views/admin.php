<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CITAS PENDIENTES</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <style>
        body {
            background-color: #B2B2B2;
        }
        .table {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .table-wrapper {
            max-height: 400px;
            overflow-y: auto;
            width: 90%;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <h1>CITAS PENDIENTES</h1>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">FECHA DE SOLICITUD</th>
                            <th scope="col">VEHICULO</th>
                            <th scope="col">SERVICIO SOLICITADO</th>
                            <th scope="col">FECHA DE CITA</th>
                            <th scope="col">URGENCIA</th>
                            <th scope="col">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require '../includes/db.php';
                        $con = new Database();
                        $pdo = $con->conectar();

                        $stmt = $pdo->prepare("
                        SELECT c.*, v.marca AS vehiculo 
                        FROM CITAS c 
                        JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID 
                        WHERE c.estado = 'pendiente' 
                        ORDER BY c.fecha_cita ASC;
                        ");
                        $stmt->execute();
                        $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($citas as $cita) {
                            echo '<tr>';
                            echo '<td>' . $cita['fecha_solicitud'] . '</td>';
                            echo '<td>' . $cita['vehiculo'] . '</td>';
                            echo '<td>' . $cita['servicio_solicitado'] . '</td>';
                            echo '<td>' . $cita['fecha_cita'] . '</td>';
                            echo '<td>' . $cita['urgencia'] . '</td>';
                            echo '<td>' . $cita['estado'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>