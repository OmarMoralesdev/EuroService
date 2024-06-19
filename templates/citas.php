<?php
// Verificar si se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibir y limpiar los datos del formulario
    $nombre = htmlspecialchars($_POST["nombre"]);
    $apellido = htmlspecialchars($_POST["apellido"]);
    $correo = htmlspecialchars($_POST["correo"]);
    $contraseña = password_hash($_POST["contraseña"], PASSWORD_DEFAULT); // Encriptar la contraseña

    // Realizar la conexión a la base de datos
    $host ="127.0.0.1";
    $usuario ="root"; 
    $contraseña =""; 
    $nombre_db ="barberiafinal_db"; 

    $conexion = new mysqli($host, $usuario, $contraseña_db, $nombre_db);

    // Verificar si hubo algún error en la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Consulta SQL para verificar si el correo electrónico ya está registrado
    $consulta = "SELECT * FROM Usuarios WHERE correo = '$correo'";
    $resultado = $conexion->query($consulta);

    // Verificar si se encontraron registros con el mismo correo electrónico
    if ($resultado->num_rows > 0) {
        echo "El correo electrónico ya está registrado. Por favor, intenta con otro.";
    } else {
        // Si el correo no está registrado, insertar el nuevo usuario en la base de datos
        $consulta_insertar = "INSERT INTO Usuarios (nombre, apellido, correo, contraseña) VALUES ('$nombre', '$apellido', '$correo', '$contraseña')";
        
        if ($conexion->query($consulta_insertar) === TRUE) {
            echo "Registro exitoso. ¡Bienvenido!";
        } else {
            echo "Error al registrar el usuario: " . $conexion->error;
        }
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    // Si no se recibieron datos por POST, redireccionar a la página de registro
    header("Location: registro.php");
    exit();
}
?>
