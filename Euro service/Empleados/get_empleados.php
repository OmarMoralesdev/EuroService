<?php
require 'database.php';

$con = new Database();
$pdo = $con->conectar();

$sql = "SELECT CLIENTES.clienteID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
            FROM CLIENTES 
            JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID 
            ";
$result = $pdo->query($sql);

$empleados = array();
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $empleados[] = $row;
}

echo json_encode($empleados);
?>
