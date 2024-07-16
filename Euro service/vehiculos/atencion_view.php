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
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <h2 class="text-center">ATENCIÓN DE VEHÍCULOS</h2>
            <div class="row mt-4">
                <?php
                include '../includes/db.php';
                $conexion = new Database();
                $conexion->conectar();
                
                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>ATENCIÓN: Muy urgente</h5>";
                        echo "<hr>";
                        echo "<p class='card-text'>Vehículos que necesitan ser atendidos con mucha urgencia...</p>";
                        echo "</div>"; // Cierre de card-body

                        // Botones en un div separado para controlar la alineación
                        echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                        echo "<button type='button' class='btn btn-dark btn-md' style='width: 80%;' data-bs-toggle='modal' data-bs-target='#modalmu'>VER...</button>";
                        echo "</div>"; // Cierre de card-footer

                        echo "</div>"; // Cierre de card
                        echo "</div>";

                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>ATENCIÓN: Urgente</h5>";
                        echo "<hr>";
                        echo "<p class='card-text'>Vehículos que necesitan ser atendidos con urgencia...</p>";
                        echo "</div>"; // Cierre de card-body

                        // Botones en un div separado para controlar la alineación
                        echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                        echo "<button type='button' class='btn btn-dark btn-md' style='width: 80%;' data-bs-toggle='modal' data-bs-target='#modalu'>VER...</button>";
                        echo "</div>"; // Cierre de card-footer

                        echo "</div>"; // Cierre de card
                        echo "</div>";

                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>ATENCIÓN: No urgente</h5>";
                        echo "<hr>";
                        echo "<p class='card-text'>Vehículos que no necesitan ser atendidos con urgencia...</p>";
                        echo "</div>"; // Cierre de card-body

                        // Botones en un div separado para controlar la alineación
                        echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                        echo "<button type='button' class='btn btn-dark btn-md' style='width: 80%;' data-bs-toggle='modal' data-bs-target='#modalnu'>VER...</button>";
                        echo "</div>"; // Cierre de card-footer

                        echo "</div>"; // Cierre de card
                        echo "</div>";

                        

                        // Modal para mostrar vehículos
                        echo "<div class='modal fade' id='modalmu' tabindex='-1' aria-labelledby='modalLabelmu' aria-hidden='true'>";
                        echo "<div class='modal-dialog modal-lg'>"; 
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        $consultita = "SELECT concat(vehiculos.marca,' ',vehiculos.modelo,' ',vehiculos.anio,' ',vehiculos.color) AS VEHICULO,
                        ordenes_trabajo.detalles_trabajo as OPERACIÓN_EFECTUAR
                        From vehiculos join citas On citas.vehiculoID=vehiculos.vehiculoID join ordenes_trabajo On
                        ordenes_trabajo.citaID=citas.citaID
                        where ordenes_trabajo.atencion='muy urgente';";
                        
                        $tabla = $conexion->seleccionar($consultita);

                        if (is_array($tabla) && count($tabla) > 0) {
                            echo "<table class='table table-hover'>";
                            echo "<thead class='table-dark' align='center'>";
                            echo "<tr>";
                            echo "<th>VEHÍCULO</th><th>OPERACIÓN A EFECTUAR</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            foreach ($tabla as $reg) {
                                echo "<tr align='center'>";
                                echo "<td> {$reg->VEHICULO} </td>";
                                echo "<td> {$reg->OPERACIÓN_EFECTUAR} </td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        }
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>CERRAR</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";

                        echo "<div class='modal fade' id='modalu' tabindex='-1' aria-labelledby='modalLabelu' aria-hidden='true'>";
                        echo "<div class='modal-dialog modal-lg'>"; 
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        $consultita = "SELECT concat(vehiculos.marca,' ',vehiculos.modelo,' ',vehiculos.anio,' ',vehiculos.color) AS VEHICULO,
                        ordenes_trabajo.detalles_trabajo as OPERACIÓN_EFECTUAR
                        From vehiculos join citas On citas.vehiculoID=vehiculos.vehiculoID join ordenes_trabajo On
                        ordenes_trabajo.citaID=citas.citaID
                        where ordenes_trabajo.atencion='urgente';";
                        
                        $tabla = $conexion->seleccionar($consultita);

                        if (is_array($tabla) && count($tabla) > 0) {
                            echo "<table class='table table-hover'>";
                            echo "<thead class='table-dark' align='center'>";
                            echo "<tr>";
                            echo "<th>VEHÍCULO</th><th>OPERACIÓN A EFECTUAR</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            foreach ($tabla as $reg) {
                                echo "<tr align='center'>";
                                echo "<td> {$reg->VEHICULO} </td>";
                                echo "<td> {$reg->OPERACIÓN_EFECTUAR} </td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        }
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>CERRAR</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";

                        echo "<div class='modal fade' id='modalnu' tabindex='-1' aria-labelledby='modalLabelnu' aria-hidden='true'>";
                        echo "<div class='modal-dialog modal-lg'>"; 
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        $consultita = "SELECT concat(vehiculos.marca,' ',vehiculos.modelo,' ',vehiculos.anio,' ',vehiculos.color) AS VEHICULO,
                        ordenes_trabajo.detalles_trabajo as OPERACIÓN_EFECTUAR
                        From vehiculos join citas On citas.vehiculoID=vehiculos.vehiculoID join ordenes_trabajo On
                        ordenes_trabajo.citaID=citas.citaID
                        where ordenes_trabajo.atencion='no urgente';";
                        
                        $tabla = $conexion->seleccionar($consultita);

                        if (is_array($tabla) && count($tabla) > 0) {
                            echo "<table class='table table-hover'>";
                            echo "<thead class='table-dark' align='center'>";
                            echo "<tr>";
                            echo "<th>VEHÍCULO</th><th>OPERACIÓN A EFECTUAR</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            foreach ($tabla as $reg) {
                                echo "<tr align='center'>";
                                echo "<td> {$reg->VEHICULO} </td>";
                                echo "<td> {$reg->OPERACIÓN_EFECTUAR} </td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        }
                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>CERRAR</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";

                    
                $conexion->desconectar();
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
