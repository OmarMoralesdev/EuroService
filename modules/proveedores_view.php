<?php
require '../includes/db.php';



$con = new Database();
$pdo = $con->conectar();

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];

    // Validar datos
    $errores = [];
    if (empty($nombre)) {
        $errores[] = "El nombre es requerido.";
    }
    if (empty($contacto)) {
        $errores[] = "El contacto es requerido.";
    }

    if (empty($errores)) {
        // Preparar la consulta SQL para insertar los datos
        $stmt = $pdo->prepare("INSERT INTO proveedores (nombre, contacto) VALUES (?, ?)");
        $stmt->execute([$nombre, $contacto]);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Proveedor registrado exitosamente.";
        } else {
            echo "Error al registrar el proveedor: " ;
        }
    } else {
        foreach ($errores as $error) {
            echo $error . "<br>";
        }
    }
}

// Obtener la lista de proveedores
$proveedores = [];
$result = $pdo->query("SELECT nombre, contacto FROM proveedores");
if ($result->rowCount() > 0) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $proveedores[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Proveedores</title>
</head>
<body>
<div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
    <div class="container">
    <h1>Registro de Proveedores</h1>
    <nav>
        <a href="#registrar">Registrar Proveedor</a> 
    </nav>

    <section id="registrar">
        <h2>Registrar Proveedor</h2>
        <form action="" method="post">
            <label for="nombre">Nombre:</label><br>
            <input type="text" id="nombre" name="nombre" required><br>
            <label for="contacto">Contacto:</label><br>
            <input type="text" id="contacto" name="contacto"><br>
            <input type="submit" value="Registrar">
        </form>
    </section>


        <h2>Lista de Proveedores</h2>
        <table border="1">
            <tr>
                <th>Nombre</th>
                <th>Contacto</th>
            </tr>
            <?php foreach ($proveedores as $proveedor): ?>
            <tr>
                <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['contacto']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    </div>
</body>
</html>
