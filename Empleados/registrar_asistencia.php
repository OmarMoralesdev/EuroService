<?php
session_start(); 
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = $_POST['empleado'];
    $asistencia = $_POST['asistencia'];
    $fecha = $_POST['fecha'];
    $hora_entrada = $_POST['hora_entrada'];
    $hora_salida = !empty($_POST['hora_salida']) ? $_POST['hora_salida'] : null;

    $fecha_actual = date('Y-m-d');
    $fecha_minima = date('Y-m-d', strtotime('-7 days', strtotime($fecha_actual)));
    $fecha_maxima = $fecha_actual;

    $time_entrada = strtotime($hora_entrada);
    $time_salida = strtotime($hora_salida);

    // Convertir las horas de inicio y fin del horario laboral a timestamps
    $hora_inicio_laboral = strtotime('08:00:00');
    $hora_fin_laboral = strtotime('18:00:00');
    $diferencia_horas = ($time_salida - $time_entrada) / 3600;

    // Validación: Verificar que no se registre una asistencia/falta/justificada al mismo empleado más de una vez en un día
    $sqlAsistencia = "SELECT count(*) AS L FROM ASISTENCIA WHERE empleadoID= ? AND fecha = ?";
    $queryGlobal = $pdo->prepare($sqlAsistencia);
    $queryGlobal->execute([$empleadoID, $fecha]);

    $rowGlobal = $queryGlobal->fetch(PDO::FETCH_ASSOC);
    $countAsistenciasGlobal = $rowGlobal['L'];

    if ($countAsistenciasGlobal >= 1) {
        $_SESSION['error'] = "No puedes tener registrado más de una vez a un empleado en la misma fecha";
        header('Location: registro_asistencia.php'); 
        exit();
    } else {
        try {
            switch ($fecha) {
                case ($fecha >= $fecha_minima && $fecha <= $fecha_maxima):
                    switch ($diferencia_horas) {
                        case ($time_entrada < $hora_inicio_laboral || $time_entrada > $hora_fin_laboral):
                            $_SESSION['error'] = "La hora de entrada mínima es a las 8:00 a.m.";
                            header('Location: registro_asistencia.php');
                            exit();
                            break;
                        case ($hora_salida && ($time_salida < $hora_inicio_laboral || $time_salida > $hora_fin_laboral)):
                            $_SESSION['error'] = "La hora de salida máxima es a las 18:00 p.m.";
                            header('Location: registro_asistencia.php');
                            exit();
                            break;
                        case ($diferencia_horas >= 4 && $time_entrada < $time_salida):
                            switch ($asistencia) {
                                case 'asistencia':
                                    $sql = "INSERT INTO ASISTENCIA (empleadoID, asistencia, fecha, hora_entrada, hora_salida) VALUES (?, ?,?, ?, ?)";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute([$empleadoID, $asistencia, $fecha, $hora_entrada, $hora_salida]);
                                    $_SESSION['bien'] ="Asistencia registrada exitosamente";
                                    header('Location: registro_asistencia.php'); 
                                    exit();
                                    break;
                                case 'falta':
                                    $sql = "INSERT INTO ASISTENCIA (empleadoID, asistencia, fecha, hora_entrada, hora_salida) VALUES (?, ?,?, ?, ?)";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute([$empleadoID, $asistencia, $fecha, $hora_entrada, $hora_salida]);
                                    $_SESSION['bien'] = "Falta registrada exitosamente";
                                    header('Location: registro_asistencia.php'); 
                                    exit();
                                    break;
                            } 
                            break;
                        case ($diferencia_horas < 4):
                            $diferencia_horas = $diferencia_horas * -1;
                            switch ($time_entrada) {
                                case ($diferencia_horas < 4):
                                    $_SESSION['error'] =  "El tiempo mínimo para una asistencia es de 4 horas";
                                    header('Location: registro_asistencia.php'); 
                                    exit();
                                    break;
                                case ($time_entrada > $time_salida):
                                    $_SESSION['error'] =  "La hora de salida no puede ser mayor a la hora de entrada";
                                    header('Location: registro_asistencia.php'); 
                                    exit();
                                    break;
                            }
                            break;
                    }
                    break;
                case ($fecha < $fecha_minima):
                    $_SESSION['error'] =  "Solo puedes elegir una fecha con 7 días de anterioridad";
                    header('Location: registro_asistencia.php'); 
                    exit();
                    break;
                case ($fecha > $fecha_maxima):
                    $_SESSION['error'] =  "No puedes elegir una fecha superior a la de hoy";
                    header('Location: registro_asistencia.php'); 
                    exit();
                    break;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header('Location: registro_asistencia.php'); 
            exit();
        }
    }
}
?>
