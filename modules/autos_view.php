<!DOCTYPE html>
<html>
<head>
    <title>Registro de Vehículos</title>
</head>
<body>
    <h2>Registrar Vehículo</h2>
    <form action="../templates/autos.php" method="post">
        <label for="clienteID">ID del Cliente:</label>
        <input type="number" id="clienteID" name="clienteID" required><br><br>

        <label for="marca">Marca:</label>
        <input type="text" id="marca" name="marca" maxlength="40" required><br><br>

        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo" maxlength="40" required><br><br>

        <label for="modelo">Año:</label>
        <input type="text" id="modelo" name="año" maxlength="40" required><br><br>

        <label for="modelo">Color:</label>
        <input type="text" id="modelo" name="color" maxlength="40" required><br><br>

        <label for="modelo">Placas:</label>
        <input type="text" id="modelo" name="placas" maxlength="40" required><br><br>

        <label for="modelo">Vin:</label>
        <input type="text" id="modelo" name="vin" maxlength="40" required><br><br>

        <label for="modelo">Inspeccion:</label>
        <input type="text" id="modelo" name="inspeccion" maxlength="40" required><br><br>

        <input type="submit" value="Registrar Vehículo">
    </form>
</body>
</html>
