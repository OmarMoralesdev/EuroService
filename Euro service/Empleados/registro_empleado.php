<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO CLIENTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .form-group {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container" style="width: 90%; margin: auto; background-color: #EBEBEB; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                <h2>Registrar Cliente</h2>
                <br>
                <form method="post" action="registrar_empleado.php">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido_paterno">Apellido Paterno:</label>
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno:</label>
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                    </div>
                    <label for="nombre">Alias:</label>
                    <input type="text" id="nombre" name="alias" class="form-control" required><br><br>
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" class="form-control" required>
                        <option value="master">Master</option>
                        <option value="intermedio">Intermedio</option>
                        <option value="ayudante">Ayudante</option>
                        <option value="administrativo">administrativo</option>
                    </select><br><br>
                    <br>
                    <button type="submit" class="btn btn-dark w-100 ">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>