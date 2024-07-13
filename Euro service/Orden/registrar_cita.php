<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $vehiculoID = $_POST['vehiculoID'];
    $servicioSolicitado = $_POST['servicioSolicitado'];
    $fechaSolicitud = date('Y-m-d');
    $fechaCita = $_POST['fechaCita'];
    $estado = 'pendiente';

    $sql = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, estado) 
            VALUES (:vehiculoID, :servicioSolicitado, :fechaSolicitud, :fechaCita, :estado)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':vehiculoID' => $vehiculoID,
        ':servicioSolicitado' => $servicioSolicitado,
        ':fechaSolicitud' => $fechaSolicitud,
        ':fechaCita' => $fechaCita,
        ':estado' => $estado,
    ]);

    echo "Cita registrada exitosamente.";
}
?>
