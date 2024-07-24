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
                <form action="crear_orden_desde_cita.php" method="post">
                    <label for="citaID">Seleccionar Cita:</label>
                    <select id="citaID" name="citaID" class="form-control" required>
                        <?php
                        require '../includes/db.php';
                        $con = new Database();
                        $pdo = $con->conectar();
                        $citas = listarCitasPendientes($pdo);
                        foreach ($citas as $citaOption) {
                            echo "<option value=\"{$citaOption['citaID']}\">Cita ID: {$citaOption['citaID']} - Vehículo: {$citaOption['marca']} {$citaOption['modelo']} {$citaOption['anio']} - Cliente: {$citaOption['nombre']} {$citaOption['apellido_paterno']} {$citaOption['apellido_materno']} - Servicio: {$citaOption['servicio_solicitado']}</option>";
                        }
                        ?>
                    </select><br><br>
                    <input type="submit" class="btn btn-dark" value="Crear Orden de Trabajo">
                </form>
            </div>
        </div>
    </div>
</body>

</html>