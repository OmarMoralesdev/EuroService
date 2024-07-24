<?php
    if (isset($_GET['tipoOrden'])) {
        $tipoOrden = $_GET['tipoOrden'];
        if ($tipoOrden == 'conCita') {
            header('Location: seleccionar_cita.php');
        } else {
            header('Location: crear_orden_sin_cita.php');
        }
    }
    ?>
    
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Orden de Trabajo</title>
</head>

<body>
    <div class="wrapper">
    <?php include '../includes/vabr.html'; ?>
    <div class="main p-2">
        <div class="container">
            <h2>CREAR ORDEN DE TRABAJO</h2>
            <div class="form-container">
                <form action="" method="get">
                    <label for="tipoOrden">Seleccione el tipo de orden:</label>
                    <select id="tipoOrden" name="tipoOrden" required>
                        <option value="conCita">Con Cita</option>
                        <option value="sinCita">Sin Cita</option>
                    </select><br><br>
                    <input type="submit"  class="btn btn-dark" value="Continuar">
                </form>
            </div>
        </div>
    </div>
</body>

</html>