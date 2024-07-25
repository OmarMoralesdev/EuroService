<?php
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

    $diferencia_horas = ($time_salida - $time_entrada) / 3600;

    try {
        switch ($fecha) {
            case ($fecha >= $fecha_minima && $fecha <= $fecha_maxima):
                switch ($diferencia_horas) {
                    case ($diferencia_horas >= 4 && $time_entrada < $time_salida):
                        $sql = "INSERT INTO Asistencia (empleadoID, asistencia, fecha, hora_entrada, hora_salida) VALUES (?, ?,?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$empleadoID, $asistencia, $fecha, $hora_entrada, $hora_salida]);
                
                        echo "Asistencia registrada exitosamente";
                        break;
                    case ($diferencia_horas < 4):
                        $diferencia_horas = $diferencia_horas * -1;
                        switch ($time_entrada) {
                            case ($diferencia_horas < 4):
                                echo "El tiempo minímo para una asistencia es de 4 horas";
                                break;
                            case ($time_entrada > $time_salida):
                                echo "La hora de salida no puede ser mayor a la hora de entrada";
                                break;
                        }
                        break;
                }
                break;
            case ($fecha < $fecha_minima):
                echo "Solo puedes elegir una fecha con 7 días de anterioridad";
                break;
            case ($fecha > $fecha_maxima):
                echo "No puedes elegir una fecha superior a la de hoy";
                break;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
