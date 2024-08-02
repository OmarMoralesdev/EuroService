<?php
session_start();
try {
    require '../includes/db.php';
    $con = new Database();
    $pdo = $con->conectar();


    $ordenID = $_POST['ordenID'];
    $nuevaUbicacionID = 5;

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

    // Actualizar la orden de trabajo con la nueva ubicación
    $sqlActualizarOrden = "UPDATE ORDENES_TRABAJO SET ubicacionID = ? WHERE ordenID = ?";
    $stmtActualizarOrden = $pdo->prepare($sqlActualizarOrden);
    $stmtActualizarOrden->execute([$nuevaUbicacionID, $ordenID]);

    if ($stmtActualizarOrden->rowCount() > 0) {
        $_SESSION['success'] = "Orden de trabajo movida exitosamente.";
    } else {
        $_SESSION['error'] = "Error al mover la orden de trabajo.";
    }
    $_SESSION['bien'] = "Entrega registrada con éxito.";
    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}
