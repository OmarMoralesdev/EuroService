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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="style.css">
    
        <style>
        body {
            background-color: black;
            color: white;
        }
        .container {
            margin-top: 75px; /* Ajustar según el tamaño de la navbar */
        }
        .card {
            margin-bottom: 0px;
        }
     
        
        .help-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #b3b3b3; /* Color de fondo del ícono */
    color: black;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 30px;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}

.help-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.help-modal-content {
    background-color: #222;
    color: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
}
.modal{
    padding-top: 2% !important;
    color: black;
    padding-top: 100px;
    width: 100%;
    height: 100%;
}

    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

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
                            <div class="modal " id="citaModal<?php echo $cita['citaID']; ?>" tabindex="-1" aria-labelledby="citaModalLabel<?php echo $cita['citaID']; ?>" aria-hidden="true">
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
                                           <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cerrar</button>
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
                        echo "<h2 class='section-heading'>MIS VEHICULOS REGISTRADOS</h2>";
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
                
                <!-- Sección de Filtros -->
                <div class="filter-section">
                    <form id="filterForm">
                        <label for="vehicleFilter">Filtrar por vehículo:</label>
                        <input type="text" id="vehicleFilter" name="vehicleFilter" placeholder="Nombre del vehículo">
                
                        <label for="dateFilter">Filtrar por fecha:</label>
                        <input type="date" id="dateFilter" name="dateFilter">
                
                        <button type="button" id="applyFilters">Aplicar Filtros</button>
                    </form>
                </div>
                
                <!-- Modal de Ayuda -->
                <div class="help-modal" id="helpModal">
                    <div class="help-modal-content">
                        <span class="close" id="closeHelpModal">&times;</span>
                        <h5>¿Cómo se usa?</h5>
                        <HR>
                        <P>MIS VEHICULOS<br>
                            En esta ventana se mostrarán todos tus vehículos actualmente registados en el sistema de EURO SERVICE 
                            <HR>
                            CITAS PENDIENTES<br>
                            En esta ventana se mostrarán todas tus citas que tienes pendientes la cual cuenta con tu vehículo, temporizador indicando el tiempo restante y el servicio que se realizará para el día de tu cita
                            <HR>
                            HISTORIAL<br>
                            En esta ventana se mostrarán todas tus citas que has tenido en el pasado, mostrando la fecha de la cita, el vehículo que se utilizó, el servicio que se realizó y el estado de la cita.
                            <HR>
                        </P>
                    </div>
                </div>
                
                <!-- Ícono de Ayuda -->
                <div class="help-icon" id="helpIcon">?</div>
                
                <script>
                    // Funcionalidad del modal de ayuda
                    document.getElementById('helpIcon').addEventListener('click', function() {
                        document.getElementById('helpModal').style.display = 'flex';
                    });
                
                    document.getElementById('closeHelpModal').addEventListener('click', function() {
                        document.getElementById('helpModal').style.display = 'none';
                    });
                
                    window.addEventListener('click', function(event) {
                        if (event.target == document.getElementById('helpModal')) {
                            document.getElementById('helpModal').style.display = 'none';
                        }
                    });
                
                    // Funcionalidad de los filtros
                    document.getElementById('applyFilters').addEventListener('click', function() {
                        const vehicleFilter = document.getElementById('vehicleFilter').value.toLowerCase();
                        const dateFilter = document.getElementById('dateFilter').value;
                
                        const vehicles = document.querySelectorAll('.vehicle-item'); // Asumiendo que cada vehículo tiene la clase 'vehicle-item'
                        const appointments = document.querySelectorAll('.appointment-item'); // Asumiendo que cada cita tiene la clase 'appointment-item'
                
                        vehicles.forEach(vehicle => {
                            const vehicleName = vehicle.querySelector('.vehicle-name').textContent.toLowerCase(); // Asumiendo que el nombre del vehículo está en un elemento con la clase 'vehicle-name'
                            if (vehicleName.includes(vehicleFilter)) {
                                vehicle.style.display = '';
                            } else {
                                vehicle.style.display = 'none';
                            }
                        });
                
                        appointments.forEach(appointment => {
                            const appointmentDate = appointment.querySelector('.appointment-date').textContent; // Asumiendo que la fecha de la cita está en un elemento con la clase 'appointment-date'
                            if (dateFilter === '' || appointmentDate === dateFilter) {
                                appointment.style.display = '';
                            } else {
                                appointment.style.display = 'none';
                            }
                        });
                    });
                </script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
