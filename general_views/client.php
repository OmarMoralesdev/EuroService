<?php
session_start();

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

// Verificar si el usuario está logueado
if (!isset($_SESSION['clienteID'])) {
    header("Location: ../login.php");
    exit();
}

$clienteID = $_SESSION['clienteID'];

try {
    $sql = "SELECT c.citaID, c.servicio_solicitado, c.fecha_cita, c.estado,
                v.vin, v.marca, v.modelo, v.anio,
                   DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
            FROM CITAS c
            INNER JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
            WHERE v.clienteID = ?
            ORDER BY c.fecha_cita DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$clienteID]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos en el Taller</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 50px auto;
        }

        h2 {
            text-align: center;
            color: #000;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .thead-dark th {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Vehículos en el Taller</h2>
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Folio</th>
                    <th>VIN</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año</th>
                    <th>Servicio Solicitado</th>
                    <th>Fecha de la Cita</th>
                    <th>Estado</th>
                    <th>Días Restantes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($citas)) : ?>
                    <?php foreach ($citas as $cita) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cita['citaID']); ?></td>
                            <td><?php echo htmlspecialchars($cita['vin']); ?></td>
                            <td><?php echo htmlspecialchars($cita['marca']); ?></td>
                            <td><?php echo htmlspecialchars($cita['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($cita['anio']); ?></td>
                            <td><?php echo htmlspecialchars($cita['servicio_solicitado']); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($cita['fecha_cita']))); ?></td>
                            <td><?php echo htmlspecialchars($cita['estado']); ?></td>
                            <td><?php echo htmlspecialchars($cita['dias_restantes']); ?> días</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="text-center">No hay vehículos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>