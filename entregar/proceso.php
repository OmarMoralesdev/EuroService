<?php
session_start();
try {
    require '../includes/db.php';
    $con = new Database();
    $pdo = $con->conectar();

    // Verificar que ordenID esté definido en POST
    if (!isset($_POST['ordenID'])) {
        $_SESSION['error'] = "No se proporcionó un ID de orden.";
        header("Location: index.php");
        exit();
    }

    $ordenID = $_POST['ordenID'];
    $nuevaUbicacionID = 1;

    // Consultar el total estimado de la orden
    $stmt = $pdo->prepare("SELECT total_estimado, ubicacionID FROM ORDENES_TRABAJO WHERE ordenID = ?");
    $stmt->execute([$ordenID]);
    $orden = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$orden) {
        $_SESSION['error'] = "Orden no encontrada.";
        header("Location: index.php");
        exit();
    }

    // Consultar el pago asociado a la orden
    $stmt = $pdo->prepare("SELECT pagoID FROM PAGOS WHERE ordenID = ?");
    $stmt->execute([$ordenID]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pago) {
        $_SESSION['error'] = "No se encontró un pago asociado a esta orden.";
        header("Location: index.php");
        exit();
    }

    $pagoID = $pago['pagoID'];

    // Registrar la entrega usando el procedimiento almacenado
    $stmt = $pdo->prepare("CALL registrar_entrega(?)");
    $stmt->execute([$pagoID]);


    // Actualizar la orden de trabajo con la nueva ubicación
    $sqlActualizarOrden = "UPDATE ORDENES_TRABAJO SET ubicacionID = ? WHERE ordenID = ?";
    $stmtActualizarOrden = $pdo->prepare($sqlActualizarOrden);
    $stmtActualizarOrden->execute([$nuevaUbicacionID, $ordenID]);

    if ($stmtActualizarOrden->rowCount() > 0) {
        $_SESSION['success'] = "Orden de trabajo movida exitosamente.";
    } else {
        $_SESSION['error'] = "Error al mover la orden de trabajo.";
    }

    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: index.php");
    exit();
}
?>
