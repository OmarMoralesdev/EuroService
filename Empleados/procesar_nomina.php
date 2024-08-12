<?php
session_start(); // Inicia la sesión

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener la semana seleccionada
        $semana_seleccionada = $_POST['week'];
        $inicio_semana = date('Y-m-d', strtotime($semana_seleccionada . 'monday this week'));
        $fin_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));

        // Validar que la semana seleccionada tenga un formato correcto
        if (!$inicio_semana || !$fin_semana) {
            throw new Exception("Formato de fecha no válido.");
        }

        // Verificar que la semana esté dentro del rango permitido
        $consulta_semanas_validas = "
            SELECT COUNT(*) AS conteo
            FROM ASISTENCIA
            WHERE fecha BETWEEN :inicio_semana AND :fin_semana
        ";
        $semanas_validas = $pdo->prepare($consulta_semanas_validas);
        $semanas_validas->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
        $conteo_semanas_validas = $semanas_validas->fetchColumn();

        if ($conteo_semanas_validas == 0) {
            throw new Exception("No hay registros de asistencia para la semana seleccionada.");
        }

        // Verificar si ya existe una nómina para la semana seleccionada
        $consulta_nomina_existente = "
            SELECT COUNT(*) AS conteo
            FROM NOMINAS
            WHERE fecha_inicio = :inicio_semana AND fecha_fin = :fin_semana
        ";
        $nomina_existente = $pdo->prepare($consulta_nomina_existente);
        $nomina_existente->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
        $conteo_nomina_existente = $nomina_existente->fetchColumn();

        if ($conteo_nomina_existente > 0) {
            throw new Exception("La nómina para esta semana ya ha sido registrada.");
        }

        // Iniciar una transacción
        $pdo->beginTransaction();

        // Consultar la asistencia y calcular la nómina
        $consulta_calculo_nomina = "
            SELECT E.empleadoID, E.alias, E.salario_diario, 
                   COUNT(CASE WHEN A.asistencia = 'falta' THEN 1 ELSE NULL END) AS faltas,
                   COUNT(*) AS total_dias
            FROM ASISTENCIA A
            JOIN EMPLEADOS E ON A.empleadoID = E.empleadoID
            WHERE A.fecha BETWEEN :inicio_semana AND :fin_semana
            GROUP BY E.empleadoID
        ";
        $calculo_nomina = $pdo->prepare($consulta_calculo_nomina);
        $calculo_nomina->execute(['inicio_semana' => $inicio_semana, 'fin_semana' => $fin_semana]);
        $datos_nomina = $calculo_nomina->fetchAll(PDO::FETCH_ASSOC);

        if (empty($datos_nomina)) {
            throw new Exception("No se encontraron datos de asistencia para la semana seleccionada.");
        }

        // Insertar datos en la base de datos
        $consulta_insercion_nomina = "
            INSERT INTO NOMINAS (empleadoID, faltas, rebajas, total, fecha_de_pago, fecha_inicio, fecha_fin)
            VALUES (:empleadoID, :faltas, :rebajas, :total, :fecha_de_pago, :fecha_inicio, :fecha_fin)
        ";
        $insercion_nomina = $pdo->prepare($consulta_insercion_nomina);

        foreach ($datos_nomina as $dato) {
            $sueldo_semanal = $dato['salario_diario'] * 5; // Asegúrate de que esto es correcto
            $total_faltas = $dato['faltas'] * $dato['salario_diario'];
            $total = $sueldo_semanal - $total_faltas;

            $insercion_nomina->execute([
                'empleadoID' => $dato['empleadoID'],
                'faltas' => $dato['faltas'],
                'rebajas' => $total_faltas,
                'total' => $total,
                'fecha_de_pago' => date('Y-m-d'),
                'fecha_inicio' => $inicio_semana,
                'fecha_fin' => $fin_semana
            ]);
        }

        // Confirmar la transacción
        $pdo->commit();
        $_SESSION['mensaje'] = "Nómina registrada exitosamente para la semana seleccionada!";
    }
} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['mensaje'] = $e->getMessage();
}

// Redirigir a la página de nóminas
header('Location: nominas.php');
exit;
?>
