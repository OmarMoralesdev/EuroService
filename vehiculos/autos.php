<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    echo "Selected Client ID: $clienteID";
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $anio = trim($_POST['anio']);
    $color = trim($_POST['color']);
    $kilometraje = trim($_POST['kilometraje']);
    $placas = trim($_POST['placas']);
    $vin = trim($_POST['vin']);

    $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
    $stmtVerificar = $pdo->prepare($verificar);
    $stmtVerificar->execute([$vin]);

    if ($stmtVerificar->rowCount() > 0) {
        echo "El vehículo ya está registrado.";
    } else {
        $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo,anio,color,kilometraje,placas,vin) VALUES (?, ?, ?,?,?, ?, ?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

        if ($stmt->rowCount() > 0) {
            echo "Vehículo registrado exitosamente.";
        } else {
            echo "Error: " . $sql . "<br>" . $pdo->errorInfo()[2];
        }
    }
}
