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
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">

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

<form id="formInspeccion" action="soloinspeccion.php" method="POST" autocomplete="off" novalidate>
    <div class="mb-3">
        <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
        <ul id="lista" class="list-group lista"></ul>
        <input type="hidden" id="clienteID" name="clienteID">
        <div class="invalid-feedback">Debes seleccionar un cliente.</div>
    </div>
    <div class="mb-3">
        <label for="vehiculoSeleccionado" class="form-label">Seleccione un vehículo:</label>
        <input type="text" class="form-control" id="vehiculoSeleccionado" readonly
               value="<?php echo isset($_SESSION['formData']['vehiculoMarca']) ? $_SESSION['formData']['vehiculoMarca'] . ' ' . $_SESSION['formData']['vehiculoModelo'] . ' ' . $_SESSION['formData']['vehiculoAnio'] : ''; ?>">
        <ul id="lista-vehiculos" class="list-group lista"></ul>
        <input type="hidden" id="vehiculoID" name="vehiculoID" value="<?php echo isset($_SESSION['formData']['vehiculoID']) ? $_SESSION['formData']['vehiculoID'] : ''; ?>">
        <div class="invalid-feedback">Debes seleccionar un vehículo.</div>
    </div>

    <div class="mb-3">
        <label for="diagnostico" class="form-label">Ingresar Diagnóstico:</label>
        <input type="text" class="form-control" id="diagnostico" name="diagnostico"
               value="<?php echo isset($_SESSION['formData']['diagnostico']) ? htmlspecialchars($_SESSION['formData']['diagnostico'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
        <div class="invalid-feedback">Debes ingresar el diagnóstico.</div>
    </div>

    <div class="mb-3">
        <label for="empleado" class="form-label">Empleado:</label>
        <select name="empleadoID" id="empleado" class="form-control" required>
            <?php
            // Función para obtener empleados disponibles
            function obtenerEmpleadosDisponibles($pdo) {
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
                $selected = (isset($_SESSION['formData']['empleadoID']) && $_SESSION['formData']['empleadoID'] == $empleado['empleadoID']) ? 'selected' : '';
                echo "<option value=\"{$empleado['empleadoID']}\" $selected>{$nombreCompleto}</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="ubicacionID" class="form-label">Ubicación de Vehículo:</label>
        <select name="ubicacionID" id="ubicacionID" class="form-control" required>
            <?php
            // Función para obtener ubicaciones activas
            function obtenerUbicacionesActivas($pdo) {
                $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si';";
                $stmt = $pdo->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $ubicaciones = obtenerUbicacionesActivas($pdo);
            foreach ($ubicaciones as $ubicacion) {
                $selected = (isset($_SESSION['formData']['ubicacionID']) && $_SESSION['formData']['ubicacionID'] == $ubicacion['ubicacionID']) ? 'selected' : '';
                echo "<option value=\"{$ubicacion['ubicacionID']}\" $selected>{$ubicacion['lugar']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="formadepago" class="form-label">Forma de Pago:</label>
        <select name="formadepago" id="formadepago" class="form-control" required>
            <option value="efectivo" <?php echo (isset($_SESSION['formData']['formadepago']) && $_SESSION['formData']['formadepago'] == 'efectivo') ? 'selected' : ''; ?>>Efectivo</option>
            <option value="tarjeta" <?php echo (isset($_SESSION['formData']['formadepago']) && $_SESSION['formData']['formadepago'] == 'tarjeta') ? 'selected' : ''; ?>>Tarjeta</option>
            <option value="transferencia" <?php echo (isset($_SESSION['formData']['formadepago']) && $_SESSION['formData']['formadepago'] == 'transferencia') ? 'selected' : ''; ?>>Transferencia</option>
        </select>
        <div class="invalid-feedback">Debes seleccionar una forma de pago.</div>
    </div>

    <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Registrar Inspección</button>
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
    <script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });
    </script>

</body>

</html>