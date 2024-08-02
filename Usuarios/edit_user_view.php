<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR DATOS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .form-control {
            margin-bottom: 10px;
        }
        
    </style>
</head>

<body>  
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-2">
            <div class="container">
                <h2>EDITAR DATOS</h2>
                <!-- ALERTA DE ERRORES -->
                <?php
        if (isset($_SESSION['alert'])) {
            echo $_SESSION['alert']['message'];
            unset($_SESSION['alert']);
        }
        ?>
                <div class="form-container">
                <label for="campo">Selecciona un cliente:</label>
                    <form id="formCita" action="edit_user.php" method="POST" autocomplete="off">
                        <div class="mb-3" style="margin-bottom: 1px;">
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente...">
                            <ul id="lista" class="list-group lista"></ul>
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
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Editar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    // Muestra el modal 
     <!-- MUESTRA EL MODAL DE EXITO -->
     <?php
    if (isset($_SESSION['modal'])) {
        $modalContent = $_SESSION['modal'];
        echo $modalContent['message'];
        unset($_SESSION['modal']);
    }
    ?>
    
    <!-- mostrar el modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalElement = document.getElementById('staticBackdrop');
            if (modalElement) {
                var myModal = new bootstrap.Modal(modalElement, {
                    keyboard: false
                });
                myModal.show();
            }
        });

        //unicamente un modal a la vez
        if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

        document.getElementById('formCita').addEventListener('submit', function(event) {
            if (!document.getElementById('clienteID').value) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('campo').classList.add('is-invalid');
            }
            this.classList.add('was-validated');
        });

        document.getElementById('campo').addEventListener('input', function() {
            const searchTerm = this.value;
            const lista = document.getElementById('lista');
            lista.innerHTML = '';
            if (searchTerm.length > 0) {
                fetch(`../Buscador/getClientes.php?search=${searchTerm}`)
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
                                    document.getElementById('telefono_actual').innerText = cliente.telefono;
                                    document.getElementById('correo_actual').innerText = cliente.correo;
                                    lista.style.display = 'none';
                                };
                                lista.appendChild(li);
                            });
                            lista.style.display = 'block';
                        } else {
                            lista.style.display = 'none';
                        }
                    });
            } else {
                lista.style.display = 'none';
            }
        });
    </script>
</body>
</html>
