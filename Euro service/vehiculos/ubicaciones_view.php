<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicaciones</title>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
        <form action="#" method="post">
        <div class="mb-3">
            <select class="form-select" name="ubi" id="ubi" required>
            <option value="">Seleccione una ubicación</option>
            <?php
                    include '../includes/db.php';
                    $conexion = new Database();
                    $conexion->conectar();
                    $consulta = "SELECT * FROM ubicaciones";
                    $ubicaciones = $conexion->seleccionar($consulta);
                    foreach($ubicaciones as $ubicacion) {
                        echo "<option value='".$ubicacion->ubicacionID."'>".$ubicacion->lugar."</option>";
                    }
                    ?>
            </select>
        </div>
            <button type="submit" class="btn btn-primary" data-bs-toggle="modal">Buscar</button>
        </form>
        <br>
        <?php
        extract($_POST);
        if($_POST)
        {
            $consulta = "SELECT distinct concat(vehiculos.marca,' ',vehiculos.modelo,' ',vehiculos.anio,' ',vehiculos.color) AS VEHICULO, 
concat(personas.nombre,' ',personas.apellido_paterno,' ',personas.apellido_materno) AS PROPIETARIO
FROM personas INNER JOIN clientes ON clientes.personaID=personas.personaID INNER JOIN vehiculos ON
vehiculos.clienteID=clientes.clienteID INNER JOIN citas ON citas.vehiculoID=vehiculos.vehiculoID INNER JOIN
ordenes_trabajo ON ordenes_trabajo.citaID=citas.citaID INNER JOIN ubicaciones ON 
ordenes_trabajo.ubicacionID=ubicaciones.ubicacionID 
where ubicaciones.ubicacionID=$ubi";

            $tabla = $conexion->seleccionar($consulta);

            echo "
            <table class='table table-hover'>
            <thead class='table-dark' align='center'>
            <tr>
            <th>Vehículo</th><th>Propietario</th>
            </tr>
            </thead>
            <tbody>";
            foreach($tabla as $reg)
            {
            echo "<tr align='center'>";
            echo "<td> $reg->VEHICULO </td>";
            echo "<td> $reg->PROPIETARIO </td>";
            echo "</tr>";
            }
            echo "</tbody>";

        }
        ?>
        </div>
    </div>
</body>
</html>