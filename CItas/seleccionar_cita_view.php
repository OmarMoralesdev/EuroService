<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
        <link rel="icon" type="image/x-icon" href="../img/incono.svg">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cita</title>
    <style>
        .form-group {
            margin-bottom: 5px;
        }

        .lista {
            display: none;
        }
    </style>
</head>

<body class="Body_citas">
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REGISTRAR CITA</h2>
                <div class="form-container">
                    <br>
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Cita registrada con exito!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='alert alert-success' role='alert'>{$_SESSION['bien']}</div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        unset($_SESSION['bien']);
                    }
                    ?>

                    <form action="registrar_cita.php" method="POST" id="x" autocomplete="off">
                        <div class="mb-3">
                            <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group lista"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="mb-3">
                            <label for="vehiculoSeleccionado" class="form-label">Seleccione un vehículo:</label>
                            <input type="text" class="form-control" id="vehiculoSeleccionado" readonly>
                            <ul id="lista-vehiculos" class="list-group lista"></ul>
                            <input type="hidden" id="vehiculoID" name="vehiculoID">
                            <div class="invalid-feedback">Debes seleccionar un vehículo.</div>
                        </div>

                        <div class="mb-3">
                            <label for="servicioSolicitado" class="form-label">Servicio Solicitado:</label>
                            <input type="text" class="form-control" id="servicioSolicitado" name="servicioSolicitado" required>
                            <div class="invalid-feedback">Debes ingresar el servicio solicitado.</div>
                        </div>
                           <!-- Formulario de Orden de Trabajo -->
                           <div class="mb-3">
                            <label for="costoManoObra" class="form-label">Costo de Mano de Obra:</label>
                            <input type="number" step="0.01" class="form-control" id="costoManoObra" name="costoManoObra" required>
                        </div>
                        <div class="mb-3">
                            <label for="costoRefacciones" class="form-label">Costo de Refacciones:</label>
                            <input type="number" step="0.01" class="form-control" id="costoRefacciones" name="costoRefacciones" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_cita" class="form-label">Fecha de la Cita:</label>
                            <input type="datetime-local" class="form-control" id="fecha_cita" name="fecha_cita" required>
                            <div class="invalid-feedback">Debes seleccionar la fecha y hora de la cita.</div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Registrar Cita</button>
                    </form>
                </div>
            </div>
        </div>

        <script src="app.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dateInput = document.getElementById('fecha_cita');
                const today = new Date();
                const twelveHoursLater = new Date(today);
                twelveHoursLater.setHours(today.getHours() + 12); // 12 horas después

                const year = twelveHoursLater.getFullYear();
                const month = String(twelveHoursLater.getMonth() + 1).padStart(2, '0');
                const day = String(twelveHoursLater.getDate()).padStart(2, '0');
                const hours = String(twelveHoursLater.getHours()).padStart(2, '0');
                const minutes = String(twelveHoursLater.getMinutes()).padStart(2, '0');
                const minDate = `${year}-${month}-${day}T${hours}:${minutes}`;
                const maxDate = `${year + 1}-${month}-${day}T17:00`;

                // Establecer el valor mínimo y máximo del Date Picker
                dateInput.min = minDate;
                dateInput.max = maxDate;

                dateInput.addEventListener('input', function() {
                    const selectedDate = new Date(dateInput.value);
                    const selectedHour = selectedDate.getHours();
                    const selectedMinutes = selectedDate.getMinutes();

                    if (selectedDate < twelveHoursLater) {
                        dateInput.setCustomValidity('La fecha debe ser al menos 12 horas después.');
                    } else if (selectedHour < 9 || (selectedHour >= 17 && selectedMinutes > 0)) {
                        dateInput.setCustomValidity('La hora debe estar dentro del horario laboral (09:00 - 17:00).');
                    } else {
                        dateInput.setCustomValidity('');
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                if ($('#staticBackdrop').length) {
                    $('#staticBackdrop').modal('show');
                }
            });
        </script>

    </div>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                let isValid = true;

                // Validar el campo del cliente
                const clienteCampo = $('#campo').val().trim();
                if (clienteCampo === '') {
                    $('#campo').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#campo').removeClass('is-invalid');
                }

                // Validar el campo del vehículo
                const vehiculoCampo = $('#vehiculoSeleccionado').val().trim();
                if (vehiculoCampo === '') {
                    $('#vehiculoSeleccionado').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#vehiculoSeleccionado').removeClass('is-invalid');
                }

                // Validar el servicio solicitado
                const servicioCampo = $('#servicioSolicitado').val().trim();
                if (servicioCampo === '') {
                    $('#servicioSolicitado').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#servicioSolicitado').removeClass('is-invalid');
                }

                // Validar la fecha de la cita
                const fechaCampo = $('#fecha_cita').val().trim();
                if (fechaCampo === '') {
                    $('#fecha_cita').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#fecha_cita').removeClass('is-invalid');
                }

                if (!isValid) {
                    e.preventDefault(); // Prevenir el envío del formulario si hay errores
                }
            });
        });
    </script>
        <script>
            $(document).ready(function() {
                if ($('#staticBackdrop').length) {
                    $('#staticBackdrop').modal('show');
                }
            });

            document.getElementById('x').addEventListener('submit', function(event) {
                let valid = true;
                const campo = document.getElementById('nombre').value;
                // Validar cammpo
                if (/\d/.test(campo)) {
                    document.getElementById('campo').classList.add('is-invalid');
                    valid = false;
                } else {
                    document.getElementById('campo').classList.remove('is-invalid');
                }
                if (!valid) {
                    event.preventDefault();
                }
            });
            function validarLetras(event) {
                const input = event.target;
                input.value = input.value.replace(/[^a-zA-Z]/g, '');
            }
            document.getElementById('campo').addEventListener('input', validarLetras);

        </script>
         <script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });
//oednietneeeeeeeeeeeeeee
        document.getElementById('x').addEventListener('submit', function(event) {
            let valid = true;
            const campo = document.getElementById('nombre').value;
            // Validar cammpo
            if (/\d/.test(campo)) {
                document.getElementById('campo').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('campo').classList.remove('is-invalid');
            }
            if (!valid) {
                event.preventDefault();
            }
        });
        function validarLetras(event) {
            const input = event.target;
            input.value = input.value.replace(/[^a-zA-Z]/g, '');
        }
        document.getElementById('campo').addEventListener('input', validarLetras);

    </script>
    <script>
    $(document).ready(function() {
        // Verifica si el modal está presente
        if ($('#staticBackdrop').length) {
            // Muestra el modal
            $('#staticBackdrop').modal('show');
            // Cierra el modal después de 2 segundos (2000 milisegundos)
            setTimeout(function() {
                $('#staticBackdrop').modal('hide');
            }, 1000);
        }
    });
</script>

</body>

</html>