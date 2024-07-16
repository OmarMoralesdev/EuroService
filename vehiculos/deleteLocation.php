<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $ubicacionID = $_GET['id'];

    $conexion = new Database();
    $conexion->conectar();
    
    $consulta = "DELETE FROM ubicaciones WHERE ubicacionID = $ubicacionID";
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    
    header('Location: ubicaciones_view.php');
    exit();
}
?>
