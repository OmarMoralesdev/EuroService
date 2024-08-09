<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ubicacionID = $_POST['ubicacionID'];
    $lugar = $_POST['lugarn'];

    $conexion = new Database();
    $conexion->conectar();

    // Actualizar el nombre de la ubicación en la base de datos
    $consulta = "UPDATE UBICACIONES SET lugar = '$lugar' WHERE ubicacionID = $ubicacionID";
    $conexion->ejecuta($consulta);

    $conexion->desconectar();

    header('Location: ubicaciones_view.php');
    exit();
}
?>