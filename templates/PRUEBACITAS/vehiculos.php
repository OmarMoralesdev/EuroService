<?php
include 'db.php';

if (isset($_GET['nombre'])) {
    $nombre = $_GET['nombre'];


    $sqlCliente = "SELECT id FROM clientes WHERE nombre LIKE ?";
    $stmtCliente = $pdo->prepare($sqlCliente);
    $searchTerm = "%" . $nombre . "%";
    $stmtCliente->execute([$searchTerm]);

    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $clienteId = $cliente['id'];

        
        $sqlVehiculos = "SELECT * FROM vehiculos WHERE cliente_id = ?";
        $stmtVehiculos = $pdo->prepare($sqlVehiculos);
        $stmtVehiculos->execute([$clienteId]);

        $vehiculos = $stmtVehiculos->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($vehiculos);
    } else {
        echo json_encode(["mensaje" => "No se encontró ningún cliente con ese nombre"]);
    }
}
?>
