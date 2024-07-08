<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Orden de Trabajo sin Cita</title>
</head>
<body>
    <h1>Crear Nueva Orden de Trabajo sin Cita</h1>
    <form action="crear_orden_sin_cita.php" method="post">
        <label for="vehiculoID">Vehículo ID:</label>
        <input type="number" id="vehiculoID" name="vehiculoID" required><br><br>

        <label for="fechaOrden">Fecha de Orden:</label>
        <input type="datetime-local" id="fechaOrden" name="fechaOrden" required><br><br>

        <label for="detallesTrabajo">Detalles del Trabajo:</label>
        <textarea id="detallesTrabajo" name="detallesTrabajo" required></textarea><br><br>

        <label for="costoManoObra">Costo de Mano de Obra:</label>
        <input type="number" step="0.01" id="costoManoObra" name="costoManoObra" required><br><br>

        <label for="costoRefacciones">Costo de Refacciones:</label>
        <input type="number" step="0.01" id="costoRefacciones" name="costoRefacciones" required><br><br>

        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" required><br><br>

        <label for="empleado">Empleado ID:</label>
        <input type="number" id="empleado" name="empleado" required><br><br>

        <label for="ubicacionID">Ubicación ID:</label>
        <input type="number" id="ubicacionID" name="ubicacionID" required><br><br>

        <label for="atencion">Atención:</label>
        <select id="atencion" name="atencion" required>
            <option value="no urgente">No Urgente</option>
            <option value="urgente">Urgente</option>
            <option value="muy urgente">Muy Urgente</option>
        </select><br><br>

        <input type="submit" value="Crear Orden de Trabajo">
    </form>
</body>
</html>
