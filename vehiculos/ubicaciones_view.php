<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicaciones</title>
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
        <?php session_start(); include '../includes/vabr.php';?>
        <div class="main p-3">
        <div class="container">
            <h2 class="text-center">UBICACIONES DE VEHÍCULOS</h2>
                <div class="form-container">
                <?php
                if (isset($_SESSION['r'])) {
                    echo "
                    <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h1 class='modal-title fs-5' id='staticBackdropLabel'>Ubicación Renombrada!</h1>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <div class='alert alert-success' role='alert'>{$_SESSION['r']}</div>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>";
                    unset($_SESSION['r']);
                }
                if (isset($_SESSION['L'])) {
                    echo "
                    <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h1 class='modal-title fs-5' id='staticBackdropLabel'>Ubicación Inhabilitada!</h1>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <div class='alert alert-success' role='alert'>{$_SESSION['L']}</div>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>";
                    unset($_SESSION['L']);
                }
                                    if (isset($_SESSION['x'])) {
                                        echo "
                                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                                            <div class='modal-dialog'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Ubicación Habilitada!</h1>
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
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Ubicación registrada!</h1>
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
                
                $consulta_ubicaciones = "SELECT u.ubicacionID, u.lugar, u.capacidad, COUNT(v.vehiculoID) AS cantidad_vehiculos
                                        FROM UBICACIONES u
                                        LEFT JOIN ORDENES_TRABAJO ot ON u.ubicacionID = ot.ubicacionID
                                        LEFT JOIN CITAS c ON ot.citaID = c.citaID
                                        LEFT JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
                                        WHERE u.activo = 'si'
                                        GROUP BY u.ubicacionID, u.lugar, u.capacidad";
                $ubicaciones = $conexion->seleccionar($consulta_ubicaciones);
                
                if (is_array($ubicaciones)) {
                    foreach ($ubicaciones as $ubicacion) {
                        $espacio_disponible = $ubicacion->capacidad - $ubicacion->cantidad_vehiculos;
                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>UBICACIÓN: {$ubicacion->lugar}</h5>";
                        echo "<hr>";
                        echo "<p class='card-text'>CAPACIDAD: {$ubicacion->capacidad}</p>";
                        echo "<p class='card-text'>OCUPADOS: {$ubicacion->cantidad_vehiculos}</p>";
                        echo "<p class='card-text'>ESPACIO DISPONIBLE: {$espacio_disponible}</p>";
                        echo "</div>"; // Cierre de card-body
// Botones en un div separado para controlar la alineación
                      // Botones en un div separado para controlar la alineación
echo "<div class='card-footer d-flex flex-wrap justify-content-between align-items-center'>";
echo "<button type='button' class='btn btn-dark btn-md mb-2 me-2 flex-fill' style='min-width: 150px;' data-bs-toggle='modal' data-bs-target='#modal{$ubicacion->ubicacionID}'>VER VEHÍCULOS</button>";
echo "<button type='button' class='btn btn-dark btn-md mb-2 me-2 flex-fill' style='min-width: 80px;' data-bs-toggle='modal' data-bs-target='#renameModal{$ubicacion->ubicacionID}'><i class='lni lni-pencil'></i></button>";
echo "<button type='button' class='btn btn-danger btn-md mb-2 flex-fill' style='min-width: 80px;' data-bs-toggle='modal' data-bs-target='#deleteModal{$ubicacion->ubicacionID}'><i class='lni lni-pause'></i></button>";
echo "</div>"; // Cierre de card-footer

echo "</div>"; // Cierre de card
echo "</div>";
   


                        // Modal para mostrar vehículos
                        echo "<div class='modal fade' id='modal{$ubicacion->ubicacionID}' tabindex='-1' aria-labelledby='modalLabel{$ubicacion->ubicacionID}' aria-hidden='true'>";
                        echo "<div class='modal-dialog modal-lg'>"; 
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                       
                        $consulta = "SELECT DISTINCT 
                            CONCAT(VEHICULOS.marca, ' ', VEHICULOS.modelo, ' ', VEHICULOS.anio, ' - ', VEHICULOS.color) AS VEHICULO, 
                            CONCAT(PERSONAS.nombre, ' ', PERSONAS.apellido_paterno, ' ', PERSONAS.apellido_materno) AS PROPIETARIO
                            FROM PERSONAS 
                            INNER JOIN CLIENTES ON CLIENTES.personaID = PERSONAS.personaID 
                            INNER JOIN VEHICULOS ON VEHICULOS.clienteID = CLIENTES.clienteID 
                            INNER JOIN CITAS ON CITAS.vehiculoID = VEHICULOS.vehiculoID 
                            INNER JOIN ORDENES_TRABAJO ON ORDENES_TRABAJO.citaID = CITAS.citaID 
                            INNER JOIN UBICACIONES ON ORDENES_TRABAJO.ubicacionID = UBICACIONES.ubicacionID 
                            WHERE UBICACIONES.ubicacionID = {$ubicacion->ubicacionID} and UBICACIONES.activo = 'si'";
                        
                        $tabla = $conexion->seleccionar($consulta);

                        if (is_array($tabla) && count($tabla) > 0) {
                            echo "<table class='table table-hover'>";
                            echo "<thead class='table-dark' align='center'>";
                            echo "<tr>";
                            echo "<th>VEHÍCULO</th><th>PROPIETARIO</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            foreach ($tabla as $reg) {
                                echo "<tr align='center'>";
                                echo "<td> {$reg->VEHICULO} </td>";
                                echo "<td> {$reg->PROPIETARIO} </td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<p>NO HAY VEHÍCULOS EN ESTE LUGAR.</p>";
                        }
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>CERRAR</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";

                        // Modal para inhabilitar ubicación
                        echo "<div class='modal fade' id='deleteModal{$ubicacion->ubicacionID}' tabindex='-1' aria-labelledby='deleteModalLabel{$ubicacion->ubicacionID}' aria-hidden='true'>";
                        echo "<div class='modal-dialog'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<h5 class='modal-title' id='deleteModalLabel{$ubicacion->ubicacionID}'>Confirmar Inhabilitación</h5>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        echo "<p>¿Estás seguro de que deseas inhabilitar la ubicación <strong>{$ubicacion->lugar}</strong>?</p>";
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                        echo "<a href='deleteLocation.php?id={$ubicacion->ubicacionID}' class='btn btn-danger'>Inhabilitar</a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";

                         // Modal para editar nombre
                         echo "<div class='modal fade' id='renameModal{$ubicacion->ubicacionID}' tabindex='-1' aria-labelledby='deleteModalLabel{$ubicacion->ubicacionID}' aria-hidden='true'>";
                         echo "<div class='modal-dialog'>";
                         echo "<div class='modal-content'>";
                         echo "<div class='modal-header'>";
                         echo "<h5 class='modal-title' id='deleteModalLabel{$ubicacion->ubicacionID}'>Confirmar Renombrar</h5>";
                         echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                         echo "</div>";
                         echo "<div class='modal-body'>";
                         echo "<form action='renameLocation.php' method='POST'>";
                         echo "<div class='mb-3'>";
                         echo "<label for='lugarn' class='form-label'>Nuevo nombre de la ubicación </label>";
                         echo "<input type='text' class='form-control' id='lugarn' name='lugarn' required>";
                         echo "</div>";
                         echo "</div>";
                         echo "<div class='modal-footer'>";
                         echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                         echo "<input type='hidden' name='ubicacionID' value='{$ubicacion->ubicacionID}'>";
                         echo "<button type='submit' class='btn btn-dark'>Renombrar</button>";
                         echo "</div>";
                         echo "</form>";
                         echo "</div>";
                         echo "</div>";
                         echo "</div>";
                    }
                }
                $conexion->desconectar();
                ?>
            </div>
            <div class="d-flex flex-column flex-md-row gap-2">
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addLocationModal">AÑADIR NUEVA UBICACIÓN</button>
    <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#enableLocationModal">HABILITAR UBICACIÓN</button>
</div></div>
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
                    <form action='AddLocation.php' method='POST'>
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

     <!-- Modal para habilitar una ubicación -->
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
                            <option value="">Selecciona la ubicación</option>
                            <?php
                    include '../class/database.php';
                    $conexion = new Database();
                    $conexion->conectar();
                    $consulta = "SELECT u.ubicacionID, u.lugar, u.capacidad, COUNT(v.vehiculoID) AS cantidad_vehiculos
                                        FROM UBICACIONES u
                                        LEFT JOIN ORDENES_TRABAJO ot ON u.ubicacionID = ot.ubicacionID
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
    <script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });
    </script>
</body>
</html>
