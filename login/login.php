<?php
require '../includes/db.php'; // Asegúrate de incluir correctamente tu archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario y limpiarlos
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    // Validar que no estén vacíos
    if (empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Preparar y ejecutar la consulta para buscar el usuario por correo electrónico
        $stmt = $conn->prepare("SELECT usuarioid, contraseña, roles FROM USUARIOS WHERE usuario = ?");
        if ($stmt === false) {
            die('Error en la preparación de la consulta: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        // Verificar si se encontró un usuario con el correo especificado
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($usuarioid, $hashed_password, $role);
            $stmt->fetch();

            // Verificar contraseña usando password_verify
            if (password_verify($password, $hashed_password)) {
                // Iniciar sesión del usuario
                session_start();
                $_SESSION['id_usuario'] = $usuarioid;
                $_SESSION['correo'] = $correo;
                $_SESSION['role'] = $role;

                // Redirigir según el rol del usuario
                if ($role === 'usuario') {
                    header("Location: ../menulogin/index.html");
                    exit();
                } elseif ($role === 'administrador') {
                    header("Location: ../administrador/index.html");
                    exit();
                } else {
                    // Manejar otros roles según sea necesario
                    header("Location: ../otra_pagina/index.html");
                    exit();
                }
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
        
        // Cerrar la declaración
        $stmt->close();
    }
}

// Cerrar la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .container-fluid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80%;
            width: 90%;
            background: linear-gradient(to right, white 45%, #1B0A51 25%, #120735 75%);
            border-radius: 20px;
            padding: 2%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .form-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: #D7D7D8;
            border-radius: 10px;
            box-shadow: 0 0 100px rgba(0, 0, 255, 0.2);
            margin-top: 5%; /* Espacio superior para el formulario */
        }
        .form-container h2 {
            margin-bottom: 20px;
            text-align: left;
        }   
        input[type="email"], input[type="password"] {
            border: 2px solid #100630;
            border-radius: 5px;
            background: linear-gradient(to right, #F0F0F0 25%, #CEC9EC 75%);
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 5px #1544FF;
        }
        button[type="submit"] {
            background: linear-gradient(to right, #1B0A51 25%, #120735 75%);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        button[type="submit"]:hover {
            background: #2E2B5F; 
            box-shadow: 0 0 10px rgba(0, 0, 255, 0.5);
        }
        a {
            display: block;
            margin-top: 10px;
            text-align: center;
        }
        a[href="Reset_Password.php"] {
            color: #A80D0D;
        }
        a[href="Registrer.php"] {
            color: #000080;
        }
        @media (max-width: 576px) {
            .container-fluid {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 10%;
            }
            .form-container {
                padding: 15px;
                margin: 0;
            }
            .form-container h2 {
                font-size: 24px;
            }
            button[type="submit"] {
                font-size: 14px;
                padding: 8px 16px;
            }
            img {
                position: static;
                margin-top: 20px;
                width: 50%;
            }
        }
        img {
            width: 30%;
            height: auto;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="form-container">
            <h1 style="text-align: center;">TALLER EURO SERVICE </h1>
            <br>
            <h2 style=" font-size: 30px;">Bienvenido</h2>
            <br>
            <form method="post" action="login.php">
                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="text" class="form-control" id="correo" name="correo" autocomplete="off" placeholder="Correo electrónico" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                </div>
                <button type="submit">Iniciar sesión</button>
                <div class="d-flex justify-content-between mt-4">
                    <a href="Reset_Password.php" title="Restablecer contraseña">Restablecer contraseña</a>
                    <a href="register.php" title="Crear cuenta">¿No tiene una cuenta? Regístrese</a>
                </div>
            </form>
        </div>
        <img src="img/logo.png" alt="Logo">
        
</body>

</html>