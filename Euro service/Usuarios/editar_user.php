<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$clienteID = $_POST['clienteID'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];

// Verificar si el cliente existe
$verificar = "SELECT * FROM CLIENTES WHERE clienteID = ?";
$stmtVerificar = $pdo->prepare($verificar);
$stmtVerificar->execute([$clienteID]);

if ($stmtVerificar->rowCount() > 0) {
    // Actualizar los datos del cliente
    $sql = "UPDATE CLIENTES SET correo = ?, telefono = ? WHERE clienteID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$correo, $telefono, $clienteID]);

    if ($stmt->rowCount() > 0) {
        echo "Datos del cliente actualizados exitosamente.";
    } else {
        echo "No se realizaron cambios.";
    }
} else {
    echo "El cliente no está registrado.";
}
?>
