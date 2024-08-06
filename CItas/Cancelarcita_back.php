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
        $_SESSION['bien'] = "Cita cancelada exitosamente";
    } else {
        $_SESSION['error'] = "Error al cancelar la cita: " . implode(", ", $queryUpdate->errorInfo());
    }
} else {
    $_SESSION['error'] = "Estado no válido o cita no encontrada";
}

// Redirige a la página deseada
header("Location: seleccionar_citacopy.php");
exit();
