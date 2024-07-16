<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO CLIENTE</title>
    <style>
        .form-group {
            margin-bottom: 5px; /* margen inferior a cada grupo de formulario */
        }
        /* Contenedor principal del formulario */
        /* Estilo para el título */
        h2 {
            text-transform: uppercase; /* título en mayúsculas */
            text-align: center; /* centrado */
        }
        /* Estilo para los campos de texto */
        input[type=text], input[type=email] {
            color: black; /* texto negro */
        }
        /* Estilo para el botón */
        .btn {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* sombra leve para el botón */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?> <!-- barra lateral -->
        <div class="main p-3">
            <div class="container">
                <div class="form-container">
                    <h2>REGISTRAR CLIENTE</h2>
                    <br>
                    <form method="post" action="../Usuarios/generate_user.php">
                        <!-- Formulario para registrar un cliente, los datos se envían al archivo generate_user.php -->
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido_paterno">Apellido Paterno:</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido_materno">Apellido Materno:</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
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
                        <!-- enviar formulario -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
