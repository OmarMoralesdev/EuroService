<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicaciones</title>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
        <form action="#" method="post">
        <div class="mb-3">
            <select class="form-select" name="ubi" id="ubi">
            <option value="">Seleccione una ubicación</option>
            </select>
        </div>
            <button type="submit" class="btn btn-primary" data-bs-toggle="modal">Buscar</button>
        </form>
        </div>
    </div>
</body>
</html>