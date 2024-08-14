<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

if (isset($_GET['clienteID'])) {
    $clienteID = $_GET['clienteID'];

    function obtenerOrdenesTrabajo($pdo, $clienteID)
    {
        try {
            $sql = "SELECT o.ordenID, o.fecha_orden, c.total_estimado, v.marca, v.modelo,c.servicio_solicitado
                    FROM ORDENES_TRABAJO o 
                    JOIN CITAS c ON o.citaID = c.citaID 
                    JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID 
                    WHERE v.clienteID = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clienteID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo json_encode(['error' => "Error en la consulta: " . $e->getMessage()]);
            return [];
        }
    }

    $ordenes = obtenerOrdenesTrabajo($pdo, $clienteID);
    echo json_encode($ordenes);
}
?>
