<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lugar = $_POST['lugar'];
    $capacidad = $_POST['capacidad'];

    $conexion = new Database();
    $conexion->conectar();
    
    $consulta = "INSERT INTO ubicaciones (lugar, capacidad) VALUES ('$lugar', $capacidad)";
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    
    header('Location: ubicaciones_view.php');
    exit();
}
?>
