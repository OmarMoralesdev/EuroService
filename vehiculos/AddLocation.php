<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lugar = $_POST['lugar'];
    $capacidad = $_POST['capacidad'];

    // contenido de los moradels
$modalContent = '';

    $conexion = new Database();
    $conexion->conectar();
    $conn = $conexion->conectar();

    // Verificar si el lugar ya existe en la base de datos
    $consulta_nombre = "SELECT COUNT(*) as count FROM UBICACIONES WHERE lugar = :lugar";
    // Preparar la consulta
    $stmt_nombre = $conn->prepare($consulta_nombre);
    // Asignar valores a los parámetros
    $stmt_nombre->bindParam(':lugar', $lugar, PDO::PARAM_STR);
    // Ejecutar la consulta
    $stmt_nombre->execute();
    // Obtener el resultado
    $resultado_nombre = $stmt_nombre->fetch(PDO::FETCH_ASSOC);

    // Verificar si el lugar ya existe en la base de datos y mostrar un modal de error si es necesario
    if ($resultado_nombre['count'] > 0) {
        echo "<!-- Modal de Error -->
<div class='modal fade' id='errorModal' tabindex='-1' aria-labelledby='errorModalLabel' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='errorModalLabel'>Error al agregar ubicación</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <p>Ya existe una ubicación con el nombre '$lugar'.</p>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
            </div>
        </div>
    </div>
</div>";

    } else {
        // Verificar si la capacidad es válida y agregar la ubicación a la base de datos si es así
        if($capacidad > 0 && $capacidad <= 40 ){

            // Insertar la ubicación en la base de datos
            $consulta = "INSERT INTO UBICACIONES (lugar, capacidad, activo) VALUES ('$lugar', $capacidad, 'si')";
            $conexion->ejecuta($consulta);
            
            $conexion->desconectar();
            
            header('Location: ubicaciones_view.php');
            exit();
            // Mostrar un modal de éxito si la ubicación se agregó correctamente 
        }else if ($capacidad < 0){
            echo "
                <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='staticBackdropLabel'>No puedes ingresar números negativos!</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                Ingresa datos válidos.<br><br>
                                
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>";
                // Redirigir al usuario después de la acción
                header('Location: ubicaciones_view.php');
                exit();
            
    
        }
        // Mostrar un modal de error si la capacidad no es válida (máximo 40 vehículos en una ubi)
        else if ($capacidad > 40){
        $modalContent = "
            <div class='modal fade' id='staticBackdrop' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='staticBackdropLabel'>La capacidad no puede ser mayor a 40</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            Ingresa datos válidos.<br><br>
                            
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>";
            // Redirigir al usuario después de la acción
            
        }
    }
    header('Location: ubicaciones_view.php');
    exit();
}
?>
