<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Cita para Orden de Trabajo</title>
</head>

<body>
<div class="wrapper">
    <?php include '../includes/vabr.html'; ?>
    <div class="main p-2">
        <div class="container">
            <h2>SELECCIONAR CITA</h2>
            <div class="form-container">
            <?php
                    if (isset( $_SESSION['bien'])) {
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
            <form method="post" action="editar_cita_back.php">
                    <div class="mb-3">
                        <label for="citaID" class="form-label">Seleccionar Cita:</label>
                        <select id="citaID" name="citaID" class="form-select" required>
                            <?php
                            require '../includes/db.php';
                            $con = new Database();
                            $pdo = $con->conectar();
                            $citas = listarCitasPendientes($pdo);
                            foreach ($citas as $citaOption) {
                                echo "<option value=\"{$citaOption['citaID']}\">Cita ID: {$citaOption['citaID']} - Vehículo: {$citaOption['marca']} {$citaOption['modelo']} {$citaOption['anio']} - Cliente: {$citaOption['nombre']} {$citaOption['apellido_paterno']} {$citaOption['apellido_materno']} - Servicio: {$citaOption['servicio_solicitado']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="buscar" class="btn btn-dark w-100">Buscar Cita</button>
                </form>
            </div>
        </div>
    </div>
    <script>
                    $(document).ready(function() {
                        if ($('#staticBackdrop').length) {
                            $('#staticBackdrop').modal('show');
                        }
                    });
                </script>
</body>

</html>