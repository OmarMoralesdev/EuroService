<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
        <title>Registrar Cliente</title>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?> <!-- barra lateral -->
        <div class="main p-3">
            <div class="container">
                <h2>REGISTRAR CLIENTE</h2>
                <div class="form-container">
                 <!-- ALERTA DE ERRORES -->
                <?php
        if (isset($_SESSION['alert'])) {
            echo $_SESSION['alert']['message'];
            unset($_SESSION['alert']);
        }
        ?>
                    <form method="post" id="x" action="./registro_cliente.php">
                    <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" class="form-control" id="nombre"  maxlength="40" name="nombre" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="apellido_paterno">Apellido Paterno:</label>
                            <input type="text" class="form-control" id="apellido_paterno" maxlength="40" name="apellido_paterno" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="apellido_materno">Apellido Materno:</label>
                            <input type="text" class="form-control" id="apellido_materno" maxlength="40" name="apellido_materno" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo electrónico:</label>
                            <input type="email" class="form-control" id="correo" maxlength="256" name="correo" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required pattern="\d{10}" maxlength="10" title="Debe contener 10 dígitos">
                        </div>
                        <br>
                        <button type="submit" class="btn btn-dark btnn d-grid gap-2 col-6 mx-auto">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MUESTRA EL MODAL DE EXITO -->
    <?php
    if (isset($_SESSION['modal'])) {
        $modalContent = $_SESSION['modal'];
        echo $modalContent['message'];
        unset($_SESSION['modal']);
    }
    ?>


    <script>
        // Muestra el modal de éxito
        document.addEventListener('DOMContentLoaded', function () {
            // Obtiene el modal
            var modalElement = document.getElementById('staticBackdrop');
            if (modalElement) {
                // Crea una instancia de bootstrap modal
                var myModal = new bootstrap.Modal(modalElement, {
                    keyboard: false
                });
                myModal.show();
            }
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

            const nombre = document.getElementById('nombre').value;
            const apellido_paterno = document.getElementById('apellido_paterno').value;
            const apellido_materno = document.getElementById('apellido_materno').value;
            const telefono = parseFloat(document.getElementById('telefono').value);

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

            // Validar salario
            if (isNaN(telefono) || telefono < 0) {
                document.getElementById('telefono').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('telefono').classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
        function validarLetras(event) {
    const input = event.target;
    input.value = input.value.replace(/[^a-zA-ZáéíóúüñÁÉÍÓÚÜÑ ]/g, '');
}

        function validarNumeros(event) {
            const input = event.target;
            input.value = input.value.replace(/[^0-9.]/g, '');
        }

        document.getElementById('nombre').addEventListener('input', validarLetras);
        document.getElementById('apellido_paterno').addEventListener('input', validarLetras);
        document.getElementById('apellido_materno').addEventListener('input', validarLetras);
        document.getElementById('telefono').addEventListener('input', validarNumeros);
    </script>
</body>
</html>