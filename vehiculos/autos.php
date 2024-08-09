<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();
session_start();

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
    $anioactual = date('Y');

    // Validar datos del servidor
    $errors = [];

    // Validar marca: debe contener al menos un carácter alfabético y no debe contener números
    if (!preg_match('/[a-zA-Z]/', $marca) || preg_match('/\d/', $marca)) {
        $errors['marca'] = "La marca debe contener al menos un carácter alfabético y no debe contener números.";
    }

    // Validar modelo: debe contener al menos un carácter alfabético
    if (!preg_match('/[a-zA-Z]/', $modelo)) {
        $errors['modelo'] = "El modelo debe contener al menos un carácter alfabético.";
    }

    // Validar año: debe estar entre 1886 y el año actual, debe ser un número de 4 dígitos
    if (!preg_match('/^\d{4}$/', $anio) || $anio < 1886 || $anio > $anioactual) {
        $errors['anio'] = "El año debe ser un valor entre 1886 y el año actual.";
    }

    // Validar color: debe contener al menos un carácter alfabético y no debe contener números
    if (!preg_match('/[a-zA-Z]/', $color) || preg_match('/\d/', $color)) {
        $errors['color'] = "El color debe contener al menos un carácter alfabético y no debe contener números.";
    }

    // Validar kilometraje: debe ser solo números y hasta 8 dígitos
    if (!preg_match('/^\d{1,8}$/', $kilometraje)) {
        $errors['kilometraje'] = "El kilometraje debe ser un número de hasta 8 dígitos.";
    }

    // Validar placas: debe contener solo letras, números y espacios, y no más de 10 caracteres
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $placas) || strlen($placas) > 10) {
        $errors['placas'] = "Las placas deben contener solo letras, números y espacios, y no más de 10 caracteres.";
    }

    // Validar VIN: debe contener solo letras, números y espacios, y no más de 20 caracteres
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $vin) || strlen($vin) > 20) {
        $errors['vin'] = "El VIN debe contener solo letras, números y espacios, y no más de 20 caracteres.";
    }

    // Si hay errores, redirigir a la vista con los mensajes de error
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header("Location: autos_view.php");
        exit();
    }

    // Verificar si el VIN ya está registrado
    $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
    $stmtVerificar = $pdo->prepare($verificar);
    $stmtVerificar->execute([$vin]);

    if ($stmtVerificar->rowCount() > 0) {
        $_SESSION['error'] = "El vehículo ya está registrado.";
        header("Location: autos_view.php");
        exit();
    } else {
        // Insertar datos del vehículo en la base de datos
        $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?,'si')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['bien'] = "Vehículo registrado exitosamente.";
            header("Location: autos_view.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . $pdo->errorInfo()[2];
            header("Location: autos_view.php");
            exit();
        }
    }
}
?>
