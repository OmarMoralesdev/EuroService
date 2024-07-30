<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$errors = [];
$success = '';
$showModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $anio = trim($_POST['anio']);
    $color = trim($_POST['color']);
    $kilometraje = trim($_POST['kilometraje']);
    $placas = trim($_POST['placas']);
    $vin = trim($_POST['vin']);
    $continuidad = isset($_POST['continuidad']) ? true : false; // Determina si se continúa con el registro de la cita e inspección

    $currentYear = date('Y');

    if ($anio < 1886 || $anio > $currentYear) {
        $errors['anio'] = "El año debe estar entre 1886 y el año actual.";
    }

    if (empty($errors)) {
        // Verificar si el VIN ya está registrado
        $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
        $stmtVerificar = $pdo->prepare($verificar);
        $stmtVerificar->execute([$vin]);

        if ($stmtVerificar->rowCount() > 0) {
            $errors['vin'] = "El vehículo ya está registrado.";
        } else {
            $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

            if ($stmt->rowCount() > 0) {
                $success = "Vehículo registrado exitosamente.";
                $vehiculoID = $pdo->lastInsertId();

                if ($continuidad) {
                    // Registrar la cita e inspección si se ha confirmado la continuidad
                    $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
                    $stmtCita = $pdo->prepare($sqlCita);
                    $stmtCita->execute([$vehiculoID, 'Inspección', date('Y-m-d'), date('Y-m-d'), 'Muy Urgente']);
                    $citaID = $pdo->lastInsertId();

                    // Insertar orden de trabajo
                    $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmtOrden = $pdo->prepare($sqlOrden);
                    $stmtOrden->execute([date('Y-m-d'), 800, 0, 'Muy Urgente', $citaID, $_POST['empleado'], $_POST['ubicacionID']]);
                    $ordenID = $pdo->lastInsertId();

                    // Insertar pago
                    $sqlPago = "INSERT INTO PAGOS (ordenID, fecha_pago, monto, tipo_pago, forma_de_pago) VALUES (?, ?, ?, 'anticipo', ?)";
                    $stmtPago = $pdo->prepare($sqlPago);
                    $stmtPago->execute([$ordenID, date('Y-m-d'), 0, $_POST['formadepago']]);

                    // Actualizar conteo de vehículos en la ubicación
                    $sqlActualizarUbicacion = "UPDATE UBICACIONES SET vehiculos_actuales = vehiculos_actuales + 1 WHERE ubicacionID = ?";
                    $stmtActualizarUbicacion = $pdo->prepare($sqlActualizarUbicacion);
                    $stmtActualizarUbicacion->execute([$_POST['ubicacionID']]);

                    $success .= " Cita e inspección registradas exitosamente.";
                } else {
                    $showModal = true;
                }
            } else {
                $errors['general'] = "Error: " . $pdo->errorInfo()[2];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehículo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main">
            <div class="container">
                <h2>REGISTRAR VEHÍCULO</h2>
                <div class="form-container">
                    <form id="formCita" action="autos.php" method="POST" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="form-group">
                            <label for="marca">Marca:</label>
                            <input type="text" id="marca" name="marca" maxlength="30" class="form-control <?php echo isset($errors['marca']) ? 'is-invalid' : ''; ?>" placeholder="Introduce la marca del vehículo" value="<?php echo htmlspecialchars($marca ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['marca'] ?? ''; ?></div>

                            <label for="modelo">Modelo:</label>
                            <input type="text" id="modelo" name="modelo" maxlength="30" class="form-control <?php echo isset($errors['modelo']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el modelo del vehículo" value="<?php echo htmlspecialchars($modelo ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['modelo'] ?? ''; ?></div>

                            <label for="anio">Año:</label>
                            <input type="number" id="anio" name="anio" min="1886" max="<?= date('Y') ?>" maxlength="4" class="form-control <?php echo isset($errors['anio']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el año del vehículo" value="<?php echo htmlspecialchars($anio ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['anio'] ?? ''; ?></div>

                            <label for="color">Color:</label>
                            <input type="text" id="color" name="color" maxlength="33" class="form-control <?php echo isset($errors['color']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el color del vehículo" value="<?php echo htmlspecialchars($color ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['color'] ?? ''; ?></div>

                            <label for="kilometraje">Kilometraje:</label>
                            <input type="text" id="kilometraje" name="kilometraje" maxlength="8" class="form-control <?php echo isset($errors['kilometraje']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el kilometraje del vehículo" value="<?php echo htmlspecialchars($kilometraje ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['kilometraje'] ?? ''; ?></div>

                            <label for="placas">Placas:</label>
                            <input type="text" id="placas" name="placas" maxlength="10" class="form-control <?php echo isset($errors['placas']) ? 'is-invalid' : ''; ?>" placeholder="Introduce las placas del vehículo" value="<?php echo htmlspecialchars($placas ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['placas'] ?? ''; ?></div>

                            <label for="vin">VIN:</label>
                            <input type="text" id="vin" name="vin" maxlength="20" class="form-control <?php echo isset($errors['vin']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el VIN del vehículo" value="<?php echo htmlspecialchars($vin ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['vin'] ?? ''; ?></div>

                            <br>
                            <input type="submit" class="btn btn-dark" value="Registrar Vehículo">
                        </div>
                    </form>

                    <!-- Modal de Confirmación de Continuidad -->
                    <div class="modal fade" id="continuidadModal" tabindex="-1" aria-labelledby="continuidadModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="continuidadModalLabel">Confirmar Continuidad</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Deseas continuar con el registro de la cita y la orden de trabajo?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                    <button type="button" class="btn btn-primary" id="confirmContinuidad">Sí</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Inspección (se mostrará si no hay continuidad) -->
                    <?php if ($showModal) : ?>
                        <div class="container mt-5">
                            <h2>Registrar Vehículo e Inspección</h2>
                            <form action="inspeccion.php" method="POST">
                                <div class="form-group">
                                    <label for="empleadoID">Empleado:</label>
                                    <select id="empleadoID" name="empleadoID" class="form-control" required>
                                        <option value="">Selecciona un empleado...</option>
                                        <?php
                                        // Obtener empleados para el dropdown
                                        $sql = "SELECT empleadoID, nombre FROM EMPLEADOS";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value=\"" . htmlspecialchars($row['empleadoID']) . "\">" . htmlspecialchars($row['nombre']) . "</option>";
                                        }
                                        ?>
                                    </select>

                                    <label for="fechaSolicitud">Fecha de Solicitud:</label>
                                    <input type="date" id="fechaSolicitud" name="fechaSolicitud" class="form-control" required>

                                    <label for="ubicacionID">Ubicación:</label>
                                    <select id="ubicacionID" name="ubicacionID" class="form-control" required>
                                        <option value="">Selecciona una ubicación...</option>
                                        <?php
                                        $sql = "SELECT ubicacionID, nombre FROM UBICACIONES";
                                        $stmt = $pdo->prepare($sql);
                                        $stmt->execute();
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value=\"" . htmlspecialchars($row['ubicacionID']) . "\">" . htmlspecialchars($row['nombre']) . "</option>";
                                        }
                                        ?>
                                    </select>

                                    <label for="formaDePago">Forma de Pago:</label>
                                    <input type="text" id="formaDePago" name="formaDePago" maxlength="20" class="form-control" placeholder="Introduce la forma de pago" required>
                                </div>

                                <input type="submit" class="btn btn-dark" value="Registrar Vehículo e Inspección">
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Mensajes de éxito o error -->
                    <?php if ($success) : ?>
                        <div class="alert alert-success mt-3"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if (isset($errors['general'])) : ?>
                        <div class="alert alert-danger mt-3"><?php echo $errors['general']; ?></div>
                    <?php endif; ?>
                    <?php if (isset($errors['vin'])) : ?>
                        <div class="alert alert-danger mt-3"><?php echo $errors['vin']; ?></div>
                    <?php endif; ?>
                    <?php if (isset($errors['anio'])) : ?>
                        <div class="alert alert-danger mt-3"><?php echo $errors['anio']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <script src="app.js"></script>
        <script>
            document.getElementById('formCita').addEventListener('submit', function(event) {
                let valid = true;
                const currentYear = new Date().getFullYear();

                // Obtener valores del formulario
                const marca = document.getElementById('marca').value.trim();
                const modelo = document.getElementById('modelo').value.trim();
                const anio = parseInt(document.getElementById('anio').value.trim(), 10);
                const color = document.getElementById('color').value.trim();
                const kilometraje = document.getElementById('kilometraje').value.trim();
                const placas = document.getElementById('placas').value.trim();
                const vin = document.getElementById('vin').value.trim();

                // Validar marca
                if (/\d/.test(marca)) {
                    document.getElementById('marca').classList.add('is-invalid');
                    valid = false;
                } else {
                    document.getElementById('marca').classList.remove('is-invalid');
                }

                // Validar año
                if (anio < 1886 || anio > currentYear || anio.toString().length !== 4) {
                    document.getElementById('anio').classList.add('is-invalid');
                    valid = false;
                } else {
                    document.getElementById('anio').classList.remove('is-invalid');
                }

                // Validar kilometraje
                if (!/^\d{1,8}$/.test(kilometraje)) {
                    document.getElementById('kilometraje').classList.add('is-invalid');
                    valid = false;
                } else {
                    document.getElementById('kilometraje').classList.remove('is-invalid');
                }

                // Validar color
                if (/\d/.test(color)) {
                    document.getElementById('color').classList.add('is-invalid');
                    valid = false;
                } else {
                    document.getElementById('color').classList.remove('is-invalid');
                }

                // Validar placas
                if (placas.length > 10) {
                    document.getElementById('placas').classList.add('is-invalid');
                    valid = false;
                } else {
                    document.getElementById('placas').classList.remove('is-invalid');
                }

                // Validar VIN
                if (vin.length > 20) {
                    document.getElementById('vin').classList.add('is-invalid');
                    valid = false;
                } else {
                    document.getElementById('vin').classList.remove('is-invalid');
                }

                if (!valid) {
                    event.preventDefault();
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php if ($showModal) : ?>
                    $('#continuidadModal').modal('show');
                <?php endif; ?>

                document.getElementById('confirmContinuidad').addEventListener('click', function() {
                    document.getElementById('formCita').submit();
                });
            });
        </script>
</body>

</html>