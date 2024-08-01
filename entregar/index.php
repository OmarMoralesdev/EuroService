<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Entrega</title>
</head>

<body>

    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="container">
                <h2>Registrar Entrega de Orden de Trabajo</h2>
                <div class="form-container">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if ($_SESSION['bien']) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Usuario registrado!</h1>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='alert alert-success' role='alert'>{$_SESSION['bien']}</div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                    ?>
                    <form action="proceso.php" method="POST">
                        <label for="ordenID">Selecciona una Orden de Trabajo:</label>
                        <select name="ordenID" id="ordenID" required>
                            <?php

                            try {
                                require '../includes/db.php';
                                $con = new Database();
                                $pdo = $con->conectar();
                                // Obtener órdenes de trabajo pendientes basadas en el estado de la cita
                                $stmt = $pdo->query("
                SELECT ot.ordenID, ot.fecha_orden
                FROM ORDENES_TRABAJO ot
                INNER JOIN CITAS c ON ot.citaID = c.citaID
                WHERE c.estado = 'pendiente' or c.estado = 'en proceso'
            ");

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value=\"{$row['ordenID']}\">Orden {$row['ordenID']} - Fecha: {$row['fecha_orden']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                            }
                            ?>
                        </select>
                        <button type="submit">Registrar Entrega</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>