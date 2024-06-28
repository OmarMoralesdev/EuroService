<?php

require '../includes/db.php';

if (isset($_GET['nombre'])) {
    $nombre = trim($_GET['nombre']);

    try {
        $stmt = $conn->prepare("SELECT * FROM CLIENTES WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            echo json_encode(['success' => true, 'cliente' => $cliente]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false]);
}

?>
