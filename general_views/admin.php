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
            height: 100vh;
            box-sizing: border-box;
        }
        .table-wrapper {
            max-height: calc(100vh - 60px);
            overflow-y: auto;
            width: 100%;
            padding-right: 20px;
            margin-right: 50px; 
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
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
                                WHERE c.estado='pendiente' 
                                ORDER BY c.fecha_cita ASC;
                                ");
                                $stmt->execute();
                                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($citas as $cita) {
                                    echo '<tr>';
                                    echo '<td>' . $cita['vehiculo'] . '</td>';
                                    echo '<td>' . $cita['servicio_solicitado'] . '</td>';
                                    echo '<td>' . $cita['fecha_cita'] . '</td>';
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
