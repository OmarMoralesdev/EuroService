<?php
require 'database.php';

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];

    try {
        $sql = "INSERT INTO EMPLEADOS (nombre, tipo) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $tipo]);

        echo "Nuevo registro creado exitosamente";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
