<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos</title>
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
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
        <div class="container">
            <h2 class="text-center">VEHÍCULOS</h2>
                <div class="form-container">
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

    <!-- Modal para añadir una nueva ubicación -->
    <div class='modal fade' id='addLocationModal' tabindex='-1' aria-labelledby='addLocationModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='addLocationModalLabel'>Añadir Nueva Ubicación</h5>
                    <button type='button' class='btn-close bg-dark' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form action='addLocation.php' method='POST'>
                        <div class='mb-3'>
                            <label for='lugar' class='form-label'>Lugar</label>
                            <input type='text' class='form-control' id='lugar' name='lugar' required>
                        </div>
                        <div class='mb-3'>
                            <label for='capacidad' class='form-label'>Capacidad</label>
                            <input type='number' class='form-control' id='capacidad' name='capacidad' required>
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

     <!-- Modal para habilitar un vehiculo -->
     <div class='modal fade' id='enableLocationModal' tabindex='-1' aria-labelledby='enableLocationModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='enableLocationModalLabel'>Habilitar Ubicación</h5>
                    <button type='button' class='btn-close bg-dark' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form action='enableLocation.php' method='POST'>
                        <div class='mb-3'>
                            <label for='lugar' class='form-label'>Lugar</label>
                            <select class="form-select" name="lugaru" id="lugaru" required>
                            <option value="Selecciona la ubicación">Selecciona la ubicación</option>
                            <?php
                    include '../class/database.php';
                    $conexion = new Database();
                    $conexion->conectar();
                    $consulta = "SELECT u.ubicacionID, u.lugar, u.capacidad, COUNT(v.vehiculoID) AS cantidad_vehiculos
                                        FROM UBICACIONES U
                                        LEFT JOIN OREDENES_TRABAJO ot ON u.ubicacionID = ot.ubicacionID
                                        LEFT JOIN CITAS c ON ot.citaID = c.citaID
                                        LEFT JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
                                        WHERE u.activo = 'no' and u.lugar != 'Dueño'
                                        GROUP BY u.ubicacionID, u.lugar, u.capacidad";
                    $desactivados = $conexion->seleccionar($consulta);
                    foreach($desactivados as $desactivado) {
                        echo "<option value='".$desactivado->ubicacionID."'>".$desactivado->lugar."</option>";
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

</body>
</html>
