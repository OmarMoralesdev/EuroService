<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nómina Automáticamente</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Registrar Nómina Automáticamente</h2>
                <div class="form-container">
                    <?php if (isset( $_SESSION['mensaje'])) : ?>
                        <div class="alert alert-<?php echo strpos( $_SESSION['mensaje'], 'ya ha sido registrada') !== false ? 'warning' : (strpos( $_SESSION['mensaje'], 'No hay registros') !== false ? 'danger' : 'success'); ?>" role="alert">
                            <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="./procesar_nomina.php">
                        <div class="form-row">
                            <div class="form-group col-md-6 offset-md-3">
                                <label for="week">Selecciona la semana:</label>
                                <input type="week" id="week" name="week" class="form-control" value="<?php echo date('Y-\WW'); ?>" required>
                            </div>
                        </div><br>
                        <button type="submit" class="btn btn-dark w-100">Registrar Nómina</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<?php
$pdo = null;
?>

