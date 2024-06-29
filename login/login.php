<?php

session_start();


require '../includes/db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
    
        $sql = "SELECT id, username, password, roles FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);

      
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
            $db_username = $row['username'];
            $hashed_password = $row['password'];
            $role = $row['roles'];

          
            if (password_verify($password, $hashed_password)) {
            
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role;

              
                if ($role === 'cliente') {
                    header("Location: ../client_view/client.html");
                    exit();
                } elseif ($role === 'administrador') {
                    header("Location: ../includes/vabr.html");
                    exit();
                } elseif ($role === 'dueño') {
                    header("Location: ../owner_view/owner.html");
                    exit();
                } else {
                    echo "Rol no reconocido.";
                }
            } else {
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "No se encontró el usuario.";
        }
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }

   
    $conn = null;
}
?>
=======
        .form-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: #A3A3A3;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .form-container h1 {
            text-align: center;
        }
        .form-container h2 {
            font-size: 30px;
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"],
        input[type="password"] {
            border: 2px solid #100630;
            border-radius: 5px;
            background: linear-gradient(to right, #F0F0F0 25%, #C9C9C9 75%);
            width: 100%;
            padding: 10px;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 5px #1544FF;
        }
        input[type="submit"] {
            background: linear-gradient(to right, #000000 25%, #171717 75%);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background: #383838;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>TALLER EURO SERVICE</h1>
        <h2>Bienvenido</h2>
        <form method="post" action="loginb.php">
            <div class="form-group">
                <label for="username">Nombre:</label>
                <input type="text" class="form-control" id="username" name="username" autocomplete="on" placeholder="Ingresa tu usuario" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
            </div>
            <input type="submit" value="Iniciar sesión">
        </form>
    </div>
</body>
</html>
>>>>>>> a580fdc06025389d0b87eede2a6711b5ad88aca2
