<?php
session_start(); 
include '../includes/db.php';

if (isset($_GET['id'])) {
    $vehiculoID = $_GET['id'];

    $conexion = new Database();
    $pdo = $conexion->conectar();

    // Consulta para obtener el nombre del vehículo
    $sqlVehiculo = "SELECT CONCAT(marca, ' ', modelo, ' ', anio, ' ', color) AS VEHICULO
                    FROM VEHICULOS
                    WHERE vehiculoID = :vehiculoID";
    $queryVehiculo = $pdo->prepare($sqlVehiculo);
    $queryVehiculo->bindParam(':vehiculoID', $vehiculoID, PDO::PARAM_INT);
    $queryVehiculo->execute();
    $rowVehiculo = $queryVehiculo->fetch(PDO::FETCH_ASSOC);
    $nombreVehiculo = $rowVehiculo['VEHICULO'];

    // Consulta para contar ubicaciones
    $sqlConteoUbicaciones = "SELECT COUNT(VEHICULOS.vehiculoID) AS conteo
                             FROM VEHICULOS 
                             JOIN CITAS ON CITAS.vehiculoID = VEHICULOS.vehiculoID 
                             JOIN ORDENES_TRABAJO ON ORDENES_TRABAJO.citaID = CITAS.citaID 
                             JOIN UBICACIONES ON ORDENES_TRABAJO.ubicacionID = UBICACIONES.ubicacionID
                             WHERE UBICACIONES.ubicacionID != 1 AND VEHICULOS.vehiculoID = :vehiculoID";
    $queryGlobal = $pdo->prepare($sqlConteoUbicaciones);
    $queryGlobal->bindParam(':vehiculoID', $vehiculoID, PDO::PARAM_INT);
    $queryGlobal->execute();
    $rowGlobal = $queryGlobal->fetch(PDO::FETCH_ASSOC);
    $countUbicacionesGlobal = $rowGlobal['conteo'];

    if ($countUbicacionesGlobal >= 1) {
        $_SESSION['error'] = "No puedes eliminar el vehículo '$nombreVehiculo', tiene una orden de trabajo en proceso.";
        header('Location: deshabilitar_car_view.php'); 
        exit();
    } else {
        $consulta = "UPDATE `VEHICULOS` SET `activo` = 'no' WHERE `vehiculoID` = :vehiculoID";
        $inhabilitar = $pdo->prepare($consulta);
        $inhabilitar->bindParam(':vehiculoID', $vehiculoID, PDO::PARAM_INT);
        $inhabilitar->execute();
        $conexion->desconectar();

        $_SESSION['bien'] = "Vehículo '$nombreVehiculo' inhabilitado exitosamente";
        header('Location: deshabilitar_car_view.php');
        exit();
    }
}
?>

