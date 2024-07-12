<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Vehículos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    
<div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container" style="width: 90%; margin: auto; background-color: #EBEBEB; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                <h2>Registrar Vehículo</h2>

                <form action="autos.php" method="post">
                    <label for="clienteID">ID del Cliente:</label>
                    <input type="number" id="clienteID" name="clienteID" required><br><br>

                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" maxlength="40" required><br><br>

                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" maxlength="40" required><br><br>

                    <label for="año">Año:</label>
                    <input type="text" id="año" name="año" maxlength="40" required><br><br>

                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" maxlength="40" required><br><br>

                    <label for="placas">Placas:</label>
                    <input type="text" id="placas" name="placas" maxlength="40" required><br><br>

                    <label for="vin">Vin:</label>
                    <input type="text" id="vin" name="vin" maxlength="40" required><br><br>

                    <input type="submit" value="Registrar Vehículo">
                </form>
            </div>
        </div>
    </div>
</body>
</html>