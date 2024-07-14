<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $personaID = trim($_POST['empleado']);
    $role = 'administrador';

    try {
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
            echo "Cuenta de administrador creada exitosamente";
        } else {
            throw new Exception("Error al insertar en CUENTAS.");
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
