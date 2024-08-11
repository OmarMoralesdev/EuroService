<?php
session_start();
try {
    require '../includes/db.php';
    $con = new Database();
    $pdo = $con->conectar();

    // Verificar que ordenID esté definido en POST
    if (!isset($_POST['ordenID'])) {
        $_SESSION['error'] = "No se proporcionó un ID de orden.";
        header("Location: entregar.php");
        exit();
    }
    $formaDePago = isset($_POST['formadepago']) ? trim($_POST['formadepago']) : '';
    $ordenID = $_POST['ordenID'];
    $nuevaUbicacionID = 4;

    // Consultar el total estimado de la orden
    $stmt = $pdo->prepare("SELECT total_estimado, ubicacionID FROM ORDENES_TRABAJO WHERE ordenID = ?");
    $stmt->execute([$ordenID]);
    $orden = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$orden) {
        $_SESSION['error'] = "Orden no encontrada.";
        header("Location: entregar.php");
        exit();
    }

    // Registrar la entrega usando el procedimiento almacenado
    $stmt = $pdo->prepare("CALL registrar_entrega(?,?)");
    $stmt->execute([$ordenID, $formaDePago]);

    // Actualizar la orden de trabajo con la nueva ubicación
    $sqlActualizarOrden = "UPDATE ORDENES_TRABAJO SET ubicacionID = ? WHERE ordenID = ?";
    $stmtActualizarOrden = $pdo->prepare($sqlActualizarOrden);
    $stmtActualizarOrden->execute([$nuevaUbicacionID, $ordenID]);

    if ($stmtActualizarOrden->rowCount() > 0) {
        $_SESSION['bien'] = "Orden de trabajo entregada exitosamente.";
    } else {
        $_SESSION['error'] = "Error al entregar la orden de trabajo.";
    }

    header("Location: entregar.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: entregar.php");
    exit();
}
?>