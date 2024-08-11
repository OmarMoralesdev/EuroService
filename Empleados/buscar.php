<?php
include '../includes/db.php';

$conexion = new Database();
$conexion->conectar();

$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

$consulta_empleados = "SELECT e.empleadoID, CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_completo, e.alias, e.tipo, n.total, (e.salario_diario*5) as total, n.bonos, n.rebajas
                    FROM EMPLEADOS e
                    INNER JOIN PERSONAS p ON e.personaID = p.personaID
                    LEFT JOIN NOMINAS n ON e.empleadoID = n.empleadoID";
                    
if (!empty($buscar)) {
    $consulta_empleados .= " WHERE p.nombre LIKE '%$buscar%' OR p.apellido_paterno LIKE '%$buscar%' OR p.apellido_materno LIKE '%$buscar%' OR e.alias LIKE '%$buscar%'";
}
$consulta_empleados .= " ORDER BY nombre_completo";

$empleados = $conexion->seleccionar($consulta_empleados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Empleados</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        .card-body {
            padding: 1rem;
        }
        .card-title {
            margin-bottom: 0.5rem;
        }
        .card-text {
            margin-bottom: 0.25rem;
        }
        .btn-container {
            display: flex;
            width: 100%;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .btn-container .btn {
            flex: 1;
        }
        .x .btn {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2 class="text-center">EMPLEADOS</h2>
                <div class="form-container">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar empleados..." name="buscar" value="<?php echo htmlspecialchars($buscar); ?>">
                            <button type="submit" class="btn btn-dark">Buscar</button>
                        </div>
                    </form>

                    <div class="row mt-4">
                        <?php
                        if (!empty($empleados)) {
                            foreach ($empleados as $empleado) {
                    
                                echo "<div class='col-md-4 mb-3'>";
                                echo "<div class='card' style='width: 100%;'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>{$empleado->nombre_completo}</h5>";
                                echo "<hr>";
                                echo "<p class='card-text'><strong>Alias:</strong> {$empleado->alias}</p>";
                                echo "<p class='card-text'><strong>Tipo:</strong> {$empleado->tipo}</p>";
                                echo "<HR>";
                                echo "<p class='card-text'><strong>Salario semanal :</strong> {$empleado->total}</p> <br>";
                                echo "<div class='btn-container mb-1'>"; // Añadido margen inferior
                                echo "<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#modalRebajas{$empleado->empleadoID}' data-empleado-id='{$empleado->empleadoID}'>BAJAR</button>";
                                echo "<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#modalAumento{$empleado->empleadoID}' data-empleado-id='{$empleado->empleadoID}'>AUMENTAR</button>";
                                echo "</div>"; 
                                echo "<HR>";
                                echo "<p class='card-text'><strong>Bonos:</strong> {$empleado->bonos}</p>";
                                echo "<p class='card-text'><strong>Rebajas:</strong> {$empleado->rebajas}</p>";
                                echo "<div class='btn-container mb-1'>"; // Añadido margen inferior
                                echo "<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#modalRebaja{$empleado->empleadoID}' data-empleado-id='{$empleado->empleadoID}'>REBAJAS</button>";
                                echo "<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#modalBonos{$empleado->empleadoID}' data-empleado-id='{$empleado->empleadoID}'>BONOS</button>";
                                echo "</div>"; 
                                echo "<HR>";
                                echo "<div class='mt-1 x'>";
                                echo "<button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#modalDeshabilitar{$empleado->empleadoID}' data-empleado-id='{$empleado->empleadoID}'>ELIMINAR</button>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";



                                // Modal para Rebajas
                                echo "<div class='modal fade' id='modalRebajas{$empleado->empleadoID}' tabindex='-1' aria-labelledby='modalRebajasLabel' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalRebajasLabel'>Agregar rebaja al sueldo del empleado</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<form id='rebajaForm{$empleado->empleadoID}' method='POST' action='AddRebaja.php'>";
                                echo "<div class='modal-body'>";
                                echo "<input type='hidden' name='empleadoID' value='{$empleado->empleadoID}'>";
                                echo "<div class='mb-3'>";
                                echo "<label for='rebaja' class='form-label'>Monto de Rebaja</label>";
                                echo "<input type='number' class='form-control' id='rebaja' name='rebaja' required>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                                echo "<button type='submit' class='btn btn-dark'>Aplicar Rebaja</button>";
                                echo "</div>";
                                echo "</form>";
                                echo "<div id='rebajaMessage{$empleado->empleadoID}' class='alert' style='display:none;'></div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                                // Modal para Aumento
                                echo "<div class='modal fade' id='modalAumento{$empleado->empleadoID}' tabindex='-1' aria-labelledby='modalAumentoLabel' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalAumentoLabel'>Agregar aumento al sueldo del empleado</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<form id='AumentoForm{$empleado->empleadoID}' method='POST' action='AddAumento.php'>";
                                echo "<div class='modal-body'>";
                                echo "<input type='hidden' name='empleadoID' value='{$empleado->empleadoID}'>";
                                echo "<div class='mb-3'>";
                                echo "<label for='aumento' class='form-label'>Monto del Aumento</label>";
                                echo "<input type='number' class='form-control' id='aumento' name='aumento' required>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                                echo "<button type='submit' class='btn btn-dark'>Aplicar Aumento</button>";
                                echo "</div>";
                                echo "</form>";
                                echo "<div id='AumentoMessage{$empleado->empleadoID}' class='alert' style='display:none;'></div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                                // Modal para Bonos
                                echo "<div class='modal fade' id='modalBonos{$empleado->empleadoID}' tabindex='-1' aria-labelledby='modalBonoLabel' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalBonosLabel'>Agregar Bono al empleado</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<form id='BonoForm{$empleado->empleadoID}' method='POST' action='AddBono.php'>";
                                echo "<div class='modal-body'>";
                                echo "<input type='hidden' name='empleadoID' value='{$empleado->empleadoID}'>";
                                echo "<div class='mb-3'>";
                                echo "<label for='bono' class='form-label'>Monto del Bonos</label>";
                                echo "<input type='number' class='form-control' id='bono' name='bono' required>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                                echo "<button type='submit' class='btn btn-dark'>Aplicar Bono</button>";
                                echo "</div>";
                                echo "</form>";
                                echo "<div id='BonoMessage{$empleado->empleadoID}' class='alert' style='display:none;'></div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                                // Modal para Rebajas
                                echo "<div class='modal fade' id='modalRebaja{$empleado->empleadoID}' tabindex='-1' aria-labelledby='modalRebajaLabel' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalRebajaLabel'>Agregar rebaja al empleado</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<form id='RebajaForm{$empleado->empleadoID}' method='POST' action='AddRebajilla.php'>";
                                echo "<div class='modal-body'>";
                                echo "<input type='hidden' name='empleadoID' value='{$empleado->empleadoID}'>";
                                echo "<div class='mb-3'>";
                                echo "<label for='rebaja' class='form-label'>Monto de la Rebaja</label>";
                                echo "<input type='number' class='form-control' id='rebaja' name='rebaja' required>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                                echo "<button type='submit' class='btn btn-dark'>Aplicar Rebaja</button>";
                                echo "</div>";
                                echo "</form>";
                                echo "<div id='RebajaMessage{$empleado->empleadoID}' class='alert' style='display:none;'></div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                                // Modal para Deshabilitar
                                echo "<div class='modal fade' id='modalDeshabilitar{$empleado->empleadoID}' tabindex='-1' aria-labelledby='modalDeshabilitarLabel' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalDeshabilitarLabel'>Deshabilitar Empleado</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<form id='DeshabilitarForm{$empleado->empleadoID}' method='POST' action='DeshabilitarEmpleado.php'>";
                                echo "<div class='modal-body'>";
                                echo "<input type='hidden' name='empleadoID' value='{$empleado->empleadoID}'>";
                                echo "<p>¿Estás seguro de que deseas deshabilitar a este empleado?</p>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>";
                                echo "<button type='submit' class='btn btn-dark'>ELIMINAR</button>";
                                echo "</div>";
                                echo "</form>";
                                echo "<div id='DeshabilitarMessage{$empleado->empleadoID}' class='alert' style='display:none;'></div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p>No se encontraron empleados.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form[id^="DeshabilitarForm"]').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formId = form.attr('id');
                var messageElement = $('#' + formId.replace('Form', 'Message'));
                var url = form.attr('action');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            messageElement.removeClass('alert-danger').addClass('alert-success');
                        } else {
                            messageElement.removeClass('alert-success').addClass('alert-danger');
                        }
                        messageElement.text(response.message).show();
                        setTimeout(function() {
                            messageElement.hide();
                            var modal = form.closest('.modal');
                            modal.modal('hide');
                            location.reload(); // Recargar la página para reflejar los cambios
                        }, 600);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        messageElement.removeClass('alert-success').addClass('alert-danger').text('Error en la solicitud').show();
                    }
                });
            });

            $('form[id^="rebajaForm"], form[id^="AumentoForm"]').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formId = form.attr('id');
                var messageElement = $('#' + formId.replace('Form', 'Message'));
                var url = form.attr('action');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            messageElement.removeClass('alert-danger').addClass('alert-success');
                        } else {
                            messageElement.removeClass('alert-success').addClass('alert-danger');
                        }
                        messageElement.text(response.message).show();
                        setTimeout(function() {
                            messageElement.hide();
                            var modal = form.closest('.modal');
                            modal.modal('hide');
                            location.reload(); // Recargar la página para reflejar los cambios
                        }, 600);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        messageElement.removeClass('alert-success').addClass('alert-danger').text('Error en la solicitud').show();
                    }
                });
            });
        });
    </script>

<script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });

        document.getElementById('rebajaForm').addEventListener('submit', function(event) {
            let valid = true;
            const rebaja = parseFloat(document.getElementById('rebaja').value);
            // Validar salario
            if (isNaN(rebaja) || rebaja < 0) {
                document.getElementById('rebaja').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('rebaja').classList.remove('is-invalid');
            }
            if (!valid) {
                event.preventDefault();
            }
        });
        function validarNumeros(event) {
            const input = event.target;
            input.value = input.value.replace(/[^0-9.]/g, '');
        }

        document.getElementById('rebaja').addEventListener('input', validarNumeros);


        document.getElementById('rebaja').addEventListener('input', function(event) {
            var value = parseFloat(event.target.value);
            if (value < 0) {
                event.target.value = '';
                alert('El stock no puede ser negativo.');
            }

        document.getElementById('rebaja').addEventListener('input', function(event) {
            var value = parseFloat(event.target.value);
            if (value < 0) {
                event.target.value = '';
                alert('La rebaja no puede ser negativa.');
            }
        });
    });
    </script>
</body>
</html>

<?php
$conexion->desconectar();
?>
