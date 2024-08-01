<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$showModal = false;
$showAlert = false;

function generateRandomPassword($length = 10) {
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

// Modal de éxito
function setModalContent($type, $message) {
    $_SESSION['modal'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Alerta de errores
function setAlertContent($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Verificar si existe el usuario
function usernameExists($pdo, $username) {
    $sql = "SELECT COUNT(*) FROM cuentas WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    return $stmt->fetchColumn() > 0;
}

function checkDuplicate($pdo, $correo, $telefono, $personaID) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE (correo = :correo OR telefono = :telefono) AND personaID <> :personaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'correo' => $correo,
        'telefono' => $telefono,
        'personaID' => $personaID
    ]);
    return $stmt->fetchColumn() > 0;
}

function checkEmailDuplicate($pdo, $correo) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE correo = :correo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['correo' => $correo]);
    return $stmt->fetchColumn() > 0;
}

function checkPhoneDuplicate($pdo, $telefono) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE telefono = :telefono";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['telefono' => $telefono]);
    return $stmt->fetchColumn() > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nombre = trim($_POST['nombre']);
        $apellido_paterno = trim($_POST['apellido_paterno']);
        $apellido_materno = trim($_POST['apellido_materno']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        
        // Validar datos desde la aplicación
        if (
            preg_match('/^[a-zA-Z\s]+$/', $nombre) &&
            preg_match('/^[a-zA-Z\s]+$/', $apellido_paterno) &&
            preg_match('/^[a-zA-Z\s]+$/', $apellido_materno) &&
            filter_var($correo, FILTER_VALIDATE_EMAIL) &&
            preg_match('/^\d{10}$/', $telefono)
        ) {
            // Verificar si el correo ya existe
            if (checkEmailDuplicate($pdo, $correo)) {
                setAlertContent('error', "
                    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Error:</strong> El correo ya está en uso.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>");
                header('Location: vista_registro_cliente.php');
                exit();
            }

            // Verificar si el teléfono ya existe
            if (checkPhoneDuplicate($pdo, $telefono)) {
                setAlertContent('error', "
                    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Error:</strong> El teléfono ya está en uso.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>");
                header('Location: vista_registro_cliente.php');
                exit();
            }

            $password = generateRandomPassword();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'cliente';
            
            $stmt_persona = $pdo->prepare("INSERT INTO personas (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES (?, ?, ?, ?, ?)");
            $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono]);
            
            if ($stmt_persona->rowCount() > 0) {
                $personaID = $pdo->lastInsertId();
                $username = generarUsernameParaCliente($pdo, $personaID);
                
                if ($username === null) {
                    throw new Exception('No se pudo generar un nombre de usuario único.');
                }
                
                // Insertar un nuevo cliente y activo
                $activo = 'si';
                $stmt_cliente = $pdo->prepare("INSERT INTO clientes (personaID, activo) VALUES (?, ?)");
                $stmt_cliente->execute([$personaID, $activo]);
                
                if ($stmt_cliente->rowCount() > 0) {
                    $clienteID = $pdo->lastInsertId();
                    
                    $stmt_rol = $pdo->prepare("SELECT rolID FROM roles WHERE nombre_rol = ?");
                    $stmt_rol->execute([$role]);
                    $rol = $stmt_rol->fetch();
                    $rolID = $rol['rolID'];
                    
                    // Contenido de validaciones
                    $showModal = false;
                    $showAlert = false;
                    
                    // Insertar un nuevo usuario exitosamente
                    $stmt_cuenta = $pdo->prepare("INSERT INTO cuentas (username, password, personaID, rolID) VALUES (?, ?, ?, ?)");
                    $stmt_cuenta->execute([$username, $hashed_password, $personaID, $rolID,]);
                    if ($stmt_cuenta->rowCount() > 0) {
                        setModalContent('success', "
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
                        </div>");
                        $showModal = true;
                    }
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'euroservice339@gmail.com';
                        $mail->Password = '';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        
                        $mail->setFrom('euroservice339@gmail.com', 'EuroService');
                        $mail->addAddress($correo);
                        
                        $mail->isHTML(true);
                        $mail->Subject = 'Creacion de cuenta';
                        $mail->Body    = "Hola, <br><br>Tu cuenta ha sido creada con éxito. Tu usuario es: $username y tu contraseña es: $password";
                        
                        $mail->send();
                    } catch (Exception $e) {
                        echo "No se pudo enviar el mensaje. Error de correo: {$mail->ErrorInfo}";
                    }
                }
            }
        }
    } catch (Exception $e) {
        setAlertContent('error', "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Error:</strong> Se produjo un error inesperado. Por favor, intente nuevamente.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>");
        error_log($e->getMessage()); // Registrar el error en el archivo de log
    }
    
    header('Location: vista_registro_cliente.php');
    exit();
}
?>
