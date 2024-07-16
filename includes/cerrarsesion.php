<?php
function cerrarsesion()
{
    session_start();
    session_destroy();
    header("Location: ../login/login_view.php");
    exit();
}


cerrarsesion();
?>
