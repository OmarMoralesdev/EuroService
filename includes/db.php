<?php
$servername = "127.0.0.1";
$username = "root";
$password = "1234";
$dbname = "TALLER_EURO";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
