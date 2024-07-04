<?php
require 'database.php';

$con = new Database();
$pdo = $con->conectar();

$sql = "SELECT empleadoID, nombre FROM EMPLEADOS";
$result = $pdo->query($sql);

$empleados = array();
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $empleados[] = $row;
}

echo json_encode($empleados);
?>
