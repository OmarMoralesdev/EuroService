<?php
session_start();
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $citaID = $_POST['citaID'];
    $_SESSION['cita'] = obtenerCitaPorID($pdo, $citaID); // Guarda la cita seleccionada en la sesión
    $_SESSION['mensaje'] = "Cita seleccionada correctamente";
    header('Location: Cancelarcita_front.php'); // Redirige a la página de edición
    exit();
}

function obtenerCitaPorID($pdo, $citaID)
{
    $stmt = $pdo->prepare('SELECT * FROM CITAS WHERE citaID = ?');
    $stmt->execute([$citaID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Cita para Orden de Trabajo</title>
    <style>
        .tarjeta {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .paginacion {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .paginacion a {
            margin: 0 5px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-decoration: none;
            color: #000;
            font-size: 16px;
        }

        .paginacion a.activo {
            background-color: #000;
            color: #fff;
        }

        @media (max-width: 600px) {
            .paginacion a {
                margin: 0 2px;
                padding: 8px;
                font-size: 14px;
            }
        }

        @media (max-width: 400px) {
            .paginacion a {
                margin: 0 1px;
                padding: 6px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-2">
            <div class="container">
                <h2>SELECCIONAR CITA</h2>
                <div class="form-container">
                    <form action="" id="x">
                    <input type="text" id="buscar" class="form-control" placeholder="Buscar por vehículo..."><br>
                    <div id="citas-contenedor"></div>
                    <div class="paginacion" id="paginacion"></div>
                    <?php
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
                    </form>
                    <form id="formularioCita" action="" method="post" style="display:none;">
                        <input type="hidden" id="citaIDSeleccionada" name="citaID">
                    </form>
                </div>
            </div>
        </div>
</body>
<script>
    let citas = <?php
                $citas = listarCitasPendientes($pdo);
                echo json_encode($citas);
                ?>;
    let paginaActual = 1;
    const tarjetasPorPagina = 5;

    function renderizarTarjetas(citas, pagina = 1) {
        const inicio = (pagina - 1) * tarjetasPorPagina;
        const fin = inicio + tarjetasPorPagina;
        const citasPaginadas = citas.slice(inicio, fin);

        const citasContenedor = document.getElementById('citas-contenedor');
        citasContenedor.innerHTML = '';

        citasPaginadas.forEach(cita => {
            const tarjeta = document.createElement('div');
            tarjeta.classList.add('tarjeta');
            tarjeta.innerHTML = `
                <p><strong>Vehículo:</strong> ${cita.marca} ${cita.modelo} ${cita.anio}</p>
                <p><strong>Cliente:</strong> ${cita.nombre} ${cita.apellido_paterno} ${cita.apellido_materno}</p>
                <p><strong>Servicio:</strong> ${cita.servicio_solicitado}</p>
                <button onclick="seleccionarCita(${cita.citaID})" class="btn btn-dark w-100">Seleccionar</button>
            `;
            citasContenedor.appendChild(tarjeta);
        });

        renderizarPaginacion(citas.length, pagina);
    }

    function renderizarPaginacion(totalElementos, paginaActual) {
        const totalPaginas = Math.ceil(totalElementos / tarjetasPorPagina);
        const paginacionContenedor = document.getElementById('paginacion');
        paginacionContenedor.innerHTML = '';

        for (let i = 1; i <= totalPaginas; i++) {
            const enlacePagina = document.createElement('a');
            enlacePagina.innerText = i;
            enlacePagina.href = '#';
            if (i === paginaActual) {
                enlacePagina.classList.add('activo');
            }
            enlacePagina.addEventListener('click', (e) => {
                e.preventDefault();
                renderizarTarjetas(citas, i);
            });
            paginacionContenedor.appendChild(enlacePagina);
        }
    }

    function seleccionarCita(citaID) {
        document.getElementById('citaIDSeleccionada').value = citaID;
        document.getElementById('formularioCita').submit();
    }

    document.getElementById('buscar').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const citasFiltradas = citas.filter(cita =>
            cita.marca.toLowerCase().includes(query) ||
            cita.modelo.toLowerCase().includes(query)
        );
        renderizarTarjetas(citasFiltradas, 1);
    });

    renderizarTarjetas(citas, paginaActual);
</script>
<script>
    $(document).ready(function() {
        if ($('#staticBackdrop').length) {
            $('#staticBackdrop').modal('show');
        }
    });
</script>


<script>
        $(document).ready(function() {
            if ($('#staticBackdrop').length) {
                $('#staticBackdrop').modal('show');
            }
        });
        document.getElementById('x').addEventListener('submit', function(event) {
            let valid = true;
            const buscar = document.getElementById('buscar').value;
            // Validar cammpo
            if (/\d/.test(buscar)) {
                document.getElementById('buscar').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('buscar').classList.remove('is-invalid');
            }
            if (!valid) {
                event.preventDefault();
            }
        });
        function validarLetras(event) {
            const input = event.target;
            input.value = input.value.replace(/[^a-zA-Z]/g, '');
        }
        document.getElementById('buscar').addEventListener('input', validarLetras);

    </script>
</html>