<?php

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$showModal = false;
$modalContent = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $activo = 'no';

    // Obtener personaID asociado con clienteID
    $stmt = $pdo->prepare("SELECT personaID FROM CLIENTES WHERE clienteID = ?");
    $stmt->execute([$clienteID]);
    $personaID = $stmt->fetchColumn();

    if (!$personaID) {
        die("Error: Cliente no encontrado.");
    } else {
        // Actualiza la base de datos
        $sql = "UPDATE CLIENTES SET activo = ? WHERE personaID = ?";

        // Usa PDO para preparar la declaración
        $stmt = $pdo->prepare($sql);

        try {
            // Ejecuta la consulta
            $stmt->execute([$activo, $personaID]);
            $showModal = true;
            $modalContent = "
                <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='staticBackdropLabel'>CLIENTE DESHABILITADO</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>";
        } catch (PDOException $e) {
            // Manejo de errores generales
            die("Error en la base de datos: " . $e->getMessage());
        }
    }
}
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
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>ELIMINAR CLIENTE</h2>
                <div class="form-container">
                    <label for="campo">Selecciona un cliente:</label>
                    <form id="formCita" action="deshabilitar_client_view.php" method="POST" autocomplete="off">
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
                            <input type="text" class="form-control" id="correo" name="correo" placeholder="Correo electrónico" readonly>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" readonly>
                        </div>

                        <button type="submit" name="enviar" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    if ($showModal) {
        echo $modalContent;
        echo "<script>
            var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
            myModal.show();
        </script>";
    }
    ?>

    <script>
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
                                    document.getElementById('telefono').value = cliente.telefono;
                                    document.getElementById('correo').value = cliente.correo;
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
        