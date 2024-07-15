<?php
// enviar_codigo.php

require '../../includes/db.php';
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$con = new Database();
$pdo = $con->conectar();
function generarCodigo() {
    return rand(100000, 999999); // Genera un código de 6 dígitos
}

function enviarCorreoRecuperacion($emailDestino, $codigoRecuperacion) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor de correo
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Cambia esto al servidor SMTP que estés usando
        $mail->SMTPAuth = true;
        $mail->Username = 'euroservice339@gmail.com'; // Cambia esto a tu correo
        $mail->Password = 'uguh ipf w rqqz ewjb'; // Cambia esto a tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        // Remitente y destinatario
        $mail->setFrom('euroservice339@gmail.com', 'EuroService');
        $mail->addAddress($emailDestino);
        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de Cuenta';
        $mail->Body    = "Hola, <br><br>Para recuperar tu cuenta, utiliza el siguiente código de recuperación: <b>$codigoRecuperacion</b>";

        $mail->send();
    } catch (Exception $e) {
        echo "No se pudo enviar el mensaje. Error de correo: {$mail->ErrorInfo}";
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailDestino = $_POST['email'];

    // Verificar si el correo está registrado
    $sql = "SELECT * FROM CLIENTES WHERE correo = :correo";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':correo', $emailDestino);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // El correo está registrado, proceder a enviar el código de recuperación
        $codigoRecuperacion = generarCodigo();

        // Guardar el código en una variable de sesión
        session_start();
        $_SESSION['codigo_recuperacion'] = $codigoRecuperacion;
        $_SESSION['email_recuperacion'] = $emailDestino;

        enviarCorreoRecuperacion($emailDestino, $codigoRecuperacion);

        // Redirigir al usuario a la página de verificación
        header("Location: verificar_codigo.php");
        exit();
    } else {
        echo "El correo electrónico no está registrado.";
    }
}
?>