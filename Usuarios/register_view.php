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

$showModal = false;
$modalContent = '';

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
                    $showModal = true;
                    $modalContent = "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Usuario registrado!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        Cuenta del cliente: <strong>$correo</strong><br><br>
                                        Contraseña del cliente: <strong>$password</strong><br><hr>
                                        Presiona siguiente para registrar su vehículo
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                        <a href='../vehiculos/autos_view.php' type='button' class='btn btn-dark'>Siguiente</a>
                                    </div>
                                </div>
                            </div>
                        </div>";
                            }            }
        } 
    } catch (PDOException $e) {
        $showModal = true;
        $errorMessage = $e->getMessage();
        
        if (strpos($errorMessage, 'Duplicate entry') !== false && strpos($errorMessage, 'for key \'correo\'') !== false) {
            $modalContent = "
                <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR AL REGISTRAR LA CUENTA</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                El correo electrónico ya está registrado. Por favor, utiliza otro correo electrónico.
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>";
        } else {
            $modalContent = "
                <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR AL REGISTRAR LA CUENTA</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                Ha ocurrido un error al registrar la cuenta. Por favor, inténtalo de nuevo más tarde.
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO CLIENTE</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        .form-group {
            margin-bottom: 5px;
        }
        h2 {
            text-transform: uppercase;
            text-align: center;
        }
        input[type=text], input[type=email] {
            color: black;
        }
        .btn {
            width: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?> <!-- barra lateral -->
        <div class="main p-3">
            <div class="container">
                <h2>REGISTRAR CLIENTE</h2>
                <div class="form-container">
                    <br>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido_paterno">Apellido Paterno:</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido_materno">Apellido Materno:</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo electrónico:</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-dark btn-block">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <?php echo $modalContent; ?>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <script>
        <?php if ($showModal): ?>
            var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'), {
                keyboard: false
            });
            myModal.show();
        <?php endif; ?>
    </script>
</body>
</html>
