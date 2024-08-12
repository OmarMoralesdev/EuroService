<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
/*require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;*/

$showModal = false;
$showAlert = false;

// Generar contraseña aleatoria de 10 caracteres de longitud con letras y números aleatorios (mayúsculas y minúsculas) 
function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    // Inicializar la contraseña aleatoria
    $randomPassword = '';
    // Generar una contraseña aleatoria de la longitud especificada con caracteres aleatorios de la lista de caracteres
    for ($i = 0; $i < $length; $i++) {
        // Agregar un carácter aleatorio de la lista de caracteres a la contraseña aleatoria 
        $randomPassword .= $characters[random_int(0, $charactersLength - 1)];
    }
    // Devolver la contraseña aleatoria generada
    return $randomPassword;
}

// Generar un nombre de usuario único para un cliente
function generarUsernameParaCliente($pdo, $personaID) {
    $sql = "SELECT nombre, apellido_paterno FROM PERSONAS WHERE personaID = :personaID";
    $stmt = $pdo->prepare($sql);
    // Ejecutar la consulta con el ID de la persona
    $stmt->execute(['personaID' => $personaID]);
    
    // Obtener la fila de la consulta
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Obtener la primera letra del nombre en mayúscula
        $firstLetter = strtoupper(substr($row['nombre'], 0, 1));
        // Crear un nombre de usuario base con la primera letra del nombre y el apellido paterno en minúscula
        $baseUsername = $firstLetter . strtolower($row['apellido_paterno']);
        // Inicializar el nombre de usuario con el nombre de usuario base
        $username = $baseUsername;
        // Inicializar el contador
        $counter = 1;
        
        // Verificar si el nombre de usuario ya existe en la base de datos
        while (usernameExists($pdo, $username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }                
        // Devolver el nombre de usuario generado
        return $username;
    }
    // Devolver nulo si no se pudo obtener el nombre y apellido de la persona
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

// Verificar si existe el usuario en la base de datos por nombre de usuario 
function usernameExists($pdo, $username) {
    $sql = "SELECT COUNT(*) FROM CUENTAS WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    return $stmt->fetchColumn() > 0;
}

// Verificar si el correo o el teléfono ya existen en la base de datos
function checkDuplicate($pdo, $correo, $telefono, $personaID) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE (correo = :correo OR telefono = :telefono) AND personaID <> :personaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'correo' => $correo,
        'telefono' => $telefono,
        'personaID' => $personaID
    ]);
    // Devolver verdadero si el correo o el teléfono ya existen en la base de datos
    return $stmt->fetchColumn() > 0;
}

// Verificar si el correo ya existe en la base de datos
function checkEmailDuplicate($pdo, $correo) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE correo = :correo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['correo' => $correo]);
    return $stmt->fetchColumn() > 0;
}

// Verificar si el teléfono ya existe en la base de datos
function checkPhoneDuplicate($pdo, $telefono) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE telefono = :telefono";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['telefono' => $telefono]);
    return $stmt->fetchColumn() > 0;
}

// Verificar si se envió un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nombre = trim($_POST['nombre']);
        $apellido_paterno = trim($_POST['apellido_paterno']);
        $apellido_materno = trim($_POST['apellido_materno']);
        $correo = trim($_POST['correo']);
        $telefono = trim($_POST['telefono']);
        
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
            // Insertar un nuevo cliente
            $password = generateRandomPassword();
            // Hash de la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Rol del usuario
            $role = 'cliente';
            
            // Insertar un nuevo cliente
            $stmt_persona = $pdo->prepare("INSERT INTO PERSONAS (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES (?, ?, ?, ?, ?)");         
               // Ejecutar la consulta con los datos del cliente
            $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono]);
            
            // Verificar si se insertó un nuevo cliente
            if ($stmt_persona->rowCount() > 0) {
                // Obtener el ID de la persona recién insertada
                $personaID = $pdo->lastInsertId();
                // Generar un nombre de usuario único para el cliente
                $username = generarUsernameParaCliente($pdo, $personaID);
                
                // Verificar si se pudo generar un nombre de usuario único
                if ($username === null) {
                    // Lanzar una excepción si no se pudo generar un nombre de usuario único
                    throw new Exception('No se pudo generar un nombre de usuario único.');
                }
                
                // Insertar un nuevo cliente y activo
                $activo = 'si';
                $stmt_cliente = $pdo->prepare("INSERT INTO CLIENTES (personaID, activo) VALUES (?, ?)");
                // Ejecutar la consulta con el ID de la persona y el estado activo
                $stmt_cliente->execute([$personaID, $activo]);
                
                // Verificar si se insertó un nuevo cliente
                if ($stmt_cliente->rowCount() > 0) {
                    // Obtener el ID del cliente recién insertado
                    $clienteID = $pdo->lastInsertId();
                    
                    // Obtener el ID del rol del usuario
                    $stmt_rol = $pdo->prepare("SELECT rolID FROM ROLES WHERE nombre_rol = ?");
                    // Ejecutar la consulta con el nombre del rol
                    $stmt_rol->execute([$role]);
                    // Obtener la fila del rol
                    $rol = $stmt_rol->fetch();
                    // Obtener el ID del rol
                    $rolID = $rol['rolID'];
                    
                    // Contenido de validaciones
                    $showModal = false;
                    $showAlert = false;
                    
                    // Insertar un nuevo usuario exitosamente
                    $stmt_cuenta = $pdo->prepare("INSERT INTO CUENTAS (username, password, personaID, rolID) VALUES (?, ?, ?, ?)");
                    $stmt_cuenta->execute([$username, $hashed_password, $personaID, $rolID]);
                    if ($stmt_cuenta->rowCount() > 0) {
                /*        require '../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta                
                        $mail = new PHPMailer(true);
                        
                        try {
     // Para obtener detalles de depuración
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'euroservice339@gmail.com';
                            $mail->Password   = 'uguh ipf w rqqz ewjb';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 587;
                        
                            $mail->setFrom('euroservice339@gmail.com', 'EuroService');
                            $mail->addAddress($correo); // Reemplaza con un correo para pruebas
                        
                            $mail->isHTML(true);
                            $mail->Subject = 'Creacion de cuenta';
                            $mail->Body    = "Hola, <br><br>Tu cuenta ha sido creada con éxito. Tu usuario es: $username y tu contraseña es: $password";
                        
                            $mail->send();
                            echo 'Mensaje enviado correctamente';
                        } catch (Exception $e) {
                            echo "No se pudo enviar el mensaje. Error de correo: {$mail->ErrorInfo}";
                        }*/
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
                }
            }
        
        // Mostrar una alerta si los datos no son válidos
    } catch (Exception $e) {
        setAlertContent('error', "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Error:</strong> Se produjo un error inesperado. Por favor, intente nuevamente.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>");
        error_log($e->getMessage()); // Registrar el error en el archivo de log
    }
    
    header('Location: ./vista_registro_cliente.php');
    exit();
}
?>