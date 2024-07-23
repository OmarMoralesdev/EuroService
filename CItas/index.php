<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita</title>
</head>
<style>
        .form-group {
            margin-bottom: 5px;
        }
    </style>
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
                <h2>REGISTRAR CITA</h2>
                <div class="form-container">
                    <br>
                    <form action="../CItas/registrar_cita.php" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>              
                        <div class="mb-3">
                            <label for="vehiculoSeleccionado" class="form-label">Seleccione un vehículo:</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="vehiculoSeleccionado" readonly>
                                <ul id="lista-vehiculos" class="list-group position-absolute" style="display: none;"></ul>
                            </div>
                            <input type="hidden" id="vehiculoID" name="vehiculoID">
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
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('fecha_cita');
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const minDate = `${year}-${month}-${day}T09:00`;
            const maxDate = `${year + 1}-${month}-${day}T17:00`;

            // Establecer el valor mínimo y máximo del Date Picker
            dateInput.min = minDate;
            dateInput.max = maxDate;

            dateInput.addEventListener('input', function() {
                const selectedDate = new Date(dateInput.value);
                const selectedHour = selectedDate.getHours();
                const selectedMinutes = selectedDate.getMinutes();

                if (selectedHour < 9 || (selectedHour >= 17 && selectedMinutes > 0)) {
                    dateInput.setCustomValidity('La hora debe estar dentro del horario laboral (09:00 - 17:00).');
                } else {
                    dateInput.setCustomValidity('');
                }
            });

            // Función para deshabilitar horas fuera del horario laboral
            dateInput.addEventListener('focus', function() {
                const calendar = document.querySelector('input[type="datetime-local"]');
                const options = calendar.querySelectorAll('option');
                options.forEach(option => {
                    const date = new Date(option.value);
                    const hour = date.getHours();
                    if (hour < 9 || hour >= 17) {
                        option.disabled = true;
                    }
                });
            });
        });
    </script>
</body>

</html>