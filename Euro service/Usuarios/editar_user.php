<?php

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    // Actualiza la base de datos
    $sql = "UPDATE CLIENTES SET correo = ?, telefono = ? WHERE clienteID = ?";
    
    // Usa PDO para preparar la declaración
    $stmt = $pdo->prepare($sql);
    
    // Ejecuta la consulta
    if ($stmt->execute([$correo, $telefono, $clienteID])) {
        echo "Datos actualizados correctamente.";
    } else {
        echo "Error al actualizar los datos.";
    }
}
?>
