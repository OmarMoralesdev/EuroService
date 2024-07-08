<?php
$host = '127.0.0.1';
$db = 'taller_euro';
$user = 'root';
$pass = '123456789';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    die();
}

function listarCitasPendientes($pdo) {
    $sql = "SELECT * FROM CITAS WHERE estado = 'pendiente'";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDetallesCita($pdo, $citaID) {
    $sql = "SELECT * FROM CITAS WHERE citaID = :citaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':citaID' => $citaID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerDetallesVehiculo($pdo, $vehiculoID) {
    $sql = "SELECT * FROM VEHICULOS WHERE vehiculoID = :vehiculoID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':vehiculoID' => $vehiculoID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerDetallesCliente($pdo, $clienteID) {
    $sql = "SELECT * FROM CLIENTES WHERE clienteID = :clienteID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':clienteID' => $clienteID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function crearOrdenTrabajo($pdo, $vehiculoID, $fechaOrden, $detallesTrabajo, $costoManoObra, $costoRefacciones, $estado, $empleado, $ubicacionID, $atencion) {
    $sql = "INSERT INTO ORDENES_TRABAJO (vehiculoID, fecha_orden, detalles_trabajo, costo_mano_obra, costo_refacciones, estado, empleado, ubicacionID, atencion) 
            VALUES (:vehiculoID, :fechaOrden, :detallesTrabajo, :costoManoObra, :costoRefacciones, :estado, :empleado, :ubicacionID, :atencion)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':vehiculoID' => $vehiculoID,
        ':fechaOrden' => $fechaOrden,
        ':detallesTrabajo' => $detallesTrabajo,
        ':costoManoObra' => $costoManoObra,
        ':costoRefacciones' => $costoRefacciones,
        ':estado' => $estado,
        ':empleado' => $empleado,
        ':ubicacionID' => $ubicacionID,
        ':atencion' => $atencion,
    ]);

    return $pdo->lastInsertId();
}

function actualizarEstadoCita($pdo, $citaID, $nuevoEstado) {
    $sql = "UPDATE CITAS SET estado = :nuevoEstado WHERE citaID = :citaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nuevoEstado' => $nuevoEstado, ':citaID' => $citaID]);
}
?>
