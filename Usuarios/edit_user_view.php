<?php

require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$showModal = false;
$modalContent = '';

// Función para verificar si el correo o el teléfono ya existen en la base de datos
function checkDuplicate($pdo, $correo, $telefono, $personaID) {
    $sql = "SELECT COUNT(*) FROM PERSONAS WHERE (correo = :correo OR telefono = :telefono) AND personaID <> :personaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['correo' => $correo, 'telefono' => $telefono, 'personaID' => $personaID]);
    return $stmt->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    // Obtener personaID asociado con clienteID
    $stmt = $pdo->prepare("SELECT personaID FROM CLIENTES WHERE clienteID = ?");
    $stmt->execute([$clienteID]);
    $personaID = $stmt->fetchColumn();

    if (!$personaID) {
        die("Error: Cliente no encontrado.");
    }

    if (checkDuplicate($pdo, $correo, $telefono, $personaID)) {
        $showModal = true;
        $modalContent = "
            <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='staticBackdropLabel'>ERROR AL ACTUALIZAR LOS DATOS</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            El correo electrónico o el número telefónico ya están registrados. Por favor, utiliza otros datos.
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>";
    } else {
        // Actualiza la base de datos
        $sql = "UPDATE PERSONAS SET correo = ?, telefono = ? WHERE personaID = ?";

        // Usa PDO para preparar la declaración
        $stmt = $pdo->prepare($sql);

        try {
            // Ejecuta la consulta
            $stmt->execute([$correo, $telefono, $personaID]);
            $showModal = true;
            $modalContent = "
                <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='staticBackdropLabel'>Datos actualizados con éxito!</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                Nuevo correo electrónico: <strong>$correo</strong><br><br>
                                <hr>
                                Nuevo número telefónico: <strong>$telefono</strong><br>
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
        if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
