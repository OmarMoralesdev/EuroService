<?php
require 'conexion.php'; // Archivo que contiene la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehiculoID = $_POST['vehiculoID'];
    $fechaOrden = $_POST['fechaOrden'];
    $detallesTrabajo = $_POST['detallesTrabajo'];
    $costoManoObra = $_POST['costoManoObra'];
    $costoRefacciones = $_POST['costoRefacciones'];
    $estado = $_POST['estado'];
    $empleado = $_POST['empleado'];
    $ubicacionID = $_POST['ubicacionID'];
    $atencion = $_POST['atencion'];

    try {
        $nuevaOrdenID = crearOrdenTrabajo($pdo, $vehiculoID, $fechaOrden, $detallesTrabajo, $costoManoObra, $costoRefacciones, $estado, $empleado, $ubicacionID, $atencion);
        echo "Nueva orden de trabajo creada con ID: $nuevaOrdenID";
    } catch (Exception $e) {
        echo "Error al crear la orden de trabajo: " . $e->getMessage();
    }
}
?>
