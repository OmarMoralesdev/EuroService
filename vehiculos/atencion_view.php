<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atención de Vehículos</title>
</head>
<body>

<div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
        <div class="container">
            <h2 class="text-center">ATENCIÓN DE VEHÍCULOS</h2>
                <div class="form-container">
            <div class="row mt-4">
                <?php
                include '../includes/db.php';
                $conexion = new Database();
                $conexion->conectar();
                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>MUY URGENTE</h5>";
                        echo "<hr>";
                        echo "<p class='card-text'>Necesitan ser atendidos con mucha urgencia...</p>";
                        echo "</div>"; // Cierre de card-body

                        // Botones en un div separado para controlar la alineación
                        echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                        echo "<button type='button' class='btn btn-dark btn-md' style='width: 100%;' data-bs-toggle='modal' data-bs-target='#modalmu'>VER...</button>";
                        echo "</div>"; // Cierre de card-footer

                        echo "</div>"; // Cierre de card
                        echo "</div>";

                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>URGENTE</h5>";
                        echo "<hr>";
                        echo "<p class='card-text'>Necesitan ser atendidos con urgencia...</p>";
                        echo "</div>"; // Cierre de card-body

                        // Botones en un div separado para controlar la alineación
                        echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                        echo "<button type='button' class='btn btn-dark btn-md' style='width: 100%;' data-bs-toggle='modal' data-bs-target='#modalu'>VER...</button>";
                        echo "</div>"; // Cierre de card-footer

                        echo "</div>"; // Cierre de card
                        echo "</div>";

                        echo "<div class='col-md-4 mb-3'>";
                        echo "<div class='card' style='width: 95%;'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>NO URGENTE</h5>";
                        echo "<hr>";
                        echo "<p class='card-text'>No necesitan ser atendidos con urgencia...</p>";
                        echo "</div>"; // Cierre de card-body

                        // Botones en un div separado para controlar la alineación
                        echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                        echo "<button type='button' class='btn btn-dark btn-md' style='width: 100%;' data-bs-toggle='modal' data-bs-target='#modalnu'>VER...</button>";
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
                        $consultita = "SELECT concat(VEHICULOS.marca,' ',VEHICULOS.modelo,' ',VEHICULOS.anio,' ',VEHICULOS.color) AS VEHICULO,
                        CITAS.servicio_solicitado as OPERACIÓN_EFECTUAR
                        From VEHICULOS join CITAS On CITAS.vehiculoID=VEHICULOS.vehiculoID join ORDENES_TRABAJO On
                        ORDENES_TRABAJO.citaID=CITAS.citaID inner join PAGOS On PAGOS.ordenID =ORDENES_TRABAJO.ordenID
                        where ORDENES_TRABAJO.atencion='muy urgente' and CITAS.estado !='completado';";
                        
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
                        }else 
                        {
                            echo "No hay vehículos que requieran atención muy urgente.";
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
                        $consultita = "SELECT concat(VEHICULOS.marca,' ',VEHICULOS.modelo,' ',VEHICULOS.anio,' ',VEHICULOS.color) AS VEHICULO,
                        CITAS.servicio_solicitado as OPERACIÓN_EFECTUAR
                        From VEHICULOS join CITAS On CITAS.vehiculoID=VEHICULOS.vehiculoID join ORDENES_TRABAJO On
                        ORDENES_TRABAJO.citaID=CITAS.citaID inner join PAGOS On PAGOS.ordenID =ORDENES_TRABAJO.ordenID
                        where ORDENES_TRABAJO.atencion='urgente' and CITAS.estado !='completado';";
                        
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
                        } else 
                        {
                            echo "No hay vehículos que requieran atención urgente.";
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
                        $consultita = "SELECT concat(VEHICULOS.marca,' ',VEHICULOS.modelo,' ',VEHICULOS.anio,' ',VEHICULOS.color) AS VEHICULO,
                        CITAS.servicio_solicitado as OPERACIÓN_EFECTUAR
                        From VEHICULOS join CITAS on CITAS.vehiculoID=VEHICULOS.vehiculoID join ORDENES_TRABAJO on
                        ORDENES_TRABAJO.citaID=CITAS.citaID inner join PAGOS On PAGOS.ordenID = ORDENES_TRABAJO.ordenID
                        where ORDENES_TRABAJO.atencion='no urgente' and CITAS.estado !='completado';
                     ";
                        
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
                        } else 
                        {
                            echo "No hay vehículos que requieran atención no urgente.";
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

</body>
</html>
