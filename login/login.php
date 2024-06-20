<?php
// db.php: archivo de configuración de la base de datos
require '../includes/db.php';

// Iniciar sesión
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Preparar y ejecutar consulta
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Verificar si el usuario existe
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $hashed_password);
            $stmt->fetch();

            // Verificar la contraseña
            if (password_verify($password, $hashed_password)) {
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $db_username;
                echo "Inicio de sesión exitoso!";
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "No se encontró el usuario.";
        }

        $stmt->close();
    } else {
        echo "Error en la consulta: " . $conn->error;
    }

    $conn->close();
}
?>
