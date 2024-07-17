<?php
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

// Verificar si se ha enviado el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['contacto'])) {
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

        // Redirigir después de insertar para evitar doble registro al refrescar
        header('Location: proveedores_view.php');
        exit();
    } else {
        foreach ($errores as $error) {
            echo $error . "<br>";
        }
    }
}

// Manejar la solicitud de búsqueda AJAX
if (isset($_GET['ajax_search'])) {
    $search = $_GET['ajax_search'];
    $stmt = $pdo->prepare("SELECT nombre, contacto FROM proveedores WHERE nombre LIKE ?");
    $stmt->execute(["%$search%"]);
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($proveedores);
    exit();
}

// Obtener la consulta de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener la lista de proveedores
$proveedores = [];
if ($search) {
    $stmt = $pdo->prepare("SELECT nombre, contacto FROM proveedores WHERE nombre LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT nombre, contacto FROM proveedores");
}

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $proveedores[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores</title>
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            padding-top: 100px; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
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
        .mover-derecha {
            margin-left: 50%;
        }
    </style>
</head>
<body>
<div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
        <div class="container">
            <h2>PROVEEDORES </h2>
                <div class="form-container">
            <div class="mover-derecha">
                <h2>Buscar Proveedores</h2>
                <form id="searchForm">
                    <input type="text" id="search" name="search" placeholder="Buscar..." onkeyup="buscarProveedores()">
                </form>
            </div>
            <h2>Lista de Proveedores</h2>
            <table border="1" id="tablaProveedores">
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
            <div class="container">
            <nav>
                <a href="#registrar" id="openModalBtn">Registrar nuevo Proveedor</a>
            </nav>

            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Registrar Proveedor</h2>
                    <form action="" method="post">
                        <label for="nombre">Nombre:</label><br>
                        <input type="text" id="nombre" name="nombre" required><br>
                        <label for="contacto">Contacto:</label><br>
                        <input type="text" id="contacto" name="contacto" require><br>
                        <input type="submit" value="Registrar">
                    </form>
                </div>
            </div>
        </div>
        
    </div>
    
</div>


<script>
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("openModalBtn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function buscarProveedores() {
        var search = document.getElementById('search').value;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?ajax_search=' + search, true);
        xhr.onload = function() {
            if (this.status == 200) {
                var proveedores = JSON.parse(this.responseText);
                var output = '<tr><th>Nombre</th><th>Contacto</th></tr>';
                for (var i in proveedores) {
                    output += '<tr><td>' + proveedores[i].nombre + '</td><td>' + proveedores[i].contacto + '</td></tr>';
                }
                document.getElementById('tablaProveedores').innerHTML = output;
            }
        }
        xhr.send();
    }
</script>

</body>
</html>
