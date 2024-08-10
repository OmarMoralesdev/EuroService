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
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDITAR DATOS</title>
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .form-control {
            margin-bottom: 10px;
        }
        
        .invalid-feedback {
            display: none;
            color: red;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>

<body>  
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
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
                    <label for="campo" id="x">Selecciona un cliente:</label>
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
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Nuevo número telefónico" maxlength="10" required>
                            <div class="invalid-feedback">Debes ingresar un número válido de hasta 10 dígitos.</div>
                        </div>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Editar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
    
    <script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });

        document.getElementById('formCita').addEventListener('submit', function(event) {
            let valid = true;

            const telefono = document.getElementById('telefono').value;

            if (telefono.length > 10 || isNaN(telefono) || telefono < 0) {
                document.getElementById('telefono').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('telefono').classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    </script>

<script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });

        document.getElementById('x').addEventListener('submit', function(event) {
            let valid = true;

            const campo = document.getElementById('campo').value;

            // Validar nombre
            if (/\d/.test(campo)) {
                document.getElementById('campo').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('campo').classList.remove('is-invalid');
            }

            // Validar salario
            if (isNaN(telefono) || telefono < 0) {
                document.getElementById('telefono').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('telefono').classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
        function validarLetras(event) {
    const input = event.target;
    input.value = input.value.replace(/[^a-zA-ZáéíóúüñÁÉÍÓÚÜÑ ]/g, '');
}

        function validarNumeros(event) {
            const input = event.target;
            input.value = input.value.replace(/[^0-9.]/g, '');
        }

        document.getElementById('telefono').addEventListener('input', validarNumeros);
        document.getElementById('campo').addEventListener('input', validarLetras);
    </script>

</body>
</html>
