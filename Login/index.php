<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .form-container {
            width: 90%;
            max-width: 400px;
            padding: 30px;
            background-color: #1e1e1e;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            animation: bounceIn 1s ease-out;
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(0,0,0,0.1));
            z-index: 0;
            animation: gradientAnimation 4s infinite;
        }

        .form-container h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #fff;
            text-align: center;
            animation: fadeInUp 1s ease-out;
            z-index: 1;
            position: relative;
        }

        .form-container .form-label {
            color: #ccc;
        }

        .btn-submit {
            background-color: #333;
            border: none;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-submit:hover {
            background-color: #555;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .alert {
            margin-bottom: 20px;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 100% 100%;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>z
    <div class="form-container">
        <?php
        session_start();
        if (isset($_SESSION['alert'])) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['alert']['message']; ?>
                <?php unset($_SESSION['alert']); ?>
            </div>
        <?php endif; ?>
        <div class="modal-body">
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
            <form method="post" action="login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="username" name="username" autocomplete="on" placeholder="Ingresa tu usuario" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                </div>
                
                <button type="submit" class="btn btn-submit d-grid gap-2 col-12 mx-auto">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>

</html>