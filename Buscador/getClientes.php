<?php
require '../includes/db.php'; // Asegúrate de que la ruta es correcta
$con = new Database();
$pdo = $con->conectar();

$searchTerm = $_GET['search'] ?? '';

$query = "SELECT CLIENTES.clienteID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno, PERSONAS.telefono, PERSONAS.correo 
          FROM CLIENTES 
          INNER JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID
          WHERE CLIENTES.activo = 'si' 
            AND (PERSONAS.nombre LIKE ? OR PERSONAS.apellido_paterno LIKE ? OR PERSONAS.apellido_materno LIKE ?)";

$stmt = $pdo->prepare($query);
$likeTerm = '%' . $searchTerm . '%';
$stmt->execute([$likeTerm, $likeTerm, $likeTerm]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result);
?>
