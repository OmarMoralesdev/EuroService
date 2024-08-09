<?php
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = isset($_POST['empleadoID']) ? $_POST['empleadoID'] : null;
    
    if ($empleadoID) {
        try {
            $sql = "UPDATE EMPLEADOS SET activo = 'no' WHERE empleadoID = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $empleadoID);  // Cambié ':empleadoID' por el índice 1
            $stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Empleado deshabilitado correctamente']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
    }
}

$conexion->desconectar();
?>
