<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es un profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener solo los cursos que imparte el profesor
$username = $_SESSION['username'];
$sql = "SELECT c.id, c.nombre, c.descripcion_breve, c.descripcion FROM cursos c 
        INNER JOIN usuarios u ON c.profesor_id = u.id 
        WHERE u.username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cursos - Profesor - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/gestionar_cursos_profesor.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <!-- Sidebar para Profesor -->
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
            <a href="gestionar_cursos_profesor.php" aria-current="page">
                <i data-lucide="book-open" class="nav-icon"></i>
                Gestionar Cursos
            </a>
            <a href="subir_recurso.php">
                <i data-lucide="upload" class="nav-icon"></i>
                Subir Recursos
            </a>
            <a href="configuracion_profesor.php">
        <i data-lucide="settings" class="nav-icon"></i>
        Configuración
    </a>
            <a href="./InicioSesion/CerrarSesion.php">
                <i data-lucide="log-out" class="nav-icon"></i>
                Cerrar sesión
            </a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <main id="main-content" class="main">
        <h1>Gestionar Cursos - Profesor</h1>

        <!-- Botón para agregar cursos -->
        <a href="agregar_curso_profesor.php" class="btn btn-primary">
            <i data-lucide="plus" class="btn-icon"></i>
            Agregar Nuevo Curso
        </a>

        <!-- Lista de cursos -->
        <section class="card courses-list" aria-labelledby="lista-cursos">
            <h2 id="lista-cursos">Lista de Cursos</h2>
            <?php if (count($cursos) > 0): ?>
                <div class="courses-grid">
                    <?php foreach ($cursos as $curso): ?>
                        <article class="course-card">
                            <div class="course-content">
                                <h3 class="course-title"><?php echo htmlspecialchars($curso['nombre']); ?></h3>
                                <p class="course-description"><?php echo htmlspecialchars($curso['descripcion_breve']); ?></p>
                            </div>
                            <div class="course-actions">
                                <a href="editar_curso_profesor.php?id=<?php echo $curso['id']; ?>" 
                                   class="btn btn-secondary" 
                                   aria-label="Editar <?php echo htmlspecialchars($curso['nombre']); ?>">
                                    <i data-lucide="edit" class="btn-icon"></i>
                                    Editar
                                </a>
                                <a href="eliminar_curso_profesor.php?id=<?php echo $curso['id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('¿Estás seguro de eliminar este curso?');" 
                                   aria-label="Eliminar <?php echo htmlspecialchars($curso['nombre']); ?>">
                                    <i data-lucide="trash-2" class="btn-icon"></i>
                                    Eliminar
                                </a>
                                <a href="subir_recurso.php?id=<?php echo $curso['id']; ?>" 
                                   class="btn btn-secondary" 
                                   aria-label="Subir recurso para <?php echo htmlspecialchars($curso['nombre']); ?>">
                                    <i data-lucide="upload" class="btn-icon"></i>
                                    Subir Recurso
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-courses">No hay cursos disponibles. ¡Comienza agregando uno nuevo!</p>
            <?php endif; ?>
        </section>
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