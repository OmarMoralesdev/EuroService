<?php
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = isset($_POST['empleadoID']) ? $_POST['empleadoID'] : null;
    $rebaja = isset($_POST['rebaja']) ? $_POST['rebaja'] : null;

    if ($empleadoID && $rebaja !== null) {
        try {
            // Obtener el valor actual de rebajas
            $sql = "SELECT rebajas FROM EMPLEADOS WHERE empleadoID = :empleadoID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':empleadoID', $empleadoID);
            $stmt->execute();
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($empleado) {
                // Calcular la nueva cantidad de rebajas
                $rebajasActuales = $empleado['rebajas'];
                $nuevaRebaja = $rebajasActuales + $rebaja;

                // Actualizar el campo rebajas
                $sql = "UPDATE EMPLEADOS SET rebajas = :nuevaRebaja WHERE empleadoID = :empleadoID";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':nuevaRebaja', $nuevaRebaja);
                $stmt->bindValue(':empleadoID', $empleadoID);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Rebaja aplicada correctamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Empleado no encontrado']);
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
