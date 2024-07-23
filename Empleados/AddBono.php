<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rebaja = $_POST['rebaja']; // Asegúrate de usar el nombre correcto del campo del formulario

    $conexion = new Database();
    $conexion->conectar();
    
    // Ejecutar la inserción en la tabla NOMINAS
    $consulta = "INSERT INTO NOMINAS (rebajas) VALUES ('$rebaja')";
    $conexion->ejecuta($consulta);
    
    
    $conexion->desconectar();
    
    // Redirigir de vuelta a salario.php
    header('Location: salario.php');
    exit();
}
?>