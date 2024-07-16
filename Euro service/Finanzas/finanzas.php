<?php
// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir el archivo de configuración de la base de datos
    require '../includes/db.php';

    // Obtener y validar los datos del formulario
    $clienteID_param = $_POST['campo'];
    $cantidad_pagada = $_POST['cantidad_pagada'];
    $receptor = $_POST['receptor'];

    // Conectar a la base de datos
    $con = new Database();
    $pdo = $con->conectar();

    try {
        // Preparar la llamada al procedimiento almacenado
        $stmt = $pdo->prepare("CALL generarReciboPago(:nombre_cliente, :cantidad_pagada, :receptor, @mensaje, @fecha_recibo, @cliente_nombre, @cantidad_pagada_recibo)");
        $stmt->bindParam(':nombre_cliente', $clienteID_param, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad_pagada', $cantidad_pagada, PDO::PARAM_STR);
        $stmt->bindParam(':receptor', $receptor, PDO::PARAM_STR);

        // Ejecutar el procedimiento almacenado
        $stmt->execute();

        // Recuperar los valores de salida
        $stmt->closeCursor();
        $result = $pdo->query("SELECT @mensaje AS mensaje, @fecha_recibo AS fecha_recibo, @cliente_nombre AS cliente_nombre, @cantidad_pagada_recibo AS cantidad_pagada_recibo")->fetch(PDO::FETCH_ASSOC);

        // Mostrar el mensaje de confirmación y los datos del recibo
        echo "<p>" . htmlspecialchars($result['mensaje']) . "</p>";
        echo "<p>Fecha del recibo: " . htmlspecialchars($result['fecha_recibo']) . "</p>";
        echo "<p>Nombre del cliente: " . htmlspecialchars($result['cliente_nombre']) . "</p>";
        echo "<p>Cantidad pagada: " . htmlspecialchars($result['cantidad_pagada_recibo']) . "</p>";

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Cerrar la conexión
    $pdo = null;
}
?>
