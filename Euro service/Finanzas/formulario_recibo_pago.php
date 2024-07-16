<?php
session_start();

// Incluir el archivo que contiene la configuración de la base de datos
require '../includes/db.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['empleadoID'])) {
    header('Location: login.php'); 
    exit();
}

$empleadoID3 = $_SESSION['cuentaID'];
$username = $_SESSION['username'];



$empleadoID = $_SESSION['empleadoID'];
$empleadoID2 =  $_SESSION['personaID'];


// Crear una conexión a la base de datos
$con = new Database();
$pdo = $con->conectar();

// Función para obtener los detalles del empleado
function obtenerDetallesempleadopersona($pdo, $empleadoID) {
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

// Obtener los detalles del empleado
$usuario = obtenerDetallesempleadopersona($pdo, $empleadoID);
$nombreCompleto = $usuario ? $usuario['nombre'] . " " . $usuario['apellido_paterno'] . " " . $usuario['apellido_materno'] : "Desconocido";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibos de pago</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html';?> 
        <div class="main p-3">
            <div class="container">
                <h2>Generar Recibo de Pago</h2>
                <form action="finanzas.php" method="post" id="formCita" novalidate>
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
        </div>
    </div>
    <script src="app.js"></script>
</body>

</html>
