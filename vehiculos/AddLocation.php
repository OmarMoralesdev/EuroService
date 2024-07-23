<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lugar = $_POST['lugar'];
    $capacidad = $_POST['capacidad'];

$modalContent = '';

    $conexion = new Database();
    $conexion->conectar();
    $conn = $conexion->conectar();

    $consulta_nombre = "SELECT COUNT(*) as count FROM ubicaciones WHERE lugar = :lugar";
    $stmt_nombre = $conn->prepare($consulta_nombre);
    $stmt_nombre->bindParam(':lugar', $lugar, PDO::PARAM_STR);
    $stmt_nombre->execute();
    $resultado_nombre = $stmt_nombre->fetch(PDO::FETCH_ASSOC);

    
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
        if($capacidad > 0 && $capacidad <= 40 ){

            $consulta = "INSERT INTO ubicaciones (lugar, capacidad, activo) VALUES ('$lugar', $capacidad, 'si')";
            $conexion->ejecuta($consulta);
            
            $conexion->desconectar();
            
            header('Location: ubicaciones_view.php');
            exit();
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
                header('Location: ubicaciones_view.php');
                exit();
            
    
        }
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
            header('Location: ubicaciones_view.php');
            exit();
    
    }
    }
}
?>
