<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO CLIENTE</title>
</head> 
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Editar datos</h2>
                <br>            
                <form action="post"  autocomplete="off">
                <div class="mb-3">
                    
                <input type="text" class="form-control" autocomplete="off" id="campo" name="campo" placeholder="Buscar cliente..." required>
                <ul id="lista" class="list-group" style="display: none;"></ul>
                <input type="hidden" id="clienteID" name="clienteID">
                <div class="invalid-feedback">Debes seleccionar un cliente.</div>
            </div>

                <form method="post" action="generate_user.php">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <br>
                        <label for="staticEmail" class="col-sm-2 col-form-label">NAME</label>
                    </div>
                    <div class="form-group">
                        <label for="apellido_paterno">Apellido Paterno:</label>
                        <br>
                        <label for="staticEmail" class="col-sm-2 col-form-label">lastname</label>
                    </div>
                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno:</label>
                        <br>
                        <label for="staticEmail" class="col-sm-2 col-form-label">lastname</label>
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
</body>
</html>