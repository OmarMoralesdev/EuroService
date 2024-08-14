<?php
session_start();
include '../includes/db.php';

if (isset($_GET['id'])) {
    $ubicacionID = $_GET['id'];

    $conexion = new Database();
    $pdo = $conexion->conectar();
    $sqlUbicacion = "SELECT lugar AS ubi
    FROM UBICACIONES
    WHERE ubicacionID = $ubicacionID";
$queryUbicacion = $pdo->prepare($sqlUbicacion);
$queryUbicacion->execute();
$rowUbicacion = $queryUbicacion->fetch(PDO::FETCH_ASSOC);
$ubi = $rowUbicacion['ubi'];
$_SESSION['L'] = "La ubicación '{$ubi}' ha sido inhabilitada.";
    
    $consulta = "UPDATE `UBICACIONES` SET `activo` = 'no' WHERE `ubicacionID` = $ubicacionID";
    $conexion->ejecuta($consulta);
    
    $conexion->desconectar();
    
    header('Location: ubicaciones_view.php');
    exit();
}
?>
