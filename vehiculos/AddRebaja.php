<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rebaja = $_POST['rebaja']; // Asegúrate de usar el nombre correcto del campo del formulario
    $empleadoID = $_POST['empleadoID']; // Asegúrate de usar el nombre correcto del campo del formulario

    $conexion = new Database();
    $conexion->conectar();
    
    // Preparar consulta para actualizar las rebajas en la tabla NOMINAS
    $consulta = "UPDATE NOMINAS SET rebajas = rebajas + $rebaja WHERE empleadoID = $empleadoID";
    
    // Ejecutar la consulta
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    
    // Devolver una respuesta JSON para el éxito
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}
?>
