<?php
session_start();
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : "";
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita y Crear Orden de Trabajo</title>
</head>

<body class="Body_citas">
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Registrar Cita y Crear Orden de Trabajo</h2>
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
                        <!-- Formulario de Cita -->
                        <div class="mb-3">
                            <label for="clienteID" class="form-label">Ingrese un cliente:</label>
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group lista" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="mb-3">
                            <label for="vehiculoSeleccionado" class="form-label">Seleccione un vehiculo:</label>
                            <ul id="lista-vehiculos" class="list-group" style="display: none;"></ul>
                            <input type="hidden" id="vehiculoID" name="vehiculoID">
                            <input type="text" class="form-control" id="vehiculoSeleccionado" placeholder="Vehículo seleccionado" readonly>
                            <div class="invalid-feedback">Debes seleccionar un vehículo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="servicioSolicitado" class="form-label">Servicio Solicitado:</label>
                            <input type="text" class="form-control" id="servicioSolicitado" name="servicioSolicitado" required>
                            <div class="invalid-feedback">Debes ingresar el servicio solicitado.</div>
                        </div>

                        <!-- Formulario de Orden de Trabajo -->
                        <div class="mb-3">
                            <label for="costoManoObra" class="form-label">Costo de Mano de Obra:</label>
                            <input type="number" step="0.01" class="form-control" id="costoManoObra" name="costoManoObra" required>
                            <div class="invalid-feedback">Debes ingresar el costo de mano de obra.</div>
                            <div class="form-text">Introduce el salario diario (no puede ser negativo).</div><br>
                        </div>
                        <div class="mb-3">
                            <label for="costoRefacciones" class="form-label">Costo de Refacciones:</label>
                            <input type="number" step="0.01" class="form-control" id="costoRefacciones" name="costoRefacciones" required>
                            <div class="invalid-feedback">Debes ingresar el costo de las refacciones.</div>
                            <div class="form-text">Introduce el salario diario (no puede ser negativo).</div><br>
                        </div>
                        <div class="mb-3">
                            <label for="anticipo" class="form-label">Anticipo:</label>
                            <input type="number" step="0.01" class="form-control" id="anticipo" name="anticipo" required>
                            <div class="invalid-feedback">Debes ingresar el anticipo.</div>
                            <div class="form-text">Introduce el salario diario (no puede ser negativo).</div><br>
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
                                    echo "<option value=\"{$ubicacion['ubicacionID']}\">{$ubicacion['lugar']}</option>";
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Debes seleccionar una ubicación.</div>
                        </div>
                      
                        <div class="mb-3">
                            <label for="formadepago" class="form-label">Forma de pago:</label>
                            <select name="formadepago" class="form-control" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function validateNonNegative(event) {
                var value = parseFloat(event.target.value);
                if (value < 0) {
                    event.target.value = '';
                    alert('El valor no puede ser negativo.');
                }
            }

            document.getElementById('costoManoObra').addEventListener('input', validateNonNegative);
            document.getElementById('costoRefacciones').addEventListener('input', validateNonNegative);
            document.getElementById('anticipo').addEventListener('input', validateNonNegative);
        });
    </script>
</body>

</html>