<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>rebajas</title>
</head>
<body>
<form action="AddRebaja.php" method="post">
<select name="empleado" class="form-control" required>
                    <?php
                    require '../includes/db.php';
                    $con = new Database();
                    $pdo = $con->conectar();
                    
                    function obtenerEmpleadosDisponibles($pdo)
                    {
                        $sql = "SELECT EMPLEADOS.personaID, PERSONAS.nombre, PERSONAS.apellido_paterno, PERSONAS.apellido_materno
                                FROM EMPLEADOS 
                                JOIN PERSONAS ON EMPLEADOS.personaID = PERSONAS.personaID
                                WHERE EMPLEADOS.tipo = 'administrativo'";
                        $stmt = $pdo->query($sql);
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    $empleados = obtenerEmpleadosDisponibles($pdo);
                    foreach ($empleados as $empleado) {
                        $nombreCompleto = "{$empleado['nombre']} {$empleado['apellido_paterno']} {$empleado['apellido_materno']}";
                        echo "<option value=\"{$empleado['personaID']}\">{$nombreCompleto}</option>";
                    }
                    ?>
                </select>
    <label for="nominaID">Seleccionar Nómina:</label>
    <select name="nominaID" id="nominaID">
        <?php
        // Obtener nóminas disponibles
        $consulta_nominast = "SELECT nominaID, fecha_inicio, fecha_fin FROM NOMINAS";
        $stmt = $pdo->prepare($consulta_nominast);
        $stmt->execute();
        $nominast = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($nominast as $nomina) {
            echo "<option value=\"{$nomina['nominaID']}\">Semana del {$nomina['fecha_inicio']} al {$nomina['fecha_fin']}</option>";
        }
        ?>
    </select>
    <label for="rebajas">Rebajas:</label>
    <input type="text" name="rebajas" id="rebajas" required>
    <button type="submit">Actualizar Rebajas</button>
</form>

</body>
</html>