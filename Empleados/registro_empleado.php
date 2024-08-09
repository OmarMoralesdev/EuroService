<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <title>REGISTRO CLIENTE</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<style>
    .wrapper {
        display: flex;
        height: 100vh;
    }

    .main {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-container {
        width: 100%;
        max-width: 100%;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .is-invalid {
        border-color: #dc3545;
    }
</style>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REGISTRAR EMPLEADO</h2>
                <div class="form-container">
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
                    <form id="x" method="post" action="registrar_empleado.php">
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
                            <label for="salario_diario" class="form-label">Salario diario:</label>
                            <input type="number" class="form-control" id="salario_diario" name="salario" min="0" step="0.01" required>
                            <div class="form-text">Introduce el salario diario.</div>
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
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluye jQuery y Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });

        document.getElementById('x').addEventListener('submit', function(event) {
            let valid = true;

            const nombre = document.getElementById('nombre').value;
            const apellido_paterno = document.getElementById('apellido_paterno').value;
            const apellido_materno = document.getElementById('apellido_materno').value;
            const alias = document.getElementById('alias').value;
            const salario_diario = parseFloat(document.getElementById('salario_diario').value);

            // Validar nombre
            if (/\d/.test(nombre)) {
                document.getElementById('nombre').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('nombre').classList.remove('is-invalid');
            }

            // Validar apellido paterno
            if (/\d/.test(apellido_paterno)) {
                document.getElementById('apellido_paterno').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('apellido_paterno').classList.remove('is-invalid');
            }

            // Validar apellido materno
            if (/\d/.test(apellido_materno)) {
                document.getElementById('apellido_materno').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('apellido_materno').classList.remove('is-invalid');
            }

            // Validar alias
            if (/\d/.test(alias)) {
                document.getElementById('alias').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('alias').classList.remove('is-invalid');
            }

            // Validar salario
            if (isNaN(salario_diario) || salario_diario < 0) {
                document.getElementById('salario_diario').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('salario_diario').classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });

        function validarLetras(event) {
            const input = event.target;
            input.value = input.value.replace(/[^a-zA-Z]/g, '');
        }

        function validarNumeros(event) {
            const input = event.target;
            input.value = input.value.replace(/[^0-9.]/g, '');
        }

        document.getElementById('nombre').addEventListener('input', validarLetras);
        document.getElementById('apellido_paterno').addEventListener('input', validarLetras);
        document.getElementById('apellido_materno').addEventListener('input', validarLetras);
        document.getElementById('alias').addEventListener('input', validarLetras);
        document.getElementById('salario_diario').addEventListener('input', validarNumeros);

        document.getElementById('salario_diario').addEventListener('input', function(event) {
            var value = parseFloat(event.target.value);
            if (value < 0) {
                event.target.value = '';
                alert('El salario diario no puede ser negativo.');
            }
        });
    </script>
</body>

</html>
