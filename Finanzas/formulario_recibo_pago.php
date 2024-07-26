<?php
require_once '../includes/db.php';
require_once '../vendor/autoload.php'; // Ajusta la ruta según donde tengas instalado TCPDF

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['empleadoID'])) {
    header('Location: login.php');
    exit();
}

$empleadoID = $_SESSION['empleadoID'];

// Crear una conexión a la base de datos
$con = new Database();
$pdo = $con->conectar();

function obtenerDetallesempleadopersona($pdo, $empleadoID)
{
    try {
        $sql = "SELECT nombre, apellido_paterno, apellido_materno FROM EMPLEADOS INNER JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID WHERE EMPLEADOS.empleadoID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empleadoID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
        return null;
    }
}

$usuario = obtenerDetallesempleadopersona($pdo, $empleadoID);
$nombreCompleto = $usuario ? $usuario['nombre'] . " " . $usuario['apellido_paterno'] . " " . $usuario['apellido_materno'] : "Desconocido";

$mostrarRecibo = true;
$mensaje = $fecha_recibo = $cliente_nombre = $cantidad_pagada_recibo = $receptor = '';
$reciboUrl = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clienteID_param = $_POST['campo'];
    $cantidad_pagada = $_POST['cantidad_pagada'];
    $receptor = $_POST['receptor'];

    try {
        $stmt = $pdo->prepare("CALL generarReciboPago(:nombre_cliente, :cantidad_pagada, :receptor, @mensaje, @fecha_recibo, @cliente_nombre, @cantidad_pagada_recibo)");
        $stmt->bindParam(':nombre_cliente', $clienteID_param, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad_pagada', $cantidad_pagada, PDO::PARAM_STR);
        $stmt->bindParam(':receptor', $receptor, PDO::PARAM_STR);
        $stmt->execute();

        $stmt->closeCursor();
        $result = $pdo->query("SELECT @mensaje AS mensaje, @fecha_recibo AS fecha_recibo, @cliente_nombre AS cliente_nombre, @cantidad_pagada_recibo AS cantidad_pagada_recibo")->fetch(PDO::FETCH_ASSOC);

        $mensaje = htmlspecialchars($result['mensaje']);
        $fecha_recibo = htmlspecialchars($result['fecha_recibo']);
        $cliente_nombre = htmlspecialchars($result['cliente_nombre']);
        $cantidad_pagada_recibo = htmlspecialchars($result['cantidad_pagada_recibo']);

        $mostrarRecibo = false;
        $reciboUrl = 'recibo_preview.php';
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Generar Recibo de Pago</title>
    <style>
        .recibo {
            width: 600px;
            border: 1px solid black;
            padding: 20px;
            font-family: Arial, sans-serif;
            margin-top: 20px;
        }

        .recibo-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .recibo-section {
            margin: 20px 0;
        }

        .recibo-label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }

        .recibo-field {
            display: inline-block;
            width: 400px;
            border-bottom: 1px solid black;
        }

        /* Estilos del modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Generar Recibo de Pago</h2>
                <div class="form-container">
                    <form action="" method="post" id="formCita" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                            <ul id="lista" class="list-group" style="display: none;"></ul>
                            <input type="hidden" id="clienteID" name="clienteID">
                            <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                            <label for="cantidad_pagada">Cantidad Pagada:</label>
                            <input type="text" id="cantidad_pagada" name="cantidad_pagada" class="form-control" required><br><br>
                            <label for="receptor">Nombre del Receptor:</label>
                            <input type="text" id="receptor" name="receptor" class="form-control" value="<?php echo htmlspecialchars($nombreCompleto); ?>" readonly required><br><br>
                            <input type="submit" value="Generar Recibo">
                        </div>
                    </form>
                </div>
                <?php if (!$mostrarRecibo && $reciboUrl) : ?>
    <div id="reciboModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Vista Previa del Recibo</h2>
            <div class="recibo">
                <div class="recibo-header">Recibo de Pago</div>
                <div class="recibo-section">
                    <div class="recibo-label">Fecha:</div>
                    <div class="recibo-field"><?php echo htmlspecialchars($fecha_recibo); ?></div>
                </div>
                <div class="recibo-section">
                    <div class="recibo-label">Cliente:</div>
                    <div class="recibo-field"><?php echo htmlspecialchars($cliente_nombre); ?></div>
                </div>
                <div class="recibo-section">
                    <div class="recibo-label">Cantidad Pagada:</div>
                    <div class="recibo-field"><?php echo htmlspecialchars($cantidad_pagada_recibo); ?></div>
                </div>
                <div class="recibo-section">
                    <div class="recibo-label">Receptor:</div>
                    <div class="recibo-field"><?php echo htmlspecialchars($receptor); ?></div>
                </div>
                <div class="recibo-section">
                    <div class="recibo-label">Firma:</div>
                    <div class="recibo-field"><?php echo htmlspecialchars($receptor); ?></div>
                </div>
            </div>
            <br>
            <a href="recibo_preview.php?cliente_nombre=<?php echo urlencode($cliente_nombre); ?>&cantidad_pagada_recibo=<?php echo urlencode($cantidad_pagada_recibo); ?>&fecha_recibo=<?php echo urlencode($fecha_recibo); ?>&receptor=<?php echo urlencode($receptor); ?>" class="btn btn-primary" target="_blank">Descargar PDF</a>
        </div>
    </div>
<?php endif; ?>


            </div>
        </div>
    </div>
    <script src="app.js"></script>
    <script>
        // Mostrar el modal de vista previa
        var modal = document.getElementById("reciboModal");
        var span = document.getElementsByClassName("close")[0];

        if (modal) {
            modal.style.display = "block";
        }

        if (span) {
            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    </script>
</body>

</html>