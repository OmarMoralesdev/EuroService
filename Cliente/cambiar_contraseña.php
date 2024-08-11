<?php
// cambiar_contraseña.php
session_start();
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clienteID = $_POST['cliente'];
    $nuevaContraseña = $_POST['nueva_contraseña'];
    $confirmarContraseña = $_POST['confirmar_contraseña'];

    // Validar que las contraseñas coincidan
    if ($nuevaContraseña === $confirmarContraseña) {
        // Hash de la nueva contraseña
        $hashContraseña = password_hash($nuevaContraseña, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $sql = "UPDATE CUENTAS 
                SET password = :password 
                WHERE personaID = (SELECT personaID FROM CLIENTES WHERE clienteID = :clienteID)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password', $hashContraseña);
        $stmt->bindParam(':clienteID', $clienteID);

        if ($stmt->execute()) {
            // Redirigir al usuario a la página de inicio de sesión
            header("Location: ../../login/login_view.php");
            exit();
        } else {
            echo "Error al cambiar la contraseña. Inténtalo de nuevo.";
        }
    } else {
        echo "Las contraseñas no coinciden. Inténtalo de nuevo.";
    }
}
?>
