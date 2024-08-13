<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

if (isset($_GET['empleadoID'])) {
    $empleadoID = $_GET['empleadoID'];

    try {
        // Preparar la consulta SQL para obtener los detalles de las órdenes del técnico
        $sql = "SELECT ORDENES_TRABAJO.ordenID, ORDENES_TRABAJO.fecha_orden, ORDENES_TRABAJO.costo_mano_obra, 
                       ORDENES_TRABAJO.costo_refacciones, ORDENES_TRABAJO.total_estimado, ORDENES_TRABAJO.anticipo, 
                       ORDENES_TRABAJO.atencion, CITAS.servicio_solicitado, CITAS.tipo_servicio,
                       CITAS.fecha_solicitud, CITAS.fecha_cita, CITAS.urgencia, CITAS.estado, VEHICULOS.marca, 
                       VEHICULOS.modelo, VEHICULOS.anio, VEHICULOS.color, VEHICULOS.kilometraje, VEHICULOS.placas, 
                       VEHICULOS.vin
                FROM ORDENES_TRABAJO
                INNER JOIN CITAS ON ORDENES_TRABAJO.citaID = CITAS.citaID
                INNER JOIN VEHICULOS ON CITAS.vehiculoID = VEHICULOS.vehiculoID
                WHERE ORDENES_TRABAJO.empleadoID = :empleadoID";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':empleadoID', $empleadoID, PDO::PARAM_INT);
        $stmt->execute();

        $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($ordenes)) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>ID de Orden</th><th>Fecha de Orden</th><th>Costo Mano de Obra</th><th>Costo Refacciones</th><th>Total Estimado</th><th>Anticipo</th><th>Atención</th><th>Servicio Solicitado</th><th>Tipo de Servicio</th><th>Fecha de Solicitud</th><th>Fecha de Cita</th><th>Estado</th><th>Detalles del Vehículo</th></tr></thead>';
            echo '<tbody>';
            foreach ($ordenes as $orden) {
                $detalleVehiculo = htmlspecialchars($orden['marca']) . ' ' . htmlspecialchars($orden['modelo']) . ', Año: ' . htmlspecialchars($orden['anio']);
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($orden['ordenID']) . '</td>';
                echo '<td>' . htmlspecialchars($orden['fecha_orden']) . '</td>';
                echo '<td>$' . number_format($orden['costo_mano_obra'], 2) . '</td>';
                echo '<td>$' . number_format($orden['costo_refacciones'], 2) . '</td>';
                echo '<td>$' . number_format($orden['total_estimado'], 2) . '</td>';
                echo '<td>$' . number_format($orden['anticipo'], 2) . '</td>';
                echo '<td>' . htmlspecialchars($orden['atencion']) . '</td>';
                echo '<td>' . htmlspecialchars($orden['servicio_solicitado']) . '</td>';
                echo '<td>' . htmlspecialchars($orden['tipo_servicio']) . '</td>';
                echo '<td>' . htmlspecialchars($orden['fecha_solicitud']) . '</td>';
                echo '<td>' . htmlspecialchars($orden['fecha_cita']) . '</td>';
        
                echo '<td>' . htmlspecialchars($orden['estado']) . '</td>';
                echo '<td>' . $detalleVehiculo . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No se encontraron detalles para las órdenes de este técnico.</p>';
        }
    } catch (PDOException $e) {
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }

    // Cerrar la conexión
    $pdo = null;
} else {
    echo '<p>Empleado no especificado.</p>';
}
?>
