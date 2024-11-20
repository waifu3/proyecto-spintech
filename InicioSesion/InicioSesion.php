<?php

require_once '../config/Connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $connection = new Connection(); 
        $pdo = $connection->connect();

        $sql = "SELECT * FROM usuarios WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Guardar datos del usuario en la sesión
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username']; 
            $_SESSION['role_id'] = $user['role_id']; 

            // Redirigir según el rol del usuario
            if ($user['role_id'] == 1) {
                header('Location: ../Home/perfil_administrador.php');  // Administrador
            } elseif ($user['role_id'] == 3) {
                header('Location: ../Home/perfil_profesor.php');  // Profesor
            } elseif ($user['role_id'] == 2) {
                header('Location: ../Home/dashboard.php');  // Usuario común
            } else {
                header("Location: ../login.php?error=" . urlencode("Acceso Denegado: Rol no válido."));
            }
            exit();
        } else {
            // Redirigir de vuelta a login.php con un mensaje de error
            header("Location: ../login.php?error=" . urlencode("Credenciales incorrectas."));
            exit();
        }
    } catch (PDOException $e) {
        // Redirigir de vuelta a login.php con un mensaje de error de conexión
        header("Location: ../login.php?error=" . urlencode("Error en la conexión: " . $e->getMessage()));
        exit();
    }
}
