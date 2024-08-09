<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTARIO DE INSUMOS</title>
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
                                        
                                        // Validar que todos los campos estén completos
                                        if (!empty($nombre) && !empty($descripcion) && !empty($precio) && !empty($categoriaID) && !empty($cantidad_stock) && !empty($ubicacion) && !empty($proveedorID)) {
                                            
                                            try {
                                                // Iniciar una transacción
                                                $pdo->beginTransaction();
                                                
                                                // Insertar el insumo en la tabla de insumos
                                                $stmt = $pdo->prepare("INSERT INTO INSUMOS (nombre, descripcion, precio, categoriaID) VALUES (:nombre, :descripcion, :precio, :categoriaID)");
                                                $stmt->bindParam(':nombre', $nombre);
                                                $stmt->bindParam(':descripcion', $descripcion);
                                                $stmt->bindParam(':precio', $precio);
                                                $stmt->bindParam(':categoriaID', $categoriaID);
                                                
                                                if ($stmt->execute()) {
                                                    $insumoID = $pdo->lastInsertId();
                                                    
                                                    // Insertar en la tabla insumo_proveedor
                                                    $stmt = $pdo->prepare("INSERT INTO INSUMO_PROVEEDOR (insumoID, proveedorID, precio) VALUES (:insumoID, :proveedorID, :precio)");
                                                    $stmt->bindParam(':insumoID', $insumoID);
                                                    $stmt->bindParam(':proveedorID', $proveedorID);
                                                    $stmt->bindParam(':precio', $precio);
                                                    
                                                    if ($stmt->execute()) {
                                                        $insumo_proveedorID = $pdo->lastInsertId();
                                                        
                                                        // Insertar en la tabla inventarios
                                                        $stmt = $pdo->prepare("INSERT INTO INVENTARIOS (insumo_proveedorID, ubicacion, cantidad_stock) VALUES (:insumo_proveedorID, :ubicacion, :cantidad_stock)");
                                                        $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                                        $stmt->bindParam(':ubicacion', $ubicacion);
                                                        $stmt->bindParam(':cantidad_stock', $cantidad_stock);
                                                        
                                                        if ($stmt->execute()) {
                                                            
                                                            // Insertar una compra en la tabla COMPRAS
                                                            $stmt = $pdo->prepare("INSERT INTO COMPRAS (fecha_compra, tipo_compraID, total) VALUES (CURDATE(), 'administrativa', :total)");
                                                            $total = $precio * $cantidad_stock;
                                                            $stmt->bindParam(':total', $total);
                                                            
                                                            if ($stmt->execute()) {
                                                                $compraID = $pdo->lastInsertId();
                                                                
                                                                // Insertar en la tabla DETALLE_COMPRA
                                                                $stmt = $pdo->prepare("INSERT INTO DETALLE_COMPRA (compraID, insumo_proveedorID, cantidad, precio_unitario, subtotal) VALUES (:compraID, :insumo_proveedorID, :cantidad, :precio_unitario, :subtotal)");
                                                                $stmt->bindParam(':compraID', $compraID);
                                                                $stmt->bindParam(':insumo_proveedorID', $insumo_proveedorID);
                                                                $stmt->bindParam(':cantidad', $cantidad_stock);
                                                                $stmt->bindParam(':precio_unitario', $precio);
                                                                $subtotal = $precio * $cantidad_stock;
                                                                $stmt->bindParam(':subtotal', $subtotal);
                                                                
                                                                if ($stmt->execute()) {
                                                                    // Confirmar la transacción
                                                                    $pdo->commit();
                                                                    echo "<div class='alert alert-success' role='alert'>Insumo y compra agregados exitosamente.</div>";
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
                                                // Deshacer la transacción en caso de error
                                                $pdo->rollBack();
                                                echo "<div class='alert alert-danger' role='alert'>{$e->getMessage()}</div>";
                                            }
                                        } else {
                                            echo "<div class='alert alert-danger' role='alert'>Por favor, complete todos los campos.</div>";
                                        }
                                    }
                                }

                                // Código para mostrar los insumos en la tabla
                                $stmt = $pdo->prepare("
                                    SELECT I.nombre, I.descripcion, I.precio, C.nombre AS categoria, INV.cantidad_stock, INV.ubicacion, INV.insumo_proveedorID
                                    FROM INSUMOS I
                                    JOIN CATEGORIAS C ON I.categoriaID = C.categoriaID
                                    JOIN INSUMO_PROVEEDOR IP ON I.insumoID = IP.insumoID
                                    JOIN INVENTARIOS INV ON IP.insumo_proveedorID = INV.insumo_proveedorID
                                    ORDER BY I.nombre ASC;
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
                    <form action="Insumos.php" id="x" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoriaID" class="form-label">Categoría</label>
                            <select class="form-select" id="categoriaID" name="categoriaID" required>
                            
                                <?php
                                $stmt = $pdo->prepare("SELECT categoriaID, nombre FROM CATEGORIAS");
                                $stmt->execute();
                                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($categorias as $categoria) {
                                    echo '<option value="' . $categoria['categoriaID'] . '">' . $categoria['nombre'] . '</option>';
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
                                <!-- Opciones de proveedor desde la base de datos -->
                                <?php
                                $stmt = $pdo->prepare("SELECT proveedorID, nombre FROM PROVEEDORES");
                                $stmt->execute();
                                $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($proveedores as $proveedor) {
                                    echo '<option value="' . $proveedor['proveedorID'] . '">' . $proveedor['nombre'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="addInsumo" class="btn btn-primary">Agregar Insumo</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>