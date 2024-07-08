<?php

require '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $clienteID = trim($_POST['clienteID']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    try {
        $stmt_update = $conn->prepare("UPDATE CLIENTES SET correo = ?, telefono = ? WHERE id = ?");
        $stmt_update->execute([$correo, $telefono, $clienteID]);
        
        if ($stmt_update->rowCount() > 0) {            echo "<div class='alert alert-success'>Cliente actualizado exitosamente.<br>Correo: <strong>$correo</strong><br>Teléfono: <strong>$telefono</strong></div>";

        } else {
            echo "<div class='alert alert-danger'>Error al actualizar el cliente. Puede que no se hayan hecho cambios.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
