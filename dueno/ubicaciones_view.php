<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicaciones</title>
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
        <?php include '../dueño/vabr.php'; ?>
        <div class="main p-3">
        <div class="container">
            <h2 class="text-center">UBICACIONES DE VEHÍCULOS</h2>
                <div class="form-container">
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
        echo "<div class='card-footer d-flex justify-content-center'>";
        echo "<button type='button' class='btn btn-dark btn-md' style='width: 80%;' data-bs-toggle='modal' data-bs-target='#modal{$ubicacion->ubicacionID}'>VER VEHÍCULOS</button>";
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
                            CONCAT(vehiculos.marca, ' ', vehiculos.modelo, ' ', vehiculos.anio, ' - ', vehiculos.color) AS VEHICULO, 
                            CONCAT(personas.nombre, ' ', personas.apellido_paterno, ' ', personas.apellido_materno) AS PROPIETARIO
                            FROM PERSONAS 
                            INNER JOIN CLIENTES ON clientes.personaID = personas.personaID 
                            INNER JOIN VEHICULOS ON vehiculos.clienteID = clientes.clienteID 
                            INNER JOIN CITAS ON citas.vehiculoID = vehiculos.vehiculoID 
                            INNER JOIN ORDENES_TRABAJO ON ordenes_trabajo.citaID = citas.citaID 
                            INNER JOIN UBICACIONES ON ordenes_trabajo.ubicacionID = ubicaciones.ubicacionID 
                            WHERE ubicaciones.ubicacionID = {$ubicacion->ubicacionID} and ubicaciones.activo = 'si'";
                        
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

                    }
                }
                $conexion->desconectar();
                ?>
            </div>
        </div>
    </div>

</body>
</html>
