<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión y tiene un rol administrativo
if (!isset($_SESSION['username']) || !isset($_SESSION['empleadoID']) || $_SESSION['role'] != 2) {
    // Redirige a la página de inicio de sesión si no es administrador
    $_SESSION['error'] = 'Acceso no autorizado. Solo los administradores pueden acceder a esta página.';
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
<link rel="icon" type="image/x-icon" href="../img/icon.svg">
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

.btnn:hover {
    transform: scale(1.01) translateY(-2px);
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
    width: 295px;
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
    background-color: #000;
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
            width: 95%;
            margin: auto;
            background: linear-gradient(to right, #202020, #000000, #202020); /* degradado lateral de oscuro a menos oscuro */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* sombra para el contenedor principal */
            padding: 20px; /* espacio interno para el contenedor principal */
            padding-top: 30px;
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
            <a href="../general_views/admin.php">EURO SERVICE</a>
        </div>
    </div>
    <ul class="sidebar-nav">

 
        <!-- Sección de Catálogo ajustada -->

        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#citas"
                aria-expanded="false" aria-controls="citas">
                <i class="lni lni-agenda"></i>
                <span>CITAS</span>
            </a>
            <ul id="citas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="../CItas/seleccionar_cita_view.php" class="sidebar-link">REGISTRAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../CItas/seleccionar_cita.php" class="sidebar-link">EDITAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../CItas/seleccionar_citacopy.php" class="sidebar-link">CANCELAR</a>
                </li>
            </ul>
        </li>


        

        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#orden"
                aria-expanded="false" aria-controls="orden">
                <i class="bi bi-clipboard"></i>
                <span>ORDEN</span>
            </a>
            <ul id="orden" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="../Orden/seleccionar.php" class="sidebar-link">REGISTRAR ORDEN</a>
                </li>
             
            </ul>
        </li>

        <li class="sidebar-item">
                <a href="../entregar/entregar.php" class="sidebar-link">
                    <i class="lni lni-checkmark-circle"></i>
                    <span>ENTREGAR</span>
                </a>
            </li>
       

            <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                data-bs-target="#empleados" aria-expanded="false" aria-controls="empleados">
                <i class="lni lni-user"></i> 
                <span>EMPLEADOS</span>
            </a>
            <ul id="empleados" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="../Empleados/registro_asistencia.php" class="sidebar-link">ASISTENCIA</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Empleados/justificar_falta.php" class="sidebar-link">JUSTIFICAR FALTA</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Empleados/registro_empleado.php" class="sidebar-link">AGREGAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Empleados/cuentaempleado.php" class="sidebar-link">HABILITAR CUENTA</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Empleados/buscar.php" class="sidebar-link">BUSCAR EMPLEADO</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Finanzas/Reporte_de_rendimiento.php" class="sidebar-link">RENDIMIENTO</a>
                </li>
            </ul>
        </li>

        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                data-bs-target="#vehiculos" aria-expanded="false" aria-controls="vehiculos">
                <i class="lni lni-car-alt"></i>
                <span>VEHICULOS</span>
            </a>
            <ul id="vehiculos" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="../vehiculos/autos_view.php" class="sidebar-link">AGREGAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../vehiculos/atencion_view.php" class="sidebar-link">ATENCIÓN</a>
                </li>
                <li class="sidebar-item">
                    <a href="../vehiculos/editCarView.php" class="sidebar-link">EDITAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../vehiculos/deshabilitar_car_view.php" class="sidebar-link">ELIMINAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../vehiculos/ubicaciones_view.php" class="sidebar-link">UBICACIONES</a>
                </li>
            </ul>
        </li>

        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#usuarios"
                aria-expanded="false" aria-controls="usuarios">
                <i class="bi bi-people"></i>
                <span>CLIENTES</span>
            </a>
            <ul id="usuarios" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="../Usuarios/vista_registro_cliente.php" class="sidebar-link">REGISTRAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Usuarios/edit_user_view.php" class="sidebar-link">EDITAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Usuarios/deshabilitar_client_view.php" class="sidebar-link">ELIMINAR</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Usuarios/buscar_view.php" class="sidebar-link">BUSCAR CLIENTE</a>
                </li>
            </ul>
      

        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#finanzas"
                aria-expanded="false" aria-controls="finanzas">
                <i class="lni lni-coin"></i>
                <span> FINANZAS Y PAGOS</span>
            </a>
            <ul id="finanzas" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="../Finanzas/Reporte_de_Ingresos.php" class="sidebar-link">INGRESOS SEMANALES</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Finanzas/Reporte_de_Gastos.php" class="sidebar-link">GASTOS</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Finanzas/Reporte_de_Nomina.php" class="sidebar-link">NOMINA</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Finanzas/formulario_recibo_pago.php" class="sidebar-link">RECIBO DE PAGO</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Finanzas/finanzas_view.php" class="sidebar-link">GRAFICA</a>
                </li>
                <li class="sidebar-item">
                    <a href="../Finanzas/gestion_pagoforms.php" class="sidebar-link">GESTION</a>
                </li>
                <li class="sidebar-item">
                    <a href="../nominas/nomina_Semana.php" class="sidebar-link">REGISTRAR NOMINAS</a>
                </li>
            </ul>

            <li class="sidebar-item">
    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse" data-bs-target="#catalogo"
        aria-expanded="false" aria-controls="catalogo">
        <i class="lni lni-dropbox"></i>
        <span>INVENTARIO</span>
    </a>
    <ul id="catalogo" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
        <li class="sidebar-item">
            <a href="../Catalogo/Categoria.php" class="sidebar-link">CATEGORÍA</a>
        </li>
        <li class="sidebar-item">
            <a href="../Catalogo/Insumos.php" class="sidebar-link">INSUMOS</a>
        </li>
        <li class="sidebar-item">
            <a href="../Catalogo/Compras.php" class="sidebar-link">COMPRAS</a>
        </li>
    </ul>
</li>


            <li class="sidebar-item">
            <a href="../Catalogo/proveedores_view.php" class="sidebar-link">
                <i class="lni lni-delivery"></i>
                <span>PROVEEDORES</span>
            </a>
        </li>
        </li>
    </ul><br>
    <div class="sidebar-footer">
        <a href="../includes/cerrarsesion.php" class="sidebar-link">  
            <i class="lni lni-exit"></i>
            <span>SALIR</span>
        </a>
    </div>
</aside>

<script>
    const hamBurger = document.querySelector(".toggle-btn");

    hamBurger.addEventListener("click", function () {
        document.querySelector("#sidebar").classList.toggle("expand");
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