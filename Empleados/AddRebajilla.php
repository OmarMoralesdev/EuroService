<?php
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = isset($_POST['empleadoID']) ? $_POST['empleadoID'] : null;
    $rebaja = isset($_POST['rebaja']) ? $_POST['rebaja'] : null;

    if ($empleadoID && $rebaja !== null) {
        try {
            // Obtener la nómina actual del empleado
            $sql = "SELECT nominaID, total, rebajas FROM NOMINAS WHERE empleadoID = :empleadoID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':empleadoID', $empleadoID);
            $stmt->execute();
            $nomina = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($nomina) {
                // Calcular la nueva cantidad de rebajas
                $nuevasRebajas = $nomina['rebajas'] + $rebaja;
                // Calcular el nuevo total de la nómina
                $nuevoTotal = $nomina['total'] - $rebaja;

                // Actualizar el campo rebajas y total en la tabla NOMINAS
                $sql = "UPDATE NOMINAS SET rebajas = :nuevasRebajas, total = :nuevoTotal WHERE nominaID = :nominaID";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':nuevasRebajas', $nuevasRebajas);
                $stmt->bindValue(':nuevoTotal', $nuevoTotal);
                $stmt->bindValue(':nominaID', $nomina['nominaID']);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Rebaja aplicada y nómina actualizada correctamente']);
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
