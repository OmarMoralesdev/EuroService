<?php
session_start();

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$clienteID = $_SESSION['clienteID'];

// Obtén el término de búsqueda
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

if (!function_exists('obtenerDetallesClientepersona')) {
    function obtenerDetallesClientepersona($pdo, $clienteID)
    {
        $sql = "SELECT nombre, apellido_paterno, apellido_materno FROM CLIENTES WHERE clienteID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

try {
    // Modifica la consulta para incluir el término de búsqueda
    $sql = "SELECT c.citaID, c.servicio_solicitado, c.fecha_cita, c.estado, c.costo_mano_obra, c.costo_refacciones, c.total_estimado,
                v.vin, v.marca, v.modelo, v.anio,
                DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
            FROM CITAS c
            INNER JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
            WHERE v.clienteID = ? 
            AND (c.estado = 'cancelado' OR c.estado = 'completado')
            AND c.servicio_solicitado LIKE ?
            ORDER BY c.fecha_cita DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$clienteID, "%$searchTerm%"]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Citas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: black;
            color: white;
        }

        .container {
            width: 80%;
            margin-top: 100px;
        }

        .card-columns {
            column-count: 1;
        }

        .card {
            align-items: center;
            background-color: #333;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            background-color: white;
            border-radius: 10px;
            width: 100%;
        }

        .card-title {
            text-align: center;
            color: black;
        }

        .card-text {
            color: black;
        }

        .modal-content {
            background-color: white;
            color: black;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">

    
        <h2 style="text-align: center;">Historial</h2>
        <form method="post" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Buscar por servicio" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button class="btn btn-dark" type="submit">Buscar</button>
            </div>
        </form>

        
        <div class="card-columns">
            <?php if (!empty($citas)) : ?>
                <?php foreach ($citas as $cita) : ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($cita['marca'] . " " . $cita['modelo']); ?></h5>
                            <p class="card-text"><strong>VIN:</strong> <?php echo htmlspecialchars($cita['vin']); ?></p>
                            <p class="card-text"><strong>Servicio:</strong> <?php echo htmlspecialchars($cita['servicio_solicitado']); ?></p>
                            <p class="card-text"><strong>Fecha de la Cita:</strong> <?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($cita['fecha_cita']))); ?></p>
                            <button type="button" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto" data-bs-toggle="modal" data-bs-target="#detalles<?php echo $cita['citaID']; ?>">Ver Detalles</button>
                        </div>
                    </div>

                    <!-- Modal detalles -->
                    <div class="modal fade" id="detalles<?php echo $cita['citaID']; ?>" tabindex="-1" aria-labelledby="detallesLabel<?php echo $cita['citaID']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detallesLabel<?php echo $cita['citaID']; ?>">Detalles de la Cita</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Folio:</strong> <?php echo htmlspecialchars($cita['citaID']); ?></p>
                                    <p><strong>VIN:</strong> <?php echo htmlspecialchars($cita['vin']); ?></p>
                                    <p><strong>Marca:</strong> <?php echo htmlspecialchars($cita['marca']); ?></p>
                                    <p><strong>Modelo:</strong> <?php echo htmlspecialchars($cita['modelo']); ?></p>
                                    <p><strong>Año:</strong> <?php echo htmlspecialchars($cita['anio']); ?></p>
                                    <p><strong>Servicio Solicitado:</strong> <?php echo htmlspecialchars($cita['servicio_solicitado']); ?></p>
                                    <p><strong>Costo mano de obra:</strong> <?php echo htmlspecialchars($cita['costo_mano_obra']); ?></p>
                                    <p><strong>Costo refacciones:</strong> <?php echo htmlspecialchars($cita['costo_refacciones']); ?></p>
                                    <p><strong>Total:</strong> <?php echo htmlspecialchars($cita['total_estimado']); ?></p>
                                    <p><strong>Fecha de la Cita:</strong> <?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($cita['fecha_cita']))); ?></p>
                                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($cita['estado']); ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-center">No hay citas registradas.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
