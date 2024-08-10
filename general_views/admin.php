<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CITAS PENDIENTES</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
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
        .atrasado {
            color: red;
        }
        
        .help-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #b3b3b3; /* Color de fondo del ícono */
    color: black;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 30px;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}

.help-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.help-modal-content {
    background-color: #222;
    color: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #fff;
    text-decoration: none;
    cursor: pointer;
}

    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2 style="text-align: center;">CITAS PENDIENTES</h2>
                <div class="form-container">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">VEHICULO</th>
                                    <th scope="col">SERVICIO SOLICITADO</th>
                                    <th scope="col">FECHA DE CITA</th>
                                    <th scope="col">TIEMPO RESTANTE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();

                                $stmt = $pdo->prepare("
                                SELECT c.*, v.marca AS vehiculo, 
                                    DATEDIFF(c.fecha_cita, CURDATE()) AS dias_restantes
                                FROM CITAS c 
                                JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
                                WHERE c.estado = 'pendiente' 
                                ORDER BY c.fecha_cita ASC;
                                ");

                                $stmt->execute();
                                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($citas as $cita) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($cita['vehiculo']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['servicio_solicitado']) . '</td>';
                                    echo '<td>' . htmlspecialchars($cita['fecha_cita']) . '</td>';
                                
                                    // Asignar tiempo restante basado en los días restantes
                                    $diasRestantes = $cita['dias_restantes'];
                                    $tiempoRestante = ($diasRestantes < 0) ? 'Atrasado' : (($diasRestantes == 0) ? 'Hoy' : $diasRestantes . ' días');
                                    $claseTiempoRestante = ($diasRestantes < 0) ? 'atrasado' : '';
                                
                                    echo '<td class="' . $claseTiempoRestante . '">' . $tiempoRestante . '</td>';
                                
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
          <!-- Modal de Ayuda -->
          <div class="help-modal" id="helpModal">
        <div class="help-modal-content">
            <span class="close" id="closeHelpModal">&times;</span>
            <h5>¿Cómo se usa?</h5>
            <HR>
            <p> PROVEEDORES <br>
            En esta ventana se mostrará un listado con todos los proveedores y sus respectivos datos de contacto.
            <HR>ENTREGADOS <br>
            En esta ventana en la cual registrarás los servicios a los vehículos totalmente completados y entregados a los clientes.
            <HR>INVENTARIO <br>
            Este modulo cuenta con 2 submodulos con funciones específicas<br> CATEGORÍA: Muestra una tabla con el nombre de la categoría, cuenta con los productos, su respectivo proveedor y precio <br><br> INSUMOS: Se muestrá un listado con todos los insumos y sus respectivos datos, adicional a esto tendrás un contador con la cantidad de productos con existencia.
<hr>CITAS <br>
Este modulo cuenta con 2 submodulos con funciones específicas<br> REGISTRAR: Muestra una tabla en la cual puedes buscar a los empleados mediante un buscador para registrarles una cita unicamente seleccionando su vehículo, servicio y fecha de la cita<br><br> EDITAR: Se muestrá un listado con todas las citas pendietes en la cual puedes modificar losy sus respectivos datos, adicional a esto tendrás un contador con la cantidad de productos con existencia.
<HR>
ORDEN <HR>
CLIENTES <HR>
VEHICULOS <HR>
EMPLEADOS <HR>
FINANZAS Y PAGOS 
</p>
        </div>
    </div>
    <!-- Ícono de Ayuda -->
    <div class="help-icon" id="helpIcon">
        ?
    </div>

    <script>
        // Funcionalidad del modal de ayuda
        document.getElementById('helpIcon').addEventListener('click', function() {
            document.getElementById('helpModal').style.display = 'flex';
        });

        document.getElementById('closeHelpModal').addEventListener('click', function() {
            document.getElementById('helpModal').style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('helpModal')) {
                document.getElementById('helpModal').style.display = 'none';
            }
        });
        </script>
</body>
</html>
