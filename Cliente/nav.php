    <nav class="navbar navbar-expand-lg navbar-dark black fixed-top">
        <div class="container-fluid">
            <p class="navbar-brand">EURO SERVICE</p>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <?php
                        $usuario = obtenerDetallesClientepersona($pdo, $clienteID);
                        if ($usuario) {
                            echo "<a class='nav-link' href='../Cliente/#cp'>CITAS PENDIENTES</a>";
                        }
                        ?>
                    </li>
                    <li class="nav-item">
                        <?php
                        $usuario = obtenerDetallesClientepersona($pdo, $clienteID);
                        if ($usuario) {
                            echo "<a class='nav-link' href='../Cliente/histo.php'>HISTORIAL</a>";
                        }
                        ?>
                    </li>
                    <li class="nav-item">
                        <?php
                        $usuario = obtenerDetallesClientepersona($pdo, $clienteID);
                        if ($usuario) {
                            echo "<a class='nav-link' href='../includes/cerrarsesion.php'>CERRAR SESIÓN</a>";
                        } else {
                            echo "<a class='nav-link' href='../index.php'>INICIAR SESIÓN</a>";
                        }
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
