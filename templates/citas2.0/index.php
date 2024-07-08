<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Orden de Trabajo</title>
</head>
<body>
    <h1>Crear Orden de Trabajo</h1>
    <form action="" method="get">
        <label for="tipoOrden">Seleccione el tipo de orden:</label>
        <select id="tipoOrden" name="tipoOrden" required>
            <option value="conCita">Con Cita</option>
            <option value="sinCita">Sin Cita</option>
        </select><br><br>
        <input type="submit" value="Continuar">
    </form>

    <h2>Registrar Cita</h2>
    <form action="registrar_cita.php" method="post">
        <label for="clienteID">Cliente ID:</label>
        <input type="number" id="clienteID" name="clienteID" required><br><br>

        <label for="vehiculoID">Vehículo ID:</label>
        <input type="number" id="vehiculoID" name="vehiculoID" required><br><br>

        <label for="servicioSolicitado">Servicio Solicitado:</label>
        <textarea id="servicioSolicitado" name="servicioSolicitado" required></textarea><br><br>

        <label for="fechaCita">Fecha de la Cita:</label>
        <input type="date" id="fechaCita" name="fechaCita" required><br><br>

        <input type="submit" value="Registrar Cita">
    </form>

    <?php
    if (isset($_GET['tipoOrden'])) {
        $tipoOrden = $_GET['tipoOrden'];
        if ($tipoOrden == 'conCita') {
            header('Location: seleccionar_cita.php');
        } else {
            header('Location: crear_orden_sin_cita.php');
        }
    }
    ?>
</body>
</html>
