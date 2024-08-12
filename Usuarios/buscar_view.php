<?php
include '../includes/db.php';

// Iniciar conexión a la base de datos
$conexion = new Database();
$conexion->conectar();

// Inicializar la variable de búsqueda
$buscar = isset($_POST['buscar']) ? $_POST['buscar'] : '';

// Configuración de paginación
$resultados_por_pagina = 6;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $resultados_por_pagina;

// Consulta SQL base para obtener clientes (con limit y offset para paginación)
$consulta_clientes = "SELECT c.clienteID, CONCAT(p.nombre, ' ', p.apellido_paterno, ' ', p.apellido_materno) AS nombre_completo, p.correo, p.telefono
                      FROM CLIENTES c
                      INNER JOIN PERSONAS p ON c.personaID = p.personaID
                      WHERE c.activo = 'si'";

// Agregar filtro si hay criterio de búsqueda
if (!empty($buscar)) {
    $consulta_clientes .= " AND (p.nombre LIKE '%$buscar%' OR p.apellido_paterno LIKE '%$buscar%' OR p.apellido_materno LIKE '%$buscar%')";
}

$consulta_clientes .= " ORDER BY nombre_completo LIMIT $inicio, $resultados_por_pagina";

// Ejecutar consulta para obtener clientes
$clientes = $conexion->seleccionar($consulta_clientes);

// Contar el total de resultados para la paginación
$total_clientes_query = "SELECT COUNT(*) AS total FROM CLIENTES c INNER JOIN PERSONAS p ON c.personaID = p.personaID WHERE c.activo = 'si'";
if (!empty($buscar)) {
    $total_clientes_query .= " AND (p.nombre LIKE '%$buscar%' OR p.apellido_paterno LIKE '%$buscar%' OR p.apellido_materno LIKE '%$buscar%')";
}
$total_clientes_result = $conexion->seleccionar($total_clientes_query);
$total_clientes = $total_clientes_result[0]->total;
$total_paginas = ceil($total_clientes / $resultados_por_pagina);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        .card-body {
            padding: 1rem;
        }
        .card-title {
            margin-bottom: 0.5rem;
        }
        .card-text {
            margin-bottom: 0.25rem;
        }
        .pagination .page-link {
            color: white; 
            background-color: black;
            border-color: black; 
        }
        .pagination .page-link:hover {
            border-color: black; 
            background-color: #252525;
        }
        .pagination .page-item.active .page-link {
            background-color: #252525;
            border-color: #000; 
        }
        /* Ajustes para modal en dispositivos móviles */
        .modal-dialog {
            max-width: 90%;
            margin: 1.75rem auto;
        }
        .modal-content {
            border-radius: 0.3rem;
        }
        .modal-header, .modal-footer {
            padding: 1rem;
        }
        .modal-body {
            padding: 1rem;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-2">
            <div class="container">
                <h2 class="text-center">BUSCAR CLIENTES</h2>
                <div class="form-container">
                    <!-- Formulario de búsqueda -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar clientes..." name="buscar" value="<?php echo htmlspecialchars($buscar); ?>">
                            <button type="submit" class="btn btn-dark">Buscar</button>
                        </div>
                    </form>

                    <!-- Mostrar resultados de clientes -->
                    <div class="row mt-4">
                        <?php
                        // si existe clinete- tarjeta con todos los datos de los clientes
                        if (!empty($clientes)) {
                            foreach ($clientes as $cliente) {
                                // Consulta para obtener vehículos del cliente
                                $consulta_vehiculos = "SELECT * FROM VEHICULOS WHERE clienteID = {$cliente->clienteID}";
                                $vehiculos = $conexion->seleccionar($consulta_vehiculos);

                                echo "<div class='col-md-4 mb-3'>";
                                echo "<div class='card' style='width: 100%;'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>{$cliente->nombre_completo}</h5>";
                                echo "<hr>";
                                echo "<p class='card-text'><strong>Correo:</strong> {$cliente->correo}</p>";
                                echo "<p class='card-text'><strong>Teléfono:</strong> {$cliente->telefono}</p>";
                                echo "<br>";
                                echo "<div class='d-flex justify-content-center'>";
                                echo "<button type='button' class='btn btn-dark btn-md ml-2' style='width: 60%;' data-bs-toggle='modal' data-bs-target='#modalCliente{$cliente->clienteID}'> Ver vehículos</button>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                
                                // Modal de vehículos
                                echo "<div class='modal fade' id='modalCliente{$cliente->clienteID}' tabindex='-1' aria-labelledby='modalLabel{$cliente->clienteID}' aria-hidden='true'>";
                                echo "<div class='modal-dialog modal-dialog-centered modal-dialog-scrollable'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='modalLabel{$cliente->clienteID}'>Vehículos de {$cliente->nombre_completo}</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<div class='modal-body'>";
                                
                                if (!empty($vehiculos)) {
                                    echo "<div class='table-responsive'>";
                                    echo "<table class='table table-striped'>";
                                    echo "<thead><tr><th>Marca</th><th>Modelo</th><th>Año</th><th>Color</th><th>Kilometraje</th></tr></thead>";
                                    echo "<tbody>";
                                    foreach ($vehiculos as $vehiculo) {
                                        echo "<tr>";
                                        echo "<td>{$vehiculo->marca}</td>";
                                        echo "<td>{$vehiculo->modelo}</td>";
                                        echo "<td>{$vehiculo->anio}</td>";
                                        echo "<td>{$vehiculo->color}</td>";
                                        echo "<td>{$vehiculo->kilometraje}</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody>";
                                    echo "</table>";
                                    echo "</div>";
                                } else {
                                    echo "<p>No se encontraron vehículos para este cliente.</p>";
                                }
                                
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            // si no se encuentra ningun cliente
                            echo "<p class='text-center'>No se encontraron clientes.</p>";
                        }
                        ?>
                    </div>

                    <!-- Paginación -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center mt-4">
                            <?php 
                            // Verifica si la página actual es mayor que 1
                            if ($pagina_actual > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?pagina=' . ($pagina_actual - 1) . '&buscar=' . urlencode($buscar); ?>">Anterior</a>
                                </li>
                            <?php endif; ?>

                            <?php 
                            // Bucle para generar enlaces a todas las páginas disponibles
                            for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <li class="page-item <?php echo ($pagina_actual === $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?pagina=' . $i . '&buscar=' . urlencode($buscar); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php 
                            // Verifica si la página actual es menor que el número total de páginas
                            if ($pagina_actual < $total_paginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?pagina=' . ($pagina_actual + 1) . '&buscar=' . urlencode($buscar); ?>">Siguiente</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
