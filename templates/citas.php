<?php
require '../includes/db.php'; // Asumiendo que db.php contiene la configuración de conexión a la base de datos

// Verificar si se han recibido los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $vehiculoID = $_POST['vehiculoID'];
    $servicio = $_POST['servicio'];
    $fecha_cita = $_POST['fecha_cita'];
    $estado = 'pendiente'; // Por defecto, la nueva cita se registra como pendiente

    // Insertar nueva cita en la base de datos
    try {
        // Preparar la consulta SQL con parámetros de marcador de posición (?)
        $stmt_cita = $conn->prepare("INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, estado)
                                     VALUES (?, ?, CURDATE(), ?, ?)");

        // Ejecutar la consulta vinculando los parámetros directamente en execute()
        $stmt_cita->execute([$vehiculoID, $servicio, $fecha_cita, $estado]);

        // Mostrar mensaje de éxito
        echo "La cita se ha registrado correctamente.";
    } catch(PDOException $e) {
        // Capturar y mostrar errores de PDO
        echo "Error al registrar la cita: " . $e->getMessage();
    }
}
?>
