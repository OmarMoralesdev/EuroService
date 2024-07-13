<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$campo = filter_input(INPUT_POST, 'campo', FILTER_SANITIZE_STRING);

if ($campo) {
    $sql = "SELECT CLIENTES.clienteID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
            FROM CLIENTES 
            JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID 
            WHERE PERSONAS.nombre LIKE ? OR PERSONAS.apellido_paterno LIKE ? OR PERSONAS.apellido_materno LIKE ? 
            ORDER BY PERSONAS.nombre ASC 
            LIMIT 10";
    $query = $pdo->prepare($sql);
    $query->execute([$campo . '%', $campo . '%', $campo . '%']);

    $clientes = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $clientes[] = $row;
    }

    echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([]);
}
?>
