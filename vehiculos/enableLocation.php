<?php
session_start();
include '../includes/db.php';
    $conexion = new Database();
    $pdo = $conexion->conectar();
    extract($_POST);
    $sqlUbicacion = "SELECT lugar AS ubi
                    FROM UBICACIONES
                    WHERE ubicacionID = :ubicacionID";
    $queryUbicacion = $pdo->prepare($sqlUbicacion);
    $queryUbicacion->bindParam(':ubicacionID', $lugaru, PDO::PARAM_INT);
    $queryUbicacion->execute();
    $rowUbicacion = $queryUbicacion->fetch(PDO::FETCH_ASSOC);
    $ubi = $rowUbicacion['ubi'];
    $_SESSION['x'] = "La ubicación {$ubi} ha sido habilitada.";
    $consulta = "UPDATE `UBICACIONES` SET `activo` = 'si' WHERE `ubicacionID` = $lugaru";

    $conexion->ejecuta($consulta);
    $conexion->desconectar();
    header('Location: ubicaciones_view.php');
    exit();
?>