<?php
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteID = $_POST['clienteID'];
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $anio = trim($_POST['anio']);
    $color = trim($_POST['color']);
    $kilometraje = trim($_POST['kilometraje']);
    $placas = trim($_POST['placas']);
    $vin = trim($_POST['vin']);

    $currentYear = date('Y');

    if ($anio < 1886 || $anio > $currentYear) {
        $errors['anio'] = "El año debe estar entre 1886 y el año actual.";
    }

    if (empty($errors)) {
        // Verificar si el VIN ya está registrado
        $verificar = "SELECT * FROM VEHICULOS WHERE vin = ?";
        $stmtVerificar = $pdo->prepare($verificar);
        $stmtVerificar->execute([$vin]);

        if ($stmtVerificar->rowCount() > 0) {
            $errors['vin'] = "El vehículo ya está registrado.";
        } else {
            $sql = "INSERT INTO VEHICULOS (clienteID, marca, modelo, anio, color, kilometraje, placas, vin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$clienteID, $marca, $modelo, $anio, $color, $kilometraje, $placas, $vin]);

            if ($stmt->rowCount() > 0) {
                $success = "Vehículo registrado exitosamente.";
            } else {
                $errors['general'] = "Error: " . $pdo->errorInfo()[2];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehículo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        .is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include '../includes/vabr.html'; ?>
    <div class="main">
        <div class="container">
            <h2>REGISTRAR VEHÍCULO</h2>
            <div class="form-container">
                <br>
                <form id="formCita" action="autos.php" method="POST" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="campo" name="campo" placeholder="Buscar cliente..." required>
                        <ul id="lista" class="list-group" style="display: none;"></ul>
                        <input type="hidden" id="clienteID" name="clienteID">
                        <div class="invalid-feedback">Debes seleccionar un cliente.</div>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" maxlength="30" class="form-control <?php echo isset($errors['marca']) ? 'is-invalid' : ''; ?>" placeholder="Introduce la marca del vehículo" value="<?php echo htmlspecialchars($marca ?? '', ENT_QUOTES); ?>" required>
                        <div class="invalid-feedback"><?php echo $errors['marca'] ?? ''; ?></div>

                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" maxlength="30" class="form-control <?php echo isset($errors['modelo']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el modelo del vehículo" value="<?php echo htmlspecialchars($modelo ?? '', ENT_QUOTES); ?>" required>
                        <div class="invalid-feedback"><?php echo $errors['modelo'] ?? ''; ?></div>

                        <label for="anio">Año:</label>
                        <input type="number" id="anio" name="anio" min="1886" max="<?= date('Y') ?>" maxlength="4" class="form-control <?php echo isset($errors['anio']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el año del vehículo" value="<?php echo htmlspecialchars($anio ?? '', ENT_QUOTES); ?>" required>
                        <div class="invalid-feedback"><?php echo $errors['anio'] ?? ''; ?></div>

                        <label for="color">Color:</label>
                        <input type="text" id="color" name="color" maxlength="33" class="form-control <?php echo isset($errors['color']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el color del vehículo" value="<?php echo htmlspecialchars($color ?? '', ENT_QUOTES); ?>" required>
                        <div class="invalid-feedback"><?php echo $errors['color'] ?? ''; ?></div>

                        <label for="kilometraje">Kilometraje:</label>
                        <input type="text" id="kilometraje" name="kilometraje" maxlength="8" class="form-control <?php echo isset($errors['kilometraje']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el kilometraje del vehículo" value="<?php echo htmlspecialchars($kilometraje ?? '', ENT_QUOTES); ?>" required>
                        <div class="invalid-feedback"><?php echo $errors['kilometraje'] ?? ''; ?></div>

                        <label for="placas">Placas:</label>
                        <input type="text" id="placas" name="placas" maxlength="10" class="form-control <?php echo isset($errors['placas']) ? 'is-invalid' : ''; ?>" placeholder="Introduce las placas del vehículo" value="<?php echo htmlspecialchars($placas ?? '', ENT_QUOTES); ?>" required>
                        <div class="invalid-feedback"><?php echo $errors['placas'] ?? ''; ?></div>

                        <label for="vin">VIN:</label>
                        <input type="text" id="vin" name="vin" maxlength="20" class="form-control <?php echo isset($errors['vin']) ? 'is-invalid' : ''; ?>" placeholder="Introduce el VIN del vehículo" value="<?php echo htmlspecialchars($vin ?? '', ENT_QUOTES); ?>" required>
                        <div class="invalid-feedback"><?php echo $errors['vin'] ?? ''; ?></div>

                        <br>
                        <input type="submit" class="btn btn-dark" value="Registrar Vehículo">
                    </div>
                </form>
                <?php if ($success): ?>
                    <div class="alert alert-success mt-3"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger mt-3"><?php echo $errors['general']; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('formCita').addEventListener('submit', function(event) {
            let valid = true;
            const currentYear = new Date().getFullYear();

            // Obtener valores del formulario
            const marca = document.getElementById('marca').value.trim();
            const modelo = document.getElementById('modelo').value.trim();
            const anio = parseInt(document.getElementById('anio').value.trim(), 10);
            const color = document.getElementById('color').value.trim();
            const kilometraje = document.getElementById('kilometraje').value.trim();
            const placas = document.getElementById('placas').value.trim();
            const vin = document.getElementById('vin').value.trim();

            // Validar marca
            if (/\d/.test(marca)) {
                document.getElementById('marca').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('marca').classList.remove('is-invalid');
            }

            // Validar año
            if (anio < 1886 || anio > currentYear || anio.toString().length !== 4) {
                document.getElementById('anio').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('anio').classList.remove('is-invalid');
            }

            // Validar kilometraje
            if (!/^\d{1,8}$/.test(kilometraje)) {
                document.getElementById('kilometraje').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('kilometraje').classList.remove('is-invalid');
            }

            // Validar color
            if (/\d/.test(color)) {
                document.getElementById('color').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('color').classList.remove('is-invalid');
            }

            // Validar placas
            if (placas.length > 10) {
                document.getElementById('placas').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('placas').classList.remove('is-invalid');
            }

            // Validar VIN
            if (vin.length > 20) {
                document.getElementById('vin').classList.add('is-invalid');
                valid = false;
            } else {
                document.getElementById('vin').classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });

        // Lógica para el buscador de clientes
        document.getElementById('campo').addEventListener('input', function() {
            const query = this.value;
            if (query.length < 3) {
                document.getElementById('lista').style.display = 'none';
                return;
            }

            fetch('buscar_view.php?query=' + query)
                .then(response => response.json())
                .then(data => {
                    const lista = document.getElementById('lista');
                    lista.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(cliente => {
                            const item = document.createElement('li');
                            item.classList.add('list-group-item');
                            item.textContent = cliente.nombre;
                            item.addEventListener('click', function() {
                                document.getElementById('campo').value = cliente.nombre;
                                document.getElementById('clienteID').value = cliente.id;
                                lista.style.display = 'none';
                            });
                            lista.appendChild(item);
                        });
                        lista.style.display = 'block';
                    } else {
                        lista.style.display = 'none';
                    }
                });
        });
    </script>
</body>
</html>
