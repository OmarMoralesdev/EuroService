<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRO CLIENTE</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-group {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container" style="width: 90%; margin: auto; background-color: #EBEBEB; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                <h2>Registrar Cliente</h2>
                <br>
                <?php
                require '../includes/db.php';

                function generateRandomPassword($length = 10) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $charactersLength = strlen($characters);
                    $randomPassword = '';
                    for ($i = 0; $i < $length; $i++) {
                        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
                    }
                    return $randomPassword;
                }

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $nombre = trim($_POST['nombre']);
                    $apellido_paterno = trim($_POST['apellido_paterno']);
                    $apellido_materno = trim($_POST['apellido_materno']);
                    $correo = trim($_POST['correo']);
                    $telefono = trim($_POST['telefono']);

                    $campos = array();
                    if ($nombre == "" || is_numeric($nombre)) {
                        array_push($campos, "El nombre no puede contener caracteres numéricos.");
                    }
                    if ($apellido_materno == "" || is_numeric($apellido_materno)) {
                        array_push($campos, "El apellido materno no puede contener caracteres numéricos.");
                    }
                    if ($apellido_paterno == "" || is_numeric($apellido_paterno)) {
                        array_push($campos, "El apellido paterno no puede contener caracteres numéricos.");
                    }
                    if ($correo == "" || strpos($correo, "@") === false) {
                        array_push($campos, "Ingresa un correo electrónico válido.");
                    }
                    if ($telefono == "" || strlen($telefono) != 10) {
                        array_push($campos, "El teléfono debe tener 10 caracteres.");
                    }
                    if ($telefono == "" || !is_numeric($telefono)) {
                        array_push($campos, "El teléfono debe ser numérico");
                    }

                    if (count($campos) > 0) {
                        echo "<div class='alert alert-danger'>";
                        foreach ($campos as $error) {
                            echo "<li>" . $error . "</li>";
                        }
                        echo "</div>";
                    } else {
                        $con = new Database();
                        $pdo = $con->conectar();

                        //corrio
                        try {
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM CLIENTES WHERE correo = ?");
                            $stmt->execute([$correo]);
                            if ($stmt->fetchColumn() > 0) {
                                echo "<div class='alert alert-danger'>El correo electrónico ya está registrado.</div>";
                            } else {
                                $password = generateRandomPassword();
                                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                $role = 'cliente';

                                $stmt_cliente = $pdo->prepare("INSERT INTO CLIENTES (nombre, apellido_paterno, apellido_materno, correo, telefono) VALUES (?, ?, ?, ?, ?)");
                                $stmt_cliente->execute([$nombre, $apellido_paterno, $apellido_materno, $correo, $telefono]);

                                if ($stmt_cliente->rowCount() > 0) {
                                    $clienteID = $pdo->lastInsertId();
                                    $stmt_usuario = $pdo->prepare("INSERT INTO users (username, roles, password, clienteID) VALUES (?, ?, ?, ?)");
                                    $stmt_usuario->execute([$correo, $role, $hashed_password, $clienteID]);

                                    if ($stmt_usuario->rowCount() > 0) {
                                        echo "<div class='alert alert-success'>Usuario registrado exitosamente.<br>Nombre de usuario: <strong>$correo</strong><br>Contraseña: <strong>$password</strong></div>";
                                    } else {
                                        echo "<div class='alert alert-danger'>Error al insertar el usuario.</div>";
                                    }
                                } else {
                                    echo "<div class='alert alert-danger'>Error al insertar el cliente.</div>";
                                }
                            }
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                        }
                    }
                }
                ?>
                <form method="post" action="register_view.php">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido_paterno">Apellido Paterno:</label>
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno:</label>
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-dark w-100">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
