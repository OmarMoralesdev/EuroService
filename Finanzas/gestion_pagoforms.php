<?php
// Datos de conexión a la base de datos
require '../includes/db.php'; // Ajusta la ruta según tu estructura de carpetas

$con = new Database();
$pdo = $con->conectar();
$resultado = []; // Inicializar como array vacío

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener la semana desde el formulario
        $semana = $_POST['semana'];

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
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mensaje_error = "Error: " . $e->getMessage();
    }
    // Cerrar la conexión
    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos Semanal</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>REPORTE DE GASTOS SEMANAL</h2>
                <div class="form-container">
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="semana">Selecciona la semana:</label>
                            <input type="week" id="semana" name="semana" class="form-control" required>
                        </div>
<br>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Ver reporte</button>

                    </form>

                    <?php if (!empty($resultado)) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                            <thead>
                                    <tr>
                                        <th>Recibo ID</th>
                                        <th>Fecha Recibo</th>
                                        <th>Cliente</th>
                                        <th>Cantidad Pagada</th>
                                        <th>Estado Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultado as $fila) : ?>
                                        <?php
                                        $estado_pago = $fila['estado_pago'];
                                        $color_celda_estado = "";

                                        if ($estado_pago == 'verde') {
                                            $color_celda_estado = "#d4edda"; // Verde claro
                                        } elseif ($estado_pago == 'naranja') {
                                            $color_celda_estado = "#fff3cd"; // Naranja claro
                                        } elseif ($estado_pago == 'rojo') {
                                            $color_celda_estado = "#f8d7da"; // Rojo claro
                                        }
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($fila['reciboID']); ?></td>
                                            <td><?= htmlspecialchars($fila['fecha_recibo']); ?></td>
                                            <td><?= htmlspecialchars($fila['cliente']); ?></td>
                                            <td><?= htmlspecialchars($fila['cantidad_pagada']); ?></td>
                                            <td style="background-color: <?= htmlspecialchars($color_celda_estado); ?>;">
                                                <?= htmlspecialchars($fila['estado_pago']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif (isset($mensaje_error)) : ?>
                        <div class="alert alert-danger mt-4"><?= htmlspecialchars($mensaje_error); ?></div>
                    <?php else : ?>
                        <p class="mt-4">No se encontraron resultados.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>