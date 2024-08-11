<?php
// Datos de conexión a la base de datos
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

try {
    // Crear una nueva instancia de la clase Database
    $con = new Database();
    $pdo = $con->conectar();

    // Obtener la semana desde el formulario
    $semana = $_POST['week'];

    // Calcular la fecha de inicio (lunes) y la fecha de fin (domingo) de la semana seleccionada
    $fecha_inicio = date('Y-m-d', strtotime($semana));
    $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));

    // Preparar la consulta SQL para llamar al procedimiento almacenado con parámetros
    $sql = "CALL ObtenerPagosSemanal(:fecha_inicio, :fecha_fin)";

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
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID Pago</th><th>ID Orden</th><th>Fecha Orden</th><th>Fecha Pago</th><th>Monto</th><th>Tipo de Pago</th><th>Forma de Pago</th><th>Estado</th></tr>";
        
        foreach ($result as $row) {
            $estado = $row['estado'];
            $color = "";

            if ($estado == 'verde') {
                $color = "#d4edda"; // Verde claro
            } elseif ($estado == 'naranja') {
                $color = "#fff3cd"; // Naranja claro
            } elseif ($estado == 'rojo') {
                $color = "#f8d7da"; // Rojo claro
            }

            echo "<tr style='background-color: $color;'>";
            echo "<td>" . htmlspecialchars($row['pagoID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ordenID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha_orden']) . "</td>";
            echo "<td>" . htmlspecialchars($row['fecha_pago']) . "</td>";
            echo "<td>" . htmlspecialchars($row['monto']) . "</td>";
            echo "<td>" . htmlspecialchars($row['tipo_pago']) . "</td>";
            echo "<td>" . htmlspecialchars($row['forma_de_pago']) . "</td>";
            echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
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
$pdo = null;
?>
