<?php
include 'db.php';

if (isset($_POST['clienteId']) && isset($_POST['vehiculoId']) && isset($_POST['fecha'])) {
    $clienteId = $_POST['clienteId'];
    $vehiculoId = $_POST['vehiculoId'];
    $fecha = $_POST['fecha'];

    $sql = "INSERT INTO citas (cliente_id, vehiculo_id, fecha) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$clienteId, $vehiculoId, $fecha]);

    if ($result) {
        echo json_encode(["mensaje" => "Cita registrada correctamente"]);
    } else {
        echo json_encode(["mensaje" => "Error al registrar la cita"]);
    }
}
?>
