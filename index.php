<?php
session_start();

// Suponiendo que tienes una lógica de validación aquí
// Si los datos son incorrectos, configurar el mensaje de alerta en la sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ejemplo de lógica de validación
    // Comparar los datos con los valores correctos
    if ($username !== 'correct_username' || $password !== 'correct_password') {
        // Configurar el mensaje de alerta en la sesión
        $_SESSION['alert'] = ['message' => 'Usuario o contraseña incorrectos.'];
        // Redireccionar a la misma página para mostrar el mensaje de alerta
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit; 
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta tags, links, stylesheets -->
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="./img/incono.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EURO SERVICE</title>
    <style>
        /* Styles */
        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .bg-fixed {
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            height: 75vh;
            width: 100%;
            position: relative;
            overflow-x: hidden;
            margin-top: 50px;
            opacity: 40%;
        }

        .bg-fixedd {
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            height: 27rem;
            width: 100%;
            position: relative;
            overflow-x: hidden;
            margin-top: 50px;
            opacity: 40%;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .content {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .features {
            padding: 50px 0;
            background-color: #222;
            animation: fadeIn 2s ease-in-out;
        }

        .features .feature-item {
            padding: 20px;
            border-radius: 10px;
            background-color: #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
        }

        .features .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.8);
        }

        .feature-item i {
            font-size: 2rem;
            color: #f5a623;
            transition: color 0.3s ease;
        }

        .feature-item:hover i {
            color: #fff;
        }

        footer {
            background-color: #111;
            color: #ccc;
            padding: 20px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            animation: fadeIn 2s ease-in-out;
        }

        footer h5 {
            font-size: 1.25rem;
            font-weight: bold;
        }
        /* Ajuste del contenedor de la imagen */
.bg-fixedd {
    background-size: cover;
    background-position: center center;
    background-attachment: fixed;
    height: 27rem; /* Mantener una altura adecuada en pantallas grandes */
    width: 100%;
    position: relative;
    overflow-x: hidden;
    margin-top: 50px;
    opacity: 40%;
}

/* Media query para pantallas más pequeñas */
@media (max-width: 768px) {
    .bg-fixedd {
        height: 50vh; /* Ajustar la altura en dispositivos móviles */
        background-attachment: scroll; /* Evitar que la imagen sea fija en móviles */
    }
}


        .social-icons a {
            color: #fff;
            margin: 0 10px;
            font-size: 1.5rem;
            transition: color 0.3s, transform 0.3s;
        }

        .social-icons a:hover {
            color: #f5a623;
            transform: scale(1.2);
        }

        .accordion-button {
            background-color: #333;
            color: #fff;
            border: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .accordion-button:not(.collapsed) {
            color: #fff;
            background-color: #444;
        }

        .accordion-button:hover {
            background-color: #555;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-header {
            background-color: #333;
            color: #fff;
        }

        .modal-content {
            background-color: #222;
            color: #fff;
        }

        .btn-light {
            background-color: #2E2E2E;
            color: #fff;
            border: none;
        }

        .btn-light:hover {
            background-color: #3c3c3c;
            color: #fff;
        }
    
        /* Tamaño de texto en dispositivos móviles */
        .fs-xs {
            font-size: 80px; /* Tamaño para pantallas pequeñas */
            font-weight: bold
        }
    
        /* Tamaño de texto en pantallas grandes */
        .fs-lg {
            font-size: 180px; /* Tamaño para pantallas grandes */
            font-weight: bold
        }

        .vh-75-lg {
            height: 75vh;
        }

        /* Tamaño del contenedor para pantallas pequeñas */
        .vh-40-sm {
            height: 40vh;
        }

        .spline-container {
            width: 100%;
            height: 68vh; /* Ajusta la altura según sea necesario */
            margin-top: 40px;
        }
    </style>
    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Abrir el modal automáticamente si hay una alerta en la sesión
            <?php if (isset($_SESSION['alert'])): ?>
                var myModal = new bootstrap.Modal(document.getElementById('loginModal'), {});
                myModal.show();
            <?php endif; ?>
        });
    </script>
</head>

<body>
<?php include('nav.php'); ?>
<nav class="navbar navbar-expand-lg navbar-dark" >
        <div class="container-fluid">
            <p class="navbar-brand">EURO SERVICE</p>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#servicios">SERVICIOS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ubi">UBICACIÓN</a>
                    </li>
                    <li class="nav-item">
                        <a href="./Login/index.php" class="nav-link btn pulse">INICIAR SESIÓN</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
?>
    <div class="bg-fixed" style="background-image: url('path/to/your/image.jpg');">
        <div class="overlay">
            <div class="text-center text-light">
                <!-- Texto con tamaño pequeño para móviles y grande para pantallas de computadora -->
                <p class="fs-xs d-block d-md-none">
                    EURO SERVICE
                </p>
                <p class="fs-lg d-none d-md-block">
                    EURO SERVICE
                </p>
            </div>
        </div>
    </div>

    <div id="servicios" class="content">
        <section class="features text-center text-light">
            <div class="container">
                <h2 class="section-heading">NUESTROS SERVICIOS</h2>
                <hr class="bg-light">
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-car"></i>
                            <h3>Reparación Completa</h3>
                            <p>Ofrecemos una amplia gama de servicios de reparación para su vehículo.</p>
                        </div>
                        <br>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-cog"></i>
                            <h3>Servicio de Mantenimiento</h3>
                            <p>Desde cambios de aceite hasta mantenimiento preventivo, lo tenemos cubierto.</p>
                        </div>
                        <br>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-cog"></i>
                            <h3>Inspección Vehicular</h3>
                            <p>Realizamos inspecciones exhaustivas para garantizar la seguridad de su vehículo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="containerr">
        <div class="bg-fixedd" style="background-image: url('./img/FONDO_LOGIN.jpg');">
            <div class="overlay"></div>
        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Sobre Nosotros</h5>
                    <p>EURO SERVICE es un taller mecánico dedicado a ofrecer servicios de alta calidad para su vehículo.</p>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <div class="social-icons">
                        <a href="https://www.tiktok.com/@euro.service?_t=8olIXbxXzJl&_r=1" class="lni lni-tiktok-alt"></a>
                        <hr>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Correo: euroservice339@gmail.com</p>
                    <p>Teléfono: +52 871 460 1498</p>
                </div>
            </div>
            <iframe id="ubi" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1799.0938076611703!2d-103.40868459325407!3d25.598681205350616!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x868fda7cf05bb3b5%3A0xf8eb1cc81ece3fb6!2sC.%20Autoridades%20Civiles%205%2C%20Ejido%20la%20Uni%C3%B3n%2C%2027420%20Torre%C3%B3n%2C%20Coah.!5e0!3m2!1sen!2smx!4v1723314448051!5m2!1sen!2smx"width="100%" height="300" style="border:1px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </footer>
</body>

</html>