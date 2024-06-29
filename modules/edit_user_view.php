<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <style>
        ul {
            list-style-type: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Editar datos</h2>
                <br>
                <div class="input-group mb-3">
                    <input type="text" id="search" class="form-control" placeholder="Nombre del cliente">
                    <button class="btn btn-dark" id="searchButton" type="button">SELECCIONAR</button>
                </div>
                <form method="post" action="edit_user.php" id="editForm" style="display:none;">
                    <input type="hidden" id="clienteID" name="clienteID">
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
                    <button type="submit" class="btn btn-dark">Actualizar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchButton').addEventListener('click', function() {
            var searchValue = document.getElementById('search').value;
            if (searchValue) {
                fetch(`search_user.php?nombre=${searchValue}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('clienteID').value = data.cliente.id;
                            document.getElementById('nombre').value = data.cliente.nombre;
                            document.getElementById('apellido_paterno').value = data.cliente.apellido_paterno;
                            document.getElementById('apellido_materno').value = data.cliente.apellido_materno;
                            document.getElementById('correo').value = data.cliente.correo;
                            document.getElementById('telefono').value = data.cliente.telefono;
                            document.getElementById('editForm').style.display = 'block';
                        } else {
                            alert('Cliente no encontrado');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                alert('Por favor ingrese un nombre');
            }
        });
    </script>
</body>
</html>
