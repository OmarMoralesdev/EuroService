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
            background-color: black;
        }
        .nav-link.btn {
            font-size: 1.25rem; /* tamaño base más grande */
            padding: 10px 20px;
        }

        .nav-link {
            color: white !important;
        }

        .nav-link.btn:hover {
            transform: scale(1.1);
        }

        .bg-fixed {
            background-image: url('../EuroService/img/FONDO LOGIN.jpg');
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            height: 85vh;
            width: 100%;
            position: relative;
            overflow-x: hidden;
            margin-top: 20px;
        }

        h2 {
            text-align: center !important;
            text-transform: uppercase;
            color: black;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .content {
            padding-top: 10vh;
        }

        .features {
            padding: 50px 0;
            background-color: #545454;
        }

        .features .feature-item {
            padding: 20px;
            border-radius: 10px;
            background-color: #242424;
            transition: transform 0.2s ease-in-out;
        }

        .features .feature-item:hover {
            transform: translateY(-9px);
        }

        footer {
            background-color: #1E1D1D;
            color: #aaa;
            padding: 20px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .accordion-button {
            background-color: #343a40;
            color: #ffd700;
        }

        .accordion-button:not(.collapsed) {
            color: #ffd700;
            background-color: #212529;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <label class="navbar-brand" href="#">EURO SERVICE</label>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button class="nav-link btn" data-bs-toggle="modal" data-bs-target="#loginModal">INICIAR SESIÓN</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="bg-fixed">
        <div class="overlay"></div>
    </div>

    <div class="content">
        <section class="features text-center text-light">
            <div class="container">
                <h2 class="section-heading">Nuestros Servicios</h2>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-car fs-1"></i>
                            <h3>Reparación Completa</h3>
                            <p>Ofrecemos una amplia gama de servicios de reparación para su vehículo.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-cog fs-1"></i>
                            <h3>Servicio de Mantenimiento</h3>
                            <p>Desde cambios de aceite hasta mantenimiento preventivo, lo tenemos cubierto.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="lni lni-checked fs-1"></i>
                            <h3>Inspección Vehicular</h3>
                            <p>Realizamos inspecciones exhaustivas para garantizar la seguridad de su vehículo.</p>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </section>
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
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <p>&copy; 2024 EURO SERVICE. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="loginModalLabel">Iniciar Sesión</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="login.php">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" name="username" autocomplete="on" placeholder="Ingresa tu usuario" required>
                            <br>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <br>
                        <input type="submit" value="Iniciar sesión" class="btn btn-dark">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if (isset($_SESSION['error']) && $_SESSION['error']) : ?>
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        <?php endif; ?>
    </script>
</body>

</html>
