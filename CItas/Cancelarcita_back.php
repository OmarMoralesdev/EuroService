<?php
session_start(); // Asegúrate de que la sesión esté iniciada

require '../includes/db.php'; // Asegúrate de incluir el archivo de configuración de la base de datos


// Conecta a la base de datos
$con = new Database();
$pdo = $con->conectar();

// Obtiene y filtra los datos del POST
$citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
$estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

if ($estado === 'cancelado' && $citaID) {
    // Actualiza el estado de la cita en la base de datos
    $sqlUpdate = "UPDATE CITAS SET estado = ? WHERE citaID = ?";
    $queryUpdate = $pdo->prepare($sqlUpdate);
    $resultUpdate = $queryUpdate->execute(['cancelado', $citaID]);

    if ($resultUpdate) {
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;
        
        // Incluir el autoload de Composer
        require '../vendor/autoload.php';  
        
        // Obtener los detalles del cliente
        $sqlCliente = "SELECT PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno, PERSONAS.correo 
                       FROM PERSONAS 
                       JOIN CITAS ON PERSONAS.personaID = CITAS.personaID 
                       WHERE CITAS.citaID = ?";
        $queryCliente = $pdo->prepare($sqlCliente);
        $queryCliente->execute([$citaID]);
        $detalles = $queryCliente->fetch(PDO::FETCH_ASSOC);

        if ($detalles) {
            // Enviar correo de notificación
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host     = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'euroservice339@gmail.com';
            $mail->Password   = 'uguh ipf w rqqz ewjb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;                       
            $mail->setFrom('euroservice339@gmail.com', 'EuroService');
            $mail->addAddress($detalles['correo'], "{$detalles['nombre']} {$detalles['apellido_paterno']} {$detalles['apellido_materno']}");

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Cancelación de Cita';
            $mail->Body    = "Estimado/a {$detalles['nombre']} {$detalles['apellido_paterno']} {$detalles['apellido_materno']},<br><br>Su cita ha sido cancelada.<br><br>Saludos,<br>EuroService";

            if (!$mail->send()) {
                $_SESSION['error'] = "Error al enviar el correo de notificación: " . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = "No se encontraron los detalles del cliente.";
        }
    } else {
        $_SESSION['error'] = "Error al cancelar la cita: " . implode(", ", $queryUpdate->errorInfo());
    }
} else {
    $_SESSION['error'] = "Estado no válido o cita no encontrada";
}

// Redirige a la página deseada
header("Location: seleccionar_citacopy.php");
exit();