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

// Obtener vehículos del cliente
if (!function_exists('obtenerVehiculosCliente')) {
    function obtenerVehiculosCliente($pdo, $clienteID)
    {
        $sql = "SELECT * FROM VEHICULOS WHERE clienteID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Obtener citas pendientes del cliente
if (!function_exists('obtenerCitasPendientes')) {
    function obtenerCitasPendientes($pdo, $clienteID)
    {
        $sql = "SELECT c.citaID, c.servicio_solicitado, c.fecha_cita, c.estado,
                    v.vin, v.marca, v.modelo, v.anio,
                    DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
                FROM CITAS c
                INNER JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
                WHERE v.clienteID = ? AND c.estado IN ('pendiente', 'en proceso')
                ORDER BY c.fecha_cita DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

try {
    $vehiculos = obtenerVehiculosCliente($pdo, $clienteID);
    $citas = obtenerCitasPendientes($pdo, $clienteID);
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
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: black;
            color: white;
        }

        .navbar {
            margin-bottom: 0;
        }

        .container {
            margin-top: 75px; /* Ajustar según el tamaño de la navbar */
        }

        .content {
            margin-bottom: 20px;
        }

        .card {
            margin-bottom: 10px;
        }

        .modal-dialog {
            margin: 0;
        }

        .modal-body {
            padding: 1rem;
        }

        .modal-footer {
            padding: 0.5rem;
        }

        .section-heading {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php
    include 'nav.php'; ?>

    <div class="container">
        <section class="features text-center">
            <h1>BIENVENID@, 
            <?php
            $usuario = obtenerDetallesClientepersona($pdo, $clienteID);
            if ($usuario) {
                echo htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['apellido_paterno']) . " " . htmlspecialchars($usuario['apellido_materno']);
            } else {
                echo "NECESITAS INICIAR SESIÓN.";
            }
            ?>
            </h1>
        </section>
<BR>
        <section id="cp" class="content text-center">
        <?php
                    $usuario = obtenerDetallesClientepersona($pdo, $clienteID);
                    if ($usuario) {
                       echo"<h2 class='section-heading'>MIS CITAS PENDIENTES</h2>";
                    }?>
            <hr class="bg-light">
            <div class="row">
                <?php if (!empty($citas)) : ?>
                    <?php foreach ($citas as $cita) : ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <?php echo htmlspecialchars($cita['marca'] . " " . $cita['modelo']); ?>
                                </div>
                                <div class="card-body">
                                    <p><strong>Fecha de la Cita:</strong> <?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($cita['fecha_cita']))); ?></p>
                                    <p><strong>Días Restantes:</strong> <?php echo htmlspecialchars($cita['dias_restantes']); ?> días</p>
                                    <p><strong>Servicio Solicitado:</strong> <?php echo htmlspecialchars($cita['servicio_solicitado']); ?></p>
                                </div>
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#citaModal<?php echo $cita['citaID']; ?>">
                                        Ver Detalles
                                    </button>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="citaModal<?php echo $cita['citaID']; ?>" tabindex="-1" aria-labelledby="citaModalLabel<?php echo $cita['citaID']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
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
                                        </div>
                                        <div class="modal-footer">
                               2             <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="text-light">No tienes citas pendientes.</p>
                <?php endif; ?>
            </div>
        </section>

        <section id="v" class="content text-center">
        <?php
                    $usuario = obtenerDetallesClientepersona($pdo, $clienteID);
                    if ($usuario) {
                        echo "<h2 class='section-heading'>MIS VEHICULOS</h2>";
                    }?>
            <hr class="bg-light">
            <div class="row">
                <?php if (!empty($vehiculos)) : ?>
                    <?php foreach ($vehiculos as $vehiculo) : ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <?php echo htmlspecialchars($vehiculo['marca'] . " " . $vehiculo['modelo']); ?>
                                </div>
                                <div class="card-body">
                                    <p><strong>Placas:</strong> <?php echo htmlspecialchars($vehiculo['placas']); ?></p>
                                    <p><strong>VIN:</strong> <?php echo htmlspecialchars($vehiculo['vin']); ?></p>
                                    <p><strong>Año:</strong> <?php echo htmlspecialchars($vehiculo['anio']); ?></p>
                                    <p><strong>Color:</strong> <?php echo htmlspecialchars($vehiculo['color']); ?></p>
                                    <p><strong>Kilometraje:</strong> <?php echo htmlspecialchars($vehiculo['kilometraje']); ?> km</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>

                    <p class="text-light">No tienes vehículos registrados.</p>
                
                
                    <?php endif; ?>

            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-7F4f4IqIv8CH3f7MWNTdfEBe1Xn0hf5oC5exxB3L+ZVMEhhL4lmFJ58vXKQG9mCz" crossorigin="anonymous"></script>
</body>
</html>