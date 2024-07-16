<?php
require '../includes/db.php'; // Asegúrate de que la ruta es correcta
$con = new Database();
$pdo = $con->conectar();

$searchTerm = $_GET['search'] ?? '';

$query = "SELECT clienteID, nombre, apellido_paterno, apellido_materno, telefono, correo FROM CLIENTES 
          INNER JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID
          WHERE nombre LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ?";
$stmt = $pdo->prepare($query);
$likeTerm = '%' . $searchTerm . '%';
$stmt->execute([$likeTerm, $likeTerm, $likeTerm]);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result);
?>
