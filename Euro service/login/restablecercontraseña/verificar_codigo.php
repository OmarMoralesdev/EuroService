<?php
// verificar_codigo.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigoIngresado = $_POST['codigo'];
    $codigoRecuperacion = $_SESSION['codigo_recuperacion'];

    if ($codigoIngresado == $codigoRecuperacion) {
        echo "Código verificado correctamente. Puedes proceder a cambiar tu contraseña.";
        header("Location: cambiar_contraseña.html");
    } else {
        echo "Código incorrecto. Inténtalo de nuevo.";
    }
}
?>
