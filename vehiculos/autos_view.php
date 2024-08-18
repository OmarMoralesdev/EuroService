<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
    <head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehículo</title>
    <style>
        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2>REGISTRAR VEHÍCULO</h2>
                <div class="form-container">

                    <?php
                    // Mostrar mensajes de error o éxito en la operación de registro de vehículo si los hay en la sesión de usuario actual
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Vehículo registrado!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='alert alert-success' role='alert'>{$_SESSION['bien']}</div>
                                        Presiona siguiente para agendar su cita
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                        <a href='../CItas/seleccionar_cita_view.php' type='button' class='btn btn-dark'>Siguiente</a>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        unset($_SESSION['bien']);
                    }

                    $formValues = isset($_SESSION['form_values']) ? $_SESSION['form_values'] : array();
                    ?>

                    <div class="d-flex flex-column flex-md-row gap-2">
                        <input type="radio" class="btn-check" name="options-base" id="option1" autocomplete="off" checked>
                        <label class="btn" for="option1">CON SEGUIMIENTO</label>
                        <input type="radio" class="btn-check" name="options-base" id="option2" autocomplete="off">
                        <label class="btn" for="option2">SIN SEGUIMIENTO</label>
                    </div>

                    <br>

                    <script>
                        document.getElementById('option2').addEventListener('change', function() {
                            if (this.checked) {
                                window.location.href = './inspeccion_view.php'; // Reemplaza con la URL deseada
                            }
                        });
                    </script>

                    <form id="formCita" action="autos.php" method="POST" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required value="<?= htmlspecialchars($formValues['campo'] ?? '') ?>">
                            <ul id="lista" class="list-group lista" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID" value="<?= htmlspecialchars($formValues['clienteID'] ?? '') ?>">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="form-group">
                            <label for="marca">Marca:</label>
                            <input type="text" id="marca" name="marca" maxlength="30" class="form-control" placeholder="Introduce la marca del vehículo" required value="<?= htmlspecialchars($formValues['marca'] ?? '') ?>">

                            <label for="modelo">Modelo:</label>
                            <input type="text" id="modelo" name="modelo" maxlength="30" class="form-control" placeholder="Introduce el modelo del vehículo" required value="<?= htmlspecialchars($formValues['modelo'] ?? '') ?>">

                            <label for="anio">Año:</label>
                            <input class="form-control" type="number" id="anio" name="anio" min="1886" max="<?= date('Y') ?>" placeholder="Introduce el año del vehículo" required maxlength="4" value="<?= htmlspecialchars($formValues['anio'] ?? '') ?>">

                            <label for="color">Color:</label>
                            <input type="text" id="color" name="color" maxlength="33" class="form-control" placeholder="Introduce el color del vehículo" required value="<?= htmlspecialchars($formValues['color'] ?? '') ?>">

                            <label for="kilometraje">Kilometraje:</label>
                            <input type="text" id="kilometraje" name="kilometraje" maxlength="8" class="form-control" placeholder="Introduce el kilometraje del vehículo" required value="<?= htmlspecialchars($formValues['kilometraje'] ?? '') ?>">

                            <label for="placas">Placas:</label>
                            <input type="text" id="placas" name="placas" maxlength="7" class="form-control" placeholder="Introduce las placas del vehículo" required value="<?= htmlspecialchars($formValues['placas'] ?? '') ?>">

                            <label for="vin">VIN:</label>
                            <input type="text" id="vin" name="vin" maxlength="17" class="form-control" placeholder="Introduce el VIN del vehículo" required value="<?= htmlspecialchars($formValues['vin'] ?? '') ?>">

                            <br>
                            <button type="submit" value="Registrar Vehículo" class="btn btn-dark d-grid gap-2 col-6 mx-auto">Registrar</button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const modalSuccess = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                            if (document.getElementById('staticBackdrop')) {
                                modalSuccess.show();
                            }
                        });

                        document.getElementById('formCita').addEventListener('submit', function(event) {
                            let valid = true;
                            const currentYear = new Date().getFullYear();
                            const marca = document.getElementById('marca').value;
                            const campo = document.getElementById('campo').value;
                            const color = document.getElementById('color').value;
                            const anio = document.getElementById('anio').value;
                            const placas = document.getElementById('placas').value;
                            const vin = document.getElementById('vin').value;

                             // Validar marca
                             if (marca.trim() === '' || /\d/.test(marca)) {
                                document.getElementById('marca').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('marca').classList.remove('is-invalid');
                            }

                            // Validar campo
                            if (/\d/.test(campo)) {
                                document.getElementById('campo').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('campo').classList.remove('is-invalid');
                            }

                            // Validar color
                            if (color.trim() === '' || /\d/.test(color)) {
                                document.getElementById('color').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('color').classList.remove('is-invalid');
                            }


                            // Validar año
                            if (anio < 1886 || anio > currentYear || isNaN(anio) || anio.toString().length !== 4) {
                                document.getElementById('anio').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('anio').classList.remove('is-invalid');
                            }

                            // Validar placas
                           if (!/^[A-Za-z0-9\- ]{1,10}$/.test(placas)) {
                                document.getElementById('placas').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('placas').classList.remove('is-invalid');
                            }

                             // Validar VIN: debe tener exactamente 17 caracteres y solo permitir letras y números
        if (vin.length !== 17 || /[^A-Za-z0-9]/.test(vin)) {
            document.getElementById('vin').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('vin').classList.remove('is-invalid');
        }

                            if (!valid) {
                                event.preventDefault();
                            }
                        });

                       function validarLetras(event) {
                            const input = event.target;
                            input.value = input.value.replace(/[^a-zA-Z\s]/g, ''); // Permite letras y espacios
                        }

                        function validarAño(event) {
                            const input = event.target;
                            input.value = input.value.replace(/[^0-9]/g, '');
                            if (input.value.length > 4) {
                                input.value = input.value.slice(0, 4);
                            }
                        }
                        
                        function validarPlacas(event) {
                            const input = event.target;
                            input.value = input.value.replace(/[^A-Za-z0-9\-]/g, ''); // placas
                        }

                        function validarKil(event) {
                            const input = event.target;
                            input.value = input.value.replace(/[^0-9]/g, '');
                            if (input.value.length > 9) {
                                input.value = input.value.slice(0, 9);
                            }
                        }                        
    function validarVin(event) {
        const input = event.target;
        input.value = input.value.replace(/[^A-Za-z0-9]/g, ''); 
    }


                        document.getElementById('campo').addEventListener('input', validarLetras);
                        document.getElementById('marca').addEventListener('input', validarLetras);
                        document.getElementById('color').addEventListener('input', validarLetras);
                        document.getElementById('placas').addEventListener('input', validarPlacas);
                        document.getElementById('kilometraje').addEventListener('input', validarKil);
                        document.getElementById('anio').addEventListener('input', validarAño);
                        document.getElementById('vin').addEventListener('input', validarVin);
                    </script>

                    <script src="app.js"></script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
