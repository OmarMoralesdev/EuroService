<?php
session_start();
// Conexión a la base de datos
try {
    require '../includes/db.php';
    $con = new Database();
    $pdo = $con->conectar();
    

    $ordenID = $_POST['ordenID'];


    $stmt = $pdo->prepare("SELECT total_estimado FROM ORDENES_TRABAJO WHERE ordenID = ?");
    $stmt->execute([$ordenID]);
    $orden = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$orden) {
       $_SESSION['error'] = "Orden no encontrada.";
    }

    $totalEstimado = $orden['total_estimado'];

  
    $stmt = $pdo->prepare("SELECT pagoID FROM PAGOS WHERE ordenID = ?");
    $stmt->execute([$ordenID]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pago) {
       $_SESSION['error'] = "No se encontró un pago asociado a esta orden.";
    }

    $pagoID = $pago['pagoID'];




    $stmt = $pdo->prepare("CALL registrar_entrega(?)");
    $stmt->execute([$pagoID]);
    $ubicacionID = $orden['ubicacionID'];
    $sqlActualizarUbicacion = "UPDATE UBICACIONES SET vehiculos_actuales = vehiculos_actuales - 1 WHERE ubicacionID = ?";
    $stmtActualizarUbicacion = $pdo->prepare($sqlActualizarUbicacion);
    $stmtActualizarUbicacion->execute([$ubicacionID]);
    $_SESSION['bien'] = "Entrega registrada con éxito.";
    header("Location: index.php");
    exit();


} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}
?>
