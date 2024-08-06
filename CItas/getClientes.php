<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$campo = filter_input(INPUT_POST, 'campo', FILTER_SANITIZE_STRING);

if ($campo) {
    $sql = "SELECT CLIENTES.clienteID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno, PERSONAS.telefono
            FROM CLIENTES 
            JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID 
            WHERE CLIENTES.activo = 'si' and PERSONAS.nombre LIKE ? OR PERSONAS.apellido_paterno LIKE ? OR PERSONAS.apellido_materno LIKE ? 
            ORDER BY PERSONAS.nombre ASC 
            LIMIT 10";
    $query = $pdo->prepare($sql);
    $query->execute([$campo . '%', $campo . '%', $campo . '%']);

    $clientes = [];
    // Recorrer los resultados
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        // Agregar los resultados al arreglo
        $clientes[] = $row;
    }
    // Enviar los resultados en formato JSON al frontend para ser procesados por JavaScript y renderizados en la vista  
    echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
} else {
    // Si no se ha enviado un campo de búsqueda, enviar un arreglo vacío
    echo json_encode([]);
}
?>
