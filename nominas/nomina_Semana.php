<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>Registrar Nómina Semanal</title>
    <style>
        .main {
            align-items: center;
        }

        .datepicker {
            background-color: #f7f7f7;
            border-radius: 5px;
            padding: 15px;
        }

        .input-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Registrar o Actualizar Nómina Semanal</h2>
                <div class="form-container">

                    <form action="procesar_nomina.php" method="post">
                    <div class="col-md-6 offset-md-3">
                            <label for="fecha">Selecciona la semana:</label>
                            <div id="week-picker" class="input-group">
                                <input type="text" id="fecha" name="fecha" class="form-control" placeholder="Selecciona el lunes de la semana" required>
                            </div>
                        </div>
                        <br>
                        <input type="submit" class="btn btn-dark d-grid btnn gap-2 col-6 mx-auto" value="Procesar Nómina">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <!-- Tu script personalizado -->
    <script src="../assets/js/weekpicker.js"></script>

</body>

</html>
