<?php
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = isset($_POST['empleadoID']) ? $_POST['empleadoID'] : null;
    $bono = isset($_POST['bono']) ? $_POST['bono'] : null;

    if ($empleadoID && $bono !== null) {
        try {
            // Obtener la nómina actual del empleado
            $sql = "SELECT nominaID, total, bonos FROM NOMINAS WHERE empleadoID = :empleadoID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':empleadoID', $empleadoID);
            $stmt->execute();
            $nomina = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($nomina) {
                // Calcular la nueva cantidad de bonos
                $nuevosBonos = $nomina['bonos'] + $bono;
                // Calcular el nuevo total de la nómina
                $nuevoTotal = $nomina['total'] + $bono;

                // Actualizar el campo bonos y total en la tabla NOMINAS
                $sql = "UPDATE NOMINAS SET bonos = :nuevosBonos, total = :nuevoTotal WHERE nominaID = :nominaID";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':nuevosBonos', $nuevosBonos);
                $stmt->bindValue(':nuevoTotal', $nuevoTotal);
                $stmt->bindValue(':nominaID', $nomina['nominaID']);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Bono aplicado y nómina actualizada correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Nómina no encontrada']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
    }
}

$conexion->desconectar();
?>
