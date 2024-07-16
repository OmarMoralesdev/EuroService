<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehículo</title>
    <style>
        .is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?> <!-- barra lateral -->
        <div class="main p-3">
            <div class="container">
                <h2>REGISTRAR VEHICULO</h2>
                <br>
                <form id="formCita" action="autos.php" method="POST" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                        <ul id="lista" class="list-group" style="display: none;"></ul>
                        <input type="hidden" id="clienteID" name="clienteID">
                        <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" maxlength="40" class="form-control" placeholder="Introduce la marca del vehículo" required>

                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" maxlength="40" class="form-control" placeholder="Introduce el modelo del vehículo" required>

                        <label for="anio">Año:</label>
                        <input type="text" id="anio" name="anio" maxlength="4" class="form-control" placeholder="Introduce el año del vehículo" required>

                        <label for="color">Color:</label>
                        <input type="text" id="color" name="color" maxlength="40" class="form-control" placeholder="Introduce el color del vehículo" required>

                        <label for="kilometraje">Kilometraje:</label>
                        <input type="text" id="kilometraje" name="kilometraje" maxlength="40" class="form-control" placeholder="Introduce el kilometraje del vehículo" required>

                        <label for="placas">Placas:</label>
                        <input type="text" id="placas" name="placas" maxlength="40" class="form-control" placeholder="Introduce las placas del vehículo" required>

                        <label for="vin">Vin:</label>
                        <input type="text" id="vin" name="vin" maxlength="40" class="form-control" placeholder="Introduce el VIN del vehículo" required><br>

                        <input type="submit" class="btn btn-primary" value="Registrar Vehículo">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="app.js"></script>

</body>
</html>
