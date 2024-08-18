<?php
require '../includes/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['fecha'])) {
        $_SESSION['error'] = ('La fecha es requerida.');
        header("Location: nomina_Semana.php");
        exit();
    }

    $fecha_inicio = $_POST['fecha'];

    // Validar que la fecha es un lunes
    if (date('N', strtotime($fecha_inicio)) !== '1') {
        $_SESSION['error'] = ('La fecha seleccionada debe ser un lunes.');
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
            $_SESSION['error'] = ('Ya existe una nómina para la fecha seleccionada.');
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
            $_SESSION['error'] = ('No todas las asistencias están registradas para la semana seleccionada.');
            header("Location: nomina_Semana.php");
            exit();
        }

        // Iterar sobre los empleados activos para insertar o actualizar nómina
        $query_empleados = "
            SELECT e.empleadoID, 
                   e.salario_diario,
                   COALESCE(n.bonos, 0) AS bonos,
                   COALESCE(n.rebajas_adicionales, 0) AS rebajas_adicionales
            FROM EMPLEADOS e
            LEFT JOIN NOMINAS n ON n.empleadoID = e.empleadoID 
            WHERE e.activo = 'si'
            GROUP BY e.empleadoID
        ";
        $stmt_empleados = $pdo->prepare($query_empleados);
        $stmt_empleados->execute();
        $empleados = $stmt_empleados->fetchAll(PDO::FETCH_ASSOC);

        foreach ($empleados as $empleado) {
            $empleadoID = $empleado['empleadoID'];
            $salario_diario = $empleado['salario_diario'];
            $bonos = $empleado['bonos'];
            $rebajas_adicionales = $empleado['rebajas_adicionales'];

            // Calcular faltas y rebajas
            $query_faltas = "
                SELECT COUNT(a.asistenciaID) AS faltas
                FROM ASISTENCIA a
                WHERE a.empleadoID = :empleadoID 
                  AND a.fecha BETWEEN :fecha_inicio AND :fecha_fin
                  AND a.asistencia = 'falta'
            ";
            $stmt_faltas = $pdo->prepare($query_faltas);
            $stmt_faltas->bindParam(':empleadoID', $empleadoID);
            $stmt_faltas->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt_faltas->bindParam(':fecha_fin', $fecha_fin);
            $stmt_faltas->execute();
            $faltas = $stmt_faltas->fetchColumn();

            $rebajas = $faltas * $salario_diario;

            // Calcular total
            $total = ($salario_diario * 7) - $rebajas + $bonos - $rebajas_adicionales;

            // Verificar si ya existe una nómina para este empleado en esta semana
            $query_check_nomina = "
                SELECT nominaID FROM NOMINAS
                WHERE empleadoID = :empleadoID
                AND fecha_inicio = :fecha_inicio
            ";
            $stmt_check_nomina = $pdo->prepare($query_check_nomina);
            $stmt_check_nomina->bindParam(':empleadoID', $empleadoID);
            $stmt_check_nomina->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt_check_nomina->execute();
            $nominaID = $stmt_check_nomina->fetchColumn();

            if ($nominaID) {
                // Actualizar nómina existente
                $query_update_nomina = "
                    UPDATE NOMINAS
                    SET faltas = :faltas,
                        rebajas = :rebajas,
                        total = :total
                    WHERE nominaID = :nominaID
                ";
                $stmt_update_nomina = $pdo->prepare($query_update_nomina);
                $stmt_update_nomina->bindParam(':faltas', $faltas);
                $stmt_update_nomina->bindParam(':rebajas', $rebajas);
                $stmt_update_nomina->bindParam(':total', $total);
                $stmt_update_nomina->bindParam(':nominaID', $nominaID);
                $stmt_update_nomina->execute();
            } else {
                // Insertar nueva nómina
                $query_insert_nomina = "
                    INSERT INTO NOMINAS (faltas, rebajas, bonos, rebajas_adicionales, fecha_de_pago, total, fecha_inicio, fecha_fin, empleadoID)
                    VALUES (:faltas, :rebajas, :bonos, :rebajas_adicionales, CURDATE(), :total, :fecha_inicio, :fecha_fin, :empleadoID)
                ";
                $stmt_insert_nomina = $pdo->prepare($query_insert_nomina);
                $stmt_insert_nomina->bindParam(':faltas', $faltas);
                $stmt_insert_nomina->bindParam(':rebajas', $rebajas);
                $stmt_insert_nomina->bindParam(':bonos', $bonos);
                $stmt_insert_nomina->bindParam(':rebajas_adicionales', $rebajas_adicionales);
                $stmt_insert_nomina->bindParam(':total', $total);
                $stmt_insert_nomina->bindParam(':fecha_inicio', $fecha_inicio);
                $stmt_insert_nomina->bindParam(':fecha_fin', $fecha_fin);
                $stmt_insert_nomina->bindParam(':empleadoID', $empleadoID);
                $stmt_insert_nomina->execute();
            }
        }

        $_SESSION['bien'] = "Nómina semanal registrada o actualizada correctamente.<br>";
        header("Location: nomina_Semana.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage() . "<br>";
        header("Location: nomina_Semana.php");
        exit();
    }

    $pdo = null;
}
?>
