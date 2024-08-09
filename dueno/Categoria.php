<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías e Insumos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
    <div class="main p-3">
        <div class="container">
            <h2 class="text-center">Categorías de insumos</h2>
            <div class="form-container">
                <?php
                include '../includes/db.php';
                $conexion = new Database();
                $pdo = $conexion->conectar();
                $alertMessage = '';
                $alertType = '';

                if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
                    $nombre = trim($_POST['nombre']);
                    $descripcion = trim($_POST['descripcion']);

                    if (empty($nombre) || empty($descripcion)) {
                        $alertMessage = 'No puedes dejar campos vacios';
                        $alertType = 'danger';
                    } else {
                        
                        $check_query = "SELECT COUNT(*) FROM CATEGORIAS WHERE nombre = :nombre";
                        $stmt = $pdo->prepare($check_query);
                        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                        $stmt->execute();
                        $exists = $stmt->fetchColumn();

                            }
                    $conexion->desconectar();
                }
                ?>

               
                <?php if ($alertMessage): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $alertMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row mt-3">
                    <?php
                    // Configuración de paginación
                    $items_per_page = 6;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $items_per_page;

                    // Consulta para obtener categorías
                    $consulta_categorias = "SELECT c.categoriaID, c.nombre AS categoria, c.descripcion
                                            FROM CATEGORIAS c
                                            ORDER BY c.nombre ASC
                                            LIMIT :offset, :items_per_page";
                    $stmt = $pdo->prepare($consulta_categorias);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
                    $stmt->execute();
                    $categorias = $stmt->fetchAll(PDO::FETCH_OBJ);

                    // Consulta para obtener el total de categorías
                    $total_categorias_query = "SELECT COUNT(*) AS total FROM CATEGORIAS";
                    $stmt = $pdo->prepare($total_categorias_query);
                    $stmt->execute();
                    $total_categorias_result = $stmt->fetch(PDO::FETCH_OBJ);
                    $total_categorias = $total_categorias_result->total;
                    $total_pages = ceil($total_categorias / $items_per_page);

                    if (is_array($categorias)) {
                        foreach ($categorias as $categoria) {
                            echo "<div class='col-md-4 mb-3'>";
                            echo "<div class='card' style='width: 95%;'>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>CATEGORÍA: {$categoria->categoria}</h5>";
                            echo "<hr>";
                            echo "<p class='card-text'>DESCRIPCIÓN: {$categoria->descripcion}</p>";
                            echo "</div>"; // Cierre de card-body

                            // Botón para ver insumos y proveedores
                            echo "<div class='card-footer d-flex justify-content-between align-items-center'>";
                            echo "<button type='button' class='btn btn-dark btn-md' style='width: 100%;' data-bs-toggle='modal' data-bs-target='#modal{$categoria->categoriaID}'>VER INSUMOS</button>";
                            echo "</div>"; // Cierre de card-footer

                            echo "</div>"; // Cierre de card
                            echo "</div>";

                            // Modal para mostrar insumos y proveedores
                            echo "<div class='modal fade' id='modal{$categoria->categoriaID}' tabindex='-1' aria-labelledby='modalLabel{$categoria->categoriaID}' aria-hidden='true'>";
                            echo "<div class='modal-dialog modal-lg'>";
                            echo "<div class='modal-content'>";
                            echo "<div class='modal-header'>";
                            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                            echo "</div>";
                            echo "<div class='modal-body'>";

                            $consulta_insumos = "SELECT i.nombre AS insumo, p.nombre AS proveedor, ip.precio
                                                FROM INSUMOS i
                                                JOIN INSUMO_PROVEEDOR ip ON i.insumoID = ip.insumoID
                                                JOIN PROVEEDORES p ON ip.proveedorID = p.proveedorID
                                                WHERE i.categoriaID = :categoriaID";
                            
                            $stmt = $pdo->prepare($consulta_insumos);
                            $stmt->bindValue(':categoriaID', $categoria->categoriaID, PDO::PARAM_INT);
                            $stmt->execute();
                            $insumos = $stmt->fetchAll(PDO::FETCH_OBJ);

                            if (is_array($insumos) && count($insumos) > 0) {
                                echo "<table class='table table-hover'>";
                                echo "<thead class='table-dark' align='center'>";
                                echo "<tr>";
                                echo "<th>INSUMO</th><th>PROVEEDOR</th><th>PRECIO</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                foreach ($insumos as $insumo) {
                                    echo "<tr align='center'>";
                                    echo "<td> {$insumo->insumo} </td>";
                                    echo "<td> {$insumo->proveedor} </td>";
                                    echo "<td> {$insumo->precio} </td>";
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";
                            } else {
                                echo "<p>NO HAY INSUMOS EN ESTA CATEGORÍA.</p>";
                            }
                            echo "</div>";
                            echo "<div class='modal-footer'>";
                            echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>CERRAR</button>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                    $conexion->desconectar();
                    ?>
                </div>
                <!-- Controles de paginación -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>
</html>
