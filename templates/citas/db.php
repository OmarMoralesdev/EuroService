<?php
$dsn = 'mysql:host=127.0.0.1;dbname=TALLER_EURO';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
