<?php
// cambiar_contraseña.php
session_start();
require '../../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevaContraseña = $_POST['nueva_contraseña'];
    $confirmarContraseña = $_POST['confirmar_contraseña'];
    $emailRecuperacion = $_SESSION['email_recuperacion'];

    if ($nuevaContraseña === $confirmarContraseña) {
        // Actualizar la contraseña en la base de datos
        $hashContraseña = password_hash($nuevaContraseña, PASSWORD_BCRYPT);
        
        $sql = "UPDATE CUENTAS 
                SET password = :password 
                WHERE personaID = (SELECT personaID FROM CLIENTES WHERE correo = :correo)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password', $hashContraseña);
        $stmt->bindParam(':correo', $emailRecuperacion);
        
        if ($stmt->execute()) {
            echo "Contraseña cambiada correctamente.";
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
