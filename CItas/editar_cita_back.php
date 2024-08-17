<?php
require '../includes/db.php';
session_start();

$con = new Database();
$pdo = $con->conectar();

$cita = null;
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        if ($citaID) {
            $sql = "SELECT * FROM CITAS WHERE citaID = ?";
            $query = $pdo->prepare($sql);
            $query->execute([$citaID]);
            $cita = $query->fetch(PDO::FETCH_ASSOC);

            if (!$cita) {
                $mensaje = "Cita no encontrada.";
            } else {
                $vehiculoID = $cita['vehiculoID'];
                $detalles = obtenerDetallesVehiculoyCliente($pdo, $vehiculoID);
                $_SESSION['cita'] = $cita;
                $_SESSION['mensaje'] = $mensaje;
                header("Location: editar_cita_view.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "ID de cita inválido.";
            header("Location: seleccionar_cita.php");
            exit();
        }

        $_SESSION['mensaje'] = $mensaje;
        header("Location: editar_cita_view.php");
        exit();
    } elseif (isset($_POST['actualizar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        $servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
        $fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);
        
    

        if (!$citaID || !$servicioSolicitado || !$fechaCita) {
            $_SESSION['error'] = "Error: Todos los campos son obligatorios.";
            header("Location: editar_cita_view.php");
            exit();
        } else {
            $fechaActual = new DateTime();
            $fechaCita = new DateTime($fechaCita);

            // Validación: La fecha de la cita debe ser posterior a la fecha actual
            if ($fechaCita < $fechaActual) {
                $_SESSION['error'] =  "Error: La fecha de la cita debe ser posterior a la fecha actual.";
                header("Location: editar_cita_view.php");
                exit();
            } else {
                $horaCita = $fechaCita->format('H:i:s');
                $horaInicioLaboral = "09:00:00";
                $horaFinLaboral = "17:00:00";

                // Validación: La hora de la cita debe estar dentro del horario laboral
                if ($horaCita < $horaInicioLaboral || $horaCita > $horaFinLaboral) {
                    $_SESSION['error'] = "Error: La cita debe programarse dentro del horario laboral (09:00 - 17:00).";
                    header("Location: editar_cita_view.php");
                    exit();
                } else {
                    // Verificar que la nueva cita no se solape con otras citas
                    $fechaInicioIntervalo = (clone $fechaCita)->modify('-30 minutes')->format('Y-m-d H:i:s');
                    $fechaFinIntervalo = (clone $fechaCita)->modify('+30 minutes')->format('Y-m-d H:i:s');

                    $sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE fecha_cita BETWEEN ? AND ? AND citaID != ?";
                    $query = $pdo->prepare($sql);
                    $query->execute([$fechaInicioIntervalo, $fechaFinIntervalo, $citaID]);

                    $row = $query->fetch(PDO::FETCH_ASSOC);
                    $countCitasProximas = $row['countCitas'];

                    if ($countCitasProximas > 0) {
                        $_SESSION['error'] =  "Error: La nueva fecha y hora de la cita solapan con otra cita existente en el intervalo de 30 minutos.";
                        header("Location: editar_cita_view.php");
                        exit();
                    } else {
                        // Actualizar la cita
                        $sqlUpdate = "UPDATE CITAS SET servicio_solicitado = ?, fecha_cita = ?  WHERE citaID = ?";
                        $queryUpdate = $pdo->prepare($sqlUpdate);
                        $resultUpdate = $queryUpdate->execute([$servicioSolicitado, $fechaCita->format('Y-m-d H:i:s'), $citaID]);

                     
                        use PHPMailer\PHPMailer\PHPMailer;
                        use PHPMailer\PHPMailer\Exception;
                        
                        // Incluir el autoload de Composer
                        require '../vendor/autoload.php';  
                        
                        
                        if ($resultUpdate) {
                            // Obtener detalles de la cita y del cliente
                            $sql = "SELECT * FROM CITAS WHERE citaID = ?";
                            $query = $pdo->prepare($sql);
                            $query->execute([$citaID]);
                            $cita = $query->fetch(PDO::FETCH_ASSOC);
                            $vehiculoID = $cita['vehiculoID'];
                            $detalles = obtenerDetallesVehiculoyCliente($pdo, $vehiculoID);
                            $_SESSION['cita'] = $cita;
                            $_SESSION['mensaje'] = "Cita editada exitosamente.";
                        
                            // Configurar el correo electrónico
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
                                $mail->setFrom('euroservice339@gmail.com', 'EuroService');
                                $mail->addAddress($detalles['correo'], "{$detalles['nombre']} {$detalles['apellido_paterno']} {$detalles['apellido_materno']}");
                                // Contenido del correo
                                $mail->isHTML(true);
                                $mail->Subject = 'Cambio de Fecha de Cita';
                                $mail->Body    = "Estimado/a {$detalles['nombre']} {$detalles['apellido_paterno']} {$detalles['apellido_materno']},<br><br>Su cita ha sido reprogramada para el día {$cita['fecha_cita']}.<br><br>Saludos,<br>EuroService";
                                // Enviar el correo
                                $mail->send();
                            } catch (Exception $e) {
                                $_SESSION['error'] = "Error al enviar el correo: {$mail->ErrorInfo}";
                            }
                        
                            header("Location: editar_cita_view.php");
                            exit();
                        } else {
                            $_SESSION['error'] = "Error al actualizar la cita.";
                            header("Location: editar_cita_view.php");
                            exit();
                        }
                    }
                }
            }
        }
    }
}
