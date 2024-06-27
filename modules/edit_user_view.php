<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO CLIENTE</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head> 

<style>
    ul{
        list-style-type: none;
    }
    </style>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Editar datos</h2>
                <br>            
                <form action="post"  autocomplete="off">

                    <div class="input-group mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Nombre del cliente">
                        <ul id="list"><ul>
                        <button class="btn btn-dark" type="submit">SELECIONAR</button>
                    </div>
                </form>

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
                    <button type="submit" class="btn btn-dark">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
