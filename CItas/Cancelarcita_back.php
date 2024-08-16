<?php
session_start(); // Asegúrate de que la sesión esté iniciada

require '../includes/db.php'; // Asegúrate de incluir el archivo de configuración de la base de datos

// Conecta a la base de datos
try {
    $con = new Database();
    $pdo = $con->conectar();
} catch (Exception $e) {
    die('Error al conectar a la base de datos: ' . $e->getMessage());
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Obtiene y filtra los datos del POST
$citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
$estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

if ($estado === 'cancelado' && $citaID) {
    // Actualiza el estado de la cita en la base de datos
    $sqlUpdate = "UPDATE CITAS SET estado = ? WHERE citaID = ?";
    $queryUpdate = $pdo->prepare($sqlUpdate);
    $resultUpdate = $queryUpdate->execute(['cancelado', $citaID]);

    if ($resultUpdate) {
        // Recuperar el correo electrónico del cliente asociado a la cita
        $sqlSelect = "SELECT p.correo FROM PERSONAS p
                      INNER JOIN CLIENTES c ON p.personaID = c.personaID
                      INNER JOIN VEHICULOS v ON c.clienteID = v.clienteID
                      INNER JOIN CITAS ci ON v.vehiculoID = ci.vehiculoID
                      WHERE ci.citaID = ?";
        $querySelect = $pdo->prepare($sqlSelect);
        $querySelect->execute([$citaID]);
        $correoDestinatario = $querySelect->fetchColumn();

        if ($correoDestinatario) {
            // Incluir el autoload de Composer
            require '../vendor/autoload.php';

            // Crear una instancia de PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host     = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'euroservice339@gmail.com';
                $mail->Password   = 'uguh ipf w rqqz ewjb';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;                       
                $mail->setFrom('euroservice339@gmail.com', 'EuroService'); // Cambia esto si es necesario
                $mail->addAddress($correoDestinatario); // Correo del destinatario recuperado

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Cancelación de Cita';
                $mail->Body    = "Estimado/a {$detalles['nombre']} {$detalles['apellido_paterno']} {$detalles['apellido_materno']},<br><br>Su cita ha sido cancelada.<br><br>Saludos,<br>EuroService";
                // Enviar el correo
                $mail->send();
            } catch (Exception $e) {
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

header("Location: seleccionar_citacopy.php");
exit();
?>
