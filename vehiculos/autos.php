<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = isset($_POST['clienteID']) ? $_POST['clienteID'] : '';
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $kilometraje = isset($_POST['kilometraje']) ? trim($_POST['kilometraje']) : '';
    $placas = isset($_POST['placas']) ? trim($_POST['placas']) : '';
    $vin = isset($_POST['vin']) ? trim($_POST['vin']) : '';
    $anioactual = date('Y');

    // Verificar si el VIN ya está registrado
    $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
    $stmtVerificar = $pdo->prepare($verificar);
    $stmtVerificar->execute([$vin]);

    if ($marca == "" || $modelo == "" || $anio == "" || $color == "" || $kilometraje == "" || $placas == "" || $vin == "") {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        $_SESSION['form_values'] = $_POST; // Guardar valores del formulario
        header("Location: autos_view.php");
        exit();
    }

    if ($stmtVerificar->rowCount() > 0) {
        $_SESSION['error'] = "El vehículo ya está registrado.";
        $_SESSION['form_values'] = $_POST; // Guardar valores del formulario
        header("Location: autos_view.php");
        exit();
    } else {
        // Insertar datos del vehículo en la base de datos
        $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?,'si')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['bien'] = "Vehículo registrado exitosamente.";
            unset($_SESSION['form_values']); // Limpiar valores del formulario en caso de éxito
            header("Location: autos_view.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $pdo->errorInfo()[2];
            $_SESSION['form_values'] = $_POST; // Guardar valores del formulario
            header("Location: autos_view.php");
            exit();
        }
    }
}
?>
