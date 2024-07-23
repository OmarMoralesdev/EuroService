<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

function generateRandomPassword($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

function generarUsernameParaCliente($pdo, $personaID) {
    $sql = "SELECT nombre, apellido_paterno FROM personas WHERE personaID = :personaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['personaID' => $personaID]);

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $firstLetter = strtoupper(substr($row['nombre'], 0, 1));
        $baseUsername = $firstLetter . strtolower($row['apellido_paterno']);
        
        $username = $baseUsername;
        $counter = 1;

        while (usernameExists($pdo, $username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    return null;
}

function usernameExists($pdo, $username) {
    $sql = "SELECT COUNT(*) FROM cuentas WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    return $stmt->fetchColumn() > 0;
}


$showModal = false;
$modalContent = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido_paterno = trim($_POST['apellido_paterno']);
    $apellido_materno = trim($_POST['apellido_materno']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    if (
        preg_match('/^[a-zA-Z\s]+$/', $nombre) &&
        preg_match('/^[a-zA-Z\s]+$/', $apellido_paterno) &&
        preg_match('/^[a-zA-Z\s]+$/', $apellido_materno) &&
        filter_var($correo, FILTER_VALIDATE_EMAIL) &&
        preg_match('/^\d{10}$/', $telefono)
    ) {
        
        $password = generateRandomPassword();
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'cliente';

        try {
            // Insertar en PERSONAS
            $stmt_persona = $pdo->prepare("INSERT INTO personas (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES (?, ?, ?, ?, ?)");
            $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono]);

            if ($stmt_persona->rowCount() > 0) {
                $personaID = $pdo->lastInsertId();
                $username = generarUsernameParaCliente($pdo, $personaID);

                if ($username === null) {
                    throw new Exception('No se pudo generar un nombre de usuario único.');
                }

                // Insertar en CLIENTES
                $stmt_cliente = $pdo->prepare("INSERT INTO clientes (personaID) VALUES (?)");
                $stmt_cliente->execute([$personaID]);

                if ($stmt_cliente->rowCount() > 0) {
                    $clienteID = $pdo->lastInsertId();

                    // Obtener rolID del rol 'cliente'
                    $stmt_rol = $pdo->prepare("SELECT rolID FROM roles WHERE nombre_rol = ?");
                    $stmt_rol->execute([$role]);
                    $rol = $stmt_rol->fetch();
                    $rolID = $rol['rolID'];

                    // Insertar en CUENTAS
                    $stmt_cuenta = $pdo->prepare("INSERT INTO cuentas (username, password, personaID, rolID) VALUES (?, ?, ?, ?)");
                    $stmt_cuenta->execute([$username, $hashed_password, $personaID, $rolID]);

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
                                            Cuenta del cliente: <strong>$username</strong><br><br>
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
                    }
                }
            }
        } catch (PDOException $e) {
            $showModal = true;
            $errorMessage = $e->getMessage();

            if (strpos($errorMessage, 'Duplicate entry') !== false && strpos($errorMessage, 'for key \'telefono\'') !== false) {
                $modalContent = "
                    <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR AL REGISTRAR LA CUENTA</h1>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    El teléfono ya está registrado. Por favor, utiliza otro número teléfonico.
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>";
            } elseif (strpos($errorMessage, 'Duplicate entry') !== false && strpos($errorMessage, 'for key \'correo\'') !== false) {
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
    } else {
        $showModal = true;
        $modalContent = "
            <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR AL REGISTRAR LA CUENTA</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            Por favor, asegúrate de que todos los campos estén correctamente llenados.
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>";
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

        input[type=text],
        input[type=email] {
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
                            <input type="text" class="form-control" id="nombre" name="nombre" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="apellido_paterno">Apellido Paterno:</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="apellido_materno">Apellido Materno:</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required pattern="[a-zA-Z\s]+" title="Solo letras y espacios">
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo electrónico:</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required pattern="\d{10}" title="Debe contener 10 dígitos">
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
        <?php if ($showModal) : ?>
            var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'), {
                keyboard: false
            });
            myModal.show();
        <?php endif; ?>
    </script>
</body>

</html>