<?php
require '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['fecha'])) {
        $_SESSION['error'] = 'La fecha es requerida.';
        header("Location: nomina_Semana.php");
        exit();
    }

    $fecha_inicio = $_POST['fecha'];

    // Validar que la fecha es un lunes
    if (date('N', strtotime($fecha_inicio)) !== '1') {
        $_SESSION['error'] = 'La fecha seleccionada debe ser un lunes.';
        header("Location: nomina_Semana.php");
        exit();
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
            $_SESSION['error'] = 'Ya existe una nómina para la fecha seleccionada.';
            header("Location: nomina_Semana.php");
            exit();
        }

        // Verificar si todas las asistencias están registradas para todos los empleados activos en la semana
        $query_asistencias = "
            SELECT e.empleadoID, COUNT(a.asistenciaID) AS dias_registrados
            FROM EMPLEADOS e
            LEFT JOIN ASISTENCIA a ON e.empleadoID = a.empleadoID 
                AND a.fecha BETWEEN :fecha_inicio AND :fecha_fin
            WHERE e.activo = 'si'
            GROUP BY e.empleadoID
            HAVING dias_registrados < 5
        ";
        $stmt_asistencias = $pdo->prepare($query_asistencias);
        $stmt_asistencias->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt_asistencias->bindParam(':fecha_fin', $fecha_fin);
        $stmt_asistencias->execute();
        
        if ($stmt_asistencias->rowCount() > 0) {
            $_SESSION['error'] = 'No todas las asistencias están registradas para la semana seleccionada.';
            header("Location: nomina_Semana.php");
            exit();
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
        WHERE e.activo = 'si'
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

        $_SESSION['bien'] = "Nómina semanal registrada o actualizada correctamente.";
        header("Location: nomina_Semana.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        // Puedes registrar el error en un archivo de log si es necesario.
        // error_log($e->getMessage(), 3, '/var/log/mi_aplicacion.log');
        header("Location: nomina_Semana.php");
        exit();
    }

    $pdo = null;
}
?>
