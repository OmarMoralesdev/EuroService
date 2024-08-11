<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTARIO</title>
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
        .alert {
            margin: 20px;
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
                    <button type="button" class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#reduceStockModal">Rebajar insumos</button>
                    <button type="button" class="btn btn-dark mb-3" data-bs-toggle="modal" data-bs-target="#increaseStockModal">Aumentar insumos</button>
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
                                    <th scope="col">PROVEEDOR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();

                                $message = '';

                                // Manejo de formulario de agregar insumo
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addInsumo'])) {
                                    $nombre = trim($_POST['nombre']);
                                    $descripcion = trim($_POST['descripcion']);
                                    $precio = trim($_POST['precio']);
                                    $categoriaID = trim($_POST['categoriaID']);
                                    $cantidad_stock = trim($_POST['cantidad_stock']);
                                    $ubicacion = trim($_POST['ubicacion']);
                                    $proveedorID = trim($_POST['proveedorID']);
                                    $tipo_compra = trim($_POST['tipo_compra']); 

                                    if (!empty($nombre) && !empty($descripcion) && !empty($precio) && !empty($categoriaID) && !empty($cantidad_stock) && !empty($ubicacion) && !empty($proveedorID) && !empty($tipo_compra)) {
                                        
                                        $validTipoCompra = ['1', '2'];
                                        if (!in_array($tipo_compra, $validTipoCompra)) {
                                            $message = "<div class='alert alert-danger' role='alert'>Tipo de compra inválido. Selecciona 'Administrativa' o 'Vehículo'.</div>";
                                        } else {
                                            try {
                                                $pdo->beginTransaction();

                                                $stmt = $pdo->prepare("
                                                    SELECT COUNT(*) 
                                                    FROM INSUMOS I
                                                    JOIN INSUMO_PROVEEDOR IP ON I.insumoID = IP.insumoID
                                                    WHERE I.nombre = :nombre AND IP.proveedorID = :proveedorID
                                                ");
                                                $stmt->bindParam(':nombre', $nombre);
                                                $stmt->bindParam(':proveedorID', $proveedorID);
                                                $stmt->execute();
                                                $count = $stmt->fetchColumn();

                                                if ($count > 0) {
                                                    throw new Exception('Este producto ya está registrado con este proveedor.');
                                                }

                                                $stmt = $pdo->prepare("INSERT INTO INSUMOS (nombre, descripcion, precio, categoriaID) VALUES (:nombre, :descripcion, :precio, :categoriaID)");
                                                $stmt->bindParam(':nombre', $nombre);
                                                $stmt->bindParam(':descripcion', $descripcion);
                                                $stmt->bindParam(':precio', $precio);
                                                $stmt->bindParam(':categoriaID', $categoriaID);
                                                if ($stmt->execute()) {
                                                    $insumoID = $pdo->lastInsertId();

                                                    $stmt = $pdo->prepare("INSERT INTO INSUMO_PROVEEDOR (insumoID, proveedorID, precio) VALUES (:insumoID, :proveedorID, :precio)");
                                                    $stmt->bindParam(':insumoID', $insumoID);
                                                    $stmt->bindParam(':proveedorID', $proveedorID);
                                                    $stmt->bindParam(':precio', $precio);
                                                    if ($stmt->execute()) {
                                                        $insumo_proveedorID = $pdo->lastInsertId();

                                                        $stmt = $pdo->prepare("INSERT INTO INVENTARIOS (insumo_proveedorID, ubicacion, cantidad_stock) VALUES (:insumo_proveedorID, :ubicacion, :cantidad_stock)");
                                                        $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                                        $stmt->bindParam(':ubicacion', $ubicacion);
                                                        $stmt->bindParam(':cantidad_stock', $cantidad_stock);
                                                        if ($stmt->execute()) {

                                                            $stmt = $pdo->prepare("INSERT INTO COMPRAS (fecha_compra, tipo_compraID, total) VALUES (CURDATE(), :tipo_compra, :total)");
                                                            $stmt->bindParam(':tipo_compra', $tipo_compra);
                                                            $total = $precio * $cantidad_stock;
                                                            $stmt->bindParam(':total', $total);
                                                            if ($stmt->execute()) {
                                                                $compraID = $pdo->lastInsertId();
                                                                
                                                                $stmt = $pdo->prepare("INSERT INTO DETALLE_COMPRA (compraID, insumo_proveedorID, cantidad, precio_unitario, subtotal) VALUES (:compraID, :insumo_proveedorID, :cantidad, :precio_unitario, :subtotal)");
                                                                $stmt->bindParam(':compraID', $compraID);
                                                                $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                                                $stmt->bindParam(':cantidad', $cantidad_stock);
                                                                $stmt->bindParam(':precio_unitario', $precio);
                                                                $subtotal = $precio * $cantidad_stock;
                                                                $stmt->bindParam(':subtotal', $subtotal);
                                                                
                                                                if ($stmt->execute()) {
                                                                    $pdo->commit();
                                                                    $message = "<div class='alert alert-success' role='alert'>Insumo y compra agregados exitosamente.</div>";
                                                                } else {
                                                                    throw new Exception('Error al agregar el detalle de la compra.');
                                                                }
                                                            } else {
                                                                throw new Exception('Error al registrar la compra.');
                                                            }
                                                        } else {
                                                            throw new Exception('Error al agregar el insumo al inventario.');
                                                        }
                                                    } else {
                                                        throw new Exception('Error al agregar el insumo y proveedor.');
                                                    }
                                                } else {
                                                    throw new Exception('Error al agregar el insumo.');
                                                }
                                            } catch (Exception $e) {
                                                $pdo->rollBack();
                                                $message = "<div class='alert alert-danger' role='alert'>{$e->getMessage()}</div>";
                                            }
                                        }
                                    } else {
                                        $message = "<div class='alert alert-danger' role='alert'>Por favor, complete todos los campos.</div>";
                                    }
                                }

                                // Manejo de formulario de reducción de stock
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reduceStock'])) {
                                    $insumo_proveedorID = trim($_POST['insumo_proveedorID']);
                                    $cantidad_reducir = trim($_POST['cantidad_reducir']);

                                    if (!empty($insumo_proveedorID) && !empty($cantidad_reducir)) {
                                        try {
                                            $pdo->beginTransaction();

                                            // Verificar existencia de insumo
                                            $stmt = $pdo->prepare("
                                                SELECT cantidad_stock 
                                                FROM INVENTARIOS 
                                                WHERE insumo_proveedorID = :insumo_proveedorID
                                            ");
                                            $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                            $stmt->execute();
                                            $current_stock = $stmt->fetchColumn();
                                            
                                            if ($current_stock === false) {
                                                throw new Exception('Insumo no encontrado.');
                                            }

                                            if ($cantidad_reducir > $current_stock) {
                                                throw new Exception('La cantidad no puede ser mayor al inventario.');
                                            }

                                            // Reducir stock
                                            $new_stock = $current_stock - $cantidad_reducir;
                                            $stmt = $pdo->prepare("
                                                UPDATE INVENTARIOS 
                                                SET cantidad_stock = :new_stock 
                                                WHERE insumo_proveedorID = :insumo_proveedorID
                                            ");
                                            $stmt->bindParam(':new_stock', $new_stock);
                                            $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                            if ($stmt->execute()) {
                                                $pdo->commit();
                                                $message = "<div class='alert alert-success' role='alert'>Cantidad reducida exitosamente.</div>";
                                            } else {
                                                throw new Exception('Error al actualizar el stock.');
                                            }
                                        } catch (Exception $e) {
                                            $pdo->rollBack();
                                            $message = "<div class='alert alert-danger' role='alert'>{$e->getMessage()}</div>";
                                        }
                                    } else {
                                        $message = "<div class='alert alert-danger' role='alert'>Por favor, complete todos los campos.</div>";
                                    }
                                }

                                // Manejo de formulario de aumento de stock
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['increaseStock'])) {
                                    $insumo_proveedorID = trim($_POST['insumo_proveedorID']);
                                    $cantidad_aumentar = trim($_POST['cantidad_aumentar']);
                                    $tipo_compra = trim($_POST['tipo_compra']);

                                    if (!empty($insumo_proveedorID) && !empty($cantidad_aumentar) && !empty($tipo_compra)) {
                                        try {
                                            $pdo->beginTransaction();

                                            // Verificar existencia de insumo
                                            $stmt = $pdo->prepare("
                                                SELECT cantidad_stock, precio 
                                                FROM INVENTARIOS 
                                                JOIN INSUMO_PROVEEDOR ON INVENTARIOS.insumo_proveedorID = INSUMO_PROVEEDOR.insumo_proveedorID
                                                WHERE INVENTARIOS.insumo_proveedorID = :insumo_proveedorID
                                            ");
                                            $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                            $stmt->execute();
                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);

                                            if (!$result) {
                                                throw new Exception('Insumo no encontrado.');
                                            }

                                            $current_stock = $result['cantidad_stock'];
                                            $precio = $result['precio'];
                                            $new_stock = $current_stock + $cantidad_aumentar;

                                            // Aumentar stock
                                            $stmt = $pdo->prepare("
                                                UPDATE INVENTARIOS 
                                                SET cantidad_stock = :new_stock 
                                                WHERE insumo_proveedorID = :insumo_proveedorID
                                            ");
                                            $stmt->bindParam(':new_stock', $new_stock);
                                            $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                            if ($stmt->execute()) {

                                                // Registrar la compra
                                                $stmt = $pdo->prepare("INSERT INTO COMPRAS (fecha_compra, tipo_compraID, total) VALUES (CURDATE(), :tipo_compra, :total)");
                                                $stmt->bindParam(':tipo_compra', $tipo_compra);
                                                $total = $precio * $cantidad_aumentar;
                                                $stmt->bindParam(':total', $total);
                                                if ($stmt->execute()) {
                                                    $compraID = $pdo->lastInsertId();
                                                    
                                                    $stmt = $pdo->prepare("INSERT INTO DETALLE_COMPRA (compraID, insumo_proveedorID, cantidad, precio_unitario, subtotal) VALUES (:compraID, :insumo_proveedorID, :cantidad, :precio_unitario, :subtotal)");
                                                    $stmt->bindParam(':compraID', $compraID);
                                                    $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                                    $stmt->bindParam(':cantidad', $cantidad_aumentar);
                                                    $stmt->bindParam(':precio_unitario', $precio);
                                                    $subtotal = $precio * $cantidad_aumentar;
                                                    $stmt->bindParam(':subtotal', $subtotal);
                                                    
                                                    if ($stmt->execute()) {
                                                        $pdo->commit();
                                                        $message = "<div class='alert alert-success' role='alert'>insumo aumentado y compra registrada exitosamente.</div>";
                                                    } else {
                                                        throw new Exception('Error al agregar el detalle de la compra.');
                                                    }
                                                } else {
                                                    throw new Exception('Error al registrar la compra.');
                                                }
                                            } else {
                                                throw new Exception('Error al actualizar el stock.');
                                            }
                                        } catch (Exception $e) {
                                            $pdo->rollBack();
                                            $message = "<div class='alert alert-danger' role='alert'>{$e->getMessage()}</div>";
                                        }
                                    } else {
                                        $message = "<div class='alert alert-danger' role='alert'>Por favor, complete todos los campos.</div>";
                                    }
                                }

                                if ($message) {
                                    echo $message;
                                }

                                // Obtener datos de insumos
                                $stmt = $pdo->prepare("
                                    SELECT I.nombre, I.descripcion, I.precio, C.nombre AS categoria, INV.cantidad_stock, INV.ubicacion, P.nombre AS proveedor, INV.insumo_proveedorID
                                    FROM INSUMOS I
                                    JOIN INVENTARIOS INV ON I.insumoID = INV.insumo_proveedorID
                                    JOIN CATEGORIAS C ON I.categoriaID = C.categoriaID
                                    JOIN INSUMO_PROVEEDOR IP ON I.insumoID = IP.insumoID
                                    JOIN PROVEEDORES P ON IP.proveedorID = P.proveedorID
                                    ORDER BY I.nombre ASC;
                                ");
                                $stmt->execute();
                                $insumos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($insumos as $insumo) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($insumo['nombre']) . '</td>';
                                    echo '<td>' . htmlspecialchars($insumo['descripcion']) . '</td>';
                                    echo '<td>' . htmlspecialchars($insumo['precio']) . '</td>';
                                    echo '<td>' . htmlspecialchars($insumo['categoria']) . '</td>';
                                    echo '<td>' . htmlspecialchars($insumo['ubicacion']) . '</td>';
                                    echo '<td>' . htmlspecialchars($insumo['cantidad_stock']) . '</td>';
                                    echo '<td>' . htmlspecialchars($insumo['proveedor']) . '</td>'; 
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para agregar insumo -->
        <div class="modal fade" id="addInsumoModal" tabindex="-1" aria-labelledby="addInsumoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addInsumoModalLabel">Agregar Insumo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                            </div>
                            <div class="mb-3">
                                <label for="categoriaID" class="form-label">Categoría</label>
                                <select class="form-select" id="categoriaID" name="categoriaID" required>
                                    <option value="" disabled selected>Selecciona una categoría</option>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT categoriaID, nombre FROM CATEGORIAS");
                                    $stmt->execute();
                                    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($categorias as $categoria) {
                                        echo '<option value="' . htmlspecialchars($categoria['categoriaID']) . '">' . htmlspecialchars($categoria['nombre']) . '</option>';
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
                            <div class="mb-3">
                                <label for="proveedorID" class="form-label">Proveedor</label>
                                <select class="form-select" id="proveedorID" name="proveedorID" required>
                                    <option value="" disabled selected>Selecciona un proveedor</option>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT proveedorID, nombre FROM PROVEEDORES");
                                    $stmt->execute();
                                    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($proveedores as $proveedor) {
                                        echo '<option value="' . htmlspecialchars($proveedor['proveedorID']) . '">' . htmlspecialchars($proveedor['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_compra" class="form-label">Tipo de Compra</label>
                                <select class="form-select" id="tipo_compra" name="tipo_compra" required>
                                    <option value="" disabled selected>Selecciona un tipo de compra</option>
                                    <option value="1">Administrativa</option>
                                    <option value="2">Vehículo</option>
                                </select>
                            </div>
                            <button type="submit" name="addInsumo" class="btn btn-primary">Agregar Insumo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para reducir stock -->
        <div class="modal fade" id="reduceStockModal" tabindex="-1" aria-labelledby="reduceStockModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reduceStockModalLabel">Reebajar insumos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="insumo_proveedorID" class="form-label">Insumo</label>
                                <select class="form-select" id="insumo_proveedorID" name="insumo_proveedorID" required>
                                    <option value="" disabled selected>Selecciona un insumo</option>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT INV.insumo_proveedorID, I.nombre 
                                                            FROM INVENTARIOS INV
                                                            JOIN INSUMOS I ON INV.insumo_proveedorID = I.insumoID
                                                            ORDER BY I.nombre");
                                    $stmt->execute();
                                    $insumos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($insumos as $insumo) {
                                        echo '<option value="' . htmlspecialchars($insumo['insumo_proveedorID']) . '">' . htmlspecialchars($insumo['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="cantidad_reducir" class="form-label">Cantidad a Reducir</label>
                                <input type="number" class="form-control" id="cantidad_reducir" name="cantidad_reducir" required>
                            </div>
                            <button type="submit" name="reduceStock" class="btn btn-primary">Rebajar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para aumentar stock -->
        <div class="modal fade" id="increaseStockModal" tabindex="-1" aria-labelledby="increaseStockModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="increaseStockModalLabel">Aumentar insumo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="insumo_proveedorID" class="form-label">Insumo</label>
                                <select class="form-select" id="insumo_proveedorID" name="insumo_proveedorID" required>
                                    <option value="" disabled selected>Selecciona un insumo</option>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT INV.insumo_proveedorID, I.nombre 
                                                            FROM INVENTARIOS INV
                                                            JOIN INSUMOS I ON INV.insumo_proveedorID = I.insumoID
                                                            ORDER BY I.nombre");
                                    $stmt->execute();
                                    $insumos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($insumos as $insumo) {
                                        echo '<option value="' . htmlspecialchars($insumo['insumo_proveedorID']) . '">' . htmlspecialchars($insumo['nombre']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="cantidad_aumentar" class="form-label">Cantidad a Aumentar</label>
                                <input type="number" class="form-control" id="cantidad_aumentar" name="cantidad_aumentar" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_compra" class="form-label">Tipo de Compra</label>
                                <select class="form-select" id="tipo_compra" name="tipo_compra" required>
                                    <option value="" disabled selected>Selecciona un tipo de compra</option>
                                    <option value="1">Administrativa</option>
                                    <option value="2">Vehículo</option>
                                </select>
                            </div>
                            <button type="submit" name="increaseStock" class="btn btn-primary">Aumentar</button>
                        </form>
                        <script>if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
