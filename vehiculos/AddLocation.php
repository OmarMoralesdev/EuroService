<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lugar = $_POST['lugar'];
    $capacidad = $_POST['capacidad'];
    $conexion = new Database();
    $conexion->conectar();
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

    } else {
        // Verificar si la capacidad es válida y agregar la ubicación a la base de datos si es así
        if($capacidad > 0 && $capacidad <= 40 ){

            // Insertar la ubicación en la base de datos
            $consulta = "INSERT INTO UBICACIONES (lugar, capacidad, activo) VALUES ('$lugar', $capacidad, 'si')";
            $conexion->ejecuta($consulta);
            $_SESSION['bien'] ="Ubicación '{$lugar}' añadida exitosamente";
            $conexion->desconectar();
            
            header('Location: ubicaciones_view.php');
            exit();
            // Mostrar un modal de éxito si la ubicación se agregó correctamente 
        }else if ($capacidad < 0){
            $_SESSION['error'] = "No puedes ingresar numeros negativos";
                // Redirigir al usuario después de la acción
                header('Location: ubicaciones_view.php');
                exit();
            
    
        }
        // Mostrar un modal de error si la capacidad no es válida (máximo 40 vehículos en una ubi)
        else if ($capacidad > 40){
            $_SESSION['error'] = "La capacidad no puede ser mayor a 40";    
        }
    }
    header('Location: ubicaciones_view.php');
    exit();
}
?>
