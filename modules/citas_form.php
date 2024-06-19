<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbería Capital</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        #ribbon {
            background-color: black;
            /* Puedes agregar un degradado para hacerlo más interesante */
            background: linear-gradient(to bottom, black, #333);
            width: 100%;
            position: fixed;
            top: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 1000;
            transition: background-color 0.3s ease;
        }
        #logo img {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
        #ribbon ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        #ribbon ul li {
            margin: 0 10px;
        }
        #ribbon ul li a {
            color: white;
            text-decoration: none;
        }
        #ribbon ul li a:hover {
            color: #666;
        }
        .container {
    max-width: 600px;
    margin: 150px auto 0; /* Ajuste del margen superior para evitar que se oculte bajo la barra superior */
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
        h1 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="email"], input[type="date"], input[type="time"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div id="ribbon">
    <div id="logo">
        <a href="../Pagina/index.php">
            <img src="../Componentes/OIP-removebg-preview.png" alt="Logo de Barbería Capital">
        </a>
    </div>
    <ul>
        <li><a href="../Servicios/servicios.php">Servicios</a></li>
        <li><a href="reservar_cita.php">Haz una Cita</a></li>
        <li><a href="../Sucurasales/suscursal.php">Sucursales</a></li>
        <li><a href="../Tienda/index_tienda.php">Tienda</a></li>
        <li><a href="../Login/Login.php">Iniciar Sesión</a></li>
    </ul>
</div>

<div class="container">
    <h1>Reserva tu cita</h1>
    <form action="procesar_cita.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required>

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>

        <label for="hora">Hora:</label>
        <input type="time" id="hora" name="hora" required>

        <label for="servicio">Selecciona un servicio:</label>
        <select id="servicio" name="servicio" required>
            <option value="">Selecciona un servicio</option>
            <option value="1">Corte de pelo</option>
            <option value="2">Afeitado</option>
            <option value="3">Corte y afeitado</option>
        </select>

        <input type="submit" value="Reservar cita">
    </form>
</div>

<div class="bottom-bar">
    <!-- Contenido de la barra inferior -->
</div>

<script>
    window.addEventListener('scroll', function() {
        var scrollPosition = window.scrollY;
        var windowHeight = window.innerHeight;
        var fullHeight = document.body.scrollHeight;

        if (scrollPosition + windowHeight >= fullHeight) {
            document.querySelector('.bottom-bar').style.display = 'block';
        } else {
            document.querySelector('.bottom-bar').style.display = 'none';
        }
    });
</script>

</body>
</html>
