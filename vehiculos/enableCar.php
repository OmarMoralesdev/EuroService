<?php
session_start();
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

extract($_POST);

$sqlVehiculo = "SELECT CONCAT(marca, ' ', modelo, ' ', anio, ' ', color) AS VEHICULO
FROM VEHICULOS
WHERE vehiculoID = :vehiculoID";
$queryVehiculo = $pdo->prepare($sqlVehiculo);
$queryVehiculo->bindParam(':vehiculoID', $auto, PDO::PARAM_INT);
$queryVehiculo->execute();
$rowVehiculo = $queryVehiculo->fetch(PDO::FETCH_ASSOC);
$nombre_Vehiculo = $rowVehiculo['VEHICULO'];
$_SESSION['x'] = "El vehículo '$nombre_Vehiculo' se ha habilitado";

$consulta = "UPDATE `VEHICULOS` SET `activo` = 'si' WHERE `vehiculoID` = $auto";
$conexion->ejecuta($consulta);
$conexion->desconectar();
header('Location: deshabilitar_car_view.php');
exit();
?>
