<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nómina Semanal</title>
</head>
<body>
    <h1>Registrar o Actualizar Nómina Semanal</h1>
    <form action="procesar_nomina.php" method="post">
        <label for="fecha">Fecha de Inicio (debe ser un lunes):</label>
        <input type="date" id="fecha" name="fecha" required>
        <br>
        <input type="submit" value="Procesar Nómina">
    </form>
</body>
</html>
