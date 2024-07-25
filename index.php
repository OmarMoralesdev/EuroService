<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EURO SERVICE</title>
    <style>
        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: black;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background-color: #000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
        }

        .nav-link {
            color: #fff !important;
            font-size: 1.2rem;
            margin-right: 15px;
            transition: color 0.3s, transform 0.3s;
        }

        .nav-link:hover {
            color: #ccc;
            transform: scale(1.1);
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

        .nav-link.btn.pulse {
            animation: pulse 3.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1.1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.9;
            }

            100% {
                transform: scale(1.4);
                opacity: 0.2;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark height: 65vh">
        <div class="container-fluid">
            <p class="navbar-brand">EURO SERVICE</p>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="nav-link btn pulse" data-bs-toggle="modal" data-bs-target="#loginModal">INICIAR SESIÓN</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="bg-fixed">
        <div class="overlay">
            <div class="text-center text-light">
                <p style="font-size: 180px; font-weight: bold;">EURO SERVICE</p>
            </div>
        </div>
    </div>

            
    
    <div class="content">
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
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-cog"></i>
                            <h3>Servicio de Mantenimiento</h3>
                            <p>Desde cambios de aceite hasta mantenimiento preventivo, lo tenemos cubierto.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-checked"></i>
                            <h3>Inspección Vehicular</h3>
                            <p>Realizamos inspecciones exhaustivas para garantizar la seguridad de su vehículo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="bg-fixed" style="background-image: url('../EuroService/img/FONDO LOGIN.jpg'); ">
        <div class="overlay">
            <div class="text-center text-light">
            </div>
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
                        <a href="#" class="bi bi-facebook"></a>
                        <a href="#" class="bi bi-twitter"></a>
                        <a href="#" class="bi bi-instagram"></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <p>Correo: contacto@eurosv.com</p>
                    <p>Teléfono: +1234567890</p>
                </div>
            </div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3598.18782262292!2d-103.41128942458826!3d25.598674315322345!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x869c1e1a44a6e3ef%3A0x8b4f22350c0a407d!2sEURO%20SERVICE!5e0!3m2!1ses!2smx!4v1649918775186!5m2!1ses!2smx" width="100%" height="300" style="border:1px;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </footer>

    <!-- Modal de Inicio de Sesión -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="POST" >
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="username" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>
                        <hr> 
                        <button type="submit" class="btn btn-light w-100">Iniciar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
