<?php
session_start();
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : "";
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita y Crear Orden de Trabajo</title>
</head>

<body class="Body_citas">
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2> Crear Orden de Trabajo SIN CITA</h2>
                <div class="form-container">
                    <form action="crear_orden_sin_cita2.php" method="post" id="formCita" novalidate autocomplete="off">
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
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Registro exitoso!</h1>
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
                        <!-- Formulario de Cita -->
                        <div class="mb-3">
                            <label for="clienteID" class="form-label">Ingrese un cliente:</label>
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." value="<?php echo isset($_SESSION['form_data']['campo']) ? htmlspecialchars($_SESSION['form_data']['campo']) : ''; ?>" required>
                            <ul id="lista" class="list-group lista" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID" value="<?php echo isset($_SESSION['form_data']['clienteID']) ? htmlspecialchars($_SESSION['form_data']['clienteID']) : ''; ?>">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="mb-3">
                            <label for="vehiculoSeleccionado" class="form-label">Seleccione un vehiculo:</label>
                            <ul id="lista-vehiculos" class="list-group" style="display: none;"></ul>
                            <input type="hidden" id="vehiculoID" name="vehiculoID" value="<?php echo isset($_SESSION['form_data']['vehiculoID']) ? htmlspecialchars($_SESSION['form_data']['vehiculoID']) : ''; ?>">
                            <input type="text" class="form-control" id="vehiculoSeleccionado" placeholder="Vehículo seleccionado" readonly value="<?php echo isset($_SESSION['form_data']['vehiculoSeleccionado']) ? htmlspecialchars($_SESSION['form_data']['vehiculoSeleccionado']) : ''; ?>">
                            <div class="invalid-feedback">Debes seleccionar un vehículo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="servicioSolicitado" class="form-label">Servicio Solicitado:</label>
                            <input type="text" class="form-control" id="servicioSolicitado" name="servicioSolicitado" value="<?php echo isset($_SESSION['form_data']['servicioSolicitado']) ? htmlspecialchars($_SESSION['form_data']['servicioSolicitado']) : ''; ?>" required>
                            <div class="invalid-feedback">Debes ingresar el servicio solicitado.</div>
                        </div>

                        <!-- Formulario de Orden de Trabajo -->
                        <div class="mb-3">
                            <label for="costoManoObra" class="form-label">Costo de Mano de Obra:</label>
                            <input type="number" step="0.01" class="form-control" id="costoManoObra" name="costoManoObra" value="<?php echo isset($_SESSION['form_data']['costoManoObra']) ? htmlspecialchars($_SESSION['form_data']['costoManoObra']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="costoRefacciones" class="form-label">Costo de Refacciones:</label>
                            <input type="number" step="0.01" class="form-control" id="costoRefacciones" name="costoRefacciones" value="<?php echo isset($_SESSION['form_data']['costoRefacciones']) ? htmlspecialchars($_SESSION['form_data']['costoRefacciones']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="anticipo" class="form-label">Anticipo:</label>
                            <input type="number" step="0.01" class="form-control" id="anticipo" name="anticipo" value="<?php echo isset($_SESSION['form_data']['anticipo']) ? htmlspecialchars($_SESSION['form_data']['anticipo']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="empleado" class="form-label">Empleado:</label>
                            <select name="empleado" class="form-control" required>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();
                                function obtenerEmpleadosDisponibles($pdo)
                                {
                                    $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
                                        FROM EMPLEADOS 
                                        JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID
                                        WHERE EMPLEADOS.tipo != 'administrativo'";
                                    $stmt = $pdo->query($sql);
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                                $empleados = obtenerEmpleadosDisponibles($pdo);
                                foreach ($empleados as $empleado) {
                                    $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                                    $selected = isset($_SESSION['form_data']['empleado']) && $_SESSION['form_data']['empleado'] == $empleado['empleadoID'] ? 'selected' : '';
                                    echo "<option value=\"{$empleado['empleadoID']}\" $selected>{$nombreCompleto}</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Debes seleccionar un empleado.</div>
                        </div>
                        <div class="mb-3">
                            <label for="ubicacionID" class="form-label">Ubicación:</label>
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
                                    $selected = isset($_SESSION['form_data']['ubicacionID']) && $_SESSION['form_data']['ubicacionID'] == $ubicacion['ubicacionID'] ? 'selected' : '';
                                    echo "<option value=\"{$ubicacion['ubicacionID']}\" $selected>{$ubicacion['lugar']}</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Debes seleccionar una ubicación.</div>
                        </div>

                        <div class="mb-3">
                            <label for="formadepago" class="form-label">Forma de pago:</label>
                            <select name="formadepago" class="form-control" required>
                                <option value="efectivo" <?php echo isset($_SESSION['form_data']['formadepago']) && $_SESSION['form_data']['formadepago'] == 'efectivo' ? 'selected' : ''; ?>>Efectivo</option>
                                <option value="tarjeta" <?php echo isset($_SESSION['form_data']['formadepago']) && $_SESSION['form_data']['formadepago'] == 'tarjeta' ? 'selected' : ''; ?>>Tarjeta</option>
                                <option value="transferencia" <?php echo isset($_SESSION['form_data']['formadepago']) && $_SESSION['form_data']['formadepago'] == 'transferencia' ? 'selected' : ''; ?>>Transferencia</option>
                            </select>
                            <div class="invalid-feedback">Debes seleccionar una forma de pago.</div>
                        </div>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Registrar Cita y Crear Orden de Trabajo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });

        document.getElementById('formCita').addEventListener('submit', function(event) {
            let valid = true;

            const campo = document.getElementById('campo').value;
            const costoManoObra = document.getElementById('costoManoObra').value;
            const costoRefacciones = document.getElementById('costoRefacciones').value;
            const anticipo = parseFloat(document.getElementById('anticipo').value);

            if (/\d/.test(campo)) {
                document.getElementById('campo').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('campo').classList.remove('is-invalid');
            }

            if (isNaN(costoManoObra) || costoManoObra < 0) {
                document.getElementById('costoManoObra').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('costoManoObra').classList.remove('is-invalid');
            }

            if (isNaN(costoRefacciones) || costoRefacciones < 0) {
                document.getElementById('costoRefacciones').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('costoRefacciones').classList.remove('is-invalid');
            }

            if (isNaN(anticipo) || anticipo < 0) {
                document.getElementById('anticipo').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('anticipo').classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });

        function validarLetras(event) {
            const input = event.target;
            input.value = input.value.replace(/[^a-zA-Z]/g, '');
        }

        function validarNumeros(event) {
            const input = event.target;
            input.value = input.value.replace(/[^0-9.]/g, '');
        }

        document.getElementById('campo').addEventListener('input', validarLetras);
        document.getElementById('costoManoObra').addEventListener('input', validarNumeros);
        document.getElementById('costoRefacciones').addEventListener('input', validarNumeros);
        document.getElementById('anticipo').addEventListener('input', validarNumeros);

        document.getElementById('anticipo').addEventListener('input', function(event) {
            var value = parseFloat(event.target.value);
            if (value < 0) {
                event.target.value = '';
                alert('El anticipo no puede ser negativo.');
            }
        });
    </script>
</body>
</html>
