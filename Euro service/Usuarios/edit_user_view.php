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
            <div class="container">
                <h2>Editar datos</h2>
                <label for="campo">Selecciona un cliente:</label>
                <form id="formCita" action="editar_user.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
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
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Apellido materno" readonly>
                    </div>
                    <div class="form-group">
                        <label for="telefono_actual">Teléfono Actual: </label><label id="telefono_actual">x</label><br>
                        <label for="correo_actual">Correo Electrónico Actual: </label><label id="correo_actual">x</label><br>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Nuevo correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Nuevo número telefónico" required>
                    </div>
                    <button type="submit" name="enviar" class="btn btn-dark w-100">Editar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formCita').addEventListener('submit', function(event) {
            if (!document.getElementById('clienteID').value) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('campo').classList.add('is-invalid');
            }
            this.classList.add('was-validated');
        });

        // Buscar y poblar datos del cliente
        document.getElementById('campo').addEventListener('input', function() {
            const searchTerm = this.value;
            const lista = document.getElementById('lista');
            lista.innerHTML = ''; // Limpiar la lista anterior
            if (searchTerm.length > 0) {
                fetch(`getClientes.php?search=${searchTerm}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            data.forEach(cliente => {
                                const li = document.createElement('li');
                                li.innerText = `${cliente.nombre} ${cliente.apellido_paterno} ${cliente.apellido_materno}`;
                                li.onclick = function() {
                                    document.getElementById('clienteID').value = cliente.clienteID;
                                    document.getElementById('nombre').value = cliente.nombre;
                                    document.getElementById('apellido_paterno').value = cliente.apellido_paterno;
                                    document.getElementById('apellido_materno').value = cliente.apellido_materno;
                                    document.getElementById('telefono_actual').innerText = cliente.telefono || 'No disponible'; // Actualiza el teléfono
                                    document.getElementById('correo_actual').innerText = cliente.correo || 'No disponible'; // Actualiza el correo
                                    lista.style.display = 'none'; // Ocultar la lista después de la selección
                                };
                                lista.appendChild(li);
                            });
                            lista.style.display = 'block'; // Mostrar la lista
                        } else {
                            lista.style.display = 'none'; // Ocultar si no hay resultados
                        }
                    })
                    .catch(error => console.error('Error fetching clients:', error));
            } else {
                lista.style.display = 'none'; // Ocultar lista si la entrada está vacía
            }
        });
    </script>
</body>

</html>