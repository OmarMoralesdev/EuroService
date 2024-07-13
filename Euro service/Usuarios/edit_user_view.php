<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR DATOS</title>
    <style>
        .form-group {
            margin-bottom: 5px;
        }
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 50px auto;
        }
        h2 {
            text-align: center;
            color: #000;
        }
        #lista {
            list-style: none;
            padding: 0;
            margin: 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
        }
        #lista li {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
        }
        #lista li:hover {
            background-color: #f0f0f0;
        }
        #lista li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
        <div class="container" style="width: 80%; margin: auto; background-color: #EBEBEB; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
            <h2>Editar datos</h2>
            <br>            
            <label for="correo_actual">Selecciona un cliente:</label>
                <form action="../Buscador/getClientes.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                        <ul id="lista" class="list-group" style="display: none;"></ul>
                        <input type="hidden" id="clienteID" name="clienteID">
                        <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Apellido paterno" readonly>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"placeholder="Apellido paterno" readonly>
                    </div>
                    <div class="form-group">
                        <br>
                        <label for="correo_actual">Teléfono Actual:</label><br>
                        <label for="correo_actual">Correo Electrónico Actual:</label><br><br>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="correo electrónico"required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="telefono" name="telefono" required placeholder="Número telefónico">
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100">Editar</button>
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

