<?php
include 'db.php';

if (isset($_GET['nombre'])) {
    $nombre = $_GET['nombre'];
    $sql = "SELECT * FROM clientes WHERE nombre LIKE ?";
    $stmt = $pdo->prepare($sql);
    $searchTerm = "%" . $nombre . "%";
    $stmt->execute([$searchTerm]);

    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clientes);
}
?>
