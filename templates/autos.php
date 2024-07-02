<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

// Obtener datos del formulario
$clienteID = $_POST['clienteID'];
$empleado = $_POST['empleado'];
$marca = $_POST['marca'];
$modelo = $_POST['modelo'];
$año = $_POST['año'];
$color = $_POST['color'];
$placas = $_POST['placas'];
$vin = $_POST['vin'];
$inspeccion = $_POST['inspeccion'];

// Preparar y ejecutar la consulta de ins,erción
$sql = "INSERT INTO VEHICULOS (clienteID,empleadoID, marca, modelo,año,color,placas,vin,inspeccionID) VALUES (?, ?, ?,?,?, ?, ?,?,?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$clienteID, $empleado, $marca, $modelo,$año,$color,$placas,$vin,$inspeccion]);

if ($stmt->execute()) {
    echo "Vehículo registrado exitosamente.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
?>
