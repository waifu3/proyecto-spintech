<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener el id del curso desde la URL
$curso_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validar que el id del curso es válido
if ($curso_id <= 0) {
    header('Location: cursos.php');
    exit;
}

try {
    // Consulta para obtener los detalles del curso
    $query = "SELECT * FROM cursos WHERE id = :curso_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt->execute();
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        echo "Curso no encontrado.";
        exit;
    }

    // Obtener módulos del curso
    $query_modulos = "SELECT * FROM modulos WHERE curso_id = :curso_id";
    $stmt_modulos = $pdo->prepare($query_modulos);
    $stmt_modulos->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_modulos->execute();
    $modulos = $stmt_modulos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los videos del curso
    $query_videos = "SELECT * FROM recursos WHERE curso_id = :curso_id AND tipo_recurso = 'video'";
    $stmt_videos = $pdo->prepare($query_videos);
    $stmt_videos->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_videos->execute();
    $videos = $stmt_videos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener otros recursos del curso (excluyendo videos)
    $query_recursos = "SELECT * FROM recursos WHERE curso_id = :curso_id AND tipo_recurso != 'video'";
    $stmt_recursos = $pdo->prepare($query_recursos);
    $stmt_recursos->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_recursos->execute();
    $recursos = $stmt_recursos->fetchAll(PDO::FETCH_ASSOC);

    // Asignar variables para la vista
    $nombre_curso = htmlspecialchars($curso['nombre']);
    $descripcion_curso = htmlspecialchars($curso['descripcion']);
    $categoria_curso = isset($curso['categoria']) ? htmlspecialchars($curso['categoria']) : 'Sin categoría';

} catch (PDOException $e) {
    // Manejar errores de base de datos
    error_log("Error de base de datos: " . $e->getMessage());
    echo "Ha ocurrido un error al cargar el curso. Por favor, inténtelo más tarde.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nombre_curso; ?> - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/curso.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <header>
        <div class="header-content container">
            <a href="javascript:history.back()" class="back-button" aria-label="Volver a la página anterior">
                <i data-lucide="arrow-left"></i>
            </a>
            <a href="index.php" class="logo-link">
                <h1 class="logo">Spintech</h1>
            </a>
            <nav aria-label="Navegación principal">
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="./Home/dashboard.php">Perfil</a></li>
                    <li><a href="cursos.php">Cursos</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </div>
        <div class="accessibility-bar">
            <div class="container">
                <div class="accessibility-controls">
                    <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
                    <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
                </div>
            </div>
        </div>
    </header>

    <main id="main-content">
        <div class="course-header">
            <div class="container">
                <h1><?php echo $nombre_curso; ?></h1>
                <p><strong>Categoría:</strong> <?php echo $categoria_curso; ?></p>
            </div>
        </div>

        <div class="container course-content">
            <section class="modules-section">
                <h2>Módulos del curso</h2>
                <?php if (!empty($modulos)): ?>
                    <ul>
                        <?php foreach ($modulos as $modulo): ?>
                            <li>
                                <h3><?php echo htmlspecialchars($modulo['nombre_modulo']); ?></h3>
                                <p><?php echo htmlspecialchars($modulo['descripcion']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay módulos disponibles para este curso.</p>
                <?php endif; ?>
            </section>

            <section class="videos-section">
                <h2>Videos del curso</h2>
                <?php if (!empty($videos)): ?>
                    <div class="video-grid">
                        <?php foreach ($videos as $video): ?>
                            <div class="video-item">
                                <h3><?php echo htmlspecialchars($video['nombre_archivo']); ?></h3>
                                <video controls>
                                    <source src="<?php echo htmlspecialchars($video['ruta_archivo']); ?>" type="video/mp4">
                                    Tu navegador no soporta la reproducción de videos.
                                </video>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No hay videos disponibles para este curso.</p>
                <?php endif; ?>
            </section>

            <aside class="recursos-adicionales">
                <h2>Recursos adicionales del curso</h2>
                <?php if (!empty($recursos)): ?>
                    <ul>
                        <?php foreach ($recursos as $recurso): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($recurso['nombre_archivo']); ?></strong>
                                <span>(<?php echo htmlspecialchars($recurso['tipo_recurso']); ?>)</span>
                                <a href="<?php echo htmlspecialchars($recurso['ruta_archivo']); ?>" target="_blank" rel="noopener noreferrer">Acceder</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No hay recursos adicionales disponibles para este curso.</p>
                <?php endif; ?>
            </aside>

            <div class="finish-course">
                <a href="#" class="btn-finish-course">Finalizar Curso</a>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal-overlay" style="display: none;">
        <div class="modal" role="dialog" aria-labelledby="modalTitle">
            <h2 id="modalTitle">¡Curso Finalizado!</h2>
            <p>Has completado exitosamente el curso. ¡Felicidades!</p>
            <button class="btn-close-modal">
                <i data-lucide="x"></i>
                <span class="sr-only">Cerrar</span>
            </button>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 Spintech. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Funcionalidad para alternar el contraste alto
        const contrastToggle = document.getElementById('contrast-toggle');
        const body = document.body;

        contrastToggle.addEventListener('click', () => {
            body.classList.toggle('high-contrast');
            const isHighContrast = body.classList.contains('high-contrast');
            localStorage.setItem('highContrast', isHighContrast);
        });

        // Funcionalidad para cambiar el tamaño de fuente
        const fontSizeToggle = document.getElementById('font-size-toggle');

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

        // Funcionalidad para mostrar el modal y redirigir
        document.querySelector('.btn-finish-course').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.modal-overlay').style.display = 'flex';
        });

        document.querySelector('.btn-close-modal').addEventListener('click', function() {
            document.querySelector('.modal-overlay').style.display = 'none';
            window.location.href = 'cursos.php';
        });
    </script>
</body>
</html>