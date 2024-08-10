<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

// Función para verificar si el correo o el teléfono ya existen en la base de datos
function checkDuplicate($pdo, $correo, $telefono, $personaID) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE (correo = :correo OR telefono = :telefono) AND personaID <> :personaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['correo' => $correo, 'telefono' => $telefono, 'personaID' => $personaID]);
    return $stmt->fetchColumn() > 0;
}

// Función para configurar el mensaje de alerta
function setAlertContent($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Verificar si se envió un formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);

    // Verificar si se proporcionaron datos para actualizar
    if (!empty($clienteID) && (!empty($correo) || !empty($telefono))) {
        // Validación de teléfono (al menos 10 dígitos) solo si se ingresa un número
        if (!empty($telefono) && strlen($telefono) < 10) {
            setAlertContent('error', "
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <strong>Error:</strong> El número de teléfono debe tener al menos 10 dígitos.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>");
            header('Location: edit_user_view.php');
            exit();
        }

        // Verificar duplicados
        if (checkDuplicate($pdo, $correo, $telefono, $clienteID)) {
            setAlertContent('error', "
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <strong>Error:</strong> El teléfono o el correo ya corresponden a un usuario existente.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>");
        } else {
            $fields = [];
            $params = [];

            if (!empty($correo)) {
                $fields[] = "correo = ?";
                $params[] = $correo;
            }

            if (!empty($telefono)) {
                $fields[] = "telefono = ?";
                $params[] = $telefono;
            }

            if (count($fields) > 0) {
                $params[] = $clienteID;
                $sql = "UPDATE PERSONAS SET " . implode(', ', $fields) . " WHERE personaID = ?";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute($params)) {
                    $_SESSION['modal'] = [
                        'message' => "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h1 class='modal-title fs-5' id='staticBackdropLabel'>Datos actualizados con éxito!</h1>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    nuevo correo: <strong>" . (!empty($correo) ? $correo : 'No cambiado') . "</strong><br>
                                    <hr>
                                    nuevo número: <strong>" . (!empty($telefono) ? $telefono : 'No cambiado') . "</strong><br>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>"
                    ];
                } else {
                    setAlertContent('error', "
                        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <strong>Error al actualizar:</strong> No se pudo actualizar la información. Intenta nuevamente.
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>");
                }
            }
        }
    } else {
        setAlertContent('error', "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Error:</strong> Debes ingresar al menos un dato para actualizar.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>");
    }

    // Redirigir al usuario después de la acción
    header('Location: edit_user_view.php');
    exit();
}
?>
