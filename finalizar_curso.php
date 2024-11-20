<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener el id del curso desde la URL
$curso_id = isset($_GET['curso_id']) ? intval($_GET['curso_id']) : 0;
$usuario_id = $_SESSION['user_id']; // Suponiendo que el ID del usuario está almacenado en la sesión

if ($curso_id > 0) {
    // Verificar si el usuario ya completó el curso
    $query_check = "SELECT * FROM cursos_completados WHERE usuario_id = :usuario_id AND curso_id = :curso_id";
    $stmt_check = $pdo->prepare($query_check);
    $stmt_check->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->rowCount() === 0) {
        // Marcar el curso como completado
        $query = "INSERT INTO cursos_completados (usuario_id, curso_id, completado, fecha_completado) VALUES (:usuario_id, :curso_id, 1, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redireccionar con un mensaje de éxito
            header("Location: curso.php?id=$curso_id&status=completed");
            exit;
        } else {
            echo "Error al marcar el curso como completado.";
        }
    } else {
        // El curso ya estaba completado
        header("Location: curso.php?id=$curso_id&status=already_completed");
        exit;
    }
} else {
    echo "ID de curso inválido.";
}
?>
