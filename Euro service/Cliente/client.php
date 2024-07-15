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
    // Obtener nombre completo del cliente
    $stmtCliente = $pdo->prepare("
        SELECT p.nombre, p.apellido_paterno, p.apellido_materno 
        FROM CLIENTES c
        JOIN PERSONAS p ON c.personaID = p.personaID 
        WHERE c.clienteID = ?");
    $stmtCliente->execute([$clienteID]);
    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

    // Obtener citas del cliente
    $stmtCitas = $pdo->prepare("
        SELECT c.citaID, c.servicio_solicitado, c.fecha_cita, c.estado,
               v.vin, v.marca, v.modelo, v.anio,
               DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
        FROM CITAS c
        INNER JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
        WHERE v.clienteID = ?
        ORDER BY c.fecha_cita DESC");
    $stmtCitas->execute([$clienteID]);
    $citas = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);
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
        h2, h4 {
            text-align: center;
            color: #000;
        }
        .logout-btn {
            float: right;
            margin-top: -40px;
        }
    </style>
</head>

<body>
    <div class="container">
       
        <h2>Vehículos en el Taller</h2>
      
        <?php if ($cliente): ?>
            <h4>Bienvenido, <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido_paterno'] . ' ' . $cliente['apellido_materno']); ?>!</h4>
        <?php endif; ?>
        <div class="d-flex justify-content-end">
            <a href="../includes/cerrarsesion.php" class="btn btn-danger logout-btn">Cerrar Sesión</a>
        </div>
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
