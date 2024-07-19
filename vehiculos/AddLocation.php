<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lugar = $_POST['lugar'];
    $capacidad = $_POST['capacidad'];

$modalContent = '';

    $conexion = new Database();
    $conexion->conectar();

    $consulta_nombre = "SELECT COUNT(*) as count FROM UBICACIONES WHERE lugar = '$lugar'";
    $resultado_nombre = $conexion->ejecuta($consulta_nombre);
    

    
    
    if($capacidad > 0 && $capacidad < 40 ){

        $consulta = "INSERT INTO ubicaciones (lugar, capacidad) VALUES ('$lugar', $capacidad)";
        $conexion->ejecuta($consulta);
        
        $conexion->desconectar();
        
        header('Location: ubicaciones_view.php');
        exit();
    }else{
        $modalContent = "
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
}
?>
