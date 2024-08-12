<?php
session_start();
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y limpiar datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $salario = trim($_POST['salario']);
    $alias = trim($_POST['alias']);
    $tipo = trim($_POST['tipo']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    // Validar que los campos no estén vacíos y no contengan solo espacios
    if (empty($nombre) || empty($apellido_paterno) || empty($apellido_materno) || empty($alias) || empty($tipo) ||
        ctype_space($nombre) || ctype_space($apellido_paterno) || ctype_space($apellido_materno) || ctype_space($alias) ||
        ctype_space($tipo) || ctype_space($correo) || ctype_space($telefono)) {
        $_SESSION['error'] = "Todos los campos son obligatorios y no pueden contener solo espacios.";
        header('Location: registro_empleado.php'); 
        exit();
    }

    // Validar longitud de los campos
    if (strlen($nombre) > 100 || strlen($apellido_paterno) > 100 || strlen($apellido_materno) > 100 || strlen($alias) > 50) {
        $_SESSION['error'] = "Uno o más campos tienen una longitud excesiva.";
        header('Location: registro_empleado.php'); 
        exit();
    }

    try {
        // Insertar en PERSONAS
        $stmt_persona = $pdo->prepare("INSERT INTO PERSONAS (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES (?, ?, ?, ?, ?)");
        $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono]);

        if ($stmt_persona->rowCount() > 0) {
            $personaID = $pdo->lastInsertId();

            // Insertar en EMPLEADOS
            $stmt_empleado = $pdo->prepare("INSERT INTO EMPLEADOS (personaID, salario_diario, alias, tipo, activo) VALUES (?, ?, ?, ?, ?)");
            $stmt_empleado->execute([$personaID, $salario, $alias, $tipo, 'si']);

            if ($stmt_empleado->rowCount() > 0) {
                $_SESSION['bien'] = "Empleado agregado exitosamente.";
                header('Location: registro_empleado.php'); 
                exit();
            } else {
                $_SESSION['error'] = "Error al agregar el empleado.";
                header('Location: registro_empleado.php'); 
                exit();
            }
        } else {
            $_SESSION['error'] = "Error al agregar la persona.";
            header('Location: registro_empleado.php'); 
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header('Location: registro_empleado.php'); 
        exit();
    }
}
?>
