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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    // Obtener personaID asociado con clienteID
    $stmt = $pdo->prepare("SELECT personaID FROM CLIENTES WHERE clienteID = ? AND activo = 'si';");
    $stmt->execute([$clienteID]);
    $personaID = $stmt->fetchColumn();

    // Verificar si el correo o teléfono están en uso
    if (checkDuplicate($pdo, $correo, $telefono, $personaID)) {
        setAlertContent('error', "
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Error:</strong> El teléfono o el correo ya corresponden a un usuario existente.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>");
            
    } else {
        $sql = "UPDATE PERSONAS SET correo = ?, telefono = ? WHERE personaID = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$correo, $telefono, $personaID])) {
            setAlertContent('success', "
                <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='staticBackdropLabel'>Datos actualizados con exito!</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            nuevo correo: <strong>$correo</strong><br>
                            <hr>
                            nuevo número: <strong>$telefono</strong><br>
                            
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>");

        } else {
            setAlertContent('error', "
                <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <strong>Error al actualizar:</strong> No se pudo actualizar la información. Intenta nuevamente.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>");
        }
    }

    // Redirigir al usuario después de la acción
    header('Location: edit_user_view.php');
    exit();
}
?>
