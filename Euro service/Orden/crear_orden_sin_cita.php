<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita y Crear Orden de Trabajo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body class="Body_citas">
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Registrar Cita y Crear Orden de Trabajo</h2>
                <form action="crear_orden_sin_cita2.php" method="post" id="formCita" novalidate autocomplete="off">
                    <!-- Formulario de Cita -->
                    <div class="mb-3">
                    <label for="clienteID" class="form-label">Ingrese un cliente:</label>
                        <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                        <ul id="lista" class="list-group" style="display: none;"></ul>
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
                    </div>
                    <div class="mb-3">
                        <label for="costoRefacciones" class="form-label">Costo de Refacciones:</label>
                        <input type="number" step="0.01" class="form-control" id="costoRefacciones" name="costoRefacciones" required>
                        <div class="invalid-feedback">Debes ingresar el costo de las refacciones.</div>
                    </div>
                    <div class="mb-3">
                        <label for="empleado" class="form-label">Empleado ID:</label>
                        <select name="empleado" class="form-control" required>
                            <?php
                            require 'conexion.php';
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
                        <label for="ubicacionID" class="form-label">Ubicación ID:</label>
                        <select name="ubicacionID" class="form-control" required>
                            <?php
                            function ubicaciones($pdo)
                            {
                                $sql = "SELECT * FROM UBICACIONES WHERE activo = 'si';";
                                $stmt = $pdo->query($sql);
                                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }
                            $filas = ubicaciones($pdo);
                            if (empty($filas)) {
                                echo "<option value=''>No hay ubicaciones disponibles</option>";
                            } else {
                                foreach ($filas as $resultado) {
                                    echo "<option value=\"{$resultado['ubicacionID']}\">{$resultado['lugar']}</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Debes seleccionar una ubicación.</div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Registrar Cita y Crear Orden de Trabajo</button>
                </form>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
</body>
</html>
