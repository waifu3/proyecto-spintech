<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Verificar si se proporcionó un ID de curso
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: gestionar_cursos_admin.php?error=id_invalido');
    exit();
}

$curso_id = $_GET['id'];

try {
    $connection = new Connection();
    $pdo = $connection->connect();
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Primero eliminar registros relacionados en cursos_completados
    $stmt = $pdo->prepare("DELETE FROM cursos_completados WHERE curso_id = ?");
    $stmt->execute([$curso_id]);
    
    // Eliminar inscripciones relacionadas
    $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE curso_id = ?");
    $stmt->execute([$curso_id]);
    
    // Eliminar recursos relacionados
    $stmt = $pdo->prepare("DELETE FROM recursos WHERE curso_id = ?");
    $stmt->execute([$curso_id]);
    
    // Eliminar videos relacionados
    $stmt = $pdo->prepare("DELETE FROM videos WHERE curso_id = ?");
    $stmt->execute([$curso_id]);
    
    // Eliminar módulos relacionados
    $stmt = $pdo->prepare("DELETE FROM modulos WHERE curso_id = ?");
    $stmt->execute([$curso_id]);
    
    // Finalmente, eliminar el curso
    $stmt = $pdo->prepare("DELETE FROM cursos WHERE id = ?");
    $stmt->execute([$curso_id]);
    
    // Confirmar la transacción
    $pdo->commit();
    
    header('Location: gestionar_cursos_admin.php?success=curso_eliminado');
    exit();
    
} catch (PDOException $e) {
    // Si hay algún error, revertir la transacción
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Registrar el error para debugging
    error_log("Error al eliminar curso ID $curso_id: " . $e->getMessage());
    
    // Redirigir con mensaje de error
    header('Location: gestionar_cursos_admin.php?error=error_eliminacion');
    exit();
}
?>