<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Editar Vehículo</title>
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: Arial, sans-serif;
        }
        .form-control {
            margin-bottom: 10px;
        }
        .invalid-feedback {
            display: none;
            color: red;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        .data {
            background-color: #a2a2a2;
            border-radius: 5px;
            padding: 10px;
        }
        .btnn {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php 
        session_start(); 
        include '../includes/vabr.php';
        ?>
        <div class="main p-3">
            <div class="container">
                <h2>EDITAR DATOS DEL VEHÍCULO</h2>
                <div class="form-container">
                    <?php
                   if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                }
                if (isset($_SESSION['bien'])) {
                    echo '<div class="alert alert-success" role="alert">' . $_SESSION['bien'] . '</div>';
                    unset($_SESSION['bien']); 
                }
                                                    ?>
                    <form id="editarCarro" action="../vehiculos/editCar.php" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="carro">Selecciona un vehículo:</label>
                            <select class="form-select" name="carro" id="carro" required>
                                <option value="">Selecciona un vehículo</option>
                                <?php
                                // Conectar a la base de datos y obtener los vehículos
                                include '../includes/db.php';
                                $conexion = new Database();
                                $pdo = $conexion->conectar(); 

                                $consulta_vehiculos = "
                                    SELECT CONCAT(VEHICULOS.marca, ' ', VEHICULOS.modelo, ' ', VEHICULOS.color, ' ', VEHICULOS.anio) AS VEHICULOS,
                                    CONCAT(PERSONAS.nombre, ' ', PERSONAS.apellido_paterno, ' ', PERSONAS.apellido_materno) AS propietario,
                                    VEHICULOS.vehiculoID, VEHICULOS.kilometraje, VEHICULOS.placas, VEHICULOS.color
                                    FROM PERSONAS
                                    JOIN CLIENTES ON CLIENTES.personaID = PERSONAS.personaID
                                    JOIN VEHICULOS ON VEHICULOS.clienteID = CLIENTES.clienteID
                                    WHERE VEHICULOS.activo = 'si'";
                                
                                $stmt = $pdo->query($consulta_vehiculos);
                                $vehiculos = $stmt->fetchAll(PDO::FETCH_OBJ);
                                foreach ($vehiculos as $carro) {
                                    echo "<option value='" . htmlspecialchars($carro->vehiculoID, ENT_QUOTES, 'UTF-8') . "' 
                                            data-placas='" . htmlspecialchars($carro->placas, ENT_QUOTES, 'UTF-8') . "' 
                                            data-kilometraje='" . htmlspecialchars($carro->kilometraje, ENT_QUOTES, 'UTF-8') . "' 
                                            data-color='" . htmlspecialchars($carro->color, ENT_QUOTES, 'UTF-8') . "' 
                                            data-propietario='" . htmlspecialchars($carro->propietario, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($carro->VEHICULOS, ENT_QUOTES, 'UTF-8') . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="data">
                            <h2> Datos actuales del vehículo</h2>
                            <div class="form-group">
                                <input type="text" class="form-control" id="placas" name="placas" placeholder="Placas actuales" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="km" name="km" placeholder="Kilometraje actual" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="color" name="color" placeholder="Color actual" readonly>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="propietario" name="propietario" placeholder="Propietario actual" readonly>
                            </div>
                        </div>
                        <br>
                        <div class="mb-3">
                            <label for="placasN">Nuevas placas:</label>
                            <input type="text" class="form-control" id="placasN" name="placasN" placeholder="Nuevas placas">
                        </div>
                        <div class="mb-3">
                            <label for="kmN">Nuevo kilometraje:</label>
                            <input type="text" class="form-control" id="kmN" name="kmN" placeholder="Nuevo kilometraje">
                        </div>
                        <div class="mb-3">
                            <label for="colorN">Nuevo color:</label>
                            <input type="text" class="form-control" id="colorN" name="colorN" placeholder="Nuevo color">
                        </div>
                        <div class="mb-3">
                            <label for="cliente">Selecciona un propietario:</label>
                            <select class="form-select" name="cliente" id="cliente">
                                <option value="">Selecciona al nuevo propietario</option>
                                <?php
                                    $consulta_clientes = "
                                        SELECT CONCAT(PERSONAS.nombre, ' ', PERSONAS.apellido_paterno, ' ', PERSONAS.apellido_materno) AS nombre,
                                        CLIENTES.clienteID
                                        FROM PERSONAS
                                        JOIN CLIENTES ON CLIENTES.personaID = PERSONAS.personaID
                                        WHERE CLIENTES.activo ='si'";
                                    
                                    $stmt = $pdo->query($consulta_clientes);
                                    $clientes = $stmt->fetchAll(PDO::FETCH_OBJ);
                                    foreach ($clientes as $cliente) {
                                        echo "<option value='" . htmlspecialchars($cliente->clienteID, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($cliente->nombre, ENT_QUOTES, 'UTF-8') . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark btnn">Editar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carroSelect = document.getElementById('carro');
            const placasInput = document.getElementById('placas');
            const kmInput = document.getElementById('km');
            const colorInput = document.getElementById('color');
            const propietarioInput = document.getElementById('propietario');

            carroSelect.addEventListener('change', function() {
                const selectedOption = carroSelect.options[carroSelect.selectedIndex];
                placasInput.value = selectedOption.getAttribute('data-placas') || '';
                kmInput.value = selectedOption.getAttribute('data-kilometraje') || '';
                colorInput.value = selectedOption.getAttribute('data-color') || '';
                propietarioInput.value = selectedOption.getAttribute('data-propietario') || '';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
