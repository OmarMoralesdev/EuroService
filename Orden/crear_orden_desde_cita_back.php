<?php
session_start();
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citaID = $_POST['citaID'];
    $empleado = $_POST['empleado'];
    $ubicacionID = $_POST['ubicacionID'];
    $anticipo = $_POST['anticipo'];
    $formaDePago = $_POST['formadepago'];
    $fechaOrden = date('Y-m-d H:i:s');
    $atencion = $_POST['atencion'];
    $fechaPago = date('Y-m-d H:i:s');

    $sqltotal = "SELECT total_estimado FROM CITAS WHERE citaID = :citaID";
    $stmtcitatotal = $pdo->prepare($sqltotal);
    $stmtcitatotal->execute([':citaID' => $citaID]);
    if ($result = $stmtcitatotal->fetch(PDO::FETCH_ASSOC)) {
        $totalEstimado = $result['total_estimado'];
        $total = $totalEstimado/2;
        if ($anticipo <= $total) {
            $_SESSION['error'] = "El anticipo debe ser menor o igual al total estimado de $total.";
            header("Location: crear_orden_desde_cita.php?citaID=$citaID");
            exit();
        }
    }
    if (
        empty($empleado) || empty($ubicacionID) || empty($anticipo) ||
        empty($formaDePago) || empty($atencion)
    ) {
        $_SESSION['error'] = "Todos los campos son obligatorios. Por favor, completa todos los campos.";
        header("Location: crear_orden_desde_cita.php?citaID=$citaID");
        exit();
    }

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Insertar la nueva orden de trabajo
        $sqlOrden = "
            INSERT INTO ORDENES_TRABAJO (fecha_orden, atencion, citaID, empleadoID, ubicacionID) 
            VALUES (?, ?, ?, ?, ?)
            ";
        $stmtOrden = $pdo->prepare($sqlOrden);
        $stmtOrden->execute([$fechaOrden, $atencion, $citaID, $empleado, $ubicacionID]);
        $ordenID = $pdo->lastInsertId();
        $tipoPago = "anticipo";

        // Esto ya se maneja en los triggers
        try {
            // Llamar al procedimiento almacenado para realizar el pago
            $sql = "CALL realizarPago(:ordenID, :fechaPago, :monto, :tipoPago, :formaDePago)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ordenID' => $ordenID,
                ':fechaPago' => $fechaPago,
                ':monto' => $anticipo,
                ':tipoPago' => $tipoPago,
                ':formaDePago' => $formaDePago,
            ]);
        } catch (PDOException $e) {
            $_SESSION['error'] = ("Error al realizar el pago: " . $e->getMessage());
            header("Location: crear_orden_desde_cita.php");
            exit();
        }
        // Confirmar la transacción
        $pdo->commit();

        $_SESSION['bien'] = "Orden de trabajo $ordenID creada con éxito.";
        header("Location: crear_orden_desde_cita.php");
        exit();
    } catch (Exception $e) {
        // En caso de error, deshacer la transacción y mostrar el mensaje de error
        $pdo->rollBack();
        $_SESSION['error'] = "Error al crear la orden de trabajo: " . $e->getMessage();
        header("Location: crear_orden_desde_cita.php");
        exit();
    }
} else {
    echo "Cita no encontrada.";
}
