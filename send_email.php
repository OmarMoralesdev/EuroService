<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->SMTPDebug = 0;                      // Habilita la depuración de SMTP (0 para no mostrar, 2 para mostrar)
    $mail->isSMTP();                           // Usar SMTP
    $mail->Host       = 'smtp.gmail.com';      // Servidor SMTP de Gmail
    $mail->SMTPAuth   = true;                  // Habilitar autenticación SMTP
    $mail->Username   = 'euroservice339@gmail.com';   // Tu correo de Gmail
    $mail->Password   = 'uguh ipf w rqqz ewjb';       // Tu contraseña de Gmail o una contraseña de aplicaciones
    $mail->SMTPSecure = 'tls';                 // Habilitar encriptación TLS
    $mail->Port       = 587;                   // Puerto SMTP de Gmail para TLS

    // Configuración del remitente
    $mail->setFrom('euroservice339@gmail.com', 'EuroService');

    // Configuración del destinatario
    $mail->addAddress('omarelpro8288@gmail.com', 'Destinatario');

    // Contenido del correo
    $mail->isHTML(true);                        // Configurar el correo como HTML
    $mail->Subject = 'Asunto del correo';
    $mail->Body    = 'Este es el cuerpo del mensaje en <b>HTML</b>';
    $mail->AltBody = 'Este es el cuerpo del mensaje en texto plano para clientes que no soportan HTML';

    // Enviar el correo
    $mail->send();
    echo 'El mensaje ha sido enviado';
} catch (Exception $e) {
    echo "No se pudo enviar el mensaje. Error: {$mail->ErrorInfo}";
}
