<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

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

function usernameExists($pdo, $username) {
    $sql = "SELECT COUNT(*) FROM cuentas WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    return $stmt->fetchColumn() > 0;
}

function setModalContent($type, $message) {
    $_SESSION['modal'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getModalContent() {
    if (isset($_SESSION['modal'])) {
        $modalContent = $_SESSION['modal'];
        unset($_SESSION['modal']); // Limpiar el contenido del modal después de mostrarlo
        return $modalContent;
    }
    return null;
}

$showModal = false;

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
            $stmt_persona = $pdo->prepare("INSERT INTO personas (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES (?, ?, ?, ?, ?)");
            $stmt_persona->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono]);

            if ($stmt_persona->rowCount() > 0) {
                $personaID = $pdo->lastInsertId();
                $username = generarUsernameParaCliente($pdo, $personaID);

                if ($username === null) {
                    throw new Exception('No se pudo generar un nombre de usuario único.');
                }

                $stmt_cliente = $pdo->prepare("INSERT INTO clientes (personaID) VALUES (?)");
                $stmt_cliente->execute([$personaID]);

                if ($stmt_cliente->rowCount() > 0) {
                    $clienteID = $pdo->lastInsertId();

                    $stmt_rol = $pdo->prepare("SELECT rolID FROM roles WHERE nombre_rol = ?");
                    $stmt_rol->execute([$role]);
                    $rol = $stmt_rol->fetch();
                    $rolID = $rol['rolID'];

                    $stmt_cuenta = $pdo->prepare("INSERT INTO cuentas (username, password, personaID, rolID) VALUES (?, ?, ?, ?)");
                    $stmt_cuenta->execute([$username, $hashed_password, $personaID, $rolID]);
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
                }
            }
        } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
    
            if (strpos($errorMessage, 'Duplicate entry') !== false && strpos($errorMessage, 'for key \'telefono\'') !== false) {
                setModalContent('error', "
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
                    </div>");
                $showModal = true;
            } elseif (strpos($errorMessage, 'Duplicate entry') !== false && strpos($errorMessage, 'for key \'correo\'') !== false) {
                setModalContent('error', "
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
                    </div>");
                $showModal = true;
            } else {
                setModalContent('error', "
                    <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR AL REGISTRAR LA CUENTA</h1>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    Verifica que todos los campos estén correctamente llenos y vuelve a intentar.
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>");
                $showModal = true;
            }
        } catch (Exception $e) {
            setModalContent('error', "
                <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR EN LOS DATOS INGRESADOS</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                Verifica que todos los campos estén correctamente llenos y vuelve a intentar.
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>");
            $showModal = true;
        }
    } else {
        setModalContent('error', "
            <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR EN LOS DATOS INGRESADOS</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            Verifica que todos los campos estén correctamente llenos y vuelve a intentar.
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>");
        $showModal = true;
    }

    header('Location: vista_registro_cliente.php');
    exit();
}
?>
