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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
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
    </style>
</head>
<body>
    <div class="wrapper">
    <?php include 'vabr.php'; ?>
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
                        // si existe clinete- tajeta con todos los datos de los clientes
                        if (!empty($clientes)) {
                            foreach ($clientes as $cliente) {
                                echo "<div class='col-md-4 mb-3'>";
                                echo "<div class='card' style='width: 100%;'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>{$cliente->nombre_completo}</h5>";
                                echo "<hr>";
                                echo "<p class='card-text'><strong>Correo:</strong> {$cliente->correo}</p>";
                                echo "<p class='card-text'><strong>Teléfono:</strong> {$cliente->telefono}</p>";
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
        // Esto permite mostrar el enlace a la página anterior solo si no estamos en la primera página
        if ($pagina_actual > 1): ?>
            <li class="page-item">
                <!-- Enlace para la página anterior -->
                <!-- Construye la URL para la página anterior y mantiene el término de búsqueda -->
                <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?pagina=' . ($pagina_actual - 1) . '&buscar=' . urlencode($buscar); ?>">Anterior</a>
            </li>
        <?php endif; ?>

        <?php 
        // Bucle para generar enlaces a todas las páginas disponibles
        // Muestra cada número de página como un enlace
        for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?php echo ($pagina_actual === $i) ? 'active' : ''; ?>">
                <!-- Enlace a la página actual -->
                <!-- Marca la página actual con la clase 'active' para destacarla -->
                <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?pagina=' . $i . '&buscar=' . urlencode($buscar); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php 
        // Verifica si la página actual es menor que el número total de páginas
        // Esto permite mostrar el enlace a la página siguiente solo si no estamos en la última página
        if ($pagina_actual < $total_paginas): ?>
            <li class="page-item">
                <!-- Enlace para la página siguiente -->
                <!-- Construye la URL para la página siguiente y mantiene el término de búsqueda -->
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

<?php
// Desconectar la base de datos al finalizar
$conexion->desconectar();
?>
