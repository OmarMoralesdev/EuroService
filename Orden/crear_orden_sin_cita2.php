<?php
            // Procesar el formulario
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $vehiculoID = $_POST['vehiculoID'];
                $servicioSolicitado = $_POST['servicioSolicitado'];
                $fechaCita = date('Y-m-d');;
                $urgencia = "si";
                $fechaSolicitud = date('Y-m-d'); // Fecha actual
                $fechaOrden = $fechaCita;
                $detallesTrabajo = $servicioSolicitado;
                $costoManoObra = $_POST['costoManoObra'];
                $costoRefacciones = $_POST['costoRefacciones'];
                $empleadoID = $_POST['empleado'];
                $ubicacionID = $_POST['ubicacionID'];
                $atencion = "Muy Urgente";

                require '../includes/db.php';
                $con = new Database();
                $pdo = $con->conectar();
                try {
                    $pdo->beginTransaction();
                    $sqlCita = "INSERT INTO CITAS (vehiculoID, servicio_solicitado, fecha_solicitud, fecha_cita, urgencia, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
                    $stmtCita = $pdo->prepare($sqlCita);
                    $stmtCita->execute([$vehiculoID, $servicioSolicitado, $fechaSolicitud, $fechaCita, $urgencia]);
                    $citaID = $pdo->lastInsertId(); 
                    $sqlOrden = "INSERT INTO ORDENES_TRABAJO (fecha_orden, detalles_trabajo, costo_mano_obra, costo_refacciones, estado, citaID, empleadoID, ubicacionID, atencion) VALUES (?, ?, ?, ?, 'pendiente', ?, ?, ?, ?)";
                    $stmtOrden = $pdo->prepare($sqlOrden);
                    $stmtOrden->execute([$fechaOrden, $detallesTrabajo, $costoManoObra, $costoRefacciones, $citaID, $empleadoID, $ubicacionID, $atencion]);
                    $pdo->commit();
                    echo '<div class="alert alert-success mt-3">Cita y orden de trabajo creadas exitosamente.</div>';
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    echo '<div class="alert alert-danger mt-3">Error: ' . $e->getMessage() . '</div>';
                }
            }
            ?>