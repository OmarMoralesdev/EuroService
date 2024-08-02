<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
session_start();
$errors = [];
$success = '';
$showModal = false;
$showInspeccionForm = false;
$vehiculoID = '';
$continuidad = false;


// Comprobar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si las claves existen en el array $_POST
    $clienteID = isset($_POST['clienteID']) ? $_POST['clienteID'] : '';
    $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $kilometraje = isset($_POST['kilometraje']) ? trim($_POST['kilometraje']) : '';
    $placas = isset($_POST['placas']) ? trim($_POST['placas']) : '';
    $vin = isset($_POST['vin']) ? trim($_POST['vin']) : '';
    $continuidad = isset($_POST['continuidad']) ? true : false;
    
    $currentYear = date('Y');

    if ($anio < 1886 || $anio > $currentYear) {
        $_SESSION['error'] = "El año debe estar entre 1886 y el año actual.";
    }

    if (empty($errors)) {
        $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
        $stmtVerificar = $pdo->prepare($verificar);
        $stmtVerificar->execute([$vin]);

        if ($stmtVerificar->rowCount() > 0) {
            $_SESSION['error'] = "El vehículo ya está registrado.";
        } else {
            $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin,activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?,'si')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

            if ($stmt->rowCount() > 0) {

                $_SESSION['vehiculo'] = $vehiculoID = $pdo->lastInsertId();

                if (!$continuidad) {
                    $showInspeccionForm = true;
                } else {


                    $_SESSION['bien'] = "Vehículo registrado exitosamente.";
                }
            } else {
                $_SESSION['error'] = "Error: " . $pdo->errorInfo()[2];
            }
        }
    }
}
?>