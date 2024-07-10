<?php
session_start();

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT cuentaID, username, password, personaID, rolID FROM CUENTAS WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cuentaID = $row['cuentaID'];
            $db_username = $row['username'];
            $hashed_password = $row['password'];
            $personaID = $row['personaID'];
            $role = $row['rolID'];

            if (password_verify($password, $hashed_password)) {
                // Obtener clienteID desde personaID
                $sql_cliente = "SELECT clienteID FROM CLIENTES WHERE personaID = ?";
                $stmt_cliente = $pdo->prepare($sql_cliente);
                $stmt_cliente->execute([$personaID]);
                $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
                $clienteID = $cliente['clienteID'];

                $_SESSION['cuentaID'] = $cuentaID;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role;
                $_SESSION['clienteID'] = $clienteID;

                // Redireccionar según el rol
                if ($role == 1) { 
                    header("Location: ../Cliente/client.php");
                    exit();
                } elseif ($role == 2) { 
                    header("Location: ../general_views/admin.php");
                    exit();
                } elseif ($role == 3) { 
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
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
}
?>
