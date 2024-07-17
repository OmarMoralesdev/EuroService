<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Control de Asistencia</title>
</head>
<style>

</style>
<body>
<div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
        <div class="container">
            <h2>CONTROL DE ASISTENCIA</h2>
                <div class="form-container">
            <form action="registrar_asistencia.php" method="post">
                <label for="empleado" class="form-label">Selecciona un empleado:</label>
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
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" class="form-control" required><br><br>

                <label for="hora_entrada">Hora de Entrada:</label>
                <input type="time" id="hora_entrada" name="hora_entrada" class="form-control" required><br><br>

                <label for="hora_salida">Hora de Salida:</label>
                <input type="time" id="hora_salida" class="form-control"><br><br>

                <input type="submit" value="Registrar">
            </form>
        </div>
        </div>
        </div>
</div>
</body>

</html>
