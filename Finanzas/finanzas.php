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

        // Mostrar el recibo con los datos procesados
        $mensaje = htmlspecialchars($result['mensaje']);
        $fecha_recibo = htmlspecialchars($result['fecha_recibo']);
        $cliente_nombre = htmlspecialchars($result['cliente_nombre']);
        $cantidad_pagada_recibo = htmlspecialchars($result['cantidad_pagada_recibo']);

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Cerrar la conexión
    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        .recibo {
            width: 600px;
            border: 1px solid black;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .recibo-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .recibo-section {
            margin: 20px 0;
        }
        .recibo-label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }
        .recibo-field {
            display: inline-block;
            width: 400px;
            border-bottom: 1px solid black;
        }
    </style>
</head>
<body>
    <div class="recibo">
        <div class="recibo-header">RECIBO DE PAGO</div>
        <div class="recibo-section">
            <span class="recibo-label">Fecha:</span>
            <span class="recibo-field"><?php echo $fecha_recibo; ?></span>
        </div>
        <div class="recibo-section">
            <span class="recibo-label">Recibi de:</span>
            <span class="recibo-field"><?php echo $cliente_nombre; ?></span>
        </div>
        <div class="recibo-section">
            <span class="recibo-label">Cantidad:</span>
            <span class="recibo-field"><?php echo $cantidad_pagada_recibo; ?></span>
        </div>
        <div class="recibo-section">
            <span class="recibo-label">Concepto:</span>
            <span class="recibo-field">Pago de Cupra</span>
        </div>
        <div class="recibo-section">
            <span class="recibo-label">Recibido por:</span>
            <span class="recibo-field"><?php echo htmlspecialchars($receptor); ?></span>
        </div>
        <div class="recibo-section">
            <span class="recibo-label">Firma:</span>
            <span class="recibo-field">_____________________</span>
        </div>
    </div>
</body>
</html>
