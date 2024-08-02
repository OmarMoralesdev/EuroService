<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Registro de Cuenta de Administrador</title>
</head>
<style>
        .wrapper {
            display: flex;
            height: 100vh;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include '../includes/vabr.php'; ?>
    <div class="main p-3">
        <div class="container">
            <h2>Registro de Cuenta de Administrador</h2>
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
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Usuario registrado!</h1>
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
            <form action="registrocuena.php" method="POST">
                <label for="empleado" class="form-label">Empleado:</label>
                <select name="empleado" class="form-control" required>
                    <?php
                    require '../includes/db.php';
                    $con = new Database();
                    $pdo = $con->conectar();
                    
                    function obtenerEmpleadosDisponibles($pdo)
                    {
                        $sql = "SELECT EMPLEADOS.personaID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
                                FROM EMPLEADOS 
                                JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID
                                WHERE EMPLEADOS.tipo = 'administrativo'";
                        $stmt = $pdo->query($sql);
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    $empleados = obtenerEmpleadosDisponibles($pdo);
                    foreach ($empleados as $empleado) {
                        $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                        echo "<option value=\"{$empleado['personaID']}\">{$nombreCompleto}</option>";
                    }
                    ?>
                </select>
                <div class="invalid-feedback">Debes seleccionar un empleado.</div>

                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" class="form-control" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" class="form-control" required><br><br>
                <label for="confirm_password">Confirmar Password:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required><br><br>
                <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Registrar</button>
            </form>
        </div>
    </div>
    <script>
    $(document).ready(function () {
        if ($('#staticBackdrop').length) {
            $('#staticBackdrop').modal('show');
        }
    });
    </script>
</body>

</html>
