<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es un profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

// Obtener el curso_id desde la URL
$curso_id = isset($_GET['curso_id']) ? intval($_GET['curso_id']) : 0;

if ($curso_id == 0) {
    $error_message = "Curso no encontrado.";
} else {
    // Conectar a la base de datos
    $connection = new Connection();
    $pdo = $connection->connect();

    // Verificar que el profesor tiene acceso al curso
    $username = $_SESSION['username'];
    $sql_profesor = "SELECT * FROM cursos WHERE id = :curso_id AND profesor_id = (SELECT id FROM usuarios WHERE username = :username)";
    $stmt_profesor = $pdo->prepare($sql_profesor);
    $stmt_profesor->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_profesor->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt_profesor->execute();

    if ($stmt_profesor->rowCount() == 0) {
        $error_message = "No tienes permisos para ver los estudiantes de este curso.";
    } else {
        // Obtener la lista de estudiantes inscritos en el curso
        $sql_estudiantes = "
            SELECT u.id, u.username, u.email 
            FROM inscripciones i
            JOIN usuarios u ON i.usuario_id = u.id
            WHERE i.curso_id = :curso_id";
        
        $stmt_estudiantes = $pdo->prepare($sql_estudiantes);
        $stmt_estudiantes->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
        $stmt_estudiantes->execute();
        $estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);

        // Obtener el nombre del curso
        $sql_curso = "SELECT nombre FROM cursos WHERE id = :curso_id";
        $stmt_curso = $pdo->prepare($sql_curso);
        $stmt_curso->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
        $stmt_curso->execute();
        $curso = $stmt_curso->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes del Curso - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/ver_estudiantes.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>
            <i data-lucide="book-open" class="nav-icon"></i>
            Spintech
        </h2>
        <nav aria-label="Navegación principal">
            <a href="./Home/perfil_profesor.php">
                <i data-lucide="user" class="nav-icon"></i>
                Perfil
            </a>
            <a href="gestionar_cursos_profesor.php">
                <i data-lucide="book-open" class="nav-icon"></i>
                Gestionar Cursos
            </a>
            <a href="subir_recurso.php">
                <i data-lucide="upload" class="nav-icon"></i>
                Subir Recursos
            </a>
            <a href="./InicioSesion/CerrarSesion.php">
                <i data-lucide="log-out" class="nav-icon"></i>
                Cerrar sesión
            </a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <main id="main-content" class="main">
        <div class="title-wrapper">
            <a href="javascript:history.back()" class="back-arrow" aria-label="Volver a la página anterior">
                <i data-lucide="arrow-left"></i>
            </a>
            <h1>Estudiantes del Curso: <?php echo isset($curso) ? htmlspecialchars($curso['nombre']) : ''; ?></h1>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>Lista de Estudiantes</h2>
                <?php if (count($estudiantes) > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de Usuario</th>
                                    <th>Correo Electrónico</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $estudiante): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($estudiante['id']); ?></td>
                                        <td><?php echo htmlspecialchars($estudiante['username']); ?></td>
                                        <td><?php echo htmlspecialchars($estudiante['email']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No hay estudiantes inscritos en este curso.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
        const contrastToggle = document.getElementById('contrast-toggle');
        const fontSizeToggle = document.getElementById('font-size-toggle');
        const body = document.body;

        contrastToggle.addEventListener('click', () => {
            body.classList.toggle('high-contrast');
            const isHighContrast = body.classList.contains('high-contrast');
            localStorage.setItem('highContrast', isHighContrast);
        });

        fontSizeToggle.addEventListener('click', () => {
            body.classList.toggle('large-text');
            const isLargeText = body.classList.contains('large-text');
            localStorage.setItem('largeText', isLargeText);
        });

        // Restaurar preferencias del usuario
        window.addEventListener('load', () => {
            if (localStorage.getItem('highContrast') === 'true') {
                body.classList.add('high-contrast');
            }
            if (localStorage.getItem('largeText') === 'true') {
                body.classList.add('large-text');
            }
        });

        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
</body>
</html>