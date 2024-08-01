<?php
session_start(); // Inicia la sesión

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener datos del formulario
        $empleadoID = $_POST['empleadoID'];
        $nominaID = $_POST['nominaID'];
        $rebajas = $_POST['rebajas'];

        // Validar que la nómina existe
        $consulta_nomina_existente = "
            SELECT COUNT(*) AS conteo
            FROM NOMINAS
            WHERE nominaID = :nominaID
        ";
        $stmt = $pdo->prepare($consulta_nomina_existente);
        $stmt->execute(['nominaID' => $nominaID]);
        $conteo_nomina_existente = $stmt->fetchColumn();

        if ($conteo_nomina_existente == 0) {
            throw new Exception("La nómina seleccionada no existe.");
        }

        // Verificar que el empleado está en la nómina seleccionada
        $consulta_empleado_en_nomina = "
            SELECT COUNT(*) AS conteo
            FROM NOMINAS
            WHERE nominaID = :nominaID AND empleadoID = :empleadoID
        ";
        $stmt = $pdo->prepare($consulta_empleado_en_nomina);
        $stmt->execute(['nominaID' => $nominaID, 'empleadoID' => $empleadoID]);
        $conteo_empleado_en_nomina = $stmt->fetchColumn();

        if ($conteo_empleado_en_nomina == 0) {
            throw new Exception("El empleado seleccionado no está en la nómina seleccionada.");
        }

        // Obtener el total original antes de aplicar rebajas
        $consulta_total_original = "
            SELECT total, rebajas
            FROM NOMINAS
            WHERE nominaID = :nominaID AND empleadoID = :empleadoID
        ";
        $stmt = $pdo->prepare($consulta_total_original);
        $stmt->execute(['nominaID' => $nominaID, 'empleadoID' => $empleadoID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("No se encontraron datos para el empleado en la nómina seleccionada.");
        }

        $total_original = $result['total'];
        $rebajas_original = $result['rebajas'];
        $total_nuevo = $total_original - ($rebajas - $rebajas_original);

        // Actualizar la nómina con las nuevas rebajas
        $consulta_actualizacion = "
            UPDATE NOMINAS
            SET rebajas = :rebajas, total = :total
            WHERE nominaID = :nominaID AND empleadoID = :empleadoID
        ";
        $stmt = $pdo->prepare($consulta_actualizacion);
        $stmt->execute([
            'nominaID' => $nominaID,
            'empleadoID' => $empleadoID,
            'rebajas' => $rebajas,
            'total' => $total_nuevo
        ]);

        $_SESSION['mensaje'] = "Rebajas actualizadas exitosamente para el empleado seleccionado.";
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
} catch (Exception $e) {
    $_SESSION['mensaje'] = $e->getMessage();
}

// Redirigir a la página de nóminas
header('Location: nominas.php');
exit;
?>
