<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'TALLER_EURO';
    private $username = 'root';
    private $password = '1234';
    private $port = '3306';
    private $pdo;

    public function conectar()
    {
        $this->pdo = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            $this->pdo = new PDO($dsn, $this->username, $this->password);
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
    $sql = "SELECT CITAS.citaID, CITAS.vehiculoID, CITAS.servicio_solicitado, VEHICULOS.marca, VEHICULOS.modelo, VEHICULOS.anio, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
            FROM CITAS 
            JOIN VEHICULOS ON CITAS.vehiculoID = VEHICULOS.vehiculoID
            JOIN CLIENTES ON VEHICULOS.clienteID = CLIENTES.clienteID
            JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID
            WHERE CITAS.estado = 'pendiente'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function listarCitasPendientes2($pdo) {
    try {
        $sql = "SELECT CITAS.citaID, CITAS.vehiculoID, CITAS.servicio_solicitado, 
                       VEHICULOS.marca, VEHICULOS.modelo, VEHICULOS.anio, 
                       PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
                FROM CITAS 
                JOIN VEHICULOS ON CITAS.vehiculoID = VEHICULOS.vehiculoID
                JOIN CLIENTES ON VEHICULOS.clienteID = CLIENTES.clienteID
                JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID
                WHERE CITAS.estado = 'pendiente'
                  AND CITAS.fecha_cita >= NOW()"; 

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Manejo de errores, puedes registrar el error o retornar un mensaje específico
        error_log("Error al obtener citas pendientes: " . $e->getMessage());
        return []; // Retorna un arreglo vacío si ocurre un error
    }
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
    $sql = "SELECT VEHICULOS.marca, VEHICULOS.modelo, VEHICULOS.anio, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
            FROM VEHICULOS 
            JOIN CLIENTES ON VEHICULOS.clienteID = CLIENTES.clienteID
            JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID
            WHERE VEHICULOS.vehiculoID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vehiculoID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerDetallesClientepersona($pdo, $clienteID)
{
    $sql = "SELECT PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
            FROM CLIENTES 
            JOIN PERSONAS ON CLIENTES.personaID = PERSONAS.personaID WHERE clienteID = :clienteID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':clienteID' => $clienteID]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function listarordenes($pdo) {
    $stmt = $pdo->query("
        SELECT ot.ordenID, ot.fecha_orden
        FROM ORDENES_TRABAJO ot
        INNER JOIN CITAS c ON ot.citaID = c.citaID
        WHERE c.estado = 'pendiente' OR c.estado = 'en proceso'
    ");
    
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $ordenes;
}