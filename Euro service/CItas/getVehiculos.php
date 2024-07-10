<?php
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();

// Verificar si se ha enviado el clienteID
$clienteID = filter_input(INPUT_POST, 'clienteID', FILTER_VALIDATE_INT);

if ($clienteID) {
    $sql = "SELECT vehiculoID, marca, modelo, anio, color, kilometraje, placas, vin 
            FROM VEHICULOS 
            WHERE clienteID = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$clienteID]);

    $vehiculos = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $vehiculos[] = $row;
    }

    echo json_encode($vehiculos, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([]);
}
?>
