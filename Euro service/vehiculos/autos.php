<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$nombre = $_POST['nombre'];
$marca = $_POST['marca'];
$modelo = $_POST['modelo'];
$año = $_POST['año'];
$color = $_POST['color'];
$placas = $_POST['placas'];
$vin = $_POST['vin'];

$verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
$stmtVerificar = $pdo->prepare($verificar);
$stmtVerificar->execute([$vin]);

if ($stmtVerificar->rowCount() > 0) {
    echo "El vehículo ya está registrado.";
} else {
    $sql = "INSERT INTO VEHICULOS (nombre, marca, modelo,año,color,placas,vin) VALUES (?, ?, ?,?,?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $marca, $modelo,$año,$color,$placas,$vin]);

    if ($stmt->rowCount() > 0) {
        echo "Vehículo registrado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $pdo->errorInfo()[2];
    }
}
?>
