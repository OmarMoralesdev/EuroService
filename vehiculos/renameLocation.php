<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ubicacionID = $_POST['ubicacionID'];
    $lugar = $_POST['lugarn'];

    $conexion = new Database();
    $conn = $conexion->conectar();

    $consulta_nombre = "SELECT COUNT(*) as count FROM UBICACIONES WHERE lugar = :lugar";
    $stmt_nombre = $conn->prepare($consulta_nombre);
    $stmt_nombre->bindParam(':lugar', $lugar, PDO::PARAM_STR);
    $stmt_nombre->execute();
    $resultado_nombre = $stmt_nombre->fetch(PDO::FETCH_ASSOC);

    // Verificar si el lugar ya existe en la base de datos y mostrar un modal de error si es necesario
    if ($resultado_nombre['count'] > 0) {
        $_SESSION['error'] = "Ya existe una ubicación con el nombre '{$lugar}'";
        header('Location: ubicaciones_view.php');
        exit();

    } else 
    {
        $consulta = "UPDATE UBICACIONES SET lugar = '$lugar' WHERE ubicacionID = $ubicacionID";
        $conexion->ejecuta($consulta);
        $_SESSION['r'] = "La ubicación ha sido renombrada a: '{$lugar}'";

    
    }
    // Actualizar el nombre de la ubicación en la base de datos
    $conexion->desconectar();

    header('Location: ubicaciones_view.php');
    exit();
}
?>