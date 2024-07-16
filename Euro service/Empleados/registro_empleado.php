<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO CLIENTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-2">
            <div class="container">
                <div class="form-container">
                    <h2>REGISTRAR EMPLEADO</h2>
                    <form method="post" action="registrar_empleado.php">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno:</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido_materno" class="form-label">Apellido Materno:</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                        </div>
                        <div class="mb-3">
                            <label for="alias" class="form-label">Alias:</label>
                            <input type="text" class="form-control" id="alias" name="alias" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo:</label>
                            <select id="tipo" name="tipo" class="form-select" required>
                                <option value="master">Master</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="ayudante">Ayudante</option>
                                <option value="administrativo">Administrativo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
