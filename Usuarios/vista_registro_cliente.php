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
    <title>Registrar Cliente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?> <!-- barra lateral -->
        <div class="main p-3">
            <div class="container">
                <h2>REGISTRAR CLIENTE</h2>
                <div class="form-container">
                    <form method="post" action="registro_cliente.php">
                    <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="apellido_paterno">Apellido Paterno:</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="apellido_materno">Apellido Materno:</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo electrónico:</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required pattern="\d{10}" title="Debe contener 10 dígitos">
                        </div>
                        <br>
                        <button type="submit" class="btn btn-dark btn-block">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <?php
    if (isset($_SESSION['modal'])) {
        $modalContent = $_SESSION['modal'];
        echo $modalContent['message'];
        unset($_SESSION['modal']);
    }
    ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalElement = document.getElementById('staticBackdrop');
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, {
                    keyboard: false
                });
                myModal.show();
            }
        });
    </script>
</body>
</html>
