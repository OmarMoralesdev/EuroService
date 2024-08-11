<?php
// Conectar a la base de datos
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

// Función para cambiar la contraseña
function cambiarContrasena($username, $nuevaContrasena) {
    global $pdo;

    // Generar el hash de la nueva contraseña
    $hash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);

    // Preparar la consulta SQL
    $sql = "UPDATE CUENTAS SET password = :password WHERE username = :username";
    $stmt = $pdo->prepare($sql);

    // Ejecutar la consulta
    $stmt->execute([
        ':password' => $hash,
        ':username' => $username
    ]);

    if ($stmt->rowCount() > 0) {
        echo "Contraseña actualizada exitosamente.";
    } else {
        echo "No se encontró ningún usuario con ese username.";
    }
}

// Obtener datos de entrada del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';

    if (!empty($username) && !empty($nuevaContrasena)) {
        cambiarContrasena($username, $nuevaContrasena);
    } else {
        echo "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
</head>
<body>
    <h1>Cambiar Contraseña</h1>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br><br>
        <label for="nueva_contrasena">Nueva Contraseña:</label>
        <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
        <br><br>
        <input type="submit" value="Cambiar Contraseña">
    </form>
</body>
</html>
