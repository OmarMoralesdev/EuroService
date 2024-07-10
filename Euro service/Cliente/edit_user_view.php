<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR DATOS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head> 
<style>
        .form-group {
            margin-bottom: 5px;
        }
    </style>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
        <div class="container" style="width: 90%; margin: auto; background-color: #EBEBEB; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                <h2>Editar datos</h2>
                <br>            
                <form action="edit_user_view.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <input type="text" class="form-contrc:\xampp\htdocs\EuroService\templates\edit_user.phpol" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                        <ul id="lista" class="list-group" style="display: none;"></ul>
                        <input type="hidden" id="clienteID" name="clienteID">
                        <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" readonly>
                    </div>
                    <div class="form-group">
                        <label for="apellido_paterno">Apellido Paterno:</label>
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" readonly>
                    </div>
                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno:</label>
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" readonly>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-dark w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="/modules/CItas/app.js"></script>
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

