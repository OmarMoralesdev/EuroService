<?php
include('../includes/header.php');
require '../includes/db.php';

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
    
    // Generar nombre de usuario basado en el correo electrónico
    $username = explode('@', $correo)[0];
    
    // Iniciar una transacción
    $conn->begin_transaction();
    
    try {
        // Insertar el nuevo cliente en la tabla CLIENTES
        $stmt_cliente = $conn->prepare("INSERT INTO CLIENTES (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES (?, ?, ?, ?, ?)");
        if ($stmt_cliente === false) {
            throw new Exception('Error en la preparación de la consulta: ' . htmlspecialchars($conn->error));
        }
        
        $stmt_cliente->bind_param("sssss", $nombre, $apellido_paterno, $apellido_materno, $correo, $telefono);
        $stmt_cliente->execute();
        
        if ($stmt_cliente->affected_rows > 0) {
            // Obtener el ID del cliente recién insertado
            $clienteID = $stmt_cliente->insert_id;
            
            // Insertar el nuevo usuario en la tabla USUARIOS
            $stmt_usuario = $conn->prepare("INSERT INTO users (username, password, clienteID) VALUES (?, ?, ?)");
            if ($stmt_usuario === false) {
                throw new Exception('Error en la preparación de la consulta: ' . htmlspecialchars($conn->error));
            }
            
            $stmt_usuario->bind_param("ssi", $username, $hashed_password, $clienteID);
            $stmt_usuario->execute();
            
            if ($stmt_usuario->affected_rows > 0) {
                // Confirmar la transacción
                $conn->commit();
                echo "<div class='alert alert-success'>Usuario registrado exitosamente.<br>Nombre de usuario: <strong>$username</strong><br>Contraseña: <strong>$password</strong></div>";
            } else {
                throw new Exception('Error al insertar el usuario.');
            }
            
            $stmt_usuario->close();
        } else {
            throw new Exception('Error al insertar el cliente.');
        }
        
        $stmt_cliente->close();
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

$conn->close();
include('../includes/footer.php');
?>
