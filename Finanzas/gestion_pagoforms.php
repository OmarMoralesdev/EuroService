<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos Semanal</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <form method="post" action="gestion_pagos.php">
                    <div class="form-row">
                        <div class="form-group col-md-6 offset-md-3">
                            <label for="week">Selecciona la semana:</label>
                            <input type="week" id="week" name="week" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Ver Reporte</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>