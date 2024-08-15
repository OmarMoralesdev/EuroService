<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nómina Semanal</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Registrar o Actualizar Nómina Semanal</h2>
                <div class="form-container">

                    <form action="procesar_nomina.php" method="post">
                        <label for="fecha">Fecha de Inicio (debe ser un lunes):</label>
                        <input type="week" id="fecha" name="fecha" class="form-control" required>
                        <br>
                        <input type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto" class="form-control"value="Procesar Nómina">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>