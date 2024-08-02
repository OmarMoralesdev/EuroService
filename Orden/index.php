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
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Crear Orden de Trabajo</h2>
                <div class="form-container">
                    <form action="" method="">
                        <label for="tipoOrden">Seleccione el tipo de orden:</label>
                        <select id="tipoOrden" name="tipoOrden" class="form-control" required>
                            <option value="conCita">Con Cita</option>
                            <option value="sinCita">Sin Cita</option>
                        </select><br>
                        <button type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto">Continuar</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>