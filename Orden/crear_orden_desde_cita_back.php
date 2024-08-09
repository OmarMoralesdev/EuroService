<?php
session_start();
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citaID = $_POST['citaID'];
    $costoManoObra = $_POST['costoManoObra'];
    $costoRefacciones = $_POST['costoRefacciones'];
    $empleado = $_POST['empleado'];
    $ubicacionID = $_POST['ubicacionID'];
    $anticipo = $_POST['anticipo'];
    $formaDePago = $_POST['formadepago'];
    $fechaOrden = date('Y-m-d H:i:s');
    $atencion = $_POST['atencion'];
    $fechaPago = date('Y-m-d H:i:s');

     // Validar que los campos requeridos no estén vacíos
     if (empty($citaID) || empty($costoManoObra) || empty($costoRefacciones) || 
     empty($empleado) || empty($ubicacionID) || empty($anticipo) || 
     empty($formaDePago) || empty($atencion)) {
     $_SESSION['error'] = "Todos los campos son obligatorios. Por favor, completa todos los campos.";
     header("Location: crear_orden_desde_cita.php?citaID=$citaID");
     exit();
 }
    // Validación inicial para evitar números negativos
    if ($costoManoObra < 0 || $costoRefacciones < 0) {
        $_SESSION['error'] = "No puedes ingresar números negativos.";
        header("Location: crear_orden_desde_cita.php?citaID=$citaID");
        exit();
    }

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Insertar la nueva orden de trabajo
        $sqlOrden = "
            INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmtOrden = $pdo->prepare($sqlOrden);
        $stmtOrden->execute([$fechaOrden, $costoManoObra, $costoRefacciones, $atencion, $citaID, $empleado, $ubicacionID]);
        $ordenID = $pdo->lastInsertId();
        
       
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
      
        $_SESSION['bien'] = "Orden de trabajo creada con éxito. ID de la orden: $ordenID";
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