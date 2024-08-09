<?php
session_start();
require '../includes/db.php';
$con = new Database();
$pdo = $con->conectar();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citaID = $_POST['citaID'];
    $costoManoObra = $_POST['costoManoObra'];
    $costoRefacciones = $_POST['costoRefacciones'];
    $empleado = $_POST['empleado'];
    $ubicacionID = $_POST['ubicacionID'];
    $anticipo = $_POST['anticipo'];
    $formaDePago = $_POST['formadepago'];
    $fechaOrden = date('Y-m-d H:i:s');
    $atencion = $_POST['atencion'];
    if ($costoManoObra < 0 || $costoRefacciones < 0) {
        $_SESSION['error'] = "No puedes ingresar números negativos.";
        header("Location: crear_orden_desde_cita.php?citaID=$citaID");
        exit();
    }

    $total_estimado = $costoManoObra + $costoRefacciones;

    try {
        $pdo->beginTransaction();

        $sqlVerificarOrden = "SELECT * FROM ORDENES_TRABAJO WHERE citaID = ?";
        $stmtVerificarOrden = $pdo->prepare($sqlVerificarOrden);
        $stmtVerificarOrden->execute([$citaID]);

        if ($stmtVerificarOrden->rowCount() > 0) {
            $pdo->rollBack();
            $_SESSION['error'] = "Ya existe una orden de trabajo para esta cita.";
            header("Location: crear_orden_desde_cita.php");
            exit();
        }

        $sqlVerificarUbicacion = "
            SELECT 
                u.capacidad AS vehiculos_maximos, 
                COUNT(v.vehiculoID) AS vehiculos_actuales 
            FROM 
                UBICACIONES u
                LEFT JOIN ORDENES_TRABAJO ot ON u.ubicacionID = ot.ubicacionID
                LEFT JOIN CITAS c ON ot.citaID = c.citaID
                LEFT JOIN VEHICULOS v ON c.vehiculoID = v.vehiculoID
            WHERE 
                u.ubicacionID = ?
            GROUP BY 
                u.ubicacionID, u.capacidad
        ";
        $stmtVerificarUbicacion = $pdo->prepare($sqlVerificarUbicacion);
        $stmtVerificarUbicacion->execute([$ubicacionID]);
        $ubicacion = $stmtVerificarUbicacion->fetch(PDO::FETCH_ASSOC);

        if (!$ubicacion) {
            $pdo->rollBack();
            $_SESSION['error'] = "Ubicación no encontrada.";
            header("Location: crear_orden_desde_cita.php");
            exit();
        }

        if ($ubicacion['vehiculos_actuales'] >= $ubicacion['vehiculos_maximos']) {
            $pdo->rollBack();
            $_SESSION['error'] = "La ubicación ya está llena.";
            header("Location: crear_orden_desde_cita.php");
            exit();
        }

        $sqlOrden = "
            INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, atencion, citaID, empleadoID, ubicacionID) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmtOrden = $pdo->prepare($sqlOrden);
        $stmtOrden->execute([$fechaOrden, $costoManoObra, $costoRefacciones ,$atencion, $citaID, $empleado, $ubicacionID]);
        $ordenID = $pdo->lastInsertId();

        $fechaPago = date('Y-m-d');
        realizarPago($pdo, $ordenID, $fechaPago, $anticipo, 'anticipo', $formaDePago);

        actualizarEstadoCita($pdo, $citaID, 'en proceso');

        $pdo->commit();

        $_SESSION['bien'] = "Orden $ordenID  de trabajo creada" ;
        header("Location: crear_orden_desde_cita.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error al crear la orden de trabajo: " . $e->getMessage();
        header("Location: crear_orden_desde_cita.php");
        exit();
    }
} else {
    echo "Cita no encontrada.";
}