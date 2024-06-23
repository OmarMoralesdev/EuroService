<?php
$servername = "127.0.0.1";
$username = "root";
$password = "1234";
$dbname = "TALLER_EURO";

// Crear conexión
try{
$conn = new PDO("mysql:host=$servername;dbname=$dbname",$username, $password, );

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e){
echo "Error: ". $e->getMessage();
}
