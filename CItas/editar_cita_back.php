<?php
require '../includes/db.php';
session_start();

$con = new Database();
$pdo = $con->conectar();

$cita = null;
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        if ($citaID) {
            $sql = "SELECT * FROM CITAS WHERE citaID = ?";
            $query = $pdo->prepare($sql);
            $query->execute([$citaID]);
            $cita = $query->fetch(PDO::FETCH_ASSOC);

            if (!$cita) {
                $mensaje = "Cita no encontrada.";
            } else {
                $vehiculoID = $cita['vehiculoID'];
                $detalles = obtenerDetallesVehiculoyCliente($pdo, $vehiculoID);
                $_SESSION['cita'] = $cita;
                $_SESSION['mensaje'] = $mensaje;
                header("Location: editar_cita_view.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "ID de cita inválido.";
            header("Location: seleccionar_cita.php");
            exit();
        }
        
        $_SESSION['mensaje'] = $mensaje;
        header("Location: editar_cita_view.php");
        exit();
    } elseif (isset($_POST['actualizar'])) {
        $citaID = filter_input(INPUT_POST, 'citaID', FILTER_SANITIZE_NUMBER_INT);
        $servicioSolicitado = filter_input(INPUT_POST, 'servicioSolicitado', FILTER_SANITIZE_STRING);
        $fechaCita = filter_input(INPUT_POST, 'fecha_cita', FILTER_SANITIZE_STRING);
        $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

        if (!$citaID || !$servicioSolicitado || !$fechaCita || !$estado) {
            $_SESSION['error'] = "Error: Todos los campos son obligatorios.";
        } else {
            $fechaActual = new DateTime();
            $fechaCita = new DateTime($fechaCita);

            // Validación: La fecha de la cita debe ser posterior a la fecha actual
            if ($fechaCita < $fechaActual) {
                $_SESSION['error'] =  "Error: La fecha de la cita debe ser posterior a la fecha actual.";
                header("Location: editar_cita_view.php");
                exit();
            } else {
                $horaCita = $fechaCita->format('H:i:s');
                $horaInicioLaboral = "09:00:00";
                $horaFinLaboral = "17:00:00";

                // Validación: La hora de la cita debe estar dentro del horario laboral
                if ($horaCita < $horaInicioLaboral || $horaCita > $horaFinLaboral) {
                    $_SESSION['error'] = "Error: La cita debe programarse dentro del horario laboral (09:00 - 17:00).";
                    header("Location: editar_cita_view.php");
                    exit();
                } else {
                    // Verificar que la nueva cita no se solape con otras citas
                    $fechaInicioIntervalo = (clone $fechaCita)->modify('-30 minutes')->format('Y-m-d H:i:s');
                    $fechaFinIntervalo = (clone $fechaCita)->modify('+30 minutes')->format('Y-m-d H:i:s');

                    $sql = "SELECT COUNT(*) AS countCitas FROM CITAS WHERE fecha_cita BETWEEN ? AND ? AND citaID != ?";
                    $query = $pdo->prepare($sql);
                    $query->execute([$fechaInicioIntervalo, $fechaFinIntervalo, $citaID]);

                    $row = $query->fetch(PDO::FETCH_ASSOC);
                    $countCitasProximas = $row['countCitas'];

                    if ($countCitasProximas > 0) {
                        $_SESSION['error'] =  "Error: La nueva fecha y hora de la cita solapan con otra cita existente en el intervalo de 30 minutos.";
                        header("Location: editar_cita_view.php");
                        exit();
                        } else {
                            // Actualizar la cita
                            $sqlUpdate = "UPDATE CITAS SET servicio_solicitado = ?, fecha_cita = ?, estado = ? WHERE citaID = ?";
                            $queryUpdate = $pdo->prepare($sqlUpdate);
                            $resultUpdate = $queryUpdate->execute([$servicioSolicitado, $fechaCita->format('Y-m-d H:i:s'), $estado, $citaID]);

                            if ($resultUpdate) {
                                $mensaje = "Cita actualizada correctamente.";
                                $sql = "SELECT * FROM CITAS WHERE citaID = ?";
                                $query = $pdo->prepare($sql);
                                $query->execute([$citaID]);
                                $cita = $query->fetch(PDO::FETCH_ASSOC);
                                $vehiculoID = $cita['vehiculoID'];
                                $detalles = obtenerDetallesVehiculoyCliente($pdo, $vehiculoID);
                                $_SESSION['cita'] = $cita;
                                $_SESSION['mensaje'] = "Cita editada exitosamente.";
                                header("Location: editar_cita_view.php");
                                exit();
                            } else {
                                $mensaje = "Error al actualizar la cita.";
                            }
                        }
                    }
                }
            }
        }
    }


?>
