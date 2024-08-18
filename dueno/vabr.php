<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username']) || $_SESSION['role'] != 3) {
    $_SESSION['error'] = 'Acceso no autorizado. Solo los dueños pueden acceder a esta página.';
    header('Location: ../Login/index.php');
    exit();
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

body {
    background-color: #232323;
    font-family: 'Poppins', sans-serif;
}

::after,
::before {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

a {
    text-decoration: none;
}

li {
    list-style: none;
}

h1 {
    font-weight: 600;
    font-size: 1.5rem;
}

.wrapper {
    display: flex;
}
        .main {
            min-height: 100vh;
            width: 100%;
            overflow: hidden;
            transition: all 0.35s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #sidebar {
            width: 70px;  
            z-index: 1000;
            top: 0;
            left: 0;
            background-color: #000;
            transition: all .35s ease-in-out;
            display: flex;
            flex-direction: column;
        }

#sidebar.expand {
    width: 260px;
}

.toggle-btn {
    background-color: transparent;
    cursor: pointer;
    border: 0;
    padding: 1rem 1.5rem;
}

.toggle-btn i {
    font-size: 1.5rem;
    color: #FFF;
}

.sidebar-logo {
    margin: auto 0;
    opacity: 0;
    transition: opacity .35s;
    overflow: hidden;
}

#sidebar.expand .sidebar-logo {
    opacity: 1;
    transition-delay: .35s;
}

.sidebar-nav {
    padding: 2rem 0;
    flex: 1 1 auto;
}

a.sidebar-link {
    padding: .625rem 1.625rem;
    color: #FFF;
    display: block;
    font-size: 0.9rem;
    white-space: nowrap;
    border-left: 3px solid transparent;
    opacity: 1;
    transition: opacity .35s;
    overflow: hidden; /* Oculta el texto cuando el sidebar no está expandido */
}

#sidebar.expand a.sidebar-link {
    opacity: 1;
    transition-delay: .35s;
}

.sidebar-link i {
    font-size: 1.1rem; /* Aumenta el tamaño del ícono si es necesario */
    margin-right: .75rem; /* Espacio entre el ícono y el texto */
    color: #FFF;
    transition: opacity .35s;
}

/* Esconde el texto en los enlaces cuando el sidebar no está expandido */
#sidebar:not(.expand) .sidebar-link span {
    display: none;
}

#sidebar:not(.expand) .sidebar-link i {
    opacity: 1; /* Muestra los íconos cuando el sidebar no está expandido */
}

/* Muestra el texto cuando el sidebar está expandido */
#sidebar.expand .sidebar-link span {
    display: inline;
}

#sidebar.expand .sidebar-link i {
    opacity: 1;
}

a.sidebar-link:hover {
    background-color: rgb(32, 32, 32);
    border-left: 3px solid #3b7ddd;
}

.sidebar-item {
    position: relative;
}

#sidebar:not(.expand) .sidebar-item .sidebar-dropdown {
    position: absolute;
    top: 0;
    left: 70px;
    background-color: #000;
    padding: 0;
    min-width: 15rem;
    display: none;
}

#sidebar:not(.expand) .sidebar-item:hover .has-dropdown+.sidebar-dropdown {
    display: block;
    max-height: 15em;
    width: 100%;
    opacity: .8;
}

#sidebar.expand .sidebar-link[data-bs-toggle="collapse"]::after {
    border: solid;
    border-width: 0 .075rem .075rem 0;
    content: "";
    display: inline-block;
    padding: 2px;
    position: absolute;
    right: 1.5rem;
    top: 1.4rem;
    transform: rotate(-135deg);
    transition: all .2s ease-out;
}

#sidebar.expand .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
    transform: rotate(45deg);
    transition: all .2s ease-out;
}

        /* contenedores de las vistas */
        .container {
            width: 90%;
            margin: auto;
            background: linear-gradient(to right, #202020, #000000, #202020); /* degradado lateral de oscuro a menos oscuro */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* sombra para el contenedor principal */
            padding: 20px; /* espacio interno para el contenedor principal */
        }

/* Contenedor interno del formulario */
.form-container {
    width: 100%;
    padding: 20px; /* espacio interno para el contenido del formulario */
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); /* sombra más marcada */
}

h2 {
    color: #ffffff;
    text-align: center;
    text-transform: uppercase; /* título en mayúsculas */
    font-family: 'chicago', sans-serif; /* Cambio de tipografía a chicago */
}

.lista {
    display: none;
    list-style: none;
    padding: 0;
    margin: 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    max-height: 150px;
    overflow-y: auto;
}

.lista li {
    padding: 10px;
    cursor: pointer;
    background-color: #fff;
    border-bottom: 1px solid #ddd;
}

.lista li:hover {
    background-color: #e2e2e2;
}

