<?php
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

$clientes = [];

$sql = "SELECT CLIENTES.clienteid, CLIENTES.nombre, CLIENTES.apellido_paterno, CLIENTES.apellido_materno, CITAS.citaID, CITAS.servicio_solicitado, CITAS.fecha_cita, CITAS.estado
        FROM CLIENTES
        LEFT JOIN CITAS ON CLIENTES.clienteid = CITAS.clienteid
        ORDER BY CLIENTES.nombre, CITAS.fecha_cita DESC";
$query = $pdo->prepare($sql);
$query->execute();
$clientes = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Clientes</title>
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
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Vista de Clientes y sus Citas</h2>
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Nombre del Cliente</th>
                    <th>Servicio Solicitado</th>
                    <th>Fecha de la Cita</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($clientes)): ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno']); ?></td>
                            <td><?php echo htmlspecialchars($cliente['servicio_solicitado']); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($cliente['fecha_cita']))); ?></td>
                            <td><?php echo htmlspecialchars($cliente['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay citas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
