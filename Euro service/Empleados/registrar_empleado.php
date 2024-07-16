<?php
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $alias = trim($_POST['alias']);
    $tipo = trim($_POST['tipo']);
    $role = 'administrador'; // This variable is unused in the current context

    // Validate inputs
    if (empty($nombre) || empty($apellido_paterno) || empty($apellido_materno) || empty($alias) || empty($tipo)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    try {
        // Insert into PERSONAS
        $stmt_persona = $pdo->prepare("INSERT INTO PERSONAS (nombre, apellido_paterno, apellido_materno) VALUES (?, ?, ?)");
        $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno]);

        if ($stmt_persona->rowCount() > 0) {
            $personaID = $pdo->lastInsertId();

            // Insert into EMPLEADOS
            $stmt_empleado = $pdo->prepare("INSERT INTO EMPLEADOS (personaID, alias, tipo) VALUES (?, ?, ?)");
            $stmt_empleado->execute([$personaID, $alias, $tipo]);

            if ($stmt_empleado->rowCount() > 0) {
                echo "Empleado agregado exitosamente.";
            } else {
                echo "Error al agregar el empleado.";
            }
        } else {
            echo "Error al agregar la persona.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
