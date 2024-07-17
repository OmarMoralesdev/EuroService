<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita</title>
</head>
<?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
        }
        ?>
<body class="Body_citas">
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <div class="form-container">
                <h2>Registrar Cita</h2>
                <br>            
                <form action="../CItas/registrar_cita.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                        <ul id="lista" class="list-group" style="display: none;"></ul>
                        <input type="hidden" id="clienteID" name="clienteID">
                        <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                    </div>
                    <div class="mb-3">
                    <label for="vehiculoSeleccionado" class="form-label">Seleccione un vehiculo:</label>
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
    
    <script src="../CItas/app.js"></script>
    <script>
         document.getElementById('formCita').addEventListener('submit', function(event) {
            if (!document.getElementById('clienteID').value) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('campo').classList.add('is-invalid');
            }
            this.classList.add('was-validated');
        });
    </script>   
</body>
</html>
