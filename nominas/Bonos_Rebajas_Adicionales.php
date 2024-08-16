<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>Actualizar Bonos y Rebajas</title>
    <style>
        .main {
            align-items: center;
        }

        .datepicker {
            background-color: #f7f7f7;
            border-radius: 5px;
            padding: 15px;
        }

        .input-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }

        .form-control {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Actualizar Bonos y Rebajas Adicionales por Empleado</h2>
                <div class="form-container">
                <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>¡Orden de Trabajo Registrada!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='alert alert-success' role='alert'>{$_SESSION['bien']}</div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        unset($_SESSION['bien']);
                    }
                    ?>
                    <form action="actualizar_nomina.php" method="post">
                        <label for="empleadoID" class="form-label">Empleado:</label>
                        <select name="empleadoID" class="form-control" required>
                            <?php
                            require '../includes/db.php';
                            $con = new Database();
                            $pdo = $con->conectar();
                            
                            function obtenerEmpleadosDisponibles($pdo) {
                                $sql = "SELECT EMPLEADOS.empleadoID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno 
                                        FROM EMPLEADOS 
                                        JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID
                                        WHERE EMPLEADOS.tipo != 'administrativo'";
                                $stmt = $pdo->query($sql);
                                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                            }

                            $empleados = obtenerEmpleadosDisponibles($pdo);
                            foreach ($empleados as $empleado) {
                                $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                                echo "<option value=\"{$empleado['empleadoID']}\">{$nombreCompleto}</option>";
                            }
                            ?>
                        </select>
                        <br>
                        <div class="col-md-6 offset-md-3">
                            <label for="fecha">Selecciona la semana:</label>
                            <div id="week-picker" class="input-group">
                                <input type="text" id="fecha" name="fecha" class="form-control" placeholder="Selecciona el lunes de la semana" required>
                            </div>
                        </div>
                        <br>
                        <label for="bonos">Bonos:</label>
                        <input type="number" id="bonos" name="bonos" step="0.01" class="form-control" required>
                        <br>
                        <label for="rebajas_adicionales">Rebajas Adicionales:</label>
                        <input type="number" id="rebajas_adicionales" name="rebajas_adicionales" step="0.01" class="form-control" required>
                        <br>
                        <input type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto" value="Actualizar">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="../assets/js/weekpicker.js"></script>
</body>

</html>
