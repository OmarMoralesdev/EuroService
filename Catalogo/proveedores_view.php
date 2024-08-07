<?php
require '../includes/db.php';

$con = new Database();
$pdo = $con->conectar();

$errorMensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['contacto'])) {
    $nombre = trim($_POST['nombre']);
    $contacto = trim($_POST['contacto']);

    $errores = [];
    if (empty($nombre)) {
        $errores[] = "El nombre es requerido.";
    }
  
    if (empty($contacto)) {
        $errores[] = "El contacto es requerido.";
    }
 

    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT * FROM PROVEEDORES WHERE nombre = ? OR contacto = ?");
     
        $stmt->execute([$nombre, $contacto]);
   
        if ($stmt->rowCount() > 0) {
            $errorMensaje = "El proveedor con este nombre o contacto ya existe.";
         
        }  else {
            $stmt = $pdo->prepare("INSERT INTO PROVEEDORES (nombre, contacto) VALUES (?, ?)");
            $stmt->execute([$nombre, $contacto]);
            unset($_POST['nombre']);
            unset($_POST['contacto']);

            header('Location: proveedores_view.php');
            exit();
        }
    } else {
        $errorMensaje = implode("", $errores);
    }
}

if (isset($_GET['ajax_search'])) {
    $search = $_GET['ajax_search'];
    $stmt = $pdo->prepare("SELECT nombre, contacto FROM PROVEEDORES WHERE nombre LIKE ?");
    $stmt->execute(["%$search%"]);
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($proveedores);
    exit();
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
$proveedores = [];
if ($search) {
    $stmt = $pdo->prepare("SELECT nombre, contacto FROM PROVEEDORES WHERE nombre LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT nombre, contacto FROM PROVEEDORES");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores</title>
    <style>
        .card-body {
            padding: 1rem;
        }
        .card-title {
            margin-bottom: 0.5rem;
        }
        .card-text {
            margin-bottom: 0.25rem;
        }
        input[type=text], input[type=email] {
            color: black; 
        }
        .btn {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            padding-top: 50px; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.4); 
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%; 
            max-width: 400px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 24px; 
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .alert {
            display: none;
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2 class="text-center">PROVEEDORES</h2>
                <div class="form-container">
                    <div class="mb-3">
                        <form id="searchForm" class="input-group">
                            <input type="text" id="search" name="search" placeholder="Buscar..." onkeyup="buscarProveedores()" class="form-control">
                        </form>
                    </div>
                    <div id="tablaProveedores" class="row mt-4">
                        <?php foreach ($proveedores as $proveedor): ?>
                            <div class='col-md-4 mb-3'>
                                <div class='card' style='width: 100%;'>
                                    <div class='card-body'>
                                        <h5 class='card-title'><?php echo htmlspecialchars($proveedor['nombre']); ?></h5>
                                        <hr>
                                        <p class='card-text'><strong>Contacto:</strong> <?php echo htmlspecialchars($proveedor['contacto']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <nav>
                        <a href="#registrar" id="openModalBtn" class="btn btn-dark">Registrar nuevo Proveedor</a>
                    </nav>
                    <div id="myModal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Registrar Proveedor</h2>
                            <div class="alert" id="errorAlert"><?php echo htmlspecialchars($errorMensaje); ?></div>
                            <form action="" method="post">
                                <label for="nombre">Nombre:</label><br>
                                <input type="text" id="nombre" name="nombre" class="form-control" required style="width: 100%;"><br>
                                <label for="contacto">Contacto:</label><br>
                                <input type="text" id="contacto" name="contacto" class="form-control" required style="width: 100%;"><br>
                                <input type="submit" value="Registrar" class="btn btn-dark" style="width: 100%;">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("openModalBtn");
        var span = document.getElementsByClassName("close")[0];
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

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
                    var output = '';
                    for (var i in proveedores) {
                        output += "<div class='col-md-4 mb-3'>";
                        output += "<div class='card' style='width: 100%;'>";
                        output += "<div class='card-body'>";
                        output += "<h5 class='card-title'>" + proveedores[i].nombre + "</h5>";
                        output += "<hr>";
                        output += "<p class='card-text'><strong>Contacto:</strong> " + proveedores[i].contacto + "</p>";
                        output += "</div>";
                        output += "</div>";
                        output += "</div>";
                    }
                    document.getElementById('tablaProveedores').innerHTML = output;
                }
            }
            xhr.send();
        }

        <?php if (!empty($errorMensaje)): ?>
        document.getElementById("errorAlert").style.display = "block";
        modal.style.display = "block";
        <?php endif; ?>
    </script>
</body>
</html>