.lista li:last-child {
    border-bottom: none;
}
.menu-icon {
    position: fixed;
   
  
    }
/* Ajuste para móviles */
@media screen and (max-width: 768px) {
    #sidebar {
        width: 0;
        height: 100%;
        overflow-x: hidden;
        position: fixed;
    }

    #sidebar.expand {
        width: 100%;
    }

    .menu-icon {
        display: block;
        font-size: 1.5rem;
        color: #FFF;
        padding: 1rem;
        cursor: pointer;
    }
}

    </style>
       <header>
        <div class="menu-icon" onclick="alternarSidebar()">
        <i class="bi bi-arrow-bar-right"></i>
        </div>
    </header>
<aside id="sidebar">
    <div class="d-flex">
        <button class="toggle-btn" type="button">
            <i class="lni lni-home"></i>
        </button>
        <div class="sidebar-logo">
            <a href="index.php">EURO SERVICE</a>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a href="proveedores_view.php" class="sidebar-link">
                <i class="lni lni-delivery"></i>
                <span>PROOVEDORES</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#catalogo"
                aria-expanded="false" aria-controls="catalogo">
                <i class="lni lni-dropbox"></i>
                <span>INVENTARIO</span>
            </a>
            <ul id="catalogo" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="Categoria.php" class="sidebar-link">CATEGORÍA</a>
                </li>
                <li class="sidebar-item">
                    <a href="Insumos.php" class="sidebar-link">INSUMOS</a>
                </li>
            </ul>
        </li>


        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#car"
                aria-expanded="false" aria-controls="car">
                <i class="lni lni-car-alt"></i>
                <span>VEHICULOS</span>
            </a>
            <ul id="car" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="atencion_view.php" class="sidebar-link">ATENCION</a>
                </li>
                <li class="sidebar-item">
                    <a href="ubicaciones_view.php" class="sidebar-link">UBICACIONES</a>
                </li>
            </ul>
        </li>


        <li class="sidebar-item">
            <a href="pendientes.php" class="sidebar-link">
                <i class="lni lni-agenda"></i>
                <span>CITAS PENDIENTES</span>
            </a>
        </li>


        <li class="sidebar-item">
            <a href="buscar_view.php" class="sidebar-link">
                <i class="bi bi-people"></i>
                <span>BUSCAR CLIENTES</span>
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="buscar.php" class="sidebar-link">
            <i class="lni lni-user"></i>
                <span>BUSCAR EMPLEADOS</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="Reporte_de_ingresos.php" class="sidebar-link">
                <i class="lni lni-coin"></i>
                <span>FINANZAS</span>
            </a>    
        </li>
    </ul>
</li>
<div class="sidebar-footer">
    <a href="../includes/cerrarsesion.php" class="sidebar-link">  
        <i class="lni lni-exit"></i>
        <span>SALIR</span>
    </a>
</div>
</aside>


<script>
$(document).ready(function () {
    $('.toggle-btn').click(function () {
        $('#sidebar').toggleClass('expand');
        $('.main').toggleClass('expand');
    });
});
</script>
<script>
    function alternarSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('expand');
    }

    // Variables para el seguimiento de los gestos de deslizamiento
    let inicioX;
    let desplazamiento = false;

    // Eventos táctiles para dispositivos móviles
    document.addEventListener('touchstart', function(evento) {
        inicioX = evento.touches[0].clientX;
        desplazamiento = false; // Inicialmente no estamos desplazándonos
    });

    document.addEventListener('touchmove', function(evento) {
        if (!inicioX) return;

        let actualX = evento.touches[0].clientX;
        let diferenciaX = inicioX - actualX;

        // Verifica si estamos en un elemento con desplazamiento horizontal
        let enElementoDesplazable = false;
        let elemento = evento.target;
        while (elemento) {
            if (elemento.scrollWidth > elemento.clientWidth) {
                enElementoDesplazable = true;
                break;
            }
            elemento = elemento.parentElement;
        }

        if (enElementoDesplazable) {
            desplazamiento = true; // Si estamos en un elemento desplazable, marcamos como desplazamiento
        }

        if (!desplazamiento) {
            // Si no estamos desplazándonos, manejamos la apertura/cierre de la barra lateral
            if (diferenciaX > 50) {
                document.getElementById('sidebar').classList.remove('expand'); // Deslizar hacia la izquierda
                inicioX = null; // Reiniciar inicioX
            } else if (diferenciaX < -50) {
                document.getElementById('sidebar').classList.add('expand'); // Deslizar hacia la derecha
                inicioX = null; // Reiniciar inicioX
            }
        }
    });

    // Reiniciar inicioX al final del toque
    document.addEventListener('touchend', function() {
        inicioX = null;
    });
</script>
