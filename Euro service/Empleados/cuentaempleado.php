<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Registro de Cuenta de Administrador</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <h2>Registro de Cuenta de Administrador</h2>
            <form action="registrocuena.php" method="POST">
                <label for="empleado" class="form-label">Empleado:</label>
                <select name="empleado" class="form-control" required>
                    <?php
                    require '../includes/db.php';
                    $con = new Database();
                    $pdo = $con->conectar();
                    
                    function obtenerEmpleadosDisponibles($pdo)
                    {
                        $sql = "SELECT EMPLEADOS.personaID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
                                FROM EMPLEADOS 
                                JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID
                                WHERE EMPLEADOS.tipo = 'administrativo'";
                        $stmt = $pdo->query($sql);
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    $empleados = obtenerEmpleadosDisponibles($pdo);
                    foreach ($empleados as $empleado) {
                        $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                        echo "<option value=\"{$empleado['personaID']}\">{$nombreCompleto}</option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">Debes seleccionar un empleado.</div>

                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" class="form-control" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" class="form-control" required><br><br>
                <label for="confirm_password">Confirmar Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required><br><br>
                <input type="submit" value="Registrar">
            </form>
        </div>
    </div>
</body>

</html>
