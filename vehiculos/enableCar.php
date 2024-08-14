<?php
include '../includes/db.php';
    $conexion = new Database();
    $conexion->conectar();
    extract($_POST);
    $consulta = "UPDATE `VEHICULOS` SET `activo` = 'si' WHERE `vehiculoID` = $auto";
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    header('Location: deshabilitar_car_view.php');
    exit();
?>