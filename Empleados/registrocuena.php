<?php
session_start(); 
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $personaID = trim($_POST['empleado']);
    $role = 'administrador';

    try {
        // Verificar que las contraseñas coincidan
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header('Location: cuentaempleado.php');
            exit();
        }

        // Verificar que la contraseña tenga al menos 8 caracteres
        if (strlen($password) < 8) {
            $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
            header('Location: cuentaempleado.php');
            exit();
        }

        // Verificar si ya existe una cuenta para el personaID
        $stmt_verificar = $pdo->prepare("SELECT COUNT(*) FROM CUENTAS WHERE personaID = ?");
        $stmt_verificar->execute([$personaID]);
        $cuenta_existente = $stmt_verificar->fetchColumn();

        if ($cuenta_existente > 0) {
            $_SESSION['error'] = "Ya existe una cuenta para este empleado.";
            header('Location: cuentaempleado.php');
            exit();
        }

        // Hashear la contraseña
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Obtener rolID del rol 'administrador'
        $stmt_rol = $pdo->prepare("SELECT rolID FROM ROLES WHERE nombre_rol = ?");
        $stmt_rol->execute([$role]);
        $rol = $stmt_rol->fetch();
        $rolID = $rol['rolID'];

        // Insertar en CUENTAS
        $stmt_cuenta = $pdo->prepare("INSERT INTO CUENTAS (username, password, personaID, rolID) VALUES (?, ?, ?, ?)");
        $stmt_cuenta->execute([$username, $hashed_password, $personaID, $rolID]);

        if ($stmt_cuenta->rowCount() > 0) {
            $_SESSION['bien'] = "Cuenta de administrador creada exitosamente";
            header('Location: cuentaempleado.php');
        exit();
        } else {
           $_SESSION['error'] = "Error al insertar en CUENTAS.";
           header('Location: cuentaempleado.php');
        exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header('Location: cuentaempleado.php');
        exit();
    }
    header('Location: cuentaempleado.php');
    exit();
}
?>
