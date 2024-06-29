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
                    header("Location: ../general_views/client.php");
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
            }
        } else {
            echo "No se encontró el usuario.";
        }
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
    $valid_username = 'usuario';
    $valid_password = 'contraseña';

    if ($_POST['username'] === $valid_username && $_POST['password'] === $valid_password) {
   
        header('Location: inicio.php');
        exit();
    } else {
 
        $_SESSION['error'] = 'Nombre de usuario o contraseña incorrectos.';
        header('Location: login.php');
        exit();
    }

    $conn = null;
}
