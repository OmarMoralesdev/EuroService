<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nueva Cita</title>
    <!-- Enlace a Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Registrar Nueva Cita</h2>
        <form action="../templates/citas.php" method="post">
            <div class="form-group">
                <label for="vehiculoID">ID del Vehículo:</label>
                <input type="text" class="form-control" id="vehiculoID" name="vehiculoID" required>
            </div>
            <div class="form-group">
                <label for="servicio">Servicio Solicitado:</label>
                <input type="text" class="form-control" id="servicio" name="servicio" required>
            </div>
            <div class="form-group">
                <label for="fecha_cita">Fecha de la Cita:</label>
                <input type="date" class="form-control" id="fecha_cita" name="fecha_cita" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Cita</button>
        </form>
    </div>

    <!-- Enlace a Bootstrap JS y dependencias de jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
