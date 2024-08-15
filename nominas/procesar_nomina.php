<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['fecha'])) {
        die('La fecha es requerida.');
    }

    $fecha_inicio = $_POST['fecha'];

    // Validar que la fecha es un lunes
    if (date('N', strtotime($fecha_inicio)) !== '1') {
        die('La fecha seleccionada debe ser un lunes.');
    }

    $fecha_fin = date('Y-m-d', strtotime($fecha_inicio . ' +6 days'));

    try {
        $con = new Database();
        $pdo = $con->conectar();

        // Verificar si ya existe una nómina para la semana seleccionada
        $query_existente = "
            SELECT COUNT(*) FROM NOMINAS 
            WHERE fecha_inicio = :fecha_inicio
        ";
        $stmt_existente = $pdo->prepare($query_existente);
        $stmt_existente->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt_existente->execute();

        if ($stmt_existente->fetchColumn() > 0) {
            die('Ya existe una nómina para la fecha seleccionada.');
        }

        // Verificar si todas las asistencias están registradas para todos los empleados activos en la semana
        $query_asistencias = "
            SELECT e.empleadoID, COUNT(a.asistenciaID) AS dias_registrados
            FROM EMPLEADOS e
            LEFT JOIN ASISTENCIA a ON e.empleadoID = a.empleadoID 
                AND a.fecha BETWEEN :fecha_inicio AND :fecha_fin
            WHERE e.activo = 1
            GROUP BY e.empleadoID
            HAVING dias_registrados < 7
        ";
        $stmt_asistencias = $pdo->prepare($query_asistencias);
        $stmt_asistencias->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt_asistencias->bindParam(':fecha_fin', $fecha_fin);
        $stmt_asistencias->execute();
        
        if ($stmt_asistencias->rowCount() > 0) {
            die('No todas las asistencias están registradas para la semana seleccionada.');
        }

        // Insertar o actualizar las nóminas para la semana
        $query = "
        INSERT INTO NOMINAS (faltas, rebajas, bonos, rebajas_adicionales, fecha_de_pago, total, fecha_inicio, fecha_fin, empleadoID)
        SELECT
            COUNT(a.asistenciaID) AS faltas,
            COUNT(a.asistenciaID) * e.salario_diario AS rebajas,
            COALESCE(n.bonos, 0) AS bonos,
            COALESCE(n.rebajas_adicionales, 0) AS rebajas_adicionales,
            CURDATE() AS fecha_de_pago,
            (e.salario_diario * 7) - (COUNT(a.asistenciaID) * e.salario_diario) + COALESCE(n.bonos, 0) - COALESCE(n.rebajas_adicionales, 0) AS total,
            :fecha_inicio,
            :fecha_fin,
            e.empleadoID
        FROM EMPLEADOS e
        LEFT JOIN ASISTENCIA a ON a.empleadoID = e.empleadoID
            AND a.fecha BETWEEN :fecha_inicio AND :fecha_fin
            AND a.asistencia = 'falta'
        LEFT JOIN NOMINAS n ON n.empleadoID = e.empleadoID
            AND n.fecha_inicio = :fecha_inicio
        WHERE e.activo = 1
        GROUP BY e.empleadoID
        ON DUPLICATE KEY UPDATE
            faltas = VALUES(faltas),
            rebajas = VALUES(rebajas),
            bonos = VALUES(bonos),
            rebajas_adicionales = VALUES(rebajas_adicionales),
            fecha_de_pago = VALUES(fecha_de_pago),
            total = VALUES(total)
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();

        echo "Nómina semanal registrada o actualizada correctamente.<br>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }

    $pdo = null;
}
?>
