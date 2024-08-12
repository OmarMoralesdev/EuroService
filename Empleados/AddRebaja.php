<?php
include '../includes/db.php';

$conexion = new Database();
$pdo = $conexion->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empleadoID = isset($_POST['empleadoID']) ? $_POST['empleadoID'] : null;
    $rebaja = isset($_POST['rebaja']) ? $_POST['rebaja'] : null;

    if ($empleadoID && $rebaja !== null) {
        try {
            // Consultar el salario diario del empleado
            $sql = "SELECT salario_diario FROM EMPLEADOS WHERE empleadoID = :empleadoID";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':empleadoID', $empleadoID);
            $stmt->execute();
            $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($empleado) {
                $salarioDiario = $empleado['salario_diario'];

                if ($rebaja > $salarioDiario * 5) { 
                    echo json_encode(['status' => 'error', 'message' => 'La rebaja no puede ser mayor al salario total']);
                } else {
                    // Aplicar la rebaja
                    $nuevoSalario = $salarioDiario - $rebaja / 5;

                    $sql = "UPDATE EMPLEADOS SET salario_diario = :nuevoSalario WHERE empleadoID = :empleadoID";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':nuevoSalario', $nuevoSalario);
                    $stmt->bindValue(':empleadoID', $empleadoID);
                    $stmt->execute();
                    echo json_encode(['status' => 'success', 'message' => 'Rebaja aplicada correctamente']);
                }
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
