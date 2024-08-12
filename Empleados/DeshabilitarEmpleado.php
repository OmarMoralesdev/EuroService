<?php
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = isset($_POST['empleadoID']) ? $_POST['empleadoID'] : null;
    
    if ($empleadoID) {
        try {
            // Deshabilitar al empleado en la tabla EMPLEADOS
            $sql = "UPDATE EMPLEADOS SET activo = 'no' WHERE empleadoID = :empleadoID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':empleadoID', $empleadoID);
            $stmt->execute();



            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Empleado deshabilitado correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontró el empleado o ya está deshabilitado']);
            }

            
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
header("Location: ../Empleados/buscar.php");

$conexion->desconectar();
?>
