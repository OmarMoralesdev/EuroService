<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleadoID = $_POST['empleadoID'];
    $bonos = $_POST['bonos'];
    $rebajas_adicionales = $_POST['rebajas_adicionales'];
    $fecha_inicio = $_POST['fecha_inicio'];

    // Validar que la fecha es un lunes
    if (date('N', strtotime($fecha_inicio)) !== '1') {
        die('La fecha seleccionada debe ser un lunes.');
    }

    $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));

    try {
        $con = new Database();
        $pdo = $con->conectar();

        // Primero, obtenemos el ID de nómina para el empleado y las fechas proporcionadas
        $query = "
        SELECT nominaID
        FROM NOMINAS
        WHERE empleadoID = :empleadoID AND fecha_inicio = :fecha_inicio
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':empleadoID', $empleadoID);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $nominaID = $result['nominaID'];
        } else {
            // Si no hay nómina existente, crear una nueva
            $query = "
            INSERT INTO NOMINAS (faltas, rebajas, bonos, rebajas_adicionales, fecha_de_pago, total, fecha_inicio, fecha_fin, empleadoID)
            VALUES (0, 0, :bonos, :rebajas_adicionales, CURDATE(), 0, :fecha_inicio, :fecha_fin, :empleadoID)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':bonos', $bonos);
            $stmt->bindParam(':rebajas_adicionales', $rebajas_adicionales);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->bindParam(':empleadoID', $empleadoID);
            $stmt->execute();
            
            $nominaID = $pdo->lastInsertId();
        }

        // Actualizamos los bonos y las rebajas adicionales para la nómina especificada
        $query = "
        UPDATE NOMINAS
        SET bonos = :bonos, rebajas_adicionales = :rebajas_adicionales
        WHERE nominaID = :nominaID
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nominaID', $nominaID);
        $stmt->bindParam(':bonos', $bonos);
        $stmt->bindParam(':rebajas_adicionales', $rebajas_adicionales);
        $stmt->execute();

        // Recalcular el total
        $query = "
        SELECT e.salario_diario, COUNT(a.asistenciaID) AS faltas
        FROM EMPLEADOS e
        LEFT JOIN ASISTENCIA a ON a.empleadoID = e.empleadoID
            AND a.fecha BETWEEN :fecha_inicio AND :fecha_fin
            AND a.asistencia = 'falta'
        WHERE e.empleadoID = :empleadoID
        GROUP BY e.empleadoID
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':empleadoID', $empleadoID);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $salario_diario = $row['salario_diario'];
        $faltas = $row['faltas'];

        $rebajas = $faltas * $salario_diario;
        $total = ($salario_diario * 7) - $rebajas + $bonos - $rebajas_adicionales;

        $query = "
        UPDATE NOMINAS
        SET faltas = :faltas, rebajas = :rebajas, total = :total
        WHERE nominaID = :nominaID
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nominaID', $nominaID);
        $stmt->bindParam(':faltas', $faltas);
        $stmt->bindParam(':rebajas', $rebajas);
        $stmt->bindParam(':total', $total);
        $stmt->execute();

        echo "Bonos y rebajas adicionales actualizados correctamente. Total recalculado.<br>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }

    $pdo = null;
}
?>
