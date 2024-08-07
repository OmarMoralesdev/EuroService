<?php
session_start();

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
 echo"inicio de login";
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
   echo"se entro en el if";
    $username = $_POST['username'];
    echo"imgreso nombre";
    $password = $_POST['password'];
    echo"entro contra";

    try {
        $sql = "SELECT cuentaID, username, password, personaID, rolID FROM CUENTAS WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        echo"consulta de usuario";

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cuentaID = $row['cuentaID'];
            $db_username = $row['username'];
            $hashed_password = $row['password'];
            $personaID = $row['personaID'];
            $role = $row['rolID'];
            echo"verifica";
            
            if (password_verify($password, $hashed_password)) {
                echo"verifica contra";

                // Verificar si la persona es un cliente
                $sql_cliente = "SELECT clienteID FROM CLIENTES WHERE personaID = ?";
                $stmt_cliente = $pdo->prepare($sql_cliente);
                $stmt_cliente->execute([$personaID]);
                $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
                echo"verifica si es cliente";

                // Verificar si la persona es un empleado
                $sql_empleado = "SELECT empleadoID FROM EMPLEADOS WHERE personaID = ?";
                $stmt_empleado = $pdo->prepare($sql_empleado);
                $stmt_empleado->execute([$personaID]);
                $empleado = $stmt_empleado->fetch(PDO::FETCH_ASSOC);
                echo"verifica empleado";

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
                    header("Location: ../Cliente/index.php");
                    echo"si es cliente";
                    exit();
                    
                } elseif ($role == 2) {
                    header("Location: ../general_views/admin.php");
                    echo"si es admin";
                    exit();
                } elseif ($role == 3) {
                    header("Location: ../dueño/dueño.php");
                    echo"si es dueño";
                    exit();
                } else {
                    header("Location: ../EuroService/index.php/#navbarNav");
                    echo"barra";
                    exit();
                }
            } else {
                header("Location: ../EuroService/index.php/#navbarNav");
                echo"barra2";
                    exit();
            }
        }
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        
    }
}
