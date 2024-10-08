<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../img/incono.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Entrega</title>
</head>

<body>
    <div class="wrapper">
        <?php include '../includes/vabr.php'; ?>
        <div class="main">
            <div class="container">
                <h2 class="mb-4">Registrar Entrega de Orden de Trabajo</h2>
                <div class="form-container">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
                    }
                    if (isset($_SESSION['bien'])) {
                        echo "
                        <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h1 class='modal-title fs-5' id='staticBackdropLabel'>¡Orden de Trabajo Registrada!</h1>
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
                        unset($_SESSION['bien']);
                    }
                    ?>
                    <form action="proceso.php" method="POST">
                        <div class="mb-3">
                            <label for="ordenID" class="form-label">Selecciona una Orden de Trabajo:</label>
                            <select name="ordenID" id="ordenID" class="form-select" required>
                                <?php
                                try {
                                    require '../includes/db.php';
                                    $con = new Database();
                                    $pdo = $con->conectar();
                                    // Obtener órdenes de trabajo pendientes basadas en el estado de la cita
                                    $stmt = $pdo->query("
                                    SELECT ot.ordenID, 
                                           ot.fecha_orden,  
                                           ci.total_estimado, 
                                           CONCAT(per.nombre, ' ', per.apellido_paterno, ' ', per.apellido_materno) AS nombre_completo_cliente,
                                           CONCAT(v.marca, ' ', v.modelo, ' ', v.anio) AS modelos
                                    FROM ORDENES_TRABAJO ot
                                    INNER JOIN CITAS ci ON ot.citaID = ci.citaID
                                    INNER JOIN VEHICULOS v ON ci.vehiculoID = v.vehiculoID
                                    INNER JOIN CLIENTES cl ON v.clienteID = cl.clienteID
                                    INNER JOIN PERSONAS per ON cl.personaID = per.personaID
                                    WHERE ci.estado = 'pendiente' OR ci.estado = 'en proceso'
                                ");
                                

                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value=\"{$row['ordenID']}\">Orden {$row['ordenID']} - Fecha: {$row['fecha_orden']} - Total: {$row['total_estimado']} - Cliente: {$row['nombre_completo_cliente']} - Vehiculo: {$row['modelos']}</option>";
                                    }
                                } catch (PDOException $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="formadepago" class="form-label">Forma de pago:</label>
                            <select name="formadepago" class="form-control" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                            <div class="invalid-feedback">Debes seleccionar una forma de pago.</div>
                        </div>

                        <button type="submit" class="btn btn-dark d-grid gap-2 col-6 mx-auto">Confirmar entrega</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        // Verifica si el modal está presente
        if ($('#staticBackdrop').length) {
            // Muestra el modal
            $('#staticBackdrop').modal('show');
            // Cierra el modal después de 2 segundos (2000 milisegundos)
            setTimeout(function() {
                $('#staticBackdrop').modal('hide');
            }, 1000);
        }
    });
</script>

</html>