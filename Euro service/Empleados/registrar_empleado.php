<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $role = 'administrador';
    $alias = $_POST['alias'];
    $tipo = $_POST['tipo'];

    try {

        $stmt_persona = $pdo->prepare("INSERT INTO PERSONAS (nombre, apellido_paterno, apellido_materno) VALUES (?, ?, ?)");
        $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno]);

        if ($stmt_persona->rowCount() > 0) {
            $personaID = $pdo->lastInsertId();

            // Insertar en EMPLEADOS
            $stmt_empleado = $pdo->prepare("INSERT INTO EMPLEADOS (personaID, alias, tipo) VALUES (?, ?, ?)");
            $stmt_empleado->execute([$personaID, $alias, $tipo]);

            if ($stmt_empleado->rowCount() > 0) {
                if ($tipo === 'administrativo') {
                    // Obtener rolID del rol 'administrador'
                    $role = 'administrador';
                    $stmt_rol = $pdo->prepare("SELECT rolID FROM ROLES WHERE nombre_rol = ?");
                    $stmt_rol->execute([$role]);
                    $rol = $stmt_rol->fetch();
                    $rolID = $rol['rolID'];

                    // Insertar en CUENTAS
                    $stmt_cuenta = $pdo->prepare("INSERT INTO CUENTAS (username, password, personaID, rolID) VALUES (?, ?, ?, ?)");
                    $stmt_cuenta->execute([$username, $hashed_password, $personaID, $rolID]);

                    if ($stmt_cuenta->rowCount() == 0) {
                        throw new Exception("Error al insertar en CUENTAS.");
                    }
                }
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
