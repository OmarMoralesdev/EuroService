<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos</title>
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .card-body {
            padding: 1rem; /* Ajusta el padding interior de la card-body */
        }
        .card-title {
            margin-bottom: 0.5rem; /* Reduce el margen inferior del título */
        }
        .card-text {
            margin-bottom: 0.25rem; /* Reduce el margen inferior del texto dentro de la card */
        }
    </style>
</head>
<body>
<div class="wrapper">
        <?php session_start(); include '../includes/vabr.php'; ?>
        <div class="main p-3">
        <div class="container">
            <h2 class="text-center">ELIMINAR VEHÍCULO</h2>
                <div class="form-container">
                <div class="d-flex flex-column flex-md-row gap-2">
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#enableCarModal">HABILITAR VEHÍCULO</button>
</div>
                <?php
                                    if (isset($_SESSION['x'])) {
                                        echo "
                                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                                            <div class='modal-dialog'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Vehículo Habilitado!</h1>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <div class='alert alert-success' role='alert'>{$_SESSION['x']}</div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                        unset($_SESSION['x']);
                                    }
                    if (isset($_SESSION['error'])) {
                        echo '<br>';
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Vehículo Inhabilitado!</h1>
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
            <div class="row mt-3">
            
                <?php
                include '../includes/db.php';
                $conexion = new Database();
                $conexion->conectar();
                
                $consulta_vehiculos = "SELECT concat(VEHICULOS.marca,' ',VEHICULOS.modelo,' ',VEHICULOS.color,' ',VEHICULOS.anio) as VEHICULOS,
concat(PERSONAS.nombre,' ',PERSONAS.apellido_paterno,' ',PERSONAS.apellido_materno) as PROPIETARIO, PERSONAS.correo
AS CORREO, PERSONAS.telefono as TELEFONO, VEHICULOS.vehiculoID
FROM PERSONAS JOIN CLIENTES ON CLIENTES.personaID = PERSONAS.personaID JOIN VEHICULOS ON
VEHICULOS.clienteID = CLIENTES.clienteID and VEHICULOS.activo = 'si'";
                $vehiculos = $conexion->seleccionar($consulta_vehiculos);
                
                if (is_array($vehiculos)) {
                    foreach ($vehiculos as $vehiculo) {
                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title' align='center'>{$vehiculo->VEHICULOS} </h5>";
                        echo "<hr>";
                        echo "<h6 class='card-title' align='center'>DATOS DEL PROPIETARIO </h6>";
                        echo "<hr>";
                        echo "<p class='card-text'>NOMBRE: {$vehiculo->PROPIETARIO}</p>";
                        echo "<p class='card-text'>CORREO: {$vehiculo->CORREO}</p>";
                        echo "<p class='card-text'>TELEFONO: {$vehiculo->TELEFONO}</p>";
                        echo "</div>"; // Cierre de card-body

                        // Botones en un div separado para controlar la alineación
                        echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                        echo "<button type='button' class='btn btn-danger btn-md ml-2' style='width: 100%;' data-bs-toggle='modal' data-bs-target='#deleteModal{$vehiculo->vehiculoID}'><i class='lni lni-pause'></i></button>";
                        echo "</div>"; // Cierre de card-footer

                        echo "</div>"; // Cierre de card
                        echo "</div>";

                        // Modal para inhabilitar vehiculo
                        echo "<div class='modal fade' id='deleteModal{$vehiculo->vehiculoID}' tabindex='-1' aria-labelledby='deleteModalLabel{$vehiculo->vehiculoID}' aria-hidden='true'>";
                        echo "<div class='modal-dialog'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<h5 class='modal-title' id='deleteModalLabel{$vehiculo->vehiculoID}'>Confirmar Inhabilitación</h5>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        echo "<p>¿Estás seguro de que deseas inhabilitar el vehículo <strong>{$vehiculo->VEHICULOS}</strong>?</p>";
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                        echo "<a href='deleteCar.php?id={$vehiculo->vehiculoID}' class='btn btn-danger'>Inhabilitar</a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
                $conexion->desconectar();
                ?>
            </div>
        </div>
    </div>

    <!-- Modal para habilitar un vehículo -->
    <div class='modal fade' id='enableCarModal' tabindex='-1' aria-labelledby='enableLocationModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='enableLocationModalLabel'>Habilitar Vehículo</h5>
                    <button type='button' class='btn-close bg-dark' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form action='enableCar.php' method='POST'>
                        <div class='mb-3'>
                            <label for='carro' class='form-label'>Vehículo</label>
                            <select class="form-select" name="auto" id="auto" required>
                            <option value="">Selecciona el vehículo</option>
                            <?php
                    include '../class/database.php';
                    $conexion = new Database();
                    $conexion->conectar();
                    $consulta = "SELECT CONCAT(VEHICULOS.marca,' ',VEHICULOS.modelo,' ',VEHICULOS.anio,' ',VEHICULOS.color) AS VEHICULO,
                                 vehiculoID
                                 FROM VEHICULOS
                                 WHERE activo = 'no'";
                    $inhabilitados = $conexion->seleccionar($consulta);
                    foreach($inhabilitados as $inhabilitado) {
                        echo "<option value='".$inhabilitado->vehiculoID."'>".$inhabilitado->VEHICULO."</option>";
                    }
                    ?>
                            </select>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                            <button type='submit' class='btn btn-dark'>Guardar</button>
                        </div>
                    </form>
                </div>
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
