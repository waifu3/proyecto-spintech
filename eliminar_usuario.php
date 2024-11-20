<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

$usuario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($usuario_id == 0) {
    echo "Usuario no encontrado.";
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Eliminar el usuario
$sql = "DELETE FROM usuarios WHERE id = :usuario_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();

// Redirigir a la página de gestión de usuarios
header("Location: gestionar_usuarios.php");
exit();
