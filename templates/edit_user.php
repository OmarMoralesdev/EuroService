<?php
require 'db.php';

$con = new Database();
$pdo = $con->conectar();
$search = $POST["search"];
$sql = "SELECT nombre, apellido_paterno, apellido_materno from CLIENTES where nombre LIKE ? ORDER BY nombre ASC";
$query = $pdo->prepare($sql);
$query->excute([$search , '%']);

$html ="";

while($row = $query -> fetch(PDO::FETCH_ASSOC)){
    $html .= "<li>" . $row["nombre"] . " - " . $row["apellido_paterno"] . "<li>";
}
echo json_encode($html, JSON_UNESCAPED_UNICODE);