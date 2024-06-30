<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 50px auto;
        }
        h2 {
            text-align: center;
            color: #000;
        }
    </style>
</head>
<body>  
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
    <div class="container">
        <h2>Registrar Cita</h2>
        <form action="../templates/CItas/registrar_cita.php" method="post" id="formCita" novalidate>
            <div class="mb-3">
                <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                <ul id="lista" class="list-group" style="display: none;"></ul>
                <input type="hidden" id="clienteID" name="clienteID">
                <div class="invalid-feedback">Debes seleccionar un cliente.</div>
            </div>
            <div class="mb-3">
                <ul id="lista-vehiculos" class="list-group" style="display: none;"></ul>
                <input type="hidden" id="vehiculoID" name="vehiculoID">
                <input type="text" class="form-control" id="vehiculoSeleccionado" readonly>
                <div class="invalid-feedback">Debes seleccionar un vehículo.</div>
            </div>
            <div class="mb-3">
                <label for="servicioSolicitado" class="form-label">Servicio Solicitado:</label>
                <input type="text" class="form-control" id="servicioSolicitado" name="servicioSolicitado" required>
                <div class="invalid-feedback">Debes ingresar el servicio solicitado.</div>
            </div>
            <div class="mb-3">
                <label for="fecha_cita" class="form-label">Fecha de la Cita:</label>
                <input type="datetime-local" class="form-control" id="fecha_cita" name="fecha_cita" required>
                <div class="invalid-feedback">Debes seleccionar la fecha y hora de la cita.</div>
            </div>
            <button type="submit" class="btn btn-dark w-100">Registrar Cita</button>
        </form>
    </div>
    </div>
    </div>
   
    <script src="../templates/CItas/app.js"></script>
    <script>
        // Validación personalizada para verificar si se ha seleccionado cliente y vehículo
        document.getElementById('formCita').addEventListener('submit', function(event) {
            if (!document.getElementById('clienteID').value || !document.getElementById('vehiculoID').value) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('campo').classList.add('is-invalid');
                document.getElementById('vehiculoSeleccionado').classList.add('is-invalid');
            }
            this.classList.add('was-validated');
        });
    </script>
</body>
</html>
