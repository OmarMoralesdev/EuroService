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
    $stmt = $pdo->prepare("SELECT personaID FROM CLIENTES WHERE clienteID = ? and activo = 'si';");
    $stmt->execute([$clienteID]);
    $personaID = $stmt->fetchColumn();

    if (!$personaID) {
        die("Error: Cliente no encontrado.");
    } else {
        // Actualiza la base de datos
        $sql = "UPDATE CLIENTES SET activo = ? WHERE personaID = ?";
        $stmt = $pdo->prepare($sql);

        try {
            // Ejecuta la consulta
            $stmt->execute([$activo, $personaID]);
            $showModal = true;
            $modalContent = "
                <div class='modal fade' id='successModal' tabindex='-1' aria-labelledby='successModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='successModalLabel'>Cliente Deshabilitado Exitosamente</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <p>El cliente ha sido deshabilitado exitosamente.</p>
                            </div>
                        </div>
                    </div>
                </div>";
        } catch (PDOException $e) {
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
    <title>Eliminar Cliente</title>
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
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-2">
            <div class="container">
                <h2>Eliminar Cliente</h2>
                <div class="form-container">
                    <label for="campo">Selecciona un cliente:</label>
                    <form id="formCita" action="deshabilitar_client_view.php" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente...">
                            <ul id="lista" class="list-group lista" style="display: none;"></ul>
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

                        <!-- Botón para mostrar el modal de confirmación -->
                        <button type="button" class="btn btn-danger d-grid gap-2 col-6 mx-auto" id="btnEliminar">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmacionModal" tabindex="-1" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmacionModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar al cliente <br> <span id="nombreCliente" style="font-weight: bold;"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de resultado -->
    <?php
    if ($showModal) {
        echo $modalContent;
        echo "<script>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            setTimeout(function() {
                successModal.hide();
            }, 2000);
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
                                    document.getElementById('telefono').value = cliente.telefono;
                                    document.getElementById('correo').value = cliente.correo;

                                    // Actualiza el nombre del cliente en el modal
                                    document.getElementById('nombreCliente').innerText = `${cliente.nombre} ${cliente.apellido_paterno} ${cliente.apellido_materno}`;
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

        document.getElementById('btnEliminar').addEventListener('click', function() {
            const confirmacionModal = new bootstrap.Modal(document.getElementById('confirmacionModal'));
            confirmacionModal.show();
        });

        document.getElementById('confirmarEliminar').addEventListener('click', function() {
            document.getElementById('formCita').submit();
        });
    </script>
</body>
</html>
