<?php
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
    

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'cliente'; 
    $conn->beginTransaction();
    
    try {
      
        $stmt_cliente = $conn->prepare("INSERT INTO CLIENTES (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES ( ?, ?, ?, ?, ?)");
        $stmt_cliente->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono]);
        
        if ($stmt_cliente->rowCount() > 0) {
          
            $clienteID = $conn->lastInsertId();
            
         
            $stmt_usuario = $conn->prepare("INSERT INTO users (username, roles, password, clienteID) VALUES (?, ?, ?, ?)");
            $stmt_usuario->execute([$correo, $role, $hashed_password, $clienteID]);
            
            if ($stmt_usuario->rowCount() > 0) {

                $conn->commit();
                echo "<div class='alert alert-success'>Usuario registrado exitosamente.<br>Nombre de usuario: <strong>$correo</strong><br>Contraseña: <strong>$password</strong></div>";
            } else {
                throw new Exception('Error al insertar el usuario.');
            }
        } else {
            throw new Exception('Error al insertar el cliente.');
        }
    } catch (Exception $e) {
     
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
