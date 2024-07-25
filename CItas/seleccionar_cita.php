<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Cita para Orden de Trabajo</title>
</head>

<body>
<div class="wrapper">
    <?php include '../includes/vabr.html'; ?>
    <div class="main p-2">
        <div class="container">
            <h2>SELECCIONAR CITA PARA CREAR ORDEN DE TRABAJO</h2>
            <div class="form-container">
            <form method="post" action="editar_cita_back.php">
                    <div class="mb-3">
                        <label for="citaID" class="form-label">Seleccionar Cita:</label>
                        <select id="citaID" name="citaID" class="form-select" required>
                            <?php
                            require '../includes/db.php';
                            $con = new Database();
                            $pdo = $con->conectar();
                            $citas = listarCitasPendientes($pdo);
                            foreach ($citas as $citaOption) {
                                echo "<option value=\"{$citaOption['citaID']}\">Cita ID: {$citaOption['citaID']} - Vehículo: {$citaOption['marca']} {$citaOption['modelo']} {$citaOption['anio']} - Cliente: {$citaOption['nombre']} {$citaOption['apellido_paterno']} {$citaOption['apellido_materno']} - Servicio: {$citaOption['servicio_solicitado']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="buscar" class="btn btn-dark w-100">Buscar Cita</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>