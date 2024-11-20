<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$curso_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($curso_id == 0) {
    echo "Curso no encontrado.";
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener el profesor_id del usuario que está eliminando el curso
$username = $_SESSION['username'];
$sql_profesor = "SELECT id FROM usuarios WHERE username = :username";
$stmt_profesor = $pdo->prepare($sql_profesor);
$stmt_profesor->bindParam(':username', $username, PDO::PARAM_STR);
$stmt_profesor->execute();
$profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);
$profesor_id = $profesor['id']; 

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Eliminar inscripciones asociadas con el curso
    $sql_delete_inscripciones = "DELETE FROM inscripciones WHERE curso_id = :curso_id";
    $stmt_inscripciones = $pdo->prepare($sql_delete_inscripciones);
    $stmt_inscripciones->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_inscripciones->execute();

    // Eliminar módulos asociados con el curso
    $sql_delete_modulos = "DELETE FROM modulos WHERE curso_id = :curso_id";
    $stmt_modulos = $pdo->prepare($sql_delete_modulos);
    $stmt_modulos->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_modulos->execute();

    // Eliminar el curso solo si pertenece al profesor
    $sql_delete_curso = "DELETE FROM cursos WHERE id = :curso_id AND profesor_id = :profesor_id";
    $stmt_curso = $pdo->prepare($sql_delete_curso);
    $stmt_curso->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_curso->bindParam(':profesor_id', $profesor_id, PDO::PARAM_INT);
    $stmt_curso->execute();

    // Confirmar la transacción
    $pdo->commit();

    // Redirigir a la página de gestión de cursos
    header("Location: gestionar_cursos_profesor.php");
    exit();

} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    $pdo->rollBack();
    echo "Error al eliminar el curso: " . $e->getMessage();
}
?>
