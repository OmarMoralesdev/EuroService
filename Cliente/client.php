<?php
session_start();

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();



$clienteID = $_SESSION['clienteID'];

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
    $sql = "SELECT c.citaID, c.servicio_solicitado, c.fecha_cita, c.estado,
                   v.vin, v.marca, v.modelo, v.anio,
                   COALESCE(SUM(o.total_estimado - o.anticipo), 0) AS monto_deuda,
                   DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
            FROM CITAS c
            INNER JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
            LEFT JOIN ORDENES_TRABAJO o ON c.citaID = o.citaID
            WHERE v.clienteID = ? AND (c.estado = 'pendiente' OR c.estado = 'en proceso')
            GROUP BY c.citaID, c.servicio_solicitado, c.fecha_cita, c.estado,
                     v.vin, v.marca, v.modelo, v.anio
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

        .welcome {
            text-align: center;
            margin-bottom: 20px;
        }

        .card-columns {
            column-count: 1;
        }

        @media (min-width: 576px) {
            .card-columns {
                column-count: 2;
            }
        }

        @media (min-width: 768px) {
            .card-columns {
                column-count: 3;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="welcome">
            <?php
            $usuario = obtenerDetallesClientepersona($pdo, $_SESSION['clienteID']);

            if ($usuario) {
                echo "Bienvenido, " . htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['apellido_paterno']) . " " . htmlspecialchars($usuario['apellido_materno']);
            } else {
                echo "Usuario no encontrado.";
            }
            ?>

            <a href="../includes/cerrarsesion.php">Cerrar sesión</a>
        </div>

        <h2>Vehículos en el Taller</h2>
        <div class="card-columns">
            <?php if (!empty($citas)) : ?>
                <?php foreach ($citas as $cita) : ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($cita['marca'] . " " . $cita['modelo']); ?></h5>
                            <p class="card-text"><strong>VIN:</strong> <?php echo htmlspecialchars($cita['vin']); ?></p>
                            <p class="card-text"><strong>Servicio:</strong> <?php echo htmlspecialchars($cita['servicio_solicitado']); ?></p>
                            <p class="card-text"><strong>Fecha de la Cita:</strong> <?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($cita['fecha_cita']))); ?></p>
                            <p class="card-text"><strong>Días Restantes:</strong> <?php echo htmlspecialchars($cita['dias_restantes']); ?> días</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#citaModal<?php echo $cita['citaID']; ?>">
                                Ver Detalles
                            </button>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="citaModal<?php echo $cita['citaID']; ?>" tabindex="-1" aria-labelledby="citaModalLabel<?php echo $cita['citaID']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="citaModalLabel<?php echo $cita['citaID']; ?>">Detalles de la Cita</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Folio:</strong> <?php echo htmlspecialchars($cita['citaID']); ?></p>
                                    <p><strong>VIN:</strong> <?php echo htmlspecialchars($cita['vin']); ?></p>
                                    <p><strong>Marca:</strong> <?php echo htmlspecialchars($cita['marca']); ?></p>
                                    <p><strong>Modelo:</strong> <?php echo htmlspecialchars($cita['modelo']); ?></p>
                                    <p><strong>Año:</strong> <?php echo htmlspecialchars($cita['anio']); ?></p>
                                    <p><strong>Servicio Solicitado:</strong> <?php echo htmlspecialchars($cita['servicio_solicitado']); ?></p>
                                    <p><strong>Fecha de la Cita:</strong> <?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($cita['fecha_cita']))); ?></p>
                                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($cita['estado']); ?></p>
                                    <p><strong>Días Restantes:</strong> <?php echo htmlspecialchars($cita['dias_restantes']); ?> días</p>
                                    <?php if ($cita['estado'] == 'en proceso') : ?>
                                        <p><strong>Deuda Actual:</strong> $<?php echo htmlspecialchars(number_format($cita['monto_deuda'], 2)); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-center">No hay vehículos registrados.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>