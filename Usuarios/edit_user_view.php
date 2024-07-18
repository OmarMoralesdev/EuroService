<?php

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
$showModal = false;
$modalContent = '';
    // Actualiza la base de datos
    $sql = "UPDATE CLIENTES SET correo = ?, telefono = ? WHERE clienteID = ?";
    
    // Usa PDO para preparar la declaración
    $stmt = $pdo->prepare($sql);
    // Ejecuta la consulta
    if ($stmt->execute([$correo, $telefono, $clienteID])) {
        $showModal = true;
        $modalContent = "
            <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='staticBackdropLabel'>Datos actualizados con exito!</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            nuevo correo eléctronico: <strong>$correo</strong><br><br>
                            <hr>
                            nuevo número telefonico: <strong>$telefono</strong><br>
                            
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>";
    } else {
        echo "Error al actualizar los datos.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR DATOS</title>
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
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

        .form-control {
            margin-bottom: 10px; /* Agregar margen inferior a los inputs */
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
        <div class="container">
            <h2>EDITAR DATOS</h2>
                <div class="form-container">
                <label for="campo">Selecciona un cliente:</label>
                <form id="formCita" action="edit_user_view.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente...">
                        <ul id="lista" class="list-group" style="display: none;"></ul>
                        <input type="hidden" id="clienteID" name="clienteID">
                        <div class="invalid-feedback">Debes seleccionar un cliente.</div><br>
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
                        <label for="correo_actual">Correo Electrónico Actual: <span id="correo_actual">No disponible</span></label><br>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Nuevo correo electrónico" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono_actual">Teléfono Actual: <span id="telefono_actual">No disponible</span></label><br>
                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Nuevo número telefónico" required>
                    </div>

                    <button type="submit" name="enviar" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Editar</button>
                </form>
            </div>
            </div>
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
                                    document.getElementById('campo').value = ''; // Vaciar el campo de búsqueda
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
