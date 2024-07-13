<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia</title>
</head>

<body>
    <h1>Control de Asistencia</h1>
    <form action="registrar_asistencia.php" method="post">
        <div class="mb-3">
            <label for="empleado" class="form-label">Empleado ID:</label>
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

        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required><br><br>

        <label for="hora_entrada">Hora de Entrada:</label>
        <input type="time" id="hora_entrada" name="hora_entrada" required><br><br>

        <label for="hora_salida">Hora de Salida:</label>
        <input type="time" id="hora_salida"><br><br>

        <input type="submit" value="Registrar">
    </form>

</body>

</html>