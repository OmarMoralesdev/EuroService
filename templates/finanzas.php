<?php
// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir el archivo de configuración de la base de datos
    require '../includes/db.php';

    // Obtener y validar los datos del formulario
    $clienteID_param = $_POST['clienteID'];
    $cantidad_pagada = $_POST['cantidad_pagada'];
    $receptor = $_POST['receptor'];
 

    // Conectar a la base de datos
    $con = new Database();
    $pdo = $con->conectar();

    try {
        // Preparar la llamada al procedimiento almacenado
        $stmt = $pdo->prepare("CALL generarReciboPago(:clienteID, :cantidadPagada, :receptor)");
        $stmt->bindParam(':clienteID', $clienteID_param, PDO::PARAM_INT);
        $stmt->bindParam(':cantidadPagada', $cantidad_pagada, PDO::PARAM_STR);
        $stmt->bindParam(':receptor', $receptor, PDO::PARAM_STR);

        // Ejecutar el procedimiento almacenado
        $stmt->execute();

        // Recuperar el mensaje de confirmación
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $mensaje = $result['mensaje'];

        // Mostrar el mensaje de confirmación
        echo "<p>$mensaje</p>";

        // Cerrar el cursor del resultado anterior
        $stmt->closeCursor();

        // Insertar información adicional en la tabla Recibos_Pago
        $fecha_recibo = date('Y-m-d'); // Fecha actual del sistema

        $stmtInsert = $pdo->prepare("INSERT INTO Recibos_Pago (fecha_recibo, cliente, cantidad_pagada, receptor) 
                                    VALUES (:fecha_recibo, :cliente, :cantidad_pagada, :receptor)");
        $stmtInsert->bindParam(':fecha_recibo', $fecha_recibo, PDO::PARAM_STR);
        $stmtInsert->bindParam(':cliente', $clienteID_param, PDO::PARAM_INT);
        $stmtInsert->bindParam(':cantidad_pagada', $cantidad_pagada, PDO::PARAM_STR);
        $stmtInsert->bindParam(':receptor', $receptor, PDO::PARAM_STR);
        $stmtInsert->execute();

        // Mostrar mensaje de éxito
        echo "<p>Recibo de pago generado y registrado correctamente.</p>";

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Cerrar la conexión
    $pdo = null;
}
?>
