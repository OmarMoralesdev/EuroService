<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
session_start();
$errors = [];
$success = '';
$showModal = false;
$showInspeccionForm = false;
$vehiculoID = '';
$continuidad = false;


// Comprobar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si las claves existen en el array $_POST
    $clienteID = isset($_POST['clienteID']) ? $_POST['clienteID'] : '';
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $kilometraje = isset($_POST['kilometraje']) ? trim($_POST['kilometraje']) : '';
    $placas = isset($_POST['placas']) ? trim($_POST['placas']) : '';
    $vin = isset($_POST['vin']) ? trim($_POST['vin']) : '';
    $continuidad = isset($_POST['continuidad']) ? true : false;
    
    $currentYear = date('Y');

    if ($anio < 1886 || $anio > $currentYear) {
        $_SESSION['error'] = "El año debe estar entre 1886 y el año actual.";
    }

    if (empty($errors)) {
        $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
        $stmtVerificar = $pdo->prepare($verificar);
        $stmtVerificar->execute([$vin]);

        if ($stmtVerificar->rowCount() > 0) {
            $_SESSION['error'] = "El vehículo ya está registrado.";
        } else {
            $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin,activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?,'si')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

            if ($stmt->rowCount() > 0) {

                $_SESSION['vehiculo'] = $vehiculoID = $pdo->lastInsertId();

                if (!$continuidad) {
                    $showInspeccionForm = true;
                } else {


                    $_SESSION['bien'] = "Vehículo registrado exitosamente.";
                }
            } else {
                $_SESSION['error'] = "Error: " . $pdo->errorInfo()[2];
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
        <?php include '../includes/vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2>REGISTRAR VEHÍCULO</h2>
                <div class="form-container">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Usuario registrado!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='alert alert-success' role='alert'>{$_SESSION['bien']}</div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        unset($_SESSION['bien']);
                    }
                    ?>
                    <form id="formCita" action="" method="POST" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group lista" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="form-group">
                            <!-- Campos del formulario -->
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

                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="continuidad" name="continuidad" <?php echo $continuidad ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="continuidad">¿Tiene continuidad el vehiculo?</label>
                            </div>

                            <br>
                            <input type="submit" class="btn btn-dark" value="Registrar Vehículo">
                        </div>
                    </form>

                    <!-- Formulario de Inspección -->
                    <?php if ($showInspeccionForm) : ?>
                        <div class="container mt-5">
                            <h2>Registrar Inspección</h2>
                            <form action="register_inspection.php" method="POST">
                                <input type="hidden" name="vehiculoID" value="<?php echo htmlspecialchars($vehiculoID, ENT_QUOTES); ?>">
                                <div class="form-group">
                                    <label for="empleado" class="form-label">Empleado ID:</label>
                                    <select name="empleadoID" class="form-control" required>
                                        <?php
                                        function obtenerEmpleadosDisponibles($pdo)
                                        {
                                            $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
                                                    FROM EMPLEADOS 
                                                    JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID";
                                            $stmt = $pdo->query($sql);
                                            return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        }
                                        $empleados = obtenerEmpleadosDisponibles($pdo);
                                        foreach ($empleados as $empleado) {
                                            $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                                            echo "<option value=\"{$empleado['empleadoID']}\">{$nombreCompleto}</option>";
                                        }
                                        ?>
                                    </select>
                                    <label for="fechaSolicitud">Fecha de Solicitud:</label>
                                    <input type="date" id="fechaSolicitud" name="fechaSolicitud" class="form-control" required>

                                    <label for="ubicacionID" class="form-label">Ubicación ID:</label>
                                    <select name="ubicacionID" class="form-control" required>
                                        <?php
                                        function obtenerUbicacionesActivas($pdo)
                                        {
                                            $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si';";
                                            $stmt = $pdo->query($sql);
                                            return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        }
                                        $ubicaciones = obtenerUbicacionesActivas($pdo);
                                        foreach ($ubicaciones as $ubicacion) {
                                            echo "<option value=\"{$ubicacion['ubicacionID']}\">{$ubicacion['lugar']}</option>";
                                        }
                                        ?>
                                    </select>

                                    <label for="formadepago" class="form-label">Forma de pago:</label>
                                    <select name="formadepago" class="form-control" required>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select><br>
                                </div>
                                <input type="submit" class="btn btn-dark" value="Registrar Inspección">
                            </form>
                        </div>
                    <?php endif; ?>



                    <script src="app.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const modalSuccess = new bootstrap.Modal(document.getElementById('modalSuccess'));
                            if (document.getElementById('modalSuccess')) {
                                modalSuccess.show();
                            }
                        });

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
                            const continuidad = document.getElementById('continuidad').checked;

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
                        $(document).ready(function() {
                            if ($('#staticBackdrop').length) {
                                $('#staticBackdrop').modal('show');
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>

</html>