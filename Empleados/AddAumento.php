<?php
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = isset($_POST['empleadoID']) ? $_POST['empleadoID'] : null;
    $aumento = isset($_POST['aumento']) ? $_POST['aumento'] : null;

    if ($empleadoID && $aumento !== null) {
        try {
            $sql = "SELECT salario_diario FROM EMPLEADOS WHERE empleadoID = :empleadoID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':empleadoID', $empleadoID);
            $stmt->execute();
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($empleado) {
                $salarioDiario = $empleado['salario_diario'];
                $nuevoSalario = $salarioDiario + $aumento / 5;

                $sql = "UPDATE EMPLEADOS SET salario_diario = :nuevoSalario WHERE empleadoID = :empleadoID";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':nuevoSalario', $nuevoSalario);
                $stmt->bindValue(':empleadoID', $empleadoID);
                $stmt->execute();

                echo json_encode(['status' => 'success', 'message' => 'Aumento aplicado correctamente']);
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
