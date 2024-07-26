<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $vehiculoID = $_GET['id'];

    $conexion = new Database();
    $conexion->conectar();
    
    $consulta = "UPDATE `vehiculos` SET `activo` = 'no' WHERE `vehiculoID` = $vehiculoID";
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    
    header('Location: deshabilitar_car_view.php');
    exit();
}
?>
