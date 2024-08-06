<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Inspección</title>
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
            <div class="container mt-4">
                <h2>Registrar Inspección</h2>
                <div class="form-container">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='modalSuccess' tabindex='-1' aria-labelledby='modalSuccessLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='modalSuccessLabel'>Inspección registrada</h5>
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

                    <form id="formInspeccion" action="" method="POST" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group lista"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="mb-3">
                            <label for="vehiculoSeleccionado" class="form-label">Seleccione un vehículo:</label>
                            <input type="text" class="form-control" id="vehiculoSeleccionado" readonly>
                            <ul id="lista-vehiculos" class="list-group lista"></ul>
                            <input type="hidden" id="vehiculoID" name="vehiculoID">
                            <div class="invalid-feedback">Debes seleccionar un vehículo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="empleado" class="form-label">Empleado:</label>
                            <select name="empleadoID" id="empleado" class="form-control" required>
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
                        </div>

                        <div class="mb-3">
                            <label for="fechaSolicitud" class="form-label">Fecha de Solicitud:</label>
                            <input type="date" id="fechaSolicitud" name="fechaSolicitud" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="ubicacionID" class="form-label">Ubicación de Vehículo:</label>
                            <select name="ubicacionID" id="ubicacionID" class="form-control" required>
                                <?php
                                // Función para obtener las ubicaciones activas en la base de datos
                                function obtenerUbicacionesActivas($pdo)
                                {
                                    // Seleccionar todas las ubicaciones activas
                                    $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si';";
                                    $stmt = $pdo->query($sql);
                                    // Retornar todas las ubicaciones activas en un array asociativo
                                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                                // Obtener todas las ubicaciones activas en la base de datos y mostrarlas en un select HTML
                                $ubicaciones = obtenerUbicacionesActivas($pdo);
                                foreach ($ubicaciones as $ubicacion) {
                                    // Mostrar cada ubicación en un option del select HTML 
                                    echo "<option value=\"{$ubicacion['ubicacionID']}\">{$ubicacion['lugar']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="formadepago" class="form-label">Forma de Pago:</label>
                            <select name="formadepago" id="formadepago" class="form-control" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                            <div class="invalid-feedback">Debes seleccionar una forma de pago.</div>
                        </div>

                        <input type="submit" class="btn btn-dark" value="Registrar Inspección">
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Código JS para manejar la interacción con el usuario y AJAX
            const modalSuccess = new bootstrap.Modal(document.getElementById('modalSuccess'));
            if (document.getElementById('modalSuccess')) {
                modalSuccess.show();
            }
        });

        document.getElementById('formInspeccion').addEventListener('submit', function(event) {
            // Validación personalizada si es necesario
        });
    </script>
</body>

</html>