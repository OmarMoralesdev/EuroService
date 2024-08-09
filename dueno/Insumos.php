<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insumos</title>
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
    <?php include 'vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2 style="text-align: center;">INVENTARIO</h2>
                <div class="form-container">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">NOMBRE</th>
                                    <th scope="col">DESCRIPCIÓN</th>
                                    <th scope="col">PRECIO</th>
                                    <th scope="col">CATEGORÍA</th>
                                    <th scope="col">PROVEEDOR</th>
                                    <th scope="col">UBICACIÓN</th>
                                    <th scope="col">CANTIDAD EN STOCK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();

                                // Código para mostrar los insumos en la tabla
                                $stmt = $pdo->prepare("
                                    SELECT i.nombre, i.descripcion, i.precio, c.nombre AS categoria, inv.cantidad_stock, inv.ubicacion, inv.insumo_proveedorID,
                                    p.nombre as proveedor
                                    FROM INSUMOS i
                                    JOIN CATEGORIAS c ON i.categoriaID = c.categoriaID
                                    JOIN INVENTARIOS inv ON i.insumoID = inv.insumo_proveedorID
                                    JOIN INSUMO_PROVEEDOR ip ON ip.insumoID = i.insumoID
                                    JOIN PROVEEDORES p ON ip.proveedorID = p.proveedorID
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
                                    echo '<td>' . $insumo['proveedor'] . '</td>';
                                    echo '<td>' . $insumo['ubicacion'] . '</td>';
                                    echo '<td>' . $insumo['cantidad_stock'] . '</td>';
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
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
