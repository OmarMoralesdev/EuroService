<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = $_POST['empleado'];
    $fecha = $_POST['fecha'];
    $hora_entrada = $_POST['hora_entrada'];
    $hora_salida = !empty($_POST['hora_salida']) ? $_POST['hora_salida'] : null;

    try {
        $sql = "INSERT INTO Asistencia (empleadoID, asistencia, fecha, hora_entrada, hora_salida) VALUES (?, 'asistencia',?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empleadoID, $fecha, $hora_entrada, $hora_salida]);

        echo "Asistencia registrada exitosamente";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
