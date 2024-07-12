<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Cita para Orden de Trabajo</title>
</head>
<body>
    <h1>Seleccionar Cita para Crear Orden de Trabajo</h1>
    <form action="crear_orden_desde_cita.php" method="post">
        <label for="citaID">Seleccionar Cita:</label>
        <select id="citaID" name="citaID" required>
            <?php
            require 'conexion.php';
            $citas = listarCitasPendientes($pdo);
            foreach ($citas as $cita) {
                echo "<option value=\"{$cita['citaID']}\">Cita ID: {$cita['citaID']} - Vehículo ID: {$cita['vehiculoID']} - Servicio: {$cita['servicio_solicitado']}</option>";
            }
            ?>
        </select><br><br>
        <input type="submit" value="Crear Orden de Trabajo">
    </form>
</body>
</html>
