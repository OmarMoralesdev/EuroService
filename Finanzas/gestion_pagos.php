<?php
// Datos de conexión a la base de datos
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();
try {


    // Obtener la semana desde el formulario
    $semana = $_POST['week'];

    // Calcular la fecha de inicio (lunes) y la fecha de fin (domingo) de la semana seleccionada
    $fecha_inicio = date('Y-m-d', strtotime($semana));
    $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));

    // Preparar la consulta SQL para llamar al procedimiento almacenado
    $sql = "CALL gestionPagosSemanal(:fecha_inicio, :fecha_fin)";

    // Preparar la sentencia
    $stmt = $pdo->prepare($sql);

    // Bind de los parámetros
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);

    // Ejecutar la sentencia
    $stmt->execute();

    // Obtener los resultados
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        // Mostrar los resultados en una tabla HTML
        echo "<table border='1'>";
        echo "<tr><th>Recibo ID</th><th>Fecha Recibo</th><th>Cliente</th><th>Cantidad Pagada</th><th>Estado Pago</th></tr>";
        
        foreach ($result as $row) {
            $estado_pago = $row['estado_pago'];
            $color = "";

            if ($estado_pago == 'verde') {
                $color = "#d4edda"; // Verde claro
            } elseif ($estado_pago == 'naranja') {
                $color = "#fff3cd"; // Naranja claro
            } elseif ($estado_pago == 'rojo') {
                $color = "#f8d7da"; // Rojo claro
            }

            echo "<tr style='background-color: $color;'>";
            echo "<td>" . htmlspecialchars($row['reciboID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha_recibo']) . "</td>";
            echo "<td>" . htmlspecialchars($row['cliente']) . "</td>";
            echo "<td>" . htmlspecialchars($row['cantidad_pagada']) . "</td>";
            echo "<td>" . htmlspecialchars($row['estado_pago']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "No se encontraron resultados.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$conn = null;
?>
