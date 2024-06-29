<?php
require 'database.php';

$con = new Database();
$pdo = $con->conectar();

$clienteID = filter_input(INPUT_POST, 'clienteID', FILTER_VALIDATE_INT);

if ($clienteID) {
    $sql = "SELECT vehiculoID, marca, modelo FROM VEHICULOS WHERE clienteID = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$clienteID]);

    $vehiculos = [];

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $vehiculos[] = $row;
    }

    echo json_encode($vehiculos, JSON_UNESCAPED_UNICODE);
}
?>
