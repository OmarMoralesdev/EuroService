<?php

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

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
    
    try {
        // Insertar en PERSONAS
        $stmt_persona = $pdo->prepare("INSERT INTO PERSONAS (nombre, apellido_paterno, apellido_materno) VALUES (?, ?, ?)");
        $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno]);
        
        if ($stmt_persona->rowCount() > 0) {
            $personaID = $pdo->lastInsertId();
            
            // Insertar en CLIENTES
            $stmt_cliente = $pdo->prepare("INSERT INTO CLIENTES (personaID, correo, telefono) VALUES (?, ?, ?)");
            $stmt_cliente->execute([$personaID, $correo, $telefono]);
            
            if ($stmt_cliente->rowCount() > 0) {
                $clienteID = $pdo->lastInsertId();
                
                // Obtener rolID del rol 'cliente'
                $stmt_rol = $pdo->prepare("SELECT rolID FROM ROLES WHERE nombre_rol = ?");
                $stmt_rol->execute([$role]);
                $rol = $stmt_rol->fetch();
                $rolID = $rol['rolID'];
                
                // Insertar en CUENTAS
                $stmt_cuenta = $pdo->prepare("INSERT INTO CUENTAS (username, password, personaID, rolID) VALUES (?, ?, ?, ?)");
                $stmt_cuenta->execute([$correo, $hashed_password, $personaID, $rolID]);
                
                if ($stmt_cuenta->rowCount() > 0) {
                    echo "<div class='alert alert-success'>Usuario registrado exitosamente.<br>Nombre de usuario: <strong>$correo</strong><br>Contraseña: <strong>$password</strong></div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al insertar la cuenta.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error al insertar el cliente.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Error al insertar la persona.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
