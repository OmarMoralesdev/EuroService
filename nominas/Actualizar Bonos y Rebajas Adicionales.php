<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Actualizar Bonos y Rebajas</title>
</head>
<body>
    <h1>Actualizar Bonos y Rebajas Adicionales por Empleado</h1>
    <form action="actualizar_nomina.php" method="post">
        <label for="empleadoID">ID del Empleado:</label>
        <input type="text" id="empleadoID" name="empleadoID" required>
        <br>
        <label for="fecha_inicio">Fecha de Inicio (debe ser un lunes):</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required>
        <br>
        <label for="bonos">Bonos:</label>
        <input type="number" id="bonos" name="bonos" step="0.01" required>
        <br>
        <label for="rebajas_adicionales">Rebajas Adicionales:</label>
        <input type="number" id="rebajas_adicionales" name="rebajas_adicionales" step="0.01" required>
        <br>
        <input type="submit" value="Actualizar">
    </form>
</body>
</html>
