<?php
// Iniciar sesión
session_start();

// Incluir archivo de configuración de la base de datos
require '../includes/db.php';

// Verificar si el método de solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    // Preparar y ejecutar consulta
    $sql = "SELECT id, username, password, roles FROM users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Verificar si el usuario existe
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $hashed_password, $role);
            $stmt->fetch();

            // Verificar la contraseña
            if (password_verify($password, $hashed_password)) {
                // Establecer variables de sesión
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role;

                // Redireccionar según el rol del usuario
                if ($role === 'cliente') {
                    header("Location: ../client_view/client.html");
                    exit();
                } elseif ($role === 'administrador') {
                    header("Location: ../includes/vabr.html");
                    exit();
                } elseif ($role === 'dueño') {
                    header("Location: ../owner_view/owner.html");
                    exit();
                } else {
                    echo "Rol no reconocido.";
                }
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "No se encontró el usuario.";
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo "Error en la consulta: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
