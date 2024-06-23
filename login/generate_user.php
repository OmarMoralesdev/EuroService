<?php
require '../includes/db.php'; // Incluye el archivo de conexión a la base de datos

function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $password = generateRandomPassword();
    
    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Definir el rol del usuario según tus requisitos
    $role = 'cliente'; // Ejemplo
    
    // Obtener la conexión a la base de datos desde el archivo db.php
    global $conn; // Permite utilizar la variable $conn declarada en db.php
    
    // Iniciar una transacción
    $conn->beginTransaction();
    
    try {
        // Insertar el nuevo cliente en la tabla CLIENTES
        $stmt_cliente = $conn->prepare("INSERT INTO CLIENTES (nombre, apellido_paterno, apellido_materno, correo, telefono, roles) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_cliente->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono, $role]);
        
        if ($stmt_cliente->rowCount() > 0) {
            // Obtener el ID del cliente recién insertado
            $clienteID = $conn->lastInsertId();
            
            // Insertar el nuevo usuario en la tabla users
            $stmt_usuario = $conn->prepare("INSERT INTO users (username, roles, password, clienteID) VALUES (?, ?, ?, ?)");
            $stmt_usuario->execute([$correo, $role, $hashed_password, $clienteID]);
            
            if ($stmt_usuario->rowCount() > 0) {
                // Confirmar la transacción
                $conn->commit();
                echo "<div class='alert alert-success'>Usuario registrado exitosamente.<br>Nombre de usuario: <strong>$correo</strong><br>Contraseña: <strong>$password</strong></div>";
            } else {
                throw new Exception('Error al insertar el usuario.');
            }
        } else {
            throw new Exception('Error al insertar el cliente.');
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
