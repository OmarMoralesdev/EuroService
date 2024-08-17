<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Actualizar Bonos y Rebajas</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Actualizar Bonos y Rebajas Adicionales por Empleado</h2>
                <div class="form-container">
                    <form action="actualizar_nomina.php" method="post">
                    <label for="empleadoID" class="form-label">Empleado:</label>
                            <select name="empleadoID" class="form-control" required>
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
                                    echo "<option value=\"{$empleado['empleadoID']}\">{$nombreCompleto}</option>";
                                }
                                ?>
                            </select>
                        <br>
                        <label for="fecha_inicio">Fecha de Inicio (debe ser un lunes):</label>
                        <input type="week" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        <br>
                        <label for="bonos">Bonos:</label>
                        <input type="number" id="bonos" name="bonos" step="0.01" class="form-control" required>
                        <br>
                        <label for="rebajas_adicionales">Rebajas Adicionales:</label>
                        <input type="number" id="rebajas_adicionales" name="rebajas_adicionales" step="0.01" class="form-control" required>
                        <br>
                        <input type="submit" class="form-control" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto" value="Actualizar">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>