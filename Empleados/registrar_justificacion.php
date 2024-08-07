<?php
session_start();
require '../includes/db.php';

// Conectar a la base de datos
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtén y valida los datos del POST
    $empleadoID = isset($_POST['empleadox']) ? filter_var($_POST['empleadox'], FILTER_VALIDATE_INT) : null;
    $fecha = isset($_POST['fecha']) ? filter_var($_POST['fecha'], FILTER_SANITIZE_STRING) : null;

    // Verifica si los datos están presentes
    if ($empleadoID && $fecha) {
        try {
            // Preparar la consulta SQL con parámetros
            $update = "UPDATE ASISTENCIA SET asistencia = 'justificado' WHERE empleadoID = :empleadoID AND fecha = :fecha";
            $stmt = $pdo->prepare($update);

            // Vincular los parámetros
            $stmt->bindParam(':empleadoID', $empleadoID, PDO::PARAM_INT);
            $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);

            // Ejecutar la consulta
            $stmt->execute();

            // Mensaje de éxito y redirección
            $_SESSION['bien'] = "Falta justificada registrada exitosamente";
            header('Location: justificar_falta.php'); 
            exit();
        } catch (PDOException $e) {
            // Mensaje de error y redirección
            $_SESSION['error'] = "Error: " . htmlspecialchars($e->getMessage());
            header('Location: justificar_falta.php'); 
            exit();
        }
    } else {
        // Mensaje de error si los datos están incompletos
        $_SESSION['error'] = "Datos insuficientes para justificar la falta.";
        header('Location: justificar_falta.php'); 
        exit();
    }
}
?>
