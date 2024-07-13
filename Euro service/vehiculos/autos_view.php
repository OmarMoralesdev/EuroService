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
                <h2>REGISTRAR VEHICULO</h2>
                <br>            
                <form action="../Buscador/getClientes.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                        <ul id="lista" class="list-group" style="display: none;"></ul>
                        <input type="hidden" id="nombre" name="nombre">
                        <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                    </div>

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
    <script src="../Buscador/app.js"></script>
    <script>
        document.getElementById('formCita').addEventListener('submit', function(event) {
            if (!document.getElementById('clienteID').value) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('campo').classList.add('is-invalid');
            }
            this.classList.add('was-validated');
        });
    </script>   
</body>
</html>