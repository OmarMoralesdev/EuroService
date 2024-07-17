<?php
// Incluir archivo de conexión a la base de datos
include '../includes/db.php';

// Iniciar conexión a la base de datos
$conexion = new Database();
$conexion->conectar();

// Inicializar la variable de búsqueda
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

$consulta_empleados = "SELECT e.empleadoID, CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_completo, e.alias, e.tipo, n.salario
                    FROM EMPLEADOS e
                    INNER JOIN PERSONAS p ON e.personaID = p.personaID
                    LEFT JOIN NOMINAS n ON e.empleadoID = n.empleadoID";

// Agregar filtro si hay criterio de búsqueda
if (!empty($buscar)) {
    $consulta_empleados .= " WHERE p.nombre LIKE '%$buscar%' OR p.apellido_paterno LIKE '%$buscar%' OR p.apellido_materno LIKE '%$buscar%' OR e.alias LIKE '%$buscar%'";
}

$consulta_empleados .= " ORDER BY nombre_completo";

// Ejecutar consulta para obtener empleados
$empleados = $conexion->seleccionar($consulta_empleados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINANZAS EMPLEADOS</title>
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
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2 class="text-center h2">CONTROL FINANCIERO EMPLEADOS</h2>
                <div class="form-container">
                    <!-- Formulario de búsqueda -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar empleados..." name="buscar" value="<?php echo htmlspecialchars($buscar); ?>">
                            <button type="submit" class="btn btn-outline-secondary">Buscar</button>
                        </div>
                    </form>

                    <!-- Mostrar resultados de empleados -->
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
                                echo "<p class='card-text'><strong>Salario:</strong> {$empleado->salario}</p>";

                                // Botones para agregar deudas y rebajas
                                echo "<button type='button' class='btn btn-dark btn-md' style='width: 49%;' data-bs-toggle='modal' data-bs-target='#modalDeudas{$empleado->empleadoID}'>Agregar Deudas</button>";
                                echo " ";
                                echo "<button type='button' class='btn btn-dark btn-md ml-2' style='width: 49%;' data-bs-toggle='modal' data-bs-target='#modalRebajas{$empleado->empleadoID}'>Agregar Rebajas</button>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                                // Modal para agregar deudas
                                echo "<div class='modal fade' id='modalDeudas{$empleado->empleadoID}' tabindex='-1' aria-labelledby='modalDeudas{$empleado->empleadoID}Label' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalDeudas{$empleado->empleadoID}Label'>Agregar Deudas para {$empleado->nombre_completo}</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<div class='modal-body'>";
                                echo "<form>";
                                echo "<div class='mb-3'>";
                                echo "<label for='deuda{$empleado->empleadoID}'>Cantidad de Deuda:</label>";
                                echo "<input type='number' class='form-control' id='deuda{$empleado->empleadoID}' name='deuda'>";
                                echo "</div>";
                                echo "</form>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>";
                                echo "<button type='button' class='btn btn-dark'>Guardar Deuda</button>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                                // Modal para agregar rebajas
                                echo "<div class='modal fade' id='modalRebajas{$empleado->empleadoID}' tabindex='-1' aria-labelledby='modalRebajas{$empleado->empleadoID}Label' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalRebajas{$empleado->empleadoID}Label'>Agregar Rebajas para {$empleado->nombre_completo}</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<div class='modal-body'>";
                                echo "<form action='AddRebaja.php' method='post' id='formRebaja{$empleado->empleadoID}'>";
                                echo "<div class='mb-3'>";
                                echo "<label for='rebaja{$empleado->empleadoID}'>Cantidad de Rebaja:</label>";
                                echo "<input type='number' class='form-control' id='rebaja{$empleado->empleadoID}' name='rebaja'>";
                                echo "</div>";
                                echo "</form>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>";
                                echo "<button type='button' class='btn btn-dark' onclick='guardarRebaja({$empleado->empleadoID})'>Guardar</button>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p class='text-center'>No se encontraron empleados.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function guardarRebaja(empleadoID) {
            // Obtener el formulario específico para este empleado
            var form = document.getElementById('formRebaja' + empleadoID);

            // Enviar el formulario usando AJAX
            var formData = new FormData(form);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'AddRebaja.php', true);
            xhr.onload = function () {
                // Cerrar el modal después de guardar
                $('#modalRebajas' + empleadoID).modal('hide');

                // Recargar la página actual para reflejar los cambios
                location.reload();
            };
            xhr.send(formData);
        }
    </script>
</body>
</html>

<?php
// Desconectar la base de datos al finalizar
$conexion->desconectar();
?>
