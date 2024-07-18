<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $length > $i; $i++) {
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
                                        cuenta del cliente: <strong>$correo</strong><br><br>
                                        contraseña del cliente: <strong>$password</strong><br><hr>
                                        presiona siguiente para registrar su vehículo
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                        <button type='button' class='btn btn-dark'>siguiente</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
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
                                        El cliente no se ha registrado con éxito.
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
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
                                    El cliente no se ha registrado con éxito.
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>";
            }
        } 
    } catch (PDOException $e) {
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
                            El cliente no se ha registrado con éxito. " . $e->getMessage() . "
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