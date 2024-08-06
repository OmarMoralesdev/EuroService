<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
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
                        // Mostrar mensaje de error en un div con clase 'alert alert-danger' 
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    // Mostrar mensaje de éxito en un div con clase 'alert alert-success'
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Usuario registrado!</h1>
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




<input type="radio" class="btn-check" name="options-base" id="option1" autocomplete="off" checked>
<label class="btn" for="option1">CON SEGUIMINETO</label>


<input type="radio" class="btn-check" name="options-base" id="option2" autocomplete="off"  >
<label class="btn" for="option2">SIN SEGUIMINTO</label>

<br>

<br>

<script>
        document.getElementById('option2').addEventListener('change', function() {
            if (this.checked) {
                window.location.href = './inspeccion_view.php'
; // Reemplaza con la URL deseada
            }
        });
    </script>
    

                    <form id="formCita" action="autos.php" method="POST" autocomplete="off" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group lista" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                        </div>
                        <div class="form-group">
                            <!-- Campos del formulario -->

                            <!--  El campo tiene un máximo de 30 caracteres y muestra un texto guía cuando está vacío. Si hay un error, se aplica una clase adicional para mostrar el estado inválido. El valor del campo se muestra de forma segura y es obligatorio. Si hay un mensaje de error asociado, se muestra debajo del campo. -->
                            <label for="marca">Marca:</label>
                            <input type="text" id="marca" name="marca" maxlength="30" class="form-control <?php echo isset($errors['marca']) ? 'is-invalid' : ''; ?>" placeholder="Introduce la marca del vehículo" value="<?php  echo htmlspecialchars($marca ?? '', ENT_QUOTES); ?>"  required>
                            <div class="invalid-feedback"><?php echo $errors['marca'] ?? ''; ?></div>

                            <label for="modelo">Modelo:</label>
                            <input type="text" id="modelo" name="modelo" maxlength="30" class="form-control <?php echo isset($errors['modelo']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el modelo del vehículo" value="<?php echo htmlspecialchars($modelo ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['modelo'] ?? ''; ?></div>

                            <label for="anio">Año:</label>
                            <input type="number" id="anio" name="anio" min="1886" max="<?= date('Y') ?>"  class="form-control <?php echo isset($errors['anio']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el año del vehículo" value="<?php echo htmlspecialchars($anio ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['anio'] ?? ''; ?></div>

                            <label for="color">Color:</label>
                            <input type="text" id="color" name="color" maxlength="33" class="form-control <?php echo isset($errors['color']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el color del vehículo" value="<?php echo htmlspecialchars($color ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['color'] ?? ''; ?></div>

                            <label for="kilometraje">Kilometraje:</label>
                            <input type="text" id="kilometraje" name="kilometraje" maxlength="8" class="form-control <?php echo isset($errors['kilometraje']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el kilometraje del vehículo" value="<?php echo htmlspecialchars($kilometraje ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['kilometraje'] ?? ''; ?></div>

                            <label for="placas">Placas:</label>
                            <input type="text" id="placas" name="placas" maxlength="10" class="form-control <?php echo isset($errors['placas']) ? 'is-invalid' : ''; ?>" placeholder="Introduce las placas del vehículo" value="<?php echo htmlspecialchars($placas ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['placas'] ?? ''; ?></div>

                            <label for="vin">VIN:</label>
                            <input type="text" id="vin" name="vin" maxlength="20" class="form-control <?php echo isset($errors['vin']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el VIN del vehículo" value="<?php echo htmlspecialchars($vin ?? '', ENT_QUOTES); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['vin'] ?? ''; ?></div>
                            <br>
                            <input type="submit" class="btn btn-dark" value="Registrar Vehículo">
                        </div>
                    </form>


                    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var miModal = new bootstrap.Modal(document.getElementById('miModal'));
            miModal.show();
        });
    </script>


                    <script src="app.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const modalSuccess = new bootstrap.Modal(document.getElementById('modalSuccess'));
                            if (document.getElementById('modalSuccess')) {
                                modalSuccess.show();
                            }
                        });

                        document.getElementById('formCita').addEventListener('submit', function(event) {
                            let valid = true;
                            const currentYear = new Date().getFullYear();

                            // Obtener valores del formulario
                            const marca = document.getElementById('marca').value.trim();
                            const modelo = document.getElementById('modelo').value.trim();
                            const anio = parseInt(document.getElementById('anio').value.trim(), 10);
                            const color = document.getElementById('color').value.trim();
                            const kilometraje = document.getElementById('kilometraje').value.trim();
                            const placas = document.getElementById('placas').value.trim();
                            const vin = document.getElementById('vin').value.trim();
                            const continuidad = document.getElementById('continuidad').checked;

                            // Validar marca
                            if (/\d/.test(marca)) {
                                document.getElementById('marca').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('marca').classList.remove('is-invalid');
                            }

                            // Validar año
                            if (anio < 1886 || anio > currentYear || anio.toString().length !== 4) {
                                document.getElementById('anio').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('anio').classList.remove('is-invalid');
                            }

                            // Validar kilometraje
                            if (!/^\d{1,8}$/.test(kilometraje)) {
                                document.getElementById('kilometraje').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('kilometraje').classList.remove('is-invalid');
                            }

                            // Validar color
                            if (/\d/.test(color)) {
                                document.getElementById('color').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('color').classList.remove('is-invalid');
                            }

                            // Validar placas
                            if (placas.length > 10) {
                                document.getElementById('placas').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('placas').classList.remove('is-invalid');
                            }

                            // Validar VIN
                            if (vin.length > 20) {
                                document.getElementById('vin').classList.add('is-invalid');
                                valid = false;
                            } else {
                                document.getElementById('vin').classList.remove('is-invalid');
                            }

                            if (!valid) {
                                event.preventDefault();
                            }
                        });
                    </script>
                    <script>
                        $(document).ready(function() {
                            if ($('#staticBackdrop').length) {
                                $('#staticBackdrop').modal('show');
                            }
                        });

                             //unicamente un modal a la vez
        if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>

</html>