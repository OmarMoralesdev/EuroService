<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTARIO DE INSUMOS</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #B2B2B2;
            margin: 0;
            padding: 0;
        }
        .table {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
        }
        .main {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px;
            height: 10vh;
            box-sizing: border-box;
        }
        .table-wrapper {
            max-height: calc(75vh - 60px);
            overflow-y: auto;
            width: 100%;
            padding-right: 20px;
            margin-right: 50px; 
        }
        .stock-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2 style="text-align: center;">INVENTARIO</h2>
                <div class="form-container">
                    <button type="button" class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#addInsumoModal">Agregar Insumo</button>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">NOMBRE</th>
                                    <th scope="col">DESCRIPCIÓN</th>
                                    <th scope="col">PRECIO</th>
                                    <th scope="col">CATEGORÍA</th>
                                    <th scope="col">UBICACIÓN</th>
                                    <th scope="col">CANTIDAD EN STOCK</th>
                                    <th scope="col">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();

                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    if (isset($_POST['addInsumo'])) {
                                        $nombre = trim($_POST['nombre']);
                                        $descripcion = trim($_POST['descripcion']);
                                        $precio = trim($_POST['precio']);
                                        $categoriaID = trim($_POST['categoriaID']);
                                        $cantidad_stock = trim($_POST['cantidad_stock']);
                                        $ubicacion = trim($_POST['ubicacion']);
                                        $proveedorID = trim($_POST['proveedorID']);

                                        if (!empty($nombre) && !empty($descripcion) && !empty($precio) && !empty($categoriaID) && !empty($cantidad_stock) && !empty($ubicacion) && !empty($proveedorID)) {
                                            // Insertar el insumo en la tabla de insumos
                                            $stmt = $pdo->prepare("INSERT INTO insumos (nombre, descripcion, precio, categoriaID) VALUES (:nombre, :descripcion, :precio, :categoriaID)");
                                            $stmt->bindParam(':nombre', $nombre);
                                            $stmt->bindParam(':descripcion', $descripcion);
                                            $stmt->bindParam(':precio', $precio);
                                            $stmt->bindParam(':categoriaID', $categoriaID);

                                            if ($stmt->execute()) {
                                                $insumoID = $pdo->lastInsertId();

                                                // Insertar en la tabla insumo_proveedor
                                                $stmt = $pdo->prepare("INSERT INTO insumo_proveedor (insumoID, proveedorID, precio) VALUES (:insumoID, :proveedorID, :precio)");
                                                $stmt->bindParam(':insumoID', $insumoID);
                                                $stmt->bindParam(':proveedorID', $proveedorID);
                                                $stmt->bindParam(':precio', $precio);

                                                if ($stmt->execute()) {
                                                    $insumo_proveedorID = $pdo->lastInsertId();

                                                    // Insertar en la tabla inventarios
                                                    $stmt = $pdo->prepare("INSERT INTO inventarios (insumo_proveedorID, ubicacion, cantidad_stock) VALUES (:insumo_proveedorID, :ubicacion, :cantidad_stock)");
                                                    $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                                    $stmt->bindParam(':ubicacion', $ubicacion);
                                                    $stmt->bindParam(':cantidad_stock', $cantidad_stock);

                                                    if ($stmt->execute()) {
                                                        echo "<div class='alert alert-success' role='alert'>Insumo agregado exitosamente.</div>";
                                                    } else {
                                                        echo "<div class='alert alert-danger' role='alert'>Error al agregar el insumo al inventario.</div>";
                                                    }
                                                } else {
                                                    echo "<div class='alert alert-danger' role='alert'>Error al agregar el insumo y proveedor.</div>";
                                                }
                                            } else {
                                                echo "<div class='alert alert-danger' role='alert'>Error al agregar el insumo.</div>";
                                            }
                                        } else {
                                            echo "<div class='alert alert-danger' role='alert'>Por favor, complete todos los campos.</div>";
                                        }
                                    } elseif (isset($_POST['incrementar'])) {
                                        $insumo_proveedorID = $_POST['insumo_proveedorID'];
                                        $stmt = $pdo->prepare("UPDATE inventarios SET cantidad_stock = cantidad_stock + 1 WHERE insumo_proveedorID = :insumo_proveedorID");
                                        $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                        $stmt->execute();
                                    } elseif (isset($_POST['disminuir'])) {
                                        $insumo_proveedorID = $_POST['insumo_proveedorID'];
                                        $stmt = $pdo->prepare("UPDATE inventarios SET cantidad_stock = cantidad_stock - 1 WHERE insumo_proveedorID = :insumo_proveedorID AND cantidad_stock > 0");
                                        $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                        $stmt->execute();
                                    }
                                }

                                // Código para mostrar los insumos en la tabla
                                $stmt = $pdo->prepare("
                                    SELECT i.nombre, i.descripcion, i.precio, c.nombre AS categoria, inv.cantidad_stock, inv.ubicacion, inv.insumo_proveedorID
                                    FROM insumos i
                                    JOIN categorias c ON i.categoriaID = c.categoriaID
                                    JOIN inventarios inv ON i.insumoID = inv.insumo_proveedorID
                                    ORDER BY i.nombre ASC;
                                ");
                                $stmt->execute();
                                $insumos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($insumos as $insumo) {
                                    echo '<tr>';
                                    echo '<td>' . $insumo['nombre'] . '</td>';
                                    echo '<td>' . $insumo['descripcion'] . '</td>';
                                    echo '<td>' . $insumo['precio'] . '</td>';
                                    echo '<td>' . $insumo['categoria'] . '</td>';
                                    echo '<td>' . $insumo['ubicacion'] . '</td>';
                                    echo '<td>' . $insumo['cantidad_stock'] . '</td>';
                                    echo '<td class="stock-buttons">';
                                    echo '<form action="" method="POST" style="display:inline-block;">';
                                    echo '<input type="hidden" name="insumo_proveedorID" value="' . $insumo['insumo_proveedorID'] . '">';
                                    echo '<button type="submit" name="incrementar" class="btn btn-success btn-sm"><i class="lni lni-plus"></i></button>';
                                    echo '</form>';
                                    echo '<form action="" method="POST" style="display:inline-block;">';
                                    echo '<input type="hidden" name="insumo_proveedorID" value="' . $insumo['insumo_proveedorID'] . '">';
                                    echo '<button type="submit" name="disminuir" class="btn btn-danger btn-sm"><i class="lni lni-minus"></i></button>';
                                    echo '</form>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addInsumoModal" tabindex="-1" aria-labelledby="addInsumoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInsumoModalLabel">Agregar Insumo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoriaID" class="form-label">Categoría</label>
                            <select class="form-select" id="categoriaID" name="categoriaID" required>
                                <option value="" selected disabled>Seleccione una categoría</option>
                                <?php
                                $stmt = $pdo->prepare("SELECT categoriaID, nombre FROM categorias ORDER BY nombre ASC");
                                $stmt->execute();
                                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($categorias as $categoria) {
                                    echo '<option value="' . $categoria['categoriaID'] . '">' . $categoria['nombre'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="proveedorID" class="form-label">Proveedor</label>
                            <select class="form-select" id="proveedorID" name="proveedorID" required>
                                <option value="" selected disabled>Seleccione un proveedor</option>
                                <?php
                                $stmt = $pdo->prepare("SELECT proveedorID, nombre FROM proveedores ORDER BY nombre ASC");
                                $stmt->execute();
                                $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($proveedores as $proveedor) {
                                    echo '<option value="' . $proveedor['proveedorID'] . '">' . $proveedor['nombre'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cantidad_stock" class="form-label">Cantidad en Stock</label>
                            <input type="number" class="form-control" id="cantidad_stock" name="cantidad_stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" name="addInsumo" class="btn btn-dark">Agregar Insumo</button>
                        </div>
                    </form>
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
