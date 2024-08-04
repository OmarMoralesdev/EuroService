<?php

class Database
{
    private $host = '127.0.0.1';
    private $db_name = 'TALLER_EURO';
    private $username = 'root';
    private $password = '1234';
    private $pdo;

    public function conectar()
    {
        $this->pdo = null;

        try {
            $this->pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }

        return $this->pdo;
    }
    public function desconectar()
    {
        $this->pdo = null;
    }
    function seleccionar($consulta)
    {
        try {
            $resultado = $this->pdo->query($consulta);
            $fila = $resultado->fetchAll(PDO::FETCH_OBJ);
            return $fila;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    function ejecuta($consulta)
    {
        try {
            $this->pdo->query($consulta);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

function listarCitasPendientes($pdo) {
    $sql = "SELECT citas.citaID, citas.vehiculoID, citas.servicio_solicitado, vehiculos.marca, vehiculos.modelo, vehiculos.anio, personas.nombre, personas.apellido_paterno, personas.apellido_materno
            FROM citas 
            JOIN vehiculos ON citas.vehiculoID = vehiculos.vehiculoID
            JOIN clientes ON vehiculos.clienteID = clientes.clienteID
            JOIN personas ON clientes.personaID = personas.personaID
            WHERE citas.estado = 'pendiente'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function obtenerDetallesCita($pdo, $citaID)
{
    $sql = "SELECT * FROM CITAS WHERE citaID = :citaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':citaID' => $citaID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerDetallesVehiculo($pdo, $vehiculoID)
{
    $sql = "SELECT * FROM VEHICULOS WHERE vehiculoID = :vehiculoID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':vehiculoID' => $vehiculoID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerDetallesCliente($pdo, $clienteID)
{
    $sql = "SELECT * FROM CLIENTES WHERE clienteID = :clienteID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':clienteID' => $clienteID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function crearOrdenTrabajo($pdo, $fechaOrden, $costoManoObra, $costoRefacciones, $total_estimado,  $atencion, $citaID, $empleado, $ubicacionID)
{
    $sql = "INSERT INTO ORDENES_TRABAJO (fecha_orden, costo_mano_obra, costo_refacciones, total_estimado, atencion, citaID, empleadoID, ubicacionID) 
            VALUES (:fechaOrden, :costoManoObra, :costoRefacciones, :total_estimado, :anticipo,  :citaID, :empleado, :ubicacionID)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':fechaOrden' => $fechaOrden,
        ':costoManoObra' => $costoManoObra,
        ':costoRefacciones' => $costoRefacciones,
        ':total_estimado' => $total_estimado,
        ':atencion' => $atencion,
        ':citaID' => $citaID,
        ':empleado' => $empleado,
        ':ubicacionID' => $ubicacionID,
    ]);

    return $pdo->lastInsertId();
}

function actualizarEstadoCita($pdo, $citaID, $nuevoEstado)
{
    $sql = "UPDATE CITAS SET estado = :nuevoEstado WHERE citaID = :citaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nuevoEstado' => $nuevoEstado, ':citaID' => $citaID]);
}

function obtenerDetallesVehiculoyCliente($pdo, $vehiculoID) {
    $sql = "SELECT vehiculos.marca, vehiculos.modelo, vehiculos.anio, personas.nombre, personas.apellido_paterno, personas.apellido_materno
            FROM vehiculos 
            JOIN clientes ON vehiculos.clienteID = clientes.clienteID
            JOIN personas ON clientes.personaID = personas.personaID
            WHERE vehiculos.vehiculoID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vehiculoID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerDetallesClientepersona($pdo, $clienteID)
{
    $sql = "SELECT personas.nombre, personas.apellido_paterno, personas.apellido_materno
            FROM clientes 
            JOIN personas ON clientes.personaID = personas.personaID WHERE clienteID = :clienteID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':clienteID' => $clienteID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function realizarPago($pdo, $ordenID, $fechaPago, $monto, $tipoPago, $formaDePago)
{
    try {
        // Insertar el pago en la tabla PAGOS
        $sqlPago = "INSERT INTO PAGOS (ordenID, fecha_pago, monto, tipo_pago, forma_de_pago)
                    VALUES (:ordenID, :fechaPago, :monto, :tipoPago, :formaDePago)";
        $stmtPago = $pdo->prepare($sqlPago);
        $stmtPago->execute([
            ':ordenID' => $ordenID,
            ':fechaPago' => $fechaPago,
            ':monto' => $monto,
            ':tipoPago' => $tipoPago,
            ':formaDePago' => $formaDePago,
        ]);

        // Actualizar el campo anticipo en la tabla ORDENES_TRABAJO
        if ($tipoPago == 'anticipo') {
            $sqlUpdateAnticipo = "UPDATE ORDENES_TRABAJO SET anticipo = :monto WHERE ordenID = :ordenID";
            $stmtUpdate = $pdo->prepare($sqlUpdateAnticipo);
            $stmtUpdate->execute([
                ':monto' => $monto,
                ':ordenID' => $ordenID,
            ]);
        }

        echo "Pago realizado y anticipo actualizado con éxito.";
    } catch (PDOException $e) {
        throw new Exception("Error al realizar el pago: " . $e->getMessage());
    }
}



