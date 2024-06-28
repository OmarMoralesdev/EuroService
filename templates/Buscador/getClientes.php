<?php

require 'database.php';

$con = new Database();
$pdo = $con->conectar();

$campo = filter_input(INPUT_POST, 'campo', FILTER_SANITIZE_STRING);

$sql = "SELECT clienteID, nombre, apellido_paterno, apellido_materno FROM CLIENTES WHERE nombre LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ? ORDER BY nombre ASC LIMIT 10";
$query = $pdo->prepare($sql);
$query->execute([$campo . '%', $campo . '%', $campo . '%']);
$clientes = [];
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $clientes[] = $row;
}
echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
?>
