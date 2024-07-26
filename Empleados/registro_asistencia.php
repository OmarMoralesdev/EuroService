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
        .wrapper {
            display: flex;
            height: 100vh;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
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
                <br>
                <div class="invalid-feedback">Debes seleccionar un empleado.</div>
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" class="form-control" required><br>

                <label for="asistencia">Asistencia:</label>
                <select class="form-select" name="asistencia" id="asistencias" required>
                            <option value="">Selecciona una opción</option>
                            <option value="asistencia">Asistencia</option>
                            <option value="falta">Falta</option>
                            <option value="justificado">Justificado</option>
                            </select><br>

                <label for="hora_entrada" id="he">Hora de Entrada:</label>
                <input type="time" id="hora_entrada" name="hora_entrada" class="form-control" required><br>

                <label for="hora_salida" id="hs">Hora de Salida:</label>
                <input type="time" id="hora_salida" name="hora_salida" class="form-control" required><br>

                <input type="submit" value="Registrar">
            </form>
        </div>
        </div>
        </div>
</div>
<script>
    document.getElementById('asistencias').addEventListener('change', function()
    {
        var selectedOption = this.value;
        switch (selectedOption)
        {
            case 'asistencia':
                document.getElementById('hora_entrada').style.display = 'block';
                document.getElementById('hora_salida').style.display = 'block';
                document.getElementById('he').style.display = 'block';
                document.getElementById('hs').style.display = 'block';
                break;
            case 'falta':
                document.getElementById('hora_entrada').style.display = 'none';
                document.getElementById('hora_salida').style.display = 'none';
                document.getElementById('he').style.display = 'none';
                document.getElementById('hs').style.display = 'none';
                document.getElementById('hora_entrada').removeAttribute('required');
                document.getElementById('hora_salida').removeAttribute('required');
                document.getElementById('hora_entrada').value='00:00:00';
                document.getElementById('hora_salida').value='00:00:00';
                break;
            case 'justificado':
                document.getElementById('hora_entrada').style.display = 'none';
                document.getElementById('hora_salida').style.display = 'none';
                document.getElementById('he').style.display = 'none';
                document.getElementById('hs').style.display = 'none';
                document.getElementById('hora_entrada').removeAttribute('required');
                document.getElementById('hora_salida').removeAttribute('required');
                document.getElementById('hora_entrada').value='00:00:00';
                document.getElementById('hora_salida').value='00:00:00';
                break;
        }
    });
</script>

</body>

</html>
