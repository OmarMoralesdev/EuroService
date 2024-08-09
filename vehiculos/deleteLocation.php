<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $ubicacionID = $_GET['id'];

    $conexion = new Database();
    $conexion->conectar();
    
    $consulta = "UPDATE `UBICACIONES` SET `activo` = 'no' WHERE `ubicacionID` = $ubicacionID";
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    
    header('Location: ubicaciones_view.php');
    exit();
}
?>
