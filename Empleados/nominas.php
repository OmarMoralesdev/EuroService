<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

try {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener la semana seleccionada
        $selected_week = $_POST['week'];
        $week_start = date('Y-m-d', strtotime($selected_week));
        $week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));

        // Verificar que la semana esté dentro del rango permitido
        $valid_weeks_query = "
            SELECT COUNT(*) AS count
            FROM ASISTENCIA
            WHERE fecha BETWEEN :week_start AND :week_end
        ";
        $stmt_valid_weeks = $pdo->prepare($valid_weeks_query);
        $stmt_valid_weeks->execute(['week_start' => $week_start, 'week_end' => $week_end]);
        $valid_weeks = $stmt_valid_weeks->fetchColumn();

        if ($valid_weeks == 0) {
            $message = "No hay registros de asistencia para la semana seleccionada.";
        } else {
            // Verificar si ya existe una nómina para la semana seleccionada
            $check_nomina_query = "
                SELECT COUNT(*) AS count
                FROM NOMINAS
                WHERE fecha_de_pago BETWEEN :week_start AND :week_end
            ";
            $stmt_check = $pdo->prepare($check_nomina_query);
            $stmt_check->execute(['week_start' => $week_start, 'week_end' => $week_end]);
            $exists = $stmt_check->fetchColumn();

            if ($exists > 0) {
                $message = "La nómina para esta semana ya ha sido registrada.";
            } else {
                // Consultar la asistencia y calcular la nómina
                $calculate_nomina_query = "
                    SELECT E.empleadoID, E.alias, E.salario_diario, 
                           COUNT(CASE WHEN A.asistencia = 'falta' THEN 1 ELSE NULL END) AS faltas,
                           COUNT(*) AS total_dias
                    FROM ASISTENCIA A
                    JOIN EMPLEADOS E ON A.empleadoID = E.empleadoID
                    WHERE A.fecha BETWEEN :week_start AND :week_end
                    GROUP BY E.empleadoID
                ";
                $stmt_calculate = $pdo->prepare($calculate_nomina_query);
                $stmt_calculate->execute(['week_start' => $week_start, 'week_end' => $week_end]);
                $nomina_data = $stmt_calculate->fetchAll(PDO::FETCH_ASSOC);

                if (empty($nomina_data)) {
                    $message = "No se encontraron datos de asistencia para la semana seleccionada.";
                } else {
                    // Insertar datos en la base de datos
                    foreach ($nomina_data as $data) {
                        $sueldo_mensual = $data['salario_diario'] * 30; // Suponiendo 30 días en el mes
                        $total_faltas = $data['faltas'] * $data['salario_diario'];
                        $total = $sueldo_mensual - $total_faltas;

                        $insert_query = "
                            INSERT INTO NOMINAS (empleadoID, faltas, rebajas, total, fecha_de_pago)
                            VALUES (:empleadoID, :faltas, :rebajas, :total, :fecha_de_pago)
                        ";
                        $stmt_insert = $pdo->prepare($insert_query);
                        $stmt_insert->execute([
                            'empleadoID' => $data['empleadoID'],
                            'faltas' => $data['faltas'],
                            'rebajas' => $total_faltas,
                            'total' => $total,
                            'fecha_de_pago' => date('Y-m-d')
                        ]);
                    }

                    $message = "Nómina registrada exitosamente para la semana seleccionada!";
                }
            }
        }

    }

} catch (PDOException $e) {
    $message = 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nómina Automáticamente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 20px;
        }
        .container {
            max-width: 900px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Registrar Nómina Automáticamente</h1>
        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo strpos($message, 'ya ha sido registrada') !== false ? 'warning' : (strpos($message, 'No hay registros') !== false ? 'danger' : 'success'); ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group col-md-6 offset-md-3">
                    <label for="week">Selecciona la semana:</label>
                    <input type="week" id="week" name="week" class="form-control" value="<?php echo date('Y-\WW'); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Registrar Nómina</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$pdo = null; // Cerrar la conexión
?>
