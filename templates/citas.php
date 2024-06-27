<?php
require '../includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehiculoID = $_POST['vehiculoID'];
    $servicio = $_POST['servicio'];
    $fecha_cita = $_POST['fecha_cita'];
    $estado = 'pendiente'; 
    try {
 
        $stmt_cita = $conn->prepare("INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, estado)
                                     VALUES (?, ?, CURDATE(), ?, ?)");

       
        $stmt_cita->execute([$vehiculoID, $servicio, $fecha_cita, $estado]);

       
        echo "La cita se ha registrado correctamente.";
    } catch(PDOException $e) {

        echo "Error al registrar la cita: " . $e->getMessage();
    }
}
?>
