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

    function listarCitasPendientes($pdo)
    {
        $sql = "SELECT * FROM CITAS WHERE estado = 'pendiente'";
        $stmt = $pdo->query($sql);
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

    function crearOrdenTrabajo($pdo, $vehiculoID, $fechaOrden, $detallesTrabajo, $costoManoObra, $costoRefacciones, $estado, $empleado, $ubicacionID, $atencion)
    {
        $sql = "INSERT INTO ORDENES_TRABAJO (fecha_orden, detalles_trabajo, costo_mano_obra, costo_refacciones, estado, citaID, empleadoID, ubicacionID, atencion) 
            VALUES (:fechaOrden, :detallesTrabajo, :costoManoObra, :costoRefacciones, :estado,:vehiculoID, :empleado, :ubicacionID, :atencion)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':vehiculoID' => $vehiculoID,
            ':fechaOrden' => $fechaOrden,
            ':detallesTrabajo' => $detallesTrabajo,
            ':costoManoObra' => $costoManoObra,
            ':costoRefacciones' => $costoRefacciones,
            ':estado' => $estado,
            ':empleado' => $empleado,
            ':ubicacionID' => $ubicacionID,
            ':atencion' => $atencion,
        ]);

        return $pdo->lastInsertId();
    }

    function actualizarEstadoCita($pdo, $citaID, $nuevoEstado)
    {
        $sql = "UPDATE CITAS SET estado = :nuevoEstado WHERE citaID = :citaID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nuevoEstado' => $nuevoEstado, ':citaID' => $citaID]);
    }
}

function listarCitasPendientes($pdo)
{
    $sql = "SELECT * FROM CITAS WHERE estado = 'pendiente'";
    $stmt = $pdo->query($sql);
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

function crearOrdenTrabajo($pdo, $vehiculoID, $fechaOrden, $detallesTrabajo, $costoManoObra, $costoRefacciones, $estado, $empleado, $ubicacionID, $atencion)
{
    $sql = "INSERT INTO ORDENES_TRABAJO (fecha_orden, detalles_trabajo, costo_mano_obra, costo_refacciones, estado, citaID, empleadoID, ubicacionID, atencion) 
            VALUES (:fechaOrden, :detallesTrabajo, :costoManoObra, :costoRefacciones, :estado,:vehiculoID, :empleado, :ubicacionID, :atencion)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':vehiculoID' => $vehiculoID,
        ':fechaOrden' => $fechaOrden,
        ':detallesTrabajo' => $detallesTrabajo,
        ':costoManoObra' => $costoManoObra,
        ':costoRefacciones' => $costoRefacciones,
        ':estado' => $estado,
        ':empleado' => $empleado,
        ':ubicacionID' => $ubicacionID,
        ':atencion' => $atencion,
    ]);

    return $pdo->lastInsertId();
}

function actualizarEstadoCita($pdo, $citaID, $nuevoEstado)
{
    $sql = "UPDATE CITAS SET estado = :nuevoEstado WHERE citaID = :citaID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nuevoEstado' => $nuevoEstado, ':citaID' => $citaID]);
}

