<?php
include '../includes/db.php';
    $conexion = new Database();
    $conexion->conectar();
    extract($_POST);
    $consulta = "UPDATE `ubicaciones` SET `activo` = 'si' WHERE `ubicacionID` = $lugaru";
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    header('Location: ubicaciones_view.php');
    exit();
?>