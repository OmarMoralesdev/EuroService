<?php

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
$showModal = false;
$modalContent = '';
    // Actualiza la base de datos
    $sql = "UPDATE PERSONAS SET correo = ?, telefono = ? from personas
    join clientes on peronas = clientes.clienteID WHERE clienteID = ?";
    
    // Usa PDO para preparar la declaración
    $stmt = $pdo->prepare($sql);
    // Ejecuta la consulta
    if ($stmt->execute([$correo, $telefono, $clienteID])) {
        $showModal = true;
        $modalContent = "
            <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='staticBackdropLabel'>Datos actualizados con exito!</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            nuevo correo eléctronico: <strong>$correo</strong><br><br>
                            <hr>
                            nuevo número telefonico: <strong>$telefono</strong><br>
                            
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>";
    } else {
        echo "Error al actualizar los datos.";
    }
}
?>
