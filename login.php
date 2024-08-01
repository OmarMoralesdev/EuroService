<?php
session_start();

require '../EuroService/includes/db.php';
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
            

                // Verificar si la persona es un cliente
                $sql_cliente = "SELECT clienteID FROM CLIENTES WHERE personaID = ?";
                $stmt_cliente = $pdo->prepare($sql_cliente);
                $stmt_cliente->execute([$personaID]);
                $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

                // Verificar si la persona es un empleado
                $sql_empleado = "SELECT empleadoID FROM EMPLEADOS WHERE personaID = ?";
                $stmt_empleado = $pdo->prepare($sql_empleado);
                $stmt_empleado->execute([$personaID]);
                $empleado = $stmt_empleado->fetch(PDO::FETCH_ASSOC);

                if ($cliente) {
                    $_SESSION['clienteID'] = $cliente['clienteID'];
                } elseif ($empleado) {
                    $_SESSION['empleadoID'] = $empleado['empleadoID'];
                }

                $_SESSION['cuentaID'] = $cuentaID;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role;
                $_SESSION['personaID'] =  $personaID;
                // Redireccionar según el rol
                if ($role == 1) {
                    header("Location: ../EuroService/Cliente");
                    exit();
                } elseif ($role == 2) {
                    header("Location: ../EuroService/general_views/admin.php");
                    exit();
                } elseif ($role == 3) {
                    header("Location: ../EuroService/dueño/dueño.php");
                    exit();
                } else {
                    echo "Rol no reconocido.";
             
                    header("Location: ../EuroService");
                    exit();
                }
            } else {
                echo "Contraseña incorrecta.";
                header("Location: ../EuroService");
                    exit();
            }
        } else {
            header("Location: ../EuroService");
            exit();
            
            echo "<div class='modal' tabindex='-1'>";
            echo "<div class='modal-dialog'>";
            echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
            echo "<h5 class='modal-title'>Modal title</h5>";
            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
            echo "</div>";
            echo "<div class='modal-body'>";
            echo "<p>Modal body text goes here.</p>";
            echo "</div>";
            echo "<div class='modal-footer'>";
            echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
}
